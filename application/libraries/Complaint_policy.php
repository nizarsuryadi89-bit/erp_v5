<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complaint_policy
{
    public static function categories()
    {
        return array('Produksi', 'Mesin', 'Order/SPK', 'Kasir/Pembayaran', 'Gudang',
            'Customer Complaint', 'IT/ERP', 'SDM/Operasional', 'Pengiriman', 'Lainnya');
    }

    public static function statuses()
    {
        return array('OPEN', 'ASSIGNED', 'ON_PROGRESS', 'RESOLVED', 'CLOSED');
    }

    public static function priorities()
    {
        return array('LOW', 'MEDIUM', 'HIGH', 'CRITICAL');
    }

    public static function transition($from, $to, $pic, $resolution, $manager)
    {
        $edges = array('OPEN' => array('ASSIGNED'), 'ASSIGNED' => array('ON_PROGRESS', 'OPEN'),
            'ON_PROGRESS' => array('RESOLVED', 'ASSIGNED'),
            'RESOLVED' => array('CLOSED', 'ON_PROGRESS'), 'CLOSED' => array('OPEN'));
        if (!isset($edges[$from]) || !in_array($to, self::statuses(), TRUE)) {
            throw new InvalidArgumentException('Status tidak valid.');
        }
        if ($from !== $to && !in_array($to, $edges[$from], TRUE)) {
            throw new InvalidArgumentException('Perubahan status harus mengikuti alur penanganan.');
        }
        if (($to === 'CLOSED' || $from === 'CLOSED') && !$manager) {
            throw new InvalidArgumentException('Hanya pengelola yang dapat menutup atau membuka kembali tiket.');
        }
        if (in_array($to, array('ASSIGNED', 'ON_PROGRESS', 'RESOLVED'), TRUE) && !$pic) {
            throw new InvalidArgumentException('Pilih PIC sebelum melanjutkan penanganan.');
        }
        if (in_array($to, array('RESOLVED', 'CLOSED'), TRUE) && trim($resolution) === '') {
            throw new InvalidArgumentException('Isi solusi penyelesaian terlebih dahulu.');
        }
    }

    public static function date($value)
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('Tanggal tidak valid.');
        }
        $d = DateTimeImmutable::createFromFormat('!Y-m-d', $value, new DateTimeZone('Asia/Jakarta'));
        if (!$d || $d->format('Y-m-d') !== $value) {
            throw new InvalidArgumentException('Tanggal harus berformat YYYY-MM-DD.');
        }
        return $value;
    }
}
