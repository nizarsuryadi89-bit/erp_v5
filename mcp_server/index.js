import express from 'express';
import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { SSEServerTransport } from '@modelcontextprotocol/sdk/server/sse.js';
import { CallToolRequestSchema, ListToolsRequestSchema } from '@modelcontextprotocol/sdk/types.js';
import mysql from 'mysql2/promise';
import dotenv from 'dotenv';
import cors from 'cors';

dotenv.config();

const app = express();
app.use(cors());

// Middleware Autentikasi (Cek Bearer Token)
const AUTH_TOKEN = process.env.AUTH_TOKEN || "2f936681227db1ba5bc59b09ee0f7e50383fac4320a3953ec8357b0bb3f6c38b";
app.use((req, res, next) => {
  const authHeader = req.headers.authorization;
  if (!authHeader || authHeader !== `Bearer ${AUTH_TOKEN}`) {
    return res.status(401).json({ error: "Unauthorized" });
  }
  next();
});

// Setup Server MCP
const server = new Server(
  { name: "tgm-erp-mcp", version: "1.0.0" },
  { capabilities: { tools: {} } }
);

// Setup Koneksi Database MySQL
const pool = mysql.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

// Definisi Tools yang akan diexpose ke AI
server.setRequestHandler(ListToolsRequestSchema, async () => {
  return {
    tools: [
      {
        name: "query_database",
        description: "Menjalankan query SELECT ke database TGM ERP. STRICTLY SELECT QUERIES ONLY.",
        inputSchema: {
          type: "object",
          properties: {
            query: {
              type: "string",
              description: "SQL SELECT query yang ingin dijalankan"
            }
          },
          required: ["query"]
        }
      },
      {
        name: "get_table_schema",
        description: "Mengambil struktur/schema dari tabel di database",
        inputSchema: {
          type: "object",
          properties: {
            table_name: {
              type: "string",
              description: "Nama tabel yang ingin dicek schemanya"
            }
          },
          required: ["table_name"]
        }
      }
    ]
  };
});

// Menangani panggilan eksekusi tool dari AI
server.setRequestHandler(CallToolRequestSchema, async (request) => {
  const { name, arguments: args } = request.params;

  if (name === "query_database") {
    const query = args.query;
    
    // Keamanan: Hanya izinkan SELECT & SHOW (Cegah modifikasi data via tool ini)
    if (!query.trim().toUpperCase().startsWith('SELECT') && !query.trim().toUpperCase().startsWith('SHOW')) {
      throw new Error("Security Error: Only SELECT or SHOW queries are allowed.");
    }
    
    try {
      const [rows] = await pool.query(query);
      return {
        content: [{ type: "text", text: JSON.stringify(rows, null, 2) }]
      };
    } catch (error) {
      return {
        content: [{ type: "text", text: `Error executing query: ${error.message}` }],
        isError: true,
      };
    }
  }

  if (name === "get_table_schema") {
    const tableName = args.table_name;
    try {
      const [rows] = await pool.query(`DESCRIBE ??`, [tableName]);
      return {
        content: [{ type: "text", text: JSON.stringify(rows, null, 2) }]
      };
    } catch (error) {
      return {
        content: [{ type: "text", text: `Error describing table: ${error.message}` }],
        isError: true,
      };
    }
  }

  throw new Error(`Tool not found: ${name}`);
});

let transport = null;

// Endpoint SSE yang akan dihubungi oleh Antigravity IDE (Koneksi Awal)
app.get("/sse", async (req, res) => {
  try {
    transport = new SSEServerTransport("/message", res);
    await server.connect(transport);
  } catch (error) {
    console.error("SSE Error:", error);
    if (!res.headersSent) {
      res.status(500).json({ error: "SSE Transport Error", details: error.message });
    }
  }
});

// Endpoint untuk menerima pesan dari MCP Client (Antigravity IDE)
app.post("/message", express.json(), async (req, res) => {
  try {
    if (!transport) {
      return res.status(400).send("SSE transport not connected");
    }
    await transport.handlePostMessage(req, res);
  } catch (error) {
    console.error("Message Error:", error);
    if (!res.headersSent) {
      res.status(500).json({ error: "Message Handling Error", details: error.message });
    }
  }
});

// Endpoint Testing (Health check)
app.get("/health", (req, res) => {
  res.json({ status: "OK", node_version: process.version });
});

// Menjalankan Server Node.js (Cocok dengan cPanel Passenger)
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`MCP Server running on port ${PORT}`);
});
