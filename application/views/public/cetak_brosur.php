<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Brosur - <?= $product['product_nama'] ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f2f2f2;
            --dark: #000000;
            --gray: #e6e6e6;
            --text-dark: #333333;
        }

        @page {
            size: A4 portrait;
            margin: 0; /* Removing margin for edge-to-edge background */
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 20px 40px;
            color: var(--text-dark);
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            height: 100vh;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .update-box {
            font-size: 10px;
            font-weight: 600;
        }
        
        .update-box .date-box {
            border: 2px solid var(--dark);
            border-radius: 8px;
            display: inline-block;
            text-align: center;
            background: white;
            overflow: hidden;
            margin-top: 5px;
        }

        .date-box .day-month {
            background-color: var(--dark);
            color: white;
            padding: 4px 10px;
            font-weight: 700;
            font-size: 12px;
        }

        .date-box .year {
            padding: 4px 10px;
            font-weight: 700;
            font-size: 14px;
        }

        .logo-center img {
            height: 40px;
            object-fit: contain;
        }

        .powered-by {
            text-align: right;
            font-size: 9px;
            font-weight: 600;
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: flex-end;
        }

        .powered-by img {
            height: 15px;
            object-fit: contain;
        }

        .main-title {
            text-align: center;
            font-family: 'Caveat', cursive;
            font-size: 42px;
            margin-top: 10px;
            margin-bottom: 0;
            color: var(--dark);
            text-transform: uppercase;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
            color: var(--text-dark);
        }

        .catalog-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .catalog-table th, .catalog-table td {
            border: 2px solid var(--bg-color); /* Matches background to create gaps */
            text-align: center;
            padding: 8px;
            font-size: 13px;
        }

        /* Group Header (like 2 MM, 3 MM) */
        .group-header th {
            background-color: var(--dark);
            color: white;
            font-size: 32px;
            font-weight: 700;
            padding: 15px;
            width: 35%;
        }

        .qty-header {
            background-color: var(--dark);
            color: white;
            font-size: 12px;
            font-weight: 600;
        }

        .qty-cols {
            background-color: var(--dark);
            color: white;
            font-size: 14px;
            font-weight: 600;
            padding: 10px !important;
        }

        /* Item Rows */
        .item-row td {
            background-color: var(--gray);
            font-weight: 600;
        }
        
        .item-row td:first-child {
            text-align: left;
            padding-left: 15px;
            font-size: 14px;
        }

        .footer-note {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            margin-top: auto;
            margin-bottom: 20px;
        }

        .scroll-indicator {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 80px;
            text-align: center;
            font-family: 'Caveat', cursive;
            font-size: 20px;
            font-weight: 700;
        }
        
        .scroll-indicator svg {
            width: 40px;
            height: 40px;
            margin-top: 5px;
        }

        /* Screen only specific */
        @media screen {
            body {
                background-color: #555;
                align-items: center;
                padding: 20px 0;
            }
            .page-container {
                background-color: var(--bg-color);
                width: 210mm;
                min-height: 297mm;
                padding: 40px;
                box-shadow: 0 0 10px rgba(0,0,0,0.5);
                position: relative;
                display: flex;
                flex-direction: column;
            }
            .print-btn-container {
                position: fixed;
                bottom: 20px;
                left: 20px;
                z-index: 1000;
            }
            .btn-print {
                background: #0d6efd;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 50px;
                font-family: 'Poppins', sans-serif;
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 4px 10px rgba(13,110,253,0.4);
                font-size: 16px;
            }
            .btn-print:hover {
                background: #0b5ed7;
            }
        }
    </style>
</head>
<body>
    
    <?php if(!isset($_GET['print'])): ?>
    <div class="print-btn-container d-print-none">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Sekarang / PDF</button>
    </div>
    <?php endif; ?>

    <div class="page-container">
        <!-- HEADER -->
        <div class="header">
            <div class="update-box">
                UPDATE :
                <br>
                <div class="date-box">
                    <?php 
                        $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
                        $m_index = (int)date('n') - 1;
                    ?>
                    <div class="day-month"><?= date('d') ?> <?= $months[$m_index] ?></div>
                    <div class="year"><?= date('Y') ?></div>
                </div>
            </div>

            <div class="logo-center">
                <img src="<?= base_url('assets/images/logo/logo.png') ?>" alt="TGM MERCH">
            </div>

            <div class="powered-by">
                POWERED :
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="tgmprint">
                <img src="<?= base_url('assets/images/logo_mrch.jpg') ?>" alt="MRCH NDYS">
                <img src="<?= base_url('assets/images/salaketik.png') ?>" alt="Salaketik">
            </div>
        </div>

        <!-- TITLE -->
        <h1 class="main-title">+ <?= htmlspecialchars($product['product_nama']) ?></h1>
        <?php if(!empty($product['product_deskripsi'])): ?>
            <div class="subtitle">(<?= htmlspecialchars($product['product_deskripsi']) ?>)</div>
        <?php else: ?>
            <div class="subtitle">(Harga di bawah belum termasuk biaya finishing khusus)</div>
        <?php endif; ?>

        <!-- DATA RESTRUCTURING -->
        <?php 
            // 1. Find all unique quantity ranges (columns)
            $qty_ranges = [];
            foreach($product['prices'] as $p) {
                $range = $p['qty_min'] . ' - ' . ($p['qty_max'] > 90000 ? '++' : $p['qty_max']) . ' pcs';
                if($p['qty_max'] > 90000) $range = '> ' . $p['qty_min'] . ' pcs';
                $qty_ranges[$p['qty_min']] = $range;
            }
            ksort($qty_ranges); // Sort by minimum quantity
            
            // 2. Group by ket_1_label (e.g. 1 Side, 2 Side) and detail_product
            $grouped = [];
            foreach($product['prices'] as $p) {
                $group_name = 'STANDAR';
                if(!empty($p['metadata'])) {
                    $meta = json_decode($p['metadata'], true);
                    if(is_array($meta)) {
                        $meta_vals = [];
                        foreach($meta as $val) {
                            $meta_vals[] = trim($val, '[]');
                        }
                        if (!empty($meta_vals)) {
                            $group_name = strtoupper(implode(' / ', $meta_vals));
                        }
                    }
                } elseif(!empty($p['ket_1_label'])) {
                    $group_name = strtoupper($p['ket_1_label']);
                }
                
                $detail_name = !empty($p['detail_product']) ? $p['detail_product'] : $product['product_nama'];
                
                if(!isset($grouped[$group_name])) {
                    $grouped[$group_name] = [];
                }
                if(!isset($grouped[$group_name][$detail_name])) {
                    $grouped[$group_name][$detail_name] = [];
                }
                $grouped[$group_name][$detail_name][$p['qty_min']] = $p['harga_1'];
            }
        ?>

        <!-- TABLE -->
        <table class="catalog-table">
            <?php foreach($grouped as $group_name => $items): ?>
                
                <!-- Group Header (e.g. "2 MM" / "1 SIDE") -->
                <tr class="group-header">
                    <th rowspan="2"><?= htmlspecialchars($group_name) ?></th>
                    <td colspan="<?= count($qty_ranges) ?>" class="qty-header">Rentang Harga</td>
                </tr>
                <tr>
                    <?php foreach($qty_ranges as $range_label): ?>
                        <td class="qty-cols"><?= htmlspecialchars($range_label) ?></td>
                    <?php endforeach; ?>
                </tr>

                <!-- Item Rows -->
                <?php foreach($items as $detail_name => $prices): ?>
                <tr class="item-row">
                    <td><?= htmlspecialchars($detail_name) ?></td>
                    <?php foreach($qty_ranges as $qty_min => $range_label): ?>
                        <td>
                            <?php 
                                if(isset($prices[$qty_min])) {
                                    echo number_format($prices[$qty_min], 0, ',', '.');
                                } else {
                                    echo "-";
                                }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>

            <?php endforeach; ?>
        </table>

        <!-- FOOTER NOTE -->
        <div class="footer-note">
            " Harga di atas sewaktu-waktu dapat berubah dan tidak terikat "
        </div>

        <div class="scroll-indicator">
            SCROLL<br>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7 13l5 5 5-5M7 6l5 5 5-5"/>
            </svg>
        </div>
    </div>
    
    <script>
        // Auto trigger print if ?print=1 is present
        if(window.location.search.includes('print=1')) {
            window.onload = function() {
                setTimeout(function() { window.print(); }, 500);
            };
        }
    </script>
</body>
</html>
