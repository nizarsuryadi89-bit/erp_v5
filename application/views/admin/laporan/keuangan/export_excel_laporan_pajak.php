<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_pajak_bulanan.xls");
?>
<table border="1">		
<thead>
    <tr>
        <th width="3%">No</th>
        <th width="10%">Tanggal Invoice</th>
        <td width="10%">No Invoice</td>
        <th width="20%">Nama Konsumen</th>
        
        <th width="10%">No HP Konsumen</th>
        <th width="10%">Email Konsumen</th>
        <th>Total Transaksi</th>
        <th>Diskon</th>
        <th>Ongkir</th>
        <th>Grand Total</th>
        
    </tr>
</thead>
<tbody>
    <?php 
        $no =1;
        foreach ($lap_pajak as $dt){
    ?>
    <tr >
        <td><?= $no ?></td>
        <td><?= date_indo($dt->pembayaran_tgl) ?></td>
        <td><?= $dt->nospk ?></td>
        <td><?= $dt->konsumen_nama ?></td>
        <td><?= $dt->konsumen_nohp ?></td>
        <td><?= $dt->konsumen_email ?></td>
        <td class="text-right">
            <?= $dt->sub_total ?>
        </td>
        <td class="text-right">
            <?= $dt->diskon ?>
        </td>
        <td class="text-right">
            <?= $dt->ongkir ?>
        </td>
        <td class="text-right">
            <?= $dt->grand_total ?>
        </td>
    </tr>
    <?php 
        $no++;
        $total_subtotal     = $total_subtotal+$dt->sub_total;
        $total_diskon       = $total_diskon+$dt->diskon;
        $total_ongkir       = $total_ongkir+$dt->ongkir;
        $total_grandtotal   = $total_grandtotal+$dt->grand_total;
        }
    ?>
    <tr>
        <th colspan="6" class="text-right"><h4><b>Jumlah</b></h4></th>
        <th class="text-right"><h4><?=$total_subtotal ?></h4></th>
        <th class="text-right"><h4><?=$total_diskon ?></h4></th>
        <th class="text-right"><h4><?=$total_ongkir ?></h4></th>
        <th class="text-right"><h4><?=$total_grandtotal ?></h4></th>
    </tr>
</table>