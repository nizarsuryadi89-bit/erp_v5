<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Warehouse postings use base units and run inside the caller's transaction. */
class Warehouse_stock
{
    private $db;
    private $session;

    public function __construct($params = [])
    {
        if (isset($params['db'], $params['session'])) {
            $this->db = $params['db']; $this->session = $params['session'];
        } else {
            $ci = get_instance(); $this->db = $ci->db; $this->session = $ci->session;
        }
    }

    public function material($id)
    {
        if (!is_scalar($id) || !ctype_digit((string)$id) || (int)$id < 1) throw new DomainException('Material tidak valid.');
        $result = $this->db->query('SELECT * FROM tbl_material WHERE material_id = ? FOR UPDATE', [(int)$id]);
        if (!$result || !($material = $result->row())) throw new DomainException('Material tidak ditemukan.');
        if ($material->konversi === null || (is_numeric($material->konversi) && (float)$material->konversi === 0.0)) $material->konversi = 1;
        if (!is_numeric($material->konversi) || !is_finite((float)$material->konversi) || (float)$material->konversi <= 0) {
            throw new DomainException('Konversi material harus lebih dari nol.');
        }
        return $material;
    }

    public function rows($material, $warehouse, $rack = null, $specific = false)
    {
        $this->material($material);
        $sql = 'SELECT * FROM tbl_stok_gudang WHERE material_id = ? AND gudang_id = ?';
        $binds = [(int)$material, (int)$warehouse];
        if ($specific) {
            if ($rack === null) $sql .= ' AND rak_id IS NULL';
            else { $sql .= ' AND rak_id = ?'; $binds[] = (int)$rack; }
        }
        $res = $this->db->query($sql . ' ORDER BY stok_id FOR UPDATE', $binds);
        if (!$res) throw new RuntimeException('Gagal membaca stok.');
        return $res->result();
    }

    public function total($material, $warehouse)
    {
        $sum = 0; foreach ($this->rows($material, $warehouse) as $r) $sum += (float)$r->qty;
        return round($sum, 2);
    }

    /** Negative allocations consume existing racks; rack-specific moves never consume another rack. */
    public function post($material_id, $warehouse, $delta, $type, $trx_id, $note = '', $rack = null, $allocate = false)
    {
        if (!is_numeric($delta) || !is_finite((float)$delta) || abs((float)$delta) > 9999999999999) throw new DomainException('Jumlah mutasi tidak valid.');
        if (!in_array($type, ['Pembelian','Pengambilan','Mutation','Opname','Adjustment','Return','Produksi'], true)) throw new DomainException('Jenis mutasi tidak valid.');
        $delta = round((float)$delta, 2);
        if ($delta == 0) return true;
        $material = $this->material($material_id);
        $rows = $this->rows($material_id, $warehouse, $rack, !$allocate || $delta > 0);
        $sum = 0; foreach ($rows as $r) $sum += (float)$r->qty;
        if ($delta < 0 && round($sum + $delta, 2) < 0) throw new DomainException('Stok tidak mencukupi untuk material ' . $material_id . '.');
        $changes = [];
        if ($delta > 0) {
            if (count($rows) > 1) throw new DomainException('Ada saldo ganda pada rak. Perbaiki data terlebih dahulu.');
            $changes[] = [$rows ? $rows[0] : null, $delta, $rack];
        } else {
            $remaining = -$delta;
            foreach ($rows as $r) {
                $use = min(max(0, (float)$r->qty), $remaining);
                if ($use > 0) { $changes[] = [$r, -$use, $r->rak_id]; $remaining = round($remaining - $use, 2); }
                if ($remaining <= 0) break;
            }
            if ($remaining > 0) throw new DomainException('Saldo rak tidak mencukupi.');
        }
        foreach ($changes as $change) {
            list($row, $base, $rack_id) = $change;
            $balance = round(($row ? (float)$row->qty : 0) + $base, 2);
            if ($row) $ok = $this->db->update('tbl_stok_gudang', ['qty' => $balance, 'last_update' => date('Y-m-d H:i:s')], ['stok_id' => $row->stok_id]);
            else $ok = $this->db->insert('tbl_stok_gudang', ['material_id' => $material_id, 'gudang_id' => $warehouse, 'rak_id' => $rack_id, 'qty' => $balance]);
            if (!$ok) throw new RuntimeException('Gagal memperbarui saldo stok.');
            $display = abs($base) / (float)$material->konversi;
            if (!$this->db->insert('tbl_stok_log', ['material_id' => $material_id, 'gudang_id' => $warehouse,
                'rak_id' => $rack_id, 'trx_type' => $type, 'trx_id' => $trx_id, 'qty_in' => $base > 0 ? $display : 0,
                'qty_out' => $base < 0 ? $display : 0, 'qty_base' => abs($base), 'qty_balance' => $balance,
                'user_id' => $this->session->userdata('id'), 'karyawan_id' => $this->session->userdata('karyawan_id') ?: null,
                'catatan' => $note, 'created_at' => date('Y-m-d H:i:s')])) throw new RuntimeException('Gagal mencatat mutasi stok.');
        }
        return true;
    }
}
