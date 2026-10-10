<!-- Essential CSS for Standalone View -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
	body {
		background-color: #f4f6f9;
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	}

	.invoice-card {
		border: none;
		border-radius: 12px;
		box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
		overflow: hidden;
		margin-top: 30px;
		background: #fff;
		max-width: 400px;
		/* Matches thermal width roughly */
		margin-left: auto;
		margin-right: auto;
		padding: 20px;
	}

	/* Styles from cetak_invoice.php adapted */
	table.a,
	table.b,
	table.footer,
	table.bayar {
		width: 100%;
		/* Full width of the card */
		font-family: Arial, sans-serif;
		font-size: 12px;
		border: none;
	}

	table.a {
		height: auto;
	}

	p {
		text-align: center;
		font-size: 10px;
		margin: 2px 0;
	}

	hr {
		border: solid 0.5px #ddd;
		margin: 10px 0;
	}

	.status-badge {
		font-size: 14px;
		padding: 10px 15px;
		border-radius: 50px;
		font-weight: 600;
		display: block;
		text-align: center;
		margin: 20px 0;
		box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
	}

	.status-pending {
		background: #fff8e1;
		color: #f39c12;
		border: 1px solid #ffe082;
	}

	.status-process {
		background: #e3f2fd;
		color: #3498db;
		border: 1px solid #90caf9;
	}

	.status-ready {
		background: #e8f5e9;
		color: #2ecc71;
		border: 1px solid #a5d6a7;
	}

	.status-taken {
		background: #ecf0f1;
		color: #7f8c8d;
		border: 1px solid #bdc3c7;
	}

	.qty-col {
		text-align: center;
		width: 15%;
	}

	.price-col {
		text-align: right;
		width: 25%;
	}

	.total-col {
		text-align: right;
		width: 25%;
	}
</style>

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
		// Prefer the historic snapshot price stored in tbl_pembayaran_produk.snapshot_harga
		if (isset($item->snapshot_harga) && is_numeric($item->snapshot_harga)) {
			return (float) $item->snapshot_harga;
		}

		$metadata = invoice_detail_metadata($item);
		if (isset($metadata['unit_price']) && is_numeric($metadata['unit_price'])) {
			return (float) $metadata['unit_price'];
		}

		if (isset($item->unit_price) && is_numeric($item->unit_price)) {
			return (float) $item->unit_price;
		}

		return (float) ($item->harga_1 ?? 0);
	}

	function invoice_detail_total($item, $harga = null)
	{
		// Prefer snapshot subtotal if available (already normalized)
		if (isset($item->snapshot_subtotal) && is_numeric($item->snapshot_subtotal)) {
			return (float) $item->snapshot_subtotal;
		}
		$metadata = invoice_detail_metadata($item);
		if (isset($metadata['subtotal']) && is_numeric($metadata['subtotal'])) {
			return (float) $metadata['subtotal'];
		}
		if (isset($item->subtotal) && $item->subtotal !== null && $item->subtotal !== '' && is_numeric($item->subtotal)) {
			return (float) $item->subtotal;
		}
		$harga = $harga === null ? invoice_detail_price($item) : (float) $harga;
		$qty = (float) ($item->qty ?? 0);
		$panjang = (float) ($item->panjang ?? 0);
		$lebar = (float) ($item->lebar ?? 0);
		$satuan_id = isset($item->satuan_id) ? (int) $item->satuan_id : 0;
		$kategori_id = isset($item->kategori_id) ? (int) $item->kategori_id : 0;
		$ket_1 = isset($item->ket_1) ? (string) $item->ket_1 : '';

		if ($satuan_id === 11 || ($kategori_id === 9 && $ket_1 === '5')) {
			return $panjang * $harga * $qty;
		}
		if ($panjang != 0 && $lebar != 0) {
			return $panjang * $lebar * $harga * $qty;
		}
		if ($panjang != 0 && $lebar == 0) {
			return $panjang * $harga * $qty;
		}

		return $harga * $qty;
	}
}
?>

