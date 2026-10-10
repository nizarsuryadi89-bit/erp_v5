<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Read legacy values without rewriting historical records. */
class Production_workflow
{
    public static function production_location($item)
    {
        return (int) (($item->lokasi_cetak ?? 0) ?: ($item->perusahaan_id ?? 0));
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
        if (in_array($action, ['complete', 'cancel'], true) && (string) $first->status_produksi !== '1') {
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
}
