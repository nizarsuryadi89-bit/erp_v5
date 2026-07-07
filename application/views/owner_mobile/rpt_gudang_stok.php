<!-- ==============================================
     STOK & INVENTARIS (OWNER DASHBOARD)
 =============================================== -->
<?php
if (!function_exists('format_stok_gudang')) {
    function format_stok_gudang($qty_base, $konversi, $satuan_gudang, $satuan_produksi) {
        $konversi = (float)($konversi ?: 1);
        $qty_base = (float)$qty_base;

        if ($qty_base <= 0) {
            return '0 ' . $satuan_gudang;
        }

        // Jika konversi <= 1 artinya tidak ada satuan produksi terpisah,
        // tampilkan jumlah langsung dalam satuan gudang (boleh desimal jika bukan bilangan bulat)
        if ($konversi <= 1) {
            if (floor($qty_base) == $qty_base) {
                return number_format($qty_base, 0, ',', '.') . ' ' . $satuan_gudang;
            }
            return number_format($qty_base, 2, ',', '.') . ' ' . $satuan_gudang;
        }

        // qty_base diasumsikan dalam satuan terkecil (produksi). Konversi = berapa produksi = 1 gudang
        $qty_stock = $qty_base / $konversi;
        $whole = floor($qty_stock);
        $remainder = $qty_stock - $whole;

        // small dalam satuan produksi (integer)
        $small = (int) round($remainder * $konversi);

        // jika small mencapai atau melebihi konversi, tambahkan ke whole
        if ($small >= $konversi) {
            $whole += floor($small / $konversi);
            $small = $small % (int)$konversi;
        }

        if ($small <= 0) {
            return number_format($whole, 0, ',', '.') . ' ' . $satuan_gudang;
        }

        if ($whole <= 0) {
            return number_format($small, 0, ',', '.') . ' ' . $satuan_produksi;
        }

        return number_format($whole, 0, ',', '.') . ' ' . $satuan_gudang . ' + ' . number_format($small, 0, ',', '.') . ' ' . $satuan_produksi;
    }
}
// baca pilihan sort dari query string (alpha = alfabet, used = paling digunakan)
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'alpha';
$sort = in_array($sort, ['alpha', 'used', 'id']) ? $sort : 'alpha';
// baca filter kategori dari query string
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
?>
<div class="app-container">
    <div class="fh-section-label" style="margin-top:0;">📦 Ringkasan Stok & Inventaris</div>

    <!-- Live Search & Info -->
    <div class="fh-card" style="margin-bottom:1rem; background:rgba(30, 58, 138, 0.03); border:1px solid rgba(30, 58, 138, 0.1);">
        <div class="fh-card-body" style="padding:15px;">
            <div style="position:relative;">
                <input type="text" id="materialSearch" class="form-control" placeholder="Cari material atau bahan baku..." 
                       style="border-radius:10px; border:1px solid #cbd5e1; padding:10px 10px 10px 35px; font-size:0.9rem; width:100%;">
                <i class="fas fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94a3b8;"></i>
            </div>
            <div style="margin-top:10px; font-size:0.75rem; color:#64748b; display:flex; align-items:center; gap:5px;">
                <i class="fas fa-info-circle"></i> Menampilkan total material aktif di cabang terpilih.
            </div>
        </div>
    </div>

            <!-- Paling Sering Digunakan (Top Used) -->
            <?php
                $top_items = [];
                if (!empty($data_list) && is_array($data_list)) {
                    foreach ($data_list as $r) {
                        $score = 0;
                        $cands = ['usage','used','usage_count','consumed','consumption','total_used','used_qty','movement','out','jumlah_pakai','pakai'];
                        foreach ($cands as $k) {
                            if (isset($r[$k]) && $r[$k] !== '') { $score = (float)$r[$k]; break; }
                        }

                        if ($score == 0) {
                            if (isset($r['stats']) && is_array($r['stats']) && isset($r['stats']['used'])) $score = (float)$r['stats']['used'];
                            elseif (isset($r['stats']) && is_object($r['stats']) && isset($r['stats']->used)) $score = (float)$r['stats']->used;
                        }

                        // fallback: use total movement fields if available
                        if ($score == 0) {
                            if (isset($r['total_movement'])) $score = (float)$r['total_movement'];
                            elseif (isset($r['movement'])) $score = (float)$r['movement'];
                        }

                        $top_items[] = [
                            'name' => $r['name'] ?? ($r['product_name'] ?? 'Unknown'),
                            'unit' => $r['unit'] ?? ($r['satuan'] ?? ''),
                            'konversi' => isset($r['konversi']) ? $r['konversi'] : 1,
                            'satuan_produksi' => $r['satuan_produksi'] ?? ($r['unit_kecil'] ?? ''),
                            'score' => $score,
                        ];
                    }

                    usort($top_items, function($a, $b) { return ($b['score'] <=> $a['score']); });
                    $top_items = array_slice($top_items, 0, 5);
                }
            ?>

            <?php if(!empty($top_items) && array_sum(array_column($top_items,'score')) > 0): ?>
                <div class="fh-section-label">🔥 Paling Sering Digunakan</div>
                <div class="fh-strip" style="margin-bottom:1rem;">
                    <?php foreach($top_items as $ti): ?>
                        <div class="fh-strip-item" style="min-width:140px;">
                            <div style="font-size:0.85rem; font-weight:800; color:#1e293b;"><?= htmlspecialchars($ti['name']) ?></div>
                            <div style="font-size:0.8rem; color:#64748b; margin-top:6px;">
                                <?php $fmt = format_stok_gudang($ti['score'], $ti['konversi'], $ti['unit'], $ti['satuan_produksi']); ?>
                                <span style="font-weight:800; color:#1e293b;"><?= $fmt ?></span>
                                <div style="font-size:0.65rem; color:#94a3b8; margin-top:4px;">digunakan</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Material Stock Cards Area -->
            <div class="fh-section-label" style="align-items:center; display:flex;">
                <span>Daftar Stok Material</span>
                <div style="margin-left:12px; font-size:0.8rem; color:#475569; display:flex; align-items:center; gap:8px;">
                    <span style="background:rgba(238,242,255,0.8); padding:4px 8px; border-radius:8px; font-weight:700;">Diurutkan:</span>
                    <select id="sortSelect" style="border-radius:8px; border:1px solid #d1d5db; padding:6px 8px; background:white; font-weight:700;">
                        <option value="alpha" <?php if($sort==='alpha') echo 'selected'; ?>>A–Z</option>
                        <option value="used" <?php if($sort==='used') echo 'selected'; ?>>Paling Digunakan</option>
                        <option value="id" <?php if($sort==='id') echo 'selected'; ?>>ID</option>
                    </select>
                    <?php
                        // build category list from data_list
                        $categories = [];
                        if (!empty($data_list) && is_array($data_list)) {
                            foreach ($data_list as $d) {
                                $cat = '';
                                if (isset($d['category']) && $d['category'] !== '') $cat = $d['category'];
                                elseif (isset($d['kategori']) && $d['kategori'] !== '') $cat = $d['kategori'];
                                elseif (isset($d['group']) && $d['group'] !== '') $cat = $d['group'];
                                if ($cat !== '') $categories[trim($cat)] = trim($cat);
                            }
                        }
                        ksort($categories, SORT_STRING);
                    ?>
                    <select id="categorySelect" style="border-radius:8px; border:1px solid #d1d5db; padding:6px 8px; background:white; font-weight:700; margin-left:8px;">
                        <option value="" <?php if($category==='') echo 'selected'; ?>>Semua Kategori</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?php if($category===$cat) echo 'selected'; ?>><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
    
    <?php
        // Siapkan daftar terurut sesuai pilihan user: alfabet atau paling digunakan
        $sorted_list = [];
            if (!empty($data_list) && is_array($data_list)) {
            $sorted_list = $data_list;

            // apply category filter if set
            if ($category !== '') {
                $filtered = [];
                foreach ($sorted_list as $r) {
                    $akey = strtolower(trim($r['category'] ?? ($r['kategori'] ?? ($r['group'] ?? ''))));
                    if ($akey === strtolower($category)) {
                        $filtered[] = $r;
                    }
                }
                $sorted_list = $filtered;
            }

            if ($sort === 'used') {
                // compute score and sort desc
                $compute_score = function($r) {
                    $score = 0;
                    $cands = ['usage','used','usage_count','consumed','consumption','total_used','used_qty','movement','out','jumlah_pakai','pakai'];
                    foreach ($cands as $k) {
                        if (isset($r[$k]) && $r[$k] !== '') { $score = (float)$r[$k]; break; }
                    }
                    if ($score == 0) {
                        if (isset($r['stats']) && is_array($r['stats']) && isset($r['stats']['used'])) $score = (float)$r['stats']['used'];
                        elseif (isset($r['stats']) && is_object($r['stats']) && isset($r['stats']->used)) $score = (float)$r['stats']->used;
                    }
                    if ($score == 0) {
                        if (isset($r['total_movement'])) $score = (float)$r['total_movement'];
                        elseif (isset($r['movement'])) $score = (float)$r['movement'];
                    }
                    return $score;
                };

                usort($sorted_list, function($a, $b) use ($compute_score) {
                    return ($compute_score($b) <=> $compute_score($a));
                });
            }
            elseif ($sort === 'id') {
                usort($sorted_list, function($a, $b) {
                    $idKeys = ['id','bahan_id','material_id','product_id','produk_id','item_id'];
                    $geta = null; $getb = null;
                    foreach ($idKeys as $k) {
                        if ($geta === null && isset($a[$k])) $geta = $a[$k];
                        if ($getb === null && isset($b[$k])) $getb = $b[$k];
                    }
                    // fallback to numeric cast if possible
                    if ($geta === null) $geta = isset($a['name']) ? $a['name'] : '';
                    if ($getb === null) $getb = isset($b['name']) ? $b['name'] : '';
                    if (is_numeric($geta) && is_numeric($getb)) return ((float)$geta <=> (float)$getb);
                    return strcmp((string)$geta, (string)$getb);
                });
            }
            else {
                usort($sorted_list, function($a, $b) {
                    $an = strtolower(trim($a['name'] ?? ($a['product_name'] ?? '')));
                    $bn = strtolower(trim($b['name'] ?? ($b['product_name'] ?? '')));
                    return $an <=> $bn;
                });
            }
        }
    ?>

    <?php if(empty($sorted_list)): ?>
        <div style="text-align:center; padding:2rem; color:#94a3b8; font-style:italic;">
            Belum ada data stok material yang tercatat.
        </div>
    <?php else: ?>
        <div class="fh-cards-area" id="materialCardsContainer">
            <?php foreach($sorted_list as $row): ?>
                <div class="fh-card material-stock-card" style="display:block; padding:15px; margin-bottom:0.75rem;" data-name="<?= strtolower(htmlspecialchars($row['name'])) ?>">
                    <!-- Card Header: Title -->
                    <div style="margin-bottom:10px;">
                        <div style="font-weight:700; color:#1e293b; font-size:0.95rem; line-height:1.3;"><?= htmlspecialchars($row['name']) ?></div>
                        <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">
                            Satuan: <span style="font-weight:600; color:#475569;"><?= htmlspecialchars($row['unit']) ?> 
                            <?= $row['konversi'] > 1 ? '(1 ' . htmlspecialchars($row['unit']) . ' = ' . number_format($row['konversi'], 0, ',', '.') . ' ' . htmlspecialchars($row['satuan_produksi']) . ')' : '' ?></span>
                        </div>
                    </div>
                    
                    <!-- Warehouse Breakdown Grid -->
                    <div style="display:grid; grid-template-columns:1fr; gap:8px; background:rgba(241, 245, 249, 0.6); padding:10px; border-radius:10px; border:1px solid #f1f5f9; margin-bottom:10px;">
                        <?php foreach($warehouses as $w): ?>
                            <?php
                                $stored = $row['warehouses'][$w->gudang_id] ?? 0;
                                if (is_array($stored)) {
                                    $qty = isset($stored['qty']) ? $stored['qty'] : (isset($stored['jumlah']) ? $stored['jumlah'] : 0);
                                } elseif (is_object($stored)) {
                                    $qty = isset($stored->qty) ? $stored->qty : (isset($stored->jumlah) ? $stored->jumlah : 0);
                                } else {
                                    $qty = $stored;
                                }
                                $qty = (float)$qty;
                            ?>
                            <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.8rem;">
                                <span style="color:#64748b; display:flex; align-items:center; gap:6px;">
                                    <i class="fas fa-warehouse" style="color:#94a3b8; font-size:0.75rem;"></i> 
                                    <?= htmlspecialchars($w->gudang_nama) ?>
                                </span>
                                <span style="font-weight:700; color:<?= $qty > 0 ? '#1e293b' : '#94a3b8' ?>;">
                                    <?= format_stok_gudang($qty, $row['konversi'], $row['unit'], $row['satuan_produksi']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Total Accumulated Stock -->
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.85rem; padding-top:8px; border-top:1px dashed #e2e8f0;">
                        <span style="font-weight:600; color:#475569;">Total Akumulasi:</span>
                        <?php
                            $total_qty_raw = $row['total_qty'] ?? 0;
                            if (is_array($total_qty_raw)) {
                                $total_qty = isset($total_qty_raw['qty']) ? $total_qty_raw['qty'] : (isset($total_qty_raw['jumlah']) ? $total_qty_raw['jumlah'] : 0);
                            } elseif (is_object($total_qty_raw)) {
                                $total_qty = isset($total_qty_raw->qty) ? $total_qty_raw->qty : (isset($total_qty_raw->jumlah) ? $total_qty_raw->jumlah : 0);
                            } else {
                                $total_qty = $total_qty_raw;
                            }
                            $total_qty = (float)$total_qty;
                        ?>
                        <span class="fh-badge" style="background:<?= $total_qty > 0 ? '#dcfce7' : '#fee2e2' ?>; color:<?= $total_qty > 0 ? '#15803d' : '#b91c1c' ?>; font-weight:800; font-size:0.85rem; border-radius:8px; padding:4px 10px;">
                            <?= format_stok_gudang($total_qty, $row['konversi'], $row['unit'], $row['satuan_produksi']) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Instant Client-side Search Filtering JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('materialSearch');
    const cards = document.querySelectorAll('.material-stock-card');
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const val = this.value;
            // update query param 'sort' while preserving others
            const url = new URL(window.location.href);
            url.searchParams.set('sort', val);
            window.location.href = url.toString();
        });
    }
    const categorySelect = document.getElementById('categorySelect');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const val = this.value;
            const url = new URL(window.location.href);
            if (val === '') url.searchParams.delete('category');
            else url.searchParams.set('category', val);
            window.location.href = url.toString();
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if (name.includes(query)) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>