<style>
	/* ... (Existing styles maintained) ... */

	/* Timeline Styles */
	.timeline {
		position: relative;
		padding: 20px 0;
		list-style: none;
	}

	.timeline:before {
		content: " ";
		position: absolute;
		top: 0;
		bottom: 0;
		left: 20px;
		width: 2px;
		background-color: #e9ecef;
	}

	.timeline-item {
		position: relative;
		padding-bottom: 20px;
		padding-left: 50px;
	}

	.timeline-item:last-child {
		padding-bottom: 0;
	}

	.timeline-marker {
		position: absolute;
		left: 11px;
		top: 0;
		width: 20px;
		height: 20px;
		border-radius: 50%;
		border: 2px solid #ccc;
		background: #fff;
	}

	.timeline-item.active .timeline-marker {
		background: #28a745;
		border-color: #28a745;
	}

	.timeline-item.passed .timeline-marker {
		background: #28a745;
		border-color: #28a745;
	}

	.timeline-content h6 {
		margin-bottom: 5px;
		font-weight: bold;
	}

	.timeline-content p {
		text-align: left;
		margin: 0;
		color: #666;
	}

	/* Custom Tab Styles */
	.nav-pills .nav-link.active,
	.nav-pills .show>.nav-link {
		background-color: #007bff;
	}

	.nav-pills .nav-link {
		color: #495057;
		font-weight: 600;
	}
</style>

