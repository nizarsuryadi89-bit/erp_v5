<style>
        table.a{
            border: solid 0px;
            width:300px;
            font-family:arial;
            font-size:12px;
			height:75px;
        
        }
		table.b{
            border: solid 0px;
            width:300px;
            font-family:arial;
            font-size:12px;
			height:50px;
        }
		table.footer{
            
            width:300px;
            font-family:arial;
            font-size:12px;
			height:30px;
			/* align:right; */
        }
		table.bayar{
            width:300px;
            font-family:arial;
            font-size:12px;
			height:30px;
        }
        p{
            text-align :center;
            font-size:10px;
        }
        hr{
            border:solid 0.5px;
        }
    </style>
</head>

<meta http-equiv='refresh' content='0.2;<?= base_url('transaksi/produksi')?>'>

<body onload="window.print();">

    <table class='a'>
        
        <!-- <tr><td colspan="3" align="center"><img src="<?= base_url('assets/images/'.$perusahaan['logo'])?>" width="50%"></td></tr> -->
        <!-- <tr><td colspan="3"><br></td></tr> -->
        <!-- <tr><td colspan="3" align="center"><p style="font-size:10px;"><?= @$perusahaan['alamat_perusahaan']?></td></p></tr> -->
       
        <!-- <tr><td colspan="3" align="center"><p style="font-size:10px;">Email : <?= @$perusahaan['email']?> <hr></td></tr> -->

        
        <tr>
            <td colspan="3" align="center">
            <p align="center" style="font-size:16; font-weight: bold;">
            NO.SPK : <?= $pembayaran['nospk'] ?></p>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center">
            
            </td>
        </tr>
        <tr><td colspan="3" align="center"><hr></td></tr>

    

        <tr><td width="30%">Konsumen</td><td width="5%">:</td> <td><?= $pembayaran['konsumen_nama'] ?></td></tr>
        <tr><td width="30%">HP</td><td width="5%">:</td> <td><?= $pembayaran['konsumen_nohp'] ?></td></tr>  
        <?php 
            $tanggal        = $pembayaran['pembayaran_tgl'];
            $jam            = $pembayaran['pembayaran_jam'];
            $tgldateline    = substr($pembayaran['dateline_tgl'], 0,10);
            
        ?>
        
        <tr><td width="30%">Tanggal/Jam</td><td width="5%">:</td> <td><?= date_indo(@$tanggal) ?> / <?= date_indo(@$jam) ?></td></tr>
        <!-- <tr><td width="30%">Kasir</td><td width="5%">:</td> <td><?= $pembayaran['username'] ?></td></tr> -->
        
        <!-- <tr><td width="30%">Dateline:</td><td width="5%">: </td> <td><?= date_indo($tgldateline) ?> - <?= @$data_transaksi['dateline_jam'] ?> </td></tr> -->
        
        <tr><td colspan="3" align="center"><hr></td></tr>
    </table>
			
    <table class='a'>
    
        <tr>
            <td width="40%"><b>Product</b></td>
            <td  align="center" width="10%"><b>Qty</td>
            <td  align="center" width="10%"><b>Uk</td>
            <td width="20%" align="center"><b>Harga</td>
            <td width="20%" align="right"><b>Jumlah</td>
        </tr>
    <?php
        if (!function_exists('invoice_detail_metadata')) {
            function invoice_detail_metadata($item)
            {
                if (empty($item->metadata) || !is_string($item->metadata)) {
                    return [];
                }

                $decoded = json_decode($item->metadata, true);
                return is_array($decoded) ? $decoded : [];
            }

            function invoice_detail_price($item)
            {
                $metadata = invoice_detail_metadata($item);
                if (isset($metadata['unit_price']) && is_numeric($metadata['unit_price'])) {
                    return (float) $metadata['unit_price'];
                }

                return (float) ($item->harga_1 ?? 0);
            }
        }
        
        foreach ($detailBayar as $dt)
        {
            $harga = invoice_detail_price($dt);
            $jumlah = $harga * $dt->qty;
        ?>
        <tr>
            <td><?= $dt->detail_product ?></td>
            <td align="center"><?= $dt->qty ?></td>
            <td></td>
            <td align="right"><?= number_format($harga,0,".",".") ?></td>
            <td align="right"><?= number_format($jumlah,0,".",".") ?></td>
        </tr>
        <?php
            @$no++;
            
        }

    ?>
    </table>
    	
            


    <!-- <table class='footer' border="0">
        <tr>
            <td width="40%"></td>
            <td align='left' colspan="2">
                <?php 
                    if ($pembayaran['piutang'] != 0){
                        if ($pembayaran['piutang'] > 0){
                            echo "Piutang";
                            echo " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
                            echo " ".number_format(@$pembayaran['piutang'],0,".",".")."";
                            
                        }else{
                            echo "Kembali";
                            echo " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
                            echo " ".number_format(@$pembayaran['piutang'],0,".",".");
                        }
                    }else{
                        echo "LUNAS";
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td align='center' colspan='3'>
                <hr>
                <p>Tidak Menerima <b>KOMPLAIN</b> atas Kerusakan/kesalahan  
                <b>PRINT DILUAR ACC atau APPROVAL</b> </p>
            </td>
        </tr>
    </table> -->
