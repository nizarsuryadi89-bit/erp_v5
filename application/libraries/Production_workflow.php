<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Read legacy values without rewriting historical records. */
class Production_workflow
{
    public static function finishing_operator($operator)
    {
        return (bool) preg_match('/finishing/i', (string) ($operator->operator_nama ?? ''));
    }

    public static function finishing_step($step)
    {
        // Older routing records have names such as F.Laminating but no finishing flag.
        return !empty($step->is_finishing) || self::finishing_operator($step) || (bool) preg_match(
            '/^(?:f[.\s_-]|finishing\b|lamina(?:si|ting|s)\b|potong\b|cutting\b|jahit\b|packing\b|pon\b|kiss\s*cut\b|die[\s-]*cut\b)/i',
            trim((string) ($step->routing_name ?? ''))
        );
    }

    public static function production_location($item)
    {
        return (int) (($item->lokasi_cetak ?? 0) ?: ($item->perusahaan_id ?? 0));
    }

    public static function finishing_requires_machine($step, $product = null)
    {
        if (!self::finishing_step($step)) return false;
        $category = (int) ($product->kategori_id ?? $step->kategori_id ?? 0);
        $name = ($product->product_nama ?? $step->product_nama ?? '') . ' ' . ($step->routing_name ?? '');
        // Digital A3 and separate finishing products named A3 do not require a machine.
        if ($category === 1 || preg_match('/\bA3\b/i', $name)) return false;
        return (bool) preg_match('/\b(?:cutting|potong|kiss\s*cut|die[\s-]*cut)\b/i', (string) ($step->routing_name ?? ''));
    }

    public static function machine_matches_locations($machine, $locations)
    {
        if (!$locations) return false;
        foreach ($locations as $location) {
            if ((int) $location < 1) return false;
            if (!empty($machine->perusahaan_id) && (int) $machine->perusahaan_id !== (int) $location) return false;
        }
        return true;
    }

    public static function step_completed($status)
    {
        return in_array((string) $status, ['2', '3', 'done', 'on_pb', 'on_costumer'], true);
    }

    public static function pending($status)
    {
        return in_array((string) $status, ['', '0', 'pending', 'approve', 'approved'], true);
    }

    public static function active_header($status)
    {
        return self::pending($status) || in_array((string) $status, ['proses', 'finishing'], true);
    }

    public static function number($value, $label)
    {
        if (is_string($value)) {
            $value = str_replace(',', '.', trim($value));
        }
        if (!is_numeric($value) || !is_finite((float) $value) || (float) $value < 0) {
            throw new DomainException($label . ' harus berupa angka nol atau positif.');
        }
        return (float) $value;
    }

    public static function ids($ids)
    {
        if (!is_array($ids) || !$ids || count($ids) > 500) {
            throw new DomainException('Pilih 1 sampai 500 item produksi.');
        }
        $result = [];
        foreach ($ids as $id) {
            if (!ctype_digit((string) $id) || (int) $id < 1) {
                throw new DomainException('ID produksi tidak valid.');
            }
            $result[(int) $id] = (int) $id;
        }
        sort($result, SORT_NUMERIC);
        return $result;
    }

    public static function current_step($steps, $action, $expected_id = null)
    {
        $running = [];
        $first = null;
        foreach ($steps as $step) {
            if ((string) $step->status_produksi === '1') {
                $running[] = $step;
            }
            if ($first === null && !self::step_completed($step->status_produksi)) {
                $first = $step;
            }
        }
        if (count($running) > 1 || ($running && $first !== $running[0])) {
            throw new DomainException('Urutan tahap tidak konsisten. Tinjau riwayat sebelum melanjutkan.');
        }
        if (!$first) {
            throw new DomainException('Tidak ada tahap terbuka. Muat ulang daftar produksi.');
        }
        if ($expected_id !== null && (int) $expected_id !== (int) $first->detail_produksi_id) {
            throw new DomainException('Tahap telah berubah. Muat ulang sebelum mengirim hasil.');
        }
        if ($action === 'start' && (!self::pending($first->status_produksi) || $running)) {
            throw new DomainException('Tahap sudah berjalan atau tidak dapat dimulai.');
        }
        $direct_finishing = $action === 'complete' && self::pending($first->status_produksi) && self::finishing_step($first);
        if (in_array($action, ['complete', 'cancel'], true) && (string) $first->status_produksi !== '1' && !$direct_finishing) {
            throw new DomainException('Mulai tahap ini dahulu sebelum mencatat hasil atau membatalkan mulai.');
        }
        return $first;
    }