<div class="row">
	<div class="col-12">
		<div class="invoice-card">

			<!-- Tabs Navigation -->
			<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist" style="justify-content: center;">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="pills-invoice-tab" data-bs-toggle="pill"
						data-bs-target="#pills-invoice" type="button" role="tab" aria-controls="pills-invoice"
						aria-selected="true">Invoice</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="pills-tracking-tab" data-bs-toggle="pill"
						data-bs-target="#pills-tracking" type="button" role="tab" aria-controls="pills-tracking"
						aria-selected="false">Tracking</button>
				</li>
			</ul>

			<div class="tab-content" id="pills-tabContent">

				<!-- Tab 1: Invoice -->
				<div class="tab-pane fade show active" id="pills-invoice" role="tabpanel"
					aria-labelledby="pills-invoice-tab">

					<!-- Header -->
					<!-- Header / Info Section -->
					<div class="mb-4">
						<div class="text-center mb-3">
							<?php if (!empty($perusahaan['logo'])): ?>
								<img src="<?= base_url('assets/images/' . $perusahaan['logo']) ?>" width="40%" class="mb-2">
							<?php else: ?>
								<h3 class="fw-bold mb-1"><?= $perusahaan['nama_perusahaan'] ?? 'TGM Printing' ?></h3>
							<?php endif; ?>
							<p class="text-secondary mb-0" style="font-size: 11px;">
								<?= $perusahaan['alamat_perusahaan'] ?? '' ?>
							</p>
							<p class="text-secondary" style="font-size: 11px;">Email: <?= $perusahaan['email'] ?? '' ?>
							</p>
						</div>

						<div
							class="text-center border-top border-bottom border-light py-2 mb-3 bg-light rounded shadow-sm">
							<span class="text-muted d-block" style="font-size: 11px; letter-spacing: 0.5px;">NO
								INVOICE</span>
							<span class="fw-bold font-monospace fs-6" style="color: #2c3e50;"><?= $nospk ?></span>
						</div>

						<div style="font-size: 13px; line-height: 1.6;" class="p-2 border rounded bg-white shadow-sm">
							<div class="row gx-2 mb-1">
								<div class="col-4 text-secondary">Konsumen</div>
								<div class="col-8 fw-bold text-dark">: <?= $pembayaran->konsumen_nama ?></div>
							</div>
							<div class="row gx-2 mb-1">
								<div class="col-4 text-secondary">No. HP</div>
								<div class="col-8 text-dark font-monospace">: <?= $pembayaran->konsumen_nohp ?></div>
							</div>
							<div class="row gx-2 mb-1">
								<div class="col-4 text-secondary">Tgl Order</div>
								<div class="col-8 text-dark">:
									<?php
									$tgl = !empty($pembayaran->created_at) ? $pembayaran->created_at : $pembayaran->tgl_bayar . ' ' . $pembayaran->jam_bayar;
									if (strtotime($tgl) > 0) {
										echo date_indo(date('Y-m-d', strtotime($tgl))) . ' <span class="text-muted">/</span> ' . date('H:i', strtotime($tgl));
									} else {
										echo "-";
									}
									?>
								</div>
							</div>
							<div class="row gx-2 mb-1 border-top pt-1 mt-1 border-dashed">
								<div class="col-6">
									<div class="text-secondary" style="font-size: 11px;">Deskprint </div>
									<div class="fw-semibold text-dark"><i class="fas fa-desktop text-muted me-1"
											style="font-size: 10px;"></i> <?= $pembayaran->username ?? '-' ?></div>
								</div>
								<div class="col-6">
									<div class="text-secondary" style="font-size: 11px;">Kasir </div>
									<div class="fw-semibold text-dark"><i class="fas fa-cash-register text-muted me-1"
											style="font-size: 10px;"></i> <?= $pembayaran->kasir_name ?? '-' ?></div>
								</div>
							</div>
						</div>
					</div>

					<style>
						.tabel-struk {
							border-collapse: separate !important;
							border-spacing: 0;
							width: 100%;
							border: 1px solid #e2e8f0;
							border-radius: 8px;
							overflow: hidden;
						}

						.tabel-struk th {
							background-color: #f1f5f9;
							color: #475569;
							font-weight: 600;
							font-size: 13px;
							letter-spacing: 0.5px;
							border-bottom: 2px solid #e2e8f0;
							padding: 10px 12px;
						}

						.tabel-struk td {
							border-bottom: 1px solid #f1f5f9;
							padding: 12px;
							color: #334155;
							font-size: 13px;
						}

						.tabel-struk tr:last-child td {
							border-bottom: none;
						}
					</style>
					<table class='a tabel-struk'>
						<tr>
							<th width="35%" style="text-align: left;">Produk</th>
							<th class="qty-col">Qty</th>
							<th class="price-col">Harga</th>
							<th class="total-col">Jumlah</th>
						</tr>
						<?php
						$subtotal = 0;
						$groupedItems = [];
						foreach ($detailProduk as $dt) {
							$catName = !empty($dt->kategori_nama) ? $dt->kategori_nama : 'LAINNYA';
							$groupedItems[$catName][] = $dt;

							// Calculate subtotal here as well
							$harga = invoice_detail_price($dt);
							$jumlah = invoice_detail_total($dt, $harga);
							$subtotal += $jumlah;
						}
						?>

						<?php foreach ($groupedItems as $categoryName => $items): ?>
							<tr>
								<td colspan="4"
									style="background-color: #f8fafc; padding: 8px 12px; border-bottom: 1px solid #e2e8f0;">
									<span class="badge"
										style="background-color: #e2e8f0; color: #475569; font-size: 10px; letter-spacing: 0.5px;">KATEGORI:
										<?= strtoupper($categoryName) ?></span>
								</td>
							</tr>
							<?php foreach ($items as $dt):
								$harga = invoice_detail_price($dt);
								$jumlah = invoice_detail_total($dt, $harga);
								?>
								<tr>
									<td style="vertical-align: top;">
										<b style="color: #0f172a;"><?= $dt->detail_product ?></b>
										<?php
										// Tampilkan Ukuran (P x L)
										$p = floatval($dt->panjang);
										$l = floatval($dt->lebar);
										$l_text = '';

										if ($l == 0 && !empty($dt->metadata)) {
											$meta = json_decode($dt->metadata, true);
											if (is_array($meta)) {
												if (!empty($meta['lebar'])) {
													$l_text = $meta['lebar'];
												} elseif (!empty($meta['ket_1']) && $dt->kategori_id == 9) {
													$l_text = $meta['ket_1'];
												}
											}
										} else if ($l > 0) {
											$l_text = $l . (isset($dt->satuan_nama) ? ' ' . $dt->satuan_nama : '');
										}

										if ($p > 0 && !empty($l_text)) {
											echo "<br><small class='text-secondary' style='font-weight: bold;'>{$p} x {$l_text}</small>";
										} elseif ($p > 0) {
											$satuan = isset($dt->satuan_nama) ? ' ' . $dt->satuan_nama : '';
											echo "<br><small class='text-secondary' style='font-weight: bold;'>{$p}{$satuan}</small>";
										}
										?>

										<?php
										// Tampilkan Dateline khusus untuk kategori Large Format
										if (isset($dt->kategori_id) && $dt->kategori_id == 2 && !empty($dt->dateline_tgl) && $dt->dateline_tgl != '0000-00-00') {
											$dl_tgl = date('d-m-Y', strtotime($dt->dateline_tgl));
											$dl_jam = !empty($dt->dateline_jam) ? substr($dt->dateline_jam, 0, 5) : '';
											echo "<br><small class='text-secondary' style='font-weight: bold;'>Dateline: {$dl_tgl} {$dl_jam}</small>";
										}
										?>

										<?php
										// [NEW] Render Metadata Dinamis
										$combined_meta = [];
										$meta_str = '';

										// 1. Ambil dari tbl_harga_product (Metadata Harga) untuk menghilangkan Hardcode Mesin/Bahan
										$harga_id = isset($dt->snapshot_source_harga_id) ? $dt->snapshot_source_harga_id : (isset($dt->harga_id) ? $dt->harga_id : 0);
										if ($harga_id > 0) {
											$CI =& get_instance();
											$harga_row = $CI->db->get_where('tbl_harga_product', ['harga_id' => $harga_id])->row();
											if ($harga_row && !empty($harga_row->metadata)) {
												$h_meta = json_decode($harga_row->metadata, true);
												if (is_array($h_meta)) {
													$combined_meta = array_merge($combined_meta, $h_meta);
												}
											} else {
												// Fallback jika tidak ada metadata di harga_product (Transaksi lama)
												if ($dt->kategori_id == 2) {
													$mesin_id = !empty($dt->jenisprint_id) ? $dt->jenisprint_id : (isset($dt->ket_1) ? $dt->ket_1 : null);
													if ($mesin_id == 2 || $mesin_id == '2')
														$combined_meta['mesin_fallback'] = "OUTDOOR";
													elseif ($mesin_id == 3 || $mesin_id == '3')
														$combined_meta['mesin_fallback'] = "INDOOR SOLVENT";
													elseif ($mesin_id == 4 || $mesin_id == '4')
														$combined_meta['mesin_fallback'] = "INDOOR UV";
												}
											}
										}

										// 2. Ambil dari tbl_produksi (Metadata Transaksi)
										if (!empty($dt->metadata)) {
											$t_meta = json_decode($dt->metadata, true);
											if (is_array($t_meta)) {
												$combined_meta = array_merge($combined_meta, $t_meta);
											}
										}

										// Pastikan ket_1 ikut diproses jika belum ada
										if (!empty($dt->ket_1) && !isset($combined_meta['ket_1'])) {
											$combined_meta['ket_1'] = $dt->ket_1;
										}


										// [NEW] Resolve schema untuk label dinamis
										$schema_map = [];
										$schema_arr = [];
										
										// Merge schemas sequentially (Category -> Subcategory -> Product) so specific overrides general
										if (!empty($dt->category_form_schema)) {
											$sc = json_decode($dt->category_form_schema, true);
											if (is_array($sc)) $schema_arr = array_merge($schema_arr, $sc);
										}
										if (!empty($dt->subcategory_form_schema)) {
											$sc = json_decode($dt->subcategory_form_schema, true);
											if (is_array($sc)) $schema_arr = array_merge($schema_arr, $sc);
										}
										if (!empty($dt->product_form_schema)) {
											$sc = json_decode($dt->product_form_schema, true);
											if (is_array($sc)) $schema_arr = array_merge($schema_arr, $sc);
										}

										if (!empty($schema_arr)) {
											foreach ($schema_arr as $field) {
												if (isset($field['name'])) {
													$schema_map[$field['name']] = $field;
												}
											}
										}

										// 3. Render Combined Metadata
										$printed_values = [];
										foreach ($combined_meta as $key => $val) {
											$key_lower = strtolower($key);
											$is_in_schema = isset($schema_map[$key]);
											$label = ucwords(str_replace('_', ' ', $key));

											if ($is_in_schema) {
												$field = $schema_map[$key];
												if (!empty($field['label'])) {
													$label = $field['label'];
												}
												if (isset($field['options']) && is_array($field['options']) && isset($field['options'][$val])) {
													$val = $field['options'][$val];
												}
											}

											// Skip technical fields
											if (!$is_in_schema) {
												if (in_array($key_lower, ['unit_price', 'subtotal', 'price_snapshot_at', 'hpp', 'range_id', 'jenisprint', 'user', 'kodeproduk', 'nospk']) || substr($key_lower, -3) === '_id' || strpos($key_lower, 'subkategori') !== false) {
													continue;
												}
											}

											// Skip ket_1 and lebar for DTF because we already display it in P x L
											if ($dt->kategori_id == 9 && in_array($key_lower, ['ket_1', 'lebar'])) {
												continue;
											}

											if ($key_lower == 'sisi' && !$is_in_schema) { // Legacy: skip if schema handles it
												if (in_array($dt->kategori_id, [2, 5, 9]) || strtolower($dt->kategori_nama ?? '') == 'large format') {
													continue;
												}
												if ($val == '1' || $val == '2') {
													$val = $val . ' Sisi';
												} elseif (strtolower(trim($val)) == '1 side') {
													$val = '1 Sisi';
												} elseif (strtolower(trim($val)) == '2 side') {
													$val = '2 Sisi';
												} elseif ($val == '3' || stripos($val, 'Tidak Ada') !== false) {
													continue;
												}

												// Dedup
												$val_clean = strtolower(trim($val));
												if (!in_array($val_clean, $printed_values)) {
													$meta_str .= "$val &bull; ";
													$printed_values[] = $val_clean;
												}
												continue;
											}

											if ($key_lower == 'mesin_fallback' && !$is_in_schema) {
												$val_clean = strtolower(trim($val));
												if (!in_array($val_clean, $printed_values)) {
													$meta_str .= "$val &bull; ";
													$printed_values[] = $val_clean;
												}
												continue;
											}

											// Hilangkan label "Ket 1:", tampilkan isinya saja
											if (strpos($key_lower, 'ket_') === 0 && !$is_in_schema) {
												$val = trim($val, '[]');
												if (!empty($val)) {

													$val_clean = strtolower(trim($val));
													if (!in_array($val_clean, $printed_values)) {
														$meta_str .= "$val &bull; ";
														$printed_values[] = $val_clean;
													}
												}
												continue;
											}

											$val_clean = strtolower(trim($val));
											if (!in_array($val_clean, $printed_values)) {
												if ($is_in_schema && !empty($label)) {
													$meta_str .= "{$label}: {$val} &bull; ";
												} else {
													$meta_str .= "{$val} &bull; ";
												}
												$printed_values[] = $val_clean;
											}
										}

										if (!empty($meta_str)): ?>
											<br><small class="text-secondary"><?= rtrim($meta_str, ' &bull; ') ?></small>
										<?php endif; ?>
									</td>
									<td class="qty-col fw-bold text-center"><?= $dt->qty ?></td>
									<td class="price-col text-secondary">
										<?= number_format($harga, 0, ',', '.') ?>
									</td>
									<td class="total-col fw-bold" style="color: #0f172a;">
										<?= number_format($jumlah, 0, ',', '.') ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</table>

					<!-- Payment Summary (Premium UI) -->
					<div class="mt-3 p-3 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
						<?php
						$diskon = isset($pembayaran->diskon_total) ? floatval($pembayaran->diskon_total) : (isset($pembayaran->diskon) ? floatval($pembayaran->diskon) : 0);
						$ongkir = isset($pembayaran->ongkir) ? floatval($pembayaran->ongkir) : 0;
						$total_tagihan_calc = (isset($pembayaran->total_tagihan) && $pembayaran->total_tagihan > 0) ? floatval($pembayaran->total_tagihan) : ($subtotal + $ongkir - $diskon);
						?>
						<div class="d-flex justify-content-between align-items-center mb-2">
							<span class="text-secondary fw-semibold"
								style="letter-spacing: 0.5px; font-size: 13px;">SUBTOTAL</span>
							<span class="fw-bold text-dark font-monospace">Rp
								<?= number_format($subtotal, 0, ".", ".") ?></span>
						</div>

						<?php if ($diskon > 0): ?>
							<div class="d-flex justify-content-between align-items-center mb-2">
								<span class="text-danger fw-semibold"
									style="letter-spacing: 0.5px; font-size: 13px;">DISKON</span>
								<span class="fw-bold text-danger font-monospace">- Rp
									<?= number_format($diskon, 0, ".", ".") ?></span>
							</div>
						<?php endif; ?>

						<?php if ($ongkir > 0): ?>
							<div class="d-flex justify-content-between align-items-center mb-2">
								<span class="text-primary fw-semibold"
									style="letter-spacing: 0.5px; font-size: 13px;">ONGKIR</span>
								<span class="fw-bold text-primary font-monospace">+ Rp
									<?= number_format($ongkir, 0, ".", ".") ?></span>
							</div>
						<?php endif; ?>

						<?php if ($diskon > 0 || $ongkir > 0 || (isset($pembayaran->total_tagihan) && $pembayaran->total_tagihan != $subtotal)): ?>
							<div class="d-flex justify-content-between align-items-center mb-3 pt-2 border-top">
								<span class="text-secondary fw-bold"
									style="letter-spacing: 0.5px; font-size: 13px;">TOTAL TAGIHAN</span>
								<span class="fw-bold text-dark font-monospace fs-5">Rp
									<?= number_format($total_tagihan_calc, 0, ".", ".") ?></span>
							</div>
						<?php endif; ?>

						<div class="border-top border-secondary border-opacity-10 pt-3">
							<?php
							$selisih = $pembayaran->total_bayar - $total_tagihan_calc;

							// Logic Status Pembayaran
							$has_payment_record = !empty($pembayaran->bayar_nospk);

							if (!$has_payment_record) {
								// Status: Belum Bayar
								echo "<div class='d-flex align-items-center justify-content-center p-2 rounded' style='background-color: #fef2f2; border: 1px solid #fecaca;'>
											<i class='fas fa-exclamation-circle text-danger me-2'></i>
											<span class='fw-bold text-danger' style='letter-spacing: 1px;'>BELUM DIBAYAR</span>
										  </div>";
								echo "<div class='text-center mt-2 text-secondary' style='font-size: 11px;'>
											Menunggu pembayaran / Approval pihak kasir.
										  </div>";
							} else {
								// Status: Sudah Bayar (Processed), bisa Lunas atau Piutang
								if ($selisih < 0) {
									// Piutang
									$piutang_amount = abs($selisih);
									echo "<div class='d-flex justify-content-between align-items-center mb-2'>
												<span class='text-secondary' style='font-size: 12px;'>Kekurangan (Piutang)</span>
												<span class='fw-bold font-monospace' style='color: #ea580c;'>Rp " . number_format($piutang_amount, 0, ".", ".") . "</span>
											  </div>";
									echo "<div class='d-flex align-items-center justify-content-center p-2 rounded' style='background-color: #fff7ed; border: 1px solid #fed7aa;'>
												<i class='fas fa-clock text-warning me-2' style='color: #ea580c !important;'></i>
												<span class='fw-bold' style='color: #ea580c; letter-spacing: 1px;'>STATUS: PIUTANG</span>
											  </div>";
								} else {
									// Lunas
									echo "<div class='d-flex align-items-center justify-content-center p-2 rounded shadow-sm' style='background-color: #f0fdf4; border: 1px solid #bbf7d0;'>
												<i class='fas fa-check-circle text-success me-2'></i>
												<span class='fw-bold text-success' style='letter-spacing: 1px; font-size: 15px;'>STATUS : LUNAS</span>
											  </div>";
								}
							}
							?>
						</div>
					</div>


					<?php if (!empty($rekening)): ?>
						<div class="mt-3">
							<h6 class="text-center fw-bold mb-3"
								style="font-size:14px; border-bottom: 2px dashed #eee; padding-bottom:10px;">Daftar
								Rekening
								Bank</h6>
							<?php foreach ($rekening as $rek): ?>
								<div class="d-flex align-items-center mb-2 p-2 border rounded bg-light position-relative"
									style="cursor: pointer; transition: all 0.2s;"
									onclick="copyToClipboard('<?= $rek->no_rekening ?>', this)">
									<div class="me-3">
										<i class="fas fa-credit-card text-primary fs-4"></i>
									</div>
									<div class="flex-grow-1">
										<div class="fw-bold text-dark" style="font-size:14px;"><?= $rek->nama_bank ?></div>
										<div class="font-monospace text-dark d-flex align-items-center"
											style="font-size:14px; letter-spacing: 1px;">
											<?= $rek->no_rekening ?>
											<i class="far fa-copy ms-2 text-muted" style="font-size: 12px;"></i>
										</div>
										<div class="text-muted small">a.n <?= $rek->atas_nama ?></div>
									</div>
									<div
										class="copy-feedback position-absolute end-0 top-50 translate-middle-y me-3 badge bg-dark d-none">
										Copied!
									</div>
								</div>
							<?php endforeach; ?>
						</div>

						<script>
							function copyToClipboard(text, element) {
								navigator.clipboard.writeText(text).then(function () {
									// Show feedback
									const feedback = element.querySelector('.copy-feedback');
									if (feedback) {
										feedback.classList.remove('d-none');
										setTimeout(() => {
											feedback.classList.add('d-none');
										}, 1500);
									}
								}, function (err) {
									console.error('Async: Could not copy text: ', err);
								});
							}
						</script>
					<?php endif; ?>

					<div class="mt-4 text-center">
						<a href="<?= base_url('cekpesanan/cetak_invoice/' . urlencode(encrypt_spk($nospk))) ?>"
							target="_blank" class="btn btn-primary d-print-none">
							<i class="fas fa-print me-2"></i> Cetak Invoice
						</a>
					</div>
				</div>

				<!-- Tab 2: Tracking -->
				<!-- Tab 2: Tracking -->
				<div class="tab-pane fade" id="pills-tracking" role="tabpanel" aria-labelledby="pills-tracking-tab">
					<h5 class="text-center mb-4 mt-2">Status Pesanan</h5>

					<?php
					// Calculate totals again for status logic
					$subtotal_status = 0;
					foreach ($detailProduk as $dt) {
						$harga = invoice_detail_price($dt);
						$jumlah = invoice_detail_total($dt, $harga);
						$subtotal_status += $jumlah;
					}

					// Logic Baru: Cek keberadaan record pembayaran
					$is_paid_processed = !empty($pembayaran->bayar_nospk);

					$status_prod = isset($cekflow['status_produksi']) ? $cekflow['status_produksi'] : 0;

					// Logic Status Updated
					// 0: Belum Bayar
					// 1: Sudah Bayar (Transaksi ke kasir dilakukan)
					// 2: Sedang Produksi
					// 3: Sudah Di PB (Packing/Ready)
					// 4: Sudah Diambil
					
					$current_step = 0;
					if (!$is_paid_processed) {
						$current_step = 0; // Belum Bayar
					} else {
						// Jika sudah ada record pembayaran (termasuk piutang), masuk step 1 (Sudah Bayar/Proses)
						$current_step = 1;

						// Cek status produksi untuk step selanjutnya
						if ($status_prod == 'proses')
							$current_step = 2; // Sedang Produksi
						elseif ($status_prod == 'done' || $status_prod == 'on_pb')
							$current_step = 4; // Sudah Di PB/Ready (Siap Diambil)
						elseif ($status_prod == 'on_costumer')
							$current_step = 5; // Sudah Diambil
					}
					?>

					<ul class="timeline">
						<!-- Step 1: Belum Bayar -->
						<li
							class="timeline-item <?= ($current_step >= 0) ? 'passed' : '' ?> <?= ($current_step == 0) ? 'active' : '' ?>">
							<div class="timeline-marker"></div>
							<div class="timeline-content">
								<h6>Belum Bayar</h6>
								<p>Menunggu pembayaran agar pesanan dapat diproses.</p>
							</div>
						</li>

						<!-- Step 2: Sudah Bayar -->
						<li
							class="timeline-item <?= ($current_step >= 1) ? 'passed' : '' ?> <?= ($current_step == 1) ? 'active' : '' ?>">
							<div class="timeline-marker"></div>
							<div class="timeline-content">
								<?php
								$diskon_track = isset($pembayaran->diskon_total) ? floatval($pembayaran->diskon_total) : (isset($pembayaran->diskon) ? floatval($pembayaran->diskon) : 0);
								$ongkir_track = isset($pembayaran->ongkir) ? floatval($pembayaran->ongkir) : 0;
								$tagihan_track = (isset($pembayaran->total_tagihan) && $pembayaran->total_tagihan > 0) ? floatval($pembayaran->total_tagihan) : ($subtotal_status + $ongkir_track - $diskon_track);
								$selisih_track = $pembayaran->total_bayar - $tagihan_track;
								if ($current_step >= 1 && $selisih_track < 0):
									?>
									<h6 class="text-danger">Pembayaran Belum Lunas (DP)</h6>
									<p class="text-danger small fw-bold">Masih ada tagihan sebesar Rp
										<?= number_format(abs($selisih_track), 0, '.', '.') ?>. Silakan lunasi saat
										pengambilan.
									</p>
								<?php else: ?>
									<h6>Sudah Bayar</h6>
									<p>Pembayaran berhasil dikonfirmasi. Pesanan akan segera diproduksi.</p>
								<?php endif; ?>
							</div>
						</li>

						<!-- Step 3: Sedang Produksi -->
						<li
							class="timeline-item <?= ($current_step >= 2) ? 'passed' : '' ?> <?= ($current_step == 2) ? 'active' : '' ?>">
							<div class="timeline-marker"></div>
							<div class="timeline-content">
								<h6>Sedang Produksi</h6>
								<p>Pesanan sedang diproduksi sesuai spesifikasi yang telah disetujui.</p>
							</div>
						</li>

						<!-- Step 4: Sudah Di PB -->
						<li
							class="timeline-item <?= ($current_step >= 4) ? 'passed' : '' ?> <?= ($current_step == 4) ? 'active' : '' ?>">
							<div class="timeline-marker"></div>
							<div class="timeline-content">
								<h6>Pesanan Selesai (Siap Diambil)</h6>
								<p>Pesanan telah selesai diproduksi dan siap diambil di toko.</p>
							</div>
						</li>

						<!-- Step 5: Sudah Diambil -->
						<li
							class="timeline-item <?= ($current_step >= 5) ? 'passed' : '' ?> <?= ($current_step == 5) ? 'active' : '' ?>">
							<div class="timeline-marker"></div>
							<div class="timeline-content">
								<h6>Sudah Diambil</h6>
								<p>Pesanan telah berhasil diterima. Terima kasih telah berbelanja di <b>TGM PRINT</b>.
								</p>
							</div>
						</li>
					</ul>
				</div>

			</div>

		</div>
	</div>
</div>

<!-- Load Bootstrap JS for Tabs if not already loaded in footer -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>