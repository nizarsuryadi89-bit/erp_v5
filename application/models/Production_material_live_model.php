<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Read-only feed of actual production stock deductions, never BOM estimates. */
class Production_material_live_model extends CI_Model
{
    const PAGE_SIZE = 50;

    public function filters($input, $actor)
    {
        $date = $input['date'] ?? date('Y-m-d');
        if (!is_string($date) || !preg_match('/\A\d{4}-\d{2}-\d{2}\z/', $date)) throw new DomainException('Tanggal tidak valid.');
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date) throw new DomainException('Tanggal tidak valid.');
        $warehouse = $input['warehouse'] ?? '';
        if ($warehouse === '') $warehouse = $actor['admin'] ? 0 : $actor['branch'];
        if (!is_scalar($warehouse) || !ctype_digit((string)$warehouse)) throw new DomainException('Gudang tidak valid.');
        $warehouse = (int)$warehouse;
        if (!$actor['admin']) {
            if ((int)$actor['branch'] < 1 || ($warehouse !== 0 && $warehouse !== (int)$actor['branch'])) throw new DomainException('Akses gudang ditolak.');
            $warehouse = (int)$actor['branch']; // "all" never expands a branch user's scope.
        }
        $page = $input['page'] ?? 1;
        if (!is_scalar($page) || !ctype_digit((string)$page) || (int)$page < 1 || (int)$page > 100000) throw new DomainException('Halaman tidak valid.');
        $search = $input['search'] ?? '';
        if (!is_string($search) || strlen($search) > 300) throw new DomainException('Pencarian terlalu panjang atau tidak valid.');
        return ['date' => $date, 'warehouse' => $warehouse, 'page' => (int)$page, 'search' => trim($search)];
    }

    public function warehouses($actor)
    {
        $sql = 'SELECT gudang_id, gudang_lokasi FROM tbl_gudang'; $binds = [];
        if (!$actor['admin']) {
            if ((int)$actor['branch'] < 1) throw new DomainException('Lokasi pengguna belum ditentukan.');
            $sql .= ' WHERE gudang_id = ?'; $binds[] = (int)$actor['branch'];
        }
        $result = $this->db->query($sql . ' ORDER BY gudang_lokasi, gudang_id', $binds);
        if (!$result) throw new RuntimeException('Daftar gudang belum dapat dimuat.');
        return $result->result_array();
    }

    public function feed($input, $actor)
    {
        $filters = $this->filters($input, $actor);
        $from = " FROM tbl_stok_log sl
            LEFT JOIN tbl_material m ON m.material_id = sl.material_id
            LEFT JOIN tbl_bahan b ON b.bahan_id = m.bahan_id
            LEFT JOIN tbl_satuan s ON s.satuan_id = b.satuan_id
            LEFT JOIN tbl_produksi p ON p.produksi_id = sl.trx_id
            LEFT JOIN tbl_product pr ON pr.product_id = p.product_id
            LEFT JOIN tbl_gudang g ON g.gudang_id = sl.gudang_id";
        $where = " WHERE sl.trx_type = 'Produksi' AND sl.qty_out > 0 AND sl.created_at >= ? AND sl.created_at < ?";
        $next = (new DateTimeImmutable($filters['date']))->modify('+1 day')->format('Y-m-d');
        $binds = [$filters['date'] . ' 00:00:00', $next . ' 00:00:00'];
        if ($filters['warehouse']) { $where .= ' AND sl.gudang_id = ?'; $binds[] = $filters['warehouse']; }
        if ($filters['search'] !== '') {
            $needle = '%' . strtr($filters['search'], ['=' => '==', '%' => '=%', '_' => '=_']) . '%';
            $where .= " AND (p.nospk LIKE ? ESCAPE '=' OR b.bahan_nama LIKE ? ESCAPE '=' OR m.varian LIKE ? ESCAPE '=' OR pr.product_nama LIKE ? ESCAPE '=')";
            for ($i = 0; $i < 4; $i++) $binds[] = $needle;
        }
        $summary = $this->db->query('SELECT COUNT(*) AS total, COUNT(DISTINCT p.nospk) AS spk_count, COUNT(DISTINCT sl.material_id) AS material_count' . $from . $where, $binds);
        if (!$summary) throw new RuntimeException('Ringkasan penggunaan bahan belum dapat dimuat.');
        $stats = $summary->row_array();
        $pages = max(1, (int)ceil((int)$stats['total'] / self::PAGE_SIZE));
        $filters['page'] = min($filters['page'], $pages);
        $offset = ($filters['page'] - 1) * self::PAGE_SIZE;
        $select = "SELECT sl.log_id, sl.trx_id AS produksi_id, sl.material_id, sl.gudang_id, sl.created_at,
            b.bahan_nama AS material_name, m.varian AS variant, s.satuan_nama AS unit,
            CASE WHEN sl.qty_base > 0 THEN sl.qty_base ELSE sl.qty_out * CASE WHEN m.konversi > 0 THEN m.konversi ELSE 1 END END AS production_qty,
            p.nospk, pr.product_nama AS product_name, g.gudang_lokasi AS warehouse_name";
        $result = $this->db->query($select . $from . $where . ' ORDER BY sl.log_id DESC LIMIT ' . self::PAGE_SIZE . ' OFFSET ' . $offset, $binds);
        if (!$result) throw new RuntimeException('Penggunaan bahan belum dapat dimuat.');
        $rows = $result->result_array();
        foreach ($rows as &$row) {
            $row['log_id'] = (int)$row['log_id']; $row['produksi_id'] = (int)$row['produksi_id'];
            $row['production_qty'] = (float)$row['production_qty'];
        }
        unset($row);
        return ['success' => true, 'rows' => $rows, 'total' => (int)$stats['total'], 'spk_count' => (int)$stats['spk_count'],
            'material_count' => (int)$stats['material_count'], 'page_size' => self::PAGE_SIZE, 'pages' => $pages,
            'filters' => $filters, 'server_time' => date('Y-m-d H:i:s'), 'today' => date('Y-m-d')];
    }
}