    /** Optimistic version for retry protection, including partial/failed results. */
    public static function version($step)
    {
        $fields = ['detail_produksi_id', 'status_produksi', 'waktu_mulai', 'waktu_selesai',
            'qty_output', 'qty_missing', 'qty_bahan_pakai', 'qty_bahan_rusak', 'catatan'];
        $values = [];
        foreach ($fields as $field) {
            $values[] = isset($step->$field) ? (string) $step->$field : '';
        }
        return hash('sha256', json_encode($values));
    }

    public static function all_completed($steps)
    {
        if (!$steps) return false;
        foreach ($steps as $step) {
            if (!self::step_completed($step->status_produksi)) return false;
        }
        return true;
    }

    public static function can_transition($from, $to)
    {
        if ((string) $from === (string) $to) return true;
        if ($to === 'proses') return self::active_header($from);
        if ($to === 'done') return in_array((string) $from, ['proses', 'finishing'], true);
        if ($to === 'on_pb') return (string) $from === 'done';
        if ($to === 'on_costumer') return (string) $from === 'on_pb';
        return false;
    }

    /** Old completed headers appear in PB without rewriting historical stock or steps. */
    public static function pb_ready_sql($alias = 'p')
    {
        if (!preg_match('/\A[a-zA-Z_][a-zA-Z0-9_]*\z/', $alias)) throw new InvalidArgumentException('Alias produksi tidak valid.');
        return "({$alias}.status_produksi = 'on_pb' OR ({$alias}.status_produksi = 'done'
            AND (COALESCE({$alias}.finishing_id, 0) = 0 OR LOWER(COALESCE({$alias}.status_finishing, '')) IN ('done','selesai','finished','on_pb'))
            AND NOT EXISTS (SELECT 1 FROM detail_produksi legacy_pb WHERE legacy_pb.produksi_id = {$alias}.produksi_id
                AND (legacy_pb.status_produksi IS NULL OR legacy_pb.status_produksi NOT IN ('2','3','done','on_pb','on_costumer')))))";
    }

    /** Only the first unfinished operation can enter the finishing queue. */
    public static function finishing_queue_sql($alias = 'p', $operator_id = null)
    {
        if (!preg_match('/\A[a-zA-Z_][a-zA-Z0-9_]*\z/', $alias)) throw new InvalidArgumentException('Alias produksi tidak valid.');
        $operator_filter = $operator_id === null ? '' : ' AND fr.operator_id = ' . (int) $operator_id;
        return "({$alias}.status_produksi IN ('','0','pending','approve','approved','proses','finishing')
            AND EXISTS (SELECT 1 FROM detail_produksi fp JOIN routing fr ON fr.product_id = {$alias}.product_id
                AND (fr.harga_id IS NULL OR fr.harga_id = {$alias}.harga_id)
                AND fr.routing_id = COALESCE(NULLIF(fp.routing_id, 0),
                    (SELECT fallback.routing_id FROM routing fallback WHERE fallback.product_id = {$alias}.product_id
                        AND fallback.routing_urutan = fp.routing_urutan AND (fallback.harga_id IS NULL OR fallback.harga_id = {$alias}.harga_id)
                        ORDER BY fallback.routing_id DESC LIMIT 1))
                WHERE fp.produksi_id = {$alias}.produksi_id AND fp.status_produksi IN ('','0','pending','approve','approved','1')
                AND (fr.is_finishing = 1 OR LOWER(fr.routing_name) REGEXP '^(f[. _-]|finishing|laminasi|laminating|laminas|potong|cutting|jahit|packing|pon|kiss[[:space:]]*cut|die[ -]*cut)'
                    OR EXISTS (SELECT 1 FROM tbl_operator fo WHERE fo.operator_id = fr.operator_id AND LOWER(fo.operator_nama) LIKE '%finishing%'))
                {$operator_filter}
                AND NOT EXISTS (SELECT 1 FROM detail_produksi previous WHERE previous.produksi_id = fp.produksi_id
                    AND (previous.routing_urutan < fp.routing_urutan OR (previous.routing_urutan = fp.routing_urutan AND previous.detail_produksi_id < fp.detail_produksi_id))
                    AND (previous.status_produksi IS NULL OR previous.status_produksi NOT IN ('2','3','done','on_pb','on_costumer')))))";
    }
}
