<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wip extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Produksi_model');
        $this->load->model('Wip_workflow_model', 'wip_workflow');
        $this->load->model('M_gudang');
        $this->load->model('Warehouse_model', 'warehouse');
    }

    public function quality()
    {
        $actor = $this->wip_workflow->actor();
        $status = $this->input->get('status', true) ?: 'pending';
        if (!in_array($status, ['pending', 'hold', 'passed', 'reprint', 'all'], true))
            $status = 'pending';
        $page = max(1, (int) $this->input->get('page'));
        $search = substr(trim((string) $this->input->get('search', true)), 0, 100);
        $csrf = $this->session->userdata('wip_qc_csrf');
        if (!$csrf) {
            $csrf = bin2hex(random_bytes(32));
            $this->session->set_userdata('wip_qc_csrf', $csrf);
        }
        $data = [
            'qc_csrf' => $csrf,
            'title' => 'Pemeriksaan QC',
            'qc_status' => $status,
            'qc_page' => $page,
            'qc_search' => $search,
            'qc_can_review' => $this->wip_quality->can_review($actor),
            'qc_enabled' => $this->wip_quality->enabled(),
            'qc_error' => ''
        ];
        try {
            $data['qc_queue'] = $this->wip_quality->queue($actor, $status, $search, $page);
        } catch (Exception $e) {
            $data['qc_queue'] = ['rows' => [], 'total' => 0];
            $data['qc_error'] = $e instanceof DomainException ? $e->getMessage() : 'Antrean QC belum dapat dimuat.';
        }
        $this->load_admin_view('admin/wip/quality', $data);
    }

    public function quality_inspect()
    {
        $this->_workflow_response(function () {
            $this->wip_workflow->assert_transactional_storage();
            $token = (string) $this->session->userdata('wip_qc_csrf');
            if (!$token || !hash_equals($token, (string) $this->input->post('wip_qc_csrf', true)))
                throw new DomainException('Sesi formulir QC tidak valid. Muat ulang halaman.');
            return $this->wip_quality->inspect(
                $this->input->post('produksi_id', true),
                $this->input->post('version', true),
                $this->input->post('accepted_qty', true),
                $this->input->post('rejected_qty', true),
                $this->input->post('note', true),
                $this->input->post('request_reprint', true) === '1',
                $this->input->post('request_key', true),
                $this->wip_workflow->actor()
            );
        });
    }

    public function quality_history()
    {
        try {
            $id = Production_workflow::ids([$this->input->get('produksi_id', true)])[0];
            if (!$this->wip_quality->assert_storage())
                throw new DomainException('QC belum tersedia.');
            $response = ['success' => true, 'rows' => $this->wip_quality->history($id, $this->wip_workflow->actor())];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e instanceof DomainException ? $e->getMessage() : 'Riwayat QC belum dapat dimuat.'];
        }
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    private function _workflow_response($work)
    {
        if (strtolower($this->input->method()) !== 'post') {
            $this->output->set_status_header(405)->set_header('Allow: POST');
            $response = ['success' => false, 'status' => false, 'message' => 'Aksi produksi harus dikirim melalui POST.'];
        } else {
            try {
                $response = $work();
            } catch (Exception $e) {
                if (!($e instanceof DomainException))
                    log_message('error', 'WIP action: ' . $e->getMessage());
                $response = [
                    'success' => false,
                    'status' => false,
                    'message' => $e instanceof DomainException
                        ? $e->getMessage() : 'Gagal memproses transaksi produksi.'
                ];
            }
        }
        $response['csrf_hash'] = $this->security->get_csrf_hash();
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    private function _workflow_map($name)
    {
        $value = $this->input->post($name, true);
        if (is_string($value))
            $value = json_decode($value, true);
        return is_array($value) ? $value : [];
    }

    private function _workflow_execute($ids, $action, $callback = null)
    {
        $actor = $this->wip_workflow->actor($action === 'start' ? null : $this->input->post('pin_operator', true), $this->get_operator_id_for_current_user());
        $expected = $this->_workflow_map('expected_step_ids');
        $versions = $this->_workflow_map('expected_versions');
        $detail_id = $this->input->post('detail_id', true) ?: $this->input->post('detail_produksi_id', true);
        if ($detail_id && count($ids) === 1)
            $expected[$ids[0]] = $detail_id;
        $materials = [];
        foreach (['material_id', 'bahan_id'] as $field) {
            $mid = $this->input->post($field, true);
            if (ctype_digit((string) $mid) && (int) $mid > 0)
                $materials[] = (int) $mid;
        }
        foreach ($this->_workflow_map('outcomes') as $outcome) {
            foreach (($outcome['bom_usage'] ?? []) as $line) {
                $mid = $line['bom_material_id'] ?? 0;
                if (ctype_digit((string) $mid) && (int) $mid > 0)
                    $materials[] = (int) $mid;
            }
        }
        return $this->wip_workflow->execute($ids, $action, $actor, $expected, $versions, $callback, $materials);
    }

    private function _workflow_fields($item, $step, $data, $result_required = true)
    {
        $fields = [];
        foreach (($result_required ? ['qty_output', 'qty_missing', 'panjang_output', 'lebar_output'] : []) as $name) {
            if (isset($data[$name]) && $data[$name] !== '')
                $fields[$name] = Production_workflow::number($data[$name], $name);
        }
        if ($result_required && !array_key_exists('qty_output', $fields))
            throw new DomainException('Isi jumlah hasil baik aktual.');
        foreach (['mesin_id', 'material_id', 'lokasi_proses'] as $name) {
            if (!empty($data[$name])) {
                if (!ctype_digit((string) $data[$name]))
                    throw new DomainException('Pilihan ' . $name . ' tidak valid.');
                $fields[$name] = (int) $data[$name];
            }
        }
        if ($result_required && empty($fields['mesin_id']) && !empty($step->mesin_id))
            $fields['mesin_id'] = (int) $step->mesin_id;
        $location = (int) (($item->lokasi_cetak ?? 0) ?: $item->perusahaan_id);
        if (
            $result_required && !empty($step->bom_line_id) && empty($step->is_finishing)
            && stripos($step->routing_name ?? '', 'rip') === false
        ) {
            $product = $this->db->get_where('tbl_product', ['product_id' => $item->product_id])->row();
            if ($product && (int) $product->kategori_id !== 10 && (int) $product->tipe_id === 1 && empty($fields['mesin_id'])) {
                throw new DomainException('Pilih mesin aktual untuk tahap cetak ini.');
            }
        }
        if (!empty($fields['mesin_id'])) {
            $machine = $this->db->get_where('tbl_mesin', ['mesin_id' => $fields['mesin_id'], 'status' => '1'])->row();
            if (!$machine)
                throw new DomainException('Mesin tidak aktif atau tidak ditemukan.');
            if (!Production_workflow::machine_matches_locations($machine, [$location]))
                throw new DomainException('Mesin yang dipilih tidak sesuai lokasi produksi item. Buka ulang form dan pilih mesin yang tersedia untuk lokasi item.');
        }
        $location = (int) (($item->lokasi_cetak ?? 0) ?: $item->perusahaan_id);
        if (!empty($fields['lokasi_proses']) && $fields['lokasi_proses'] !== $location) {
            throw new DomainException('Lokasi proses harus sesuai lokasi produksi item.');
        }
        if (!empty($data['catatan']))
            $fields['catatan'] = trim((string) $data['catatan']);
        if (!empty($data['alasan_missing']))
            $fields['alasan_missing'] = trim((string) $data['alasan_missing']);
        return $fields;
    }

    private function _workflow_require_material($step, $usage)
    {
        if (empty($step->bom_line_id) || stripos($step->routing_name ?? '', 'rip') !== false)
            return;
        $bom = $this->db->get_where('bom_lines', ['bom_line_id' => $step->bom_line_id])->row();
        if ($bom && !empty($bom->bahan_id) && (float) ($bom->quantity ?? 0) > 0 && $usage <= 0) {
            throw new DomainException('Tahap ini membutuhkan bahan. Isi pemakaian bahan aktual sebelum mencatat hasil.');
        }
    }

    private function _workflow_post_material($item, $step, $material_id, $qty, $actor)
    {
        $qty = Production_workflow::number($qty, 'Pemakaian bahan');
        if (!$qty)
            return;
        if (!ctype_digit((string) $material_id) || (int) $material_id < 1)
            throw new DomainException('Pilih material untuk pemakaian bahan.');
        if (!$this->update_warehouse_stock($item->produksi_id, $step->detail_produksi_id, $material_id, $qty, null, $actor['employee_id'])) {
            throw new DomainException('Pemakaian bahan gagal atau stok tidak mencukupi. Hasil belum disimpan.');
        }
    }

    private function _workflow_advance()
    {
        $this->_workflow_response(function () {
            $id = $this->input->post('produksi_id', true);
            $type = $this->input->post('type', true);
            $action = $type === 'next' ? 'complete' : $type;
            $data = $this->input->post(null, true);
            return $this->_workflow_execute([$id], $action, function ($item, $step, $actor) use ($action, $data) {
                if ($action !== 'complete')
                    return $action === 'start' ? $this->_workflow_fields($item, $step, $data, false) : [];
                $fields = $this->_workflow_fields($item, $step, $data);
                $used = Production_workflow::number($data['qty_pakai'] ?? 0, 'Bahan terpakai');
                $scrap = Production_workflow::number($data['qty_rusak'] ?? 0, 'Bahan rusak');
                $this->_workflow_require_material($step, $used + $scrap);
                $this->_workflow_post_material($item, $step, $data['bahan_id'] ?? 0, $used + $scrap, $actor);
                $fields['qty_bahan_pakai'] = (float) ($step->qty_bahan_pakai ?? 0) + $used;
                $fields['qty_bahan_rusak'] = (float) ($step->qty_bahan_rusak ?? 0) + $scrap;
                if (!empty($data['bahan_id']))
                    $fields['material_id'] = (int) $data['bahan_id'];
                return $fields;
            });
        });
    }

    private function _workflow_detail_action($action)
    {
        $this->_workflow_response(function () use ($action) {
            $detail_id = $this->input->post('detail_id', true) ?: $this->input->post('detail_produksi_id', true);
            if (!ctype_digit((string) $detail_id) || (int) $detail_id < 1)
                throw new DomainException('ID tahap tidak valid.');
            $detail = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $detail_id])->row();
            if (!$detail)
                throw new DomainException('Tahap tidak ditemukan.');
            $posted_id = $this->input->post('produksi_id', true);
            if ($posted_id && (int) $posted_id !== (int) $detail->produksi_id)
                throw new DomainException('Tahap bukan milik item produksi ini.');
            $data = $this->input->post(null, true);
            return $this->_workflow_execute([$detail->produksi_id], $action, function ($item, $step, $actor) use ($action, $data) {
                if ($action !== 'complete')
                    return $action === 'start' ? $this->_workflow_fields($item, $step, $data, false) : [];
                $data['qty_missing'] = $data['qty_missing'] ?? ($data['qty_failed'] ?? 0);
                $data['alasan_missing'] = $data['alasan_missing'] ?? ($data['alasan_failed'] ?? '');
                $fields = $this->_workflow_fields($item, $step, $data);
                $used = Production_workflow::number($data['qty_bahan_pakai'] ?? 0, 'Bahan terpakai');
                $scrap = Production_workflow::number($data['qty_bahan_rusak'] ?? 0, 'Bahan rusak');
                $this->_workflow_require_material($step, $used + $scrap);
                $material = $data['material_id'] ?? ($step->material_id ?? 0);
                $this->_workflow_post_material($item, $step, $material, $used + $scrap, $actor);
                $fields['qty_bahan_pakai'] = (float) ($step->qty_bahan_pakai ?? 0) + $used;
                $fields['qty_bahan_rusak'] = (float) ($step->qty_bahan_rusak ?? 0) + $scrap;
                return $fields;
            });
        });
    }

    private function _workflow_spk_ids($nospk, $category = null, $status = null)
    {
        if (!$nospk)
            throw new DomainException('Nomor SPK wajib diisi.');
        $this->db->select('p.produksi_id')->from('tbl_produksi p')
            ->join('tbl_product pr', 'pr.product_id = p.product_id')->where('p.nospk', $nospk);
        if ($category) {
            if (in_array((int) $category, [2, 5, 8, 9], true))
                $this->db->where('pr.kategori_id', (int) $category);
            else
                $this->db->where_not_in('pr.kategori_id', [2, 5, 8, 9]);
        }
        if ($status)
            $this->db->where('p.status_produksi', $status);
        return array_column($this->db->order_by('p.produksi_id')->get()->result_array(), 'produksi_id');
    }

    private function _workflow_legacy_rejected()
    {
        $this->_workflow_response(function () {
            throw new DomainException('Gunakan tombol Mulai/Catat Hasil pada daftar produksi. Perubahan status langsung tidak diperbolehkan.');
        });
    }

    // =========================================================================
    // MAIN PAGES
    // =========================================================================

    public function index()
    {
        try {
            $operator_id = $this->get_operator_id_for_current_user();

            $data['produksi'] = $this->Produksi_model->get_all_produksi($operator_id);
            $data['pb_list'] = $this->Produksi_model->get_pb_list(); // Added to fix empty tab
            $data['handover_list'] = $this->Produksi_model->get_produksi_handover(); // Using get_produksi_handover to match view

            $raw_summary = $this->Produksi_model->get_summary($operator_id);
            $data['summary'] = [
                'total' => ($raw_summary->total_queue ?? 0) + ($raw_summary->processing ?? 0),
                'selesai' => $raw_summary->done ?? 0,
                'proses' => $raw_summary->processing ?? 0,
                'terlambat' => 0 // To be implemented if deadline exists
            ];

            $this->load_admin_view('admin/wip/wip_tracking', $data);
        } catch (Exception $e) {
            log_message('error', 'WIP Index - Error: ' . $e->getMessage());
            $data['produksi'] = [];
            $data['summary'] = ['total' => 0, 'selesai' => 0, 'proses' => 0, 'terlambat' => 0];
            $this->load_admin_view('admin/wip/wip_tracking', $data);
        }
    }

    public function kinerja_operator()
    {
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-t');

        $data = [
            'title' => 'Kinerja Operator / Divisi Produksi',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'kinerja' => $this->Produksi_model->get_kinerja_operator($start_date, $end_date)
        ];

        $this->load_admin_view('admin/wip/kinerja_operator', $data);
    }

    private function _apply_produksi_filters($filter_status_view, $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode = 'all', $full_joins = true, $is_global_search = false)
    {
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id', 'left');
        $this->db->join('tbl_pemesanan', 'tbl_pemesanan.nospk = tbl_produksi.nospk', 'left');

        if ($full_joins) {
            $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_produksi.konsumen_id', 'left');
            $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
            $this->db->join('tbl_subkategori', 'tbl_subkategori.subkategori_id = tbl_product.subkategori_id', 'left');
            $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id', 'left');
            $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_produksi.perusahaan_id', 'left');
            $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id', 'left');
            $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi', 'left');
            $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left');
        } elseif ($is_global_search && !empty($this->input->get('search_spk'))) {
            $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_produksi.konsumen_id', 'left');
        }

        $this->db->group_start();
        $this->db->where_in('tbl_product.tipe_id', [1, 2]);
        $this->db->or_group_start();
        $this->db->where_in('tbl_product.kategori_id', [1, 2, 3, 4, 5, 8, 9, 10, 16, 22]);
        $this->db->where('tbl_product.tipe_id', 3);
        $this->db->group_end();
        $this->db->group_end();

        if ($filter_metode !== 'all') {
            if ($filter_metode === 'offline') {
                $this->db->group_start();
                $this->db->where('tbl_pemesanan.id_jenis_transaksi', null);
                $this->db->or_where('tbl_pemesanan.id_jenis_transaksi', 0);
                $this->db->group_end();
            } else {
                $this->db->where('tbl_pemesanan.id_jenis_transaksi', $filter_metode);
            }
        }

        if ($is_global_search) {
            $search_spk = $this->input->get('search_spk');
            if (!empty($search_spk)) {
                $this->db->group_start();
                $this->db->where('tbl_produksi.nospk', $search_spk);
                $this->db->or_like('LOWER(tbl_konsumen.konsumen_nama)', strtolower($search_spk));
                $this->db->group_end();
            }
        }

        if (!$is_global_search) {
            if ($filter_status_view === 'pb') {
                $this->db->where('tbl_produksi.status_produksi', 'on_pb');
            } elseif ($filter_status_view === 'konsumen') {
                $this->db->where('tbl_produksi.status_produksi', 'on_costumer');
            } elseif ($filter_status_view === 'ready_pb') {
                $this->db->where('tbl_produksi.status_produksi', 'done');
                if ($this->wip_quality->installed())
                    $this->db->where("NOT EXISTS (SELECT 1 FROM tbl_wip_qc q WHERE q.produksi_id = tbl_produksi.produksi_id AND q.status <> 'passed')", null, false);
            } elseif ($filter_status_view === 'finishing') {
                $this->db->group_start();
                $this->db->where('tbl_produksi.status_produksi', 'finishing');
                $this->db->or_group_start();
                $this->db->where('tbl_produksi.status_produksi', 'proses');
                $this->db->where("EXISTS (SELECT 1 FROM detail_produksi fp JOIN routing fr ON fr.routing_id = fp.routing_id
                    WHERE fp.produksi_id = tbl_produksi.produksi_id AND fr.is_finishing = 1
                    AND fp.status_produksi IN ('0', '', '1'))", null, false);
                $this->db->group_end()->group_end();
            } elseif ($filter_status_view === 'proses') {
                $this->db->where_in('tbl_produksi.status_produksi', ['proses', 'finishing']);
            } elseif ($filter_status_view === 'pending') {
                $this->db->group_start();
                $this->db->where('tbl_produksi.status_produksi', 'pending');
                $this->db->or_where('tbl_produksi.status_produksi', 'approved');
                $this->db->or_where('tbl_produksi.status_produksi', 'approve');
                $this->db->or_where('tbl_produksi.status_produksi', '');
                $this->db->or_where('tbl_produksi.status_produksi', '0');
                $this->db->group_end();
                // Filter out rijek
                $this->db->group_start();
                $this->db->not_like('tbl_produksi.catatan', '[DIKEMBALIKAN KE PRODUKSI]', 'both');
                $this->db->or_where('tbl_produksi.catatan IS NULL');
                $this->db->group_end();
            } elseif ($filter_status_view === 'rijek') {
                $this->db->group_start()->like('tbl_produksi.catatan', '[DIKEMBALIKAN KE PRODUKSI]')
                    ->or_like('tbl_produksi.catatan', '[MASALAH]')->or_like('tbl_produksi.catatan', '[RIJEK]')->group_end();
            } elseif ($filter_status_view === 'all_active' || $filter_status_view === 'global_stats') {
                // For global stats, we don't filter out done items if we want to count ready_to_pb
                // but actually global stats only counts pending, proses, done. 
                // We exclude on_costumer, but KEEP on_pb so they can be tracked in active views.
                $this->db->where('tbl_produksi.status_produksi !=', 'on_costumer');
            } else {
                $this->db->where('tbl_produksi.status_produksi !=', 'on_pb');
                $this->db->where('tbl_produksi.status_produksi !=', 'on_costumer');
                $this->db->where('tbl_produksi.status_produksi !=', 'done');
            }

            $this->db->where('tbl_produksi.status_produksi IS NOT NULL');
            $this->db->where('tbl_pemesanan.status_pembayaran', 'paid');

            if ($active_operator !== 'all') {
                if ($filter_status_view !== 'ready_pb' && $filter_status_view !== 'pb' && $filter_status_view !== 'konsumen') {
                    $this->db->group_start();
                    // PIPELINE VISIBILITY: Show job to operator if they have ANY incomplete step in the routing, 
                    // not just if it's currently their exact turn. This allows downstream operators (e.g. Finishing) 
                    // to see jobs that are still being processed by upstream operators (e.g. Print A3).
                    $this->db->where("EXISTS (
                        SELECT 1 
                        FROM detail_produksi dp 
                        LEFT JOIN routing r ON (dp.routing_id > 0 AND r.routing_id = dp.routing_id) OR ((dp.routing_id IS NULL OR dp.routing_id = 0 OR dp.routing_id = '') AND r.product_id = tbl_produksi.product_id AND r.routing_urutan = dp.routing_urutan)
                        WHERE dp.produksi_id = tbl_produksi.produksi_id 
                        AND (dp.status_produksi IS NULL OR dp.status_produksi NOT IN ('2', '3', 'done', 'on_pb', 'on_costumer'))
                        AND (dp.operator_id = " . (int) $active_operator . " OR r.operator_id = " . (int) $active_operator . ")
                    )", NULL, FALSE);
                    $this->db->group_end();
                } else {
                    $this->db->group_start();
                    // Allow viewing in ready_pb, pb, konsumen if they participated in the SPK at all
                    $this->db->where("EXISTS (
                        SELECT 1 
                        FROM detail_produksi dp 
                        LEFT JOIN routing r ON (dp.routing_id > 0 AND r.routing_id = dp.routing_id) OR ((dp.routing_id IS NULL OR dp.routing_id = 0 OR dp.routing_id = '') AND r.product_id = tbl_produksi.product_id AND r.routing_urutan = dp.routing_urutan)
                        WHERE dp.produksi_id = tbl_produksi.produksi_id 
                        AND (dp.operator_id = " . (int) $active_operator . " OR r.operator_id = " . (int) $active_operator . ")
                    )", NULL, FALSE);
                    $this->db->group_end();
                }
            }

        } // Status/operator filters

        if (!$is_admin) {
            $this->db->group_start();
            $this->db->where('tbl_produksi.lokasi_cetak', $perusahaan);
            $this->db->or_group_start();
            $this->db->where("(tbl_produksi.lokasi_cetak IS NULL OR tbl_produksi.lokasi_cetak = '' OR tbl_produksi.lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('tbl_produksi.perusahaan_id', $perusahaan);
            $this->db->group_end();
            $this->db->group_end();
        } elseif ($is_admin && !empty($filter_cabang) && $filter_cabang !== 'all') {
            $this->db->group_start();
            $this->db->where('tbl_produksi.lokasi_cetak', $filter_cabang);
            $this->db->or_group_start();
            $this->db->where("(tbl_produksi.lokasi_cetak IS NULL OR tbl_produksi.lokasi_cetak = '' OR tbl_produksi.lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('tbl_produksi.perusahaan_id', $filter_cabang);
            $this->db->group_end();
            $this->db->group_end();
        }

        // Explicit date filters; active work is not hidden by age.
        if (!isset($_GET['date_start']) && !isset($_GET['date_end'])) {
            $filter_date_start = null;
            $filter_date_end = null;
        } else {
            $filter_date_start = $this->input->get('date_start');
            $filter_date_end = $this->input->get('date_end');
        }
        if (!$is_global_search && !empty($filter_date_start)) {
            $this->db->where('DATE(tbl_pemesanan.created_at) >=', $filter_date_start);
        }
        if (!$is_global_search && !empty($filter_date_end)) {
            $this->db->where('DATE(tbl_pemesanan.created_at) <=', $filter_date_end);
        }
    }

    public function produksi()
    {
        // Create operator ID variable early to avoid DB Builder collision
        $current_operator_id = $this->get_operator_id_for_current_user();

        // $current_operator_id is already defined
        $filter_status_view = $this->input->get('status_view') ?: 'pending';
        $data['filter_status_view'] = $filter_status_view;
        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');
        $is_admin = in_array((int) $level, [1, 8, 17, 22], true);
        $filter_cabang = $this->input->get('cabang');

        $filter_metode = $this->input->get('metode') ?: 'all';
        $data['filter_metode'] = $filter_metode;

        if (!isset($_GET['date_start']) && !isset($_GET['date_end'])) {
            $data['filter_date_start'] = null;
            $data['filter_date_end'] = null;
        } else {
            $data['filter_date_start'] = $this->input->get('date_start');
            $data['filter_date_end'] = $this->input->get('date_end');
        }
        $data['metode_list'] = $this->db->get('tbl_jenis_transaksi')->result();

        $active_operator = $this->input->get('operator');
        if ($active_operator === null) {
            $active_operator = $current_operator_id ?: 'all';
        }
        if ($current_operator_id !== null) {
            $active_operator = $current_operator_id;
        }

        // --- FETCH GLOBAL STATS FOR ACTIVE FILTERS ---
        $stats = [
            'total_spk' => 0,
            'antrian' => 0,
            'produksi' => 0,
            'selesai' => 0,
            'ready_to_pb' => 0,
            'rijek' => 0
        ];

        // Global Antrian
        $this->db->select('COUNT(tbl_produksi.produksi_id) as count_antrian');
        $this->db->from('tbl_produksi');
        $this->_apply_produksi_filters('pending', $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode, false);
        $stats['antrian'] = $this->db->get()->row()->count_antrian;

        // Global Produksi
        $this->db->select('COUNT(tbl_produksi.produksi_id) as count_produksi');
        $this->db->from('tbl_produksi');
        $this->_apply_produksi_filters('proses', $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode, false);
        $stats['produksi'] = $this->db->get()->row()->count_produksi;

        // Global Rijek
        $this->db->select('COUNT(tbl_produksi.produksi_id) as count_rijek');
        $this->db->from('tbl_produksi');
        $this->_apply_produksi_filters('rijek', $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode, false);
        $stats['rijek'] = $this->db->get()->row()->count_rijek;

        // Global Selesai
        $this->db->select('COUNT(tbl_produksi.produksi_id) as count_selesai');
        $this->db->from('tbl_produksi');
        $this->_apply_produksi_filters('ready_pb', $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode, false);
        $stats['selesai'] = $this->db->get()->row()->count_selesai;

        // Global PB
        $this->db->select('COUNT(tbl_produksi.produksi_id) as count_pb');
        $this->db->from('tbl_produksi');
        $this->_apply_produksi_filters('pb', $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode, false);
        $stats['pb'] = $this->db->get()->row()->count_pb;

        // Global Konsumen
        $this->db->select('COUNT(tbl_produksi.produksi_id) as count_konsumen');
        $this->db->from('tbl_produksi');
        $this->_apply_produksi_filters('konsumen', $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode, false);
        $stats['konsumen'] = $this->db->get()->row()->count_konsumen;

        $this->db->select('COUNT(tbl_produksi.produksi_id) as total')->from('tbl_produksi');
        $this->_apply_produksi_filters('finishing', $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode, false);
        $stats['finishing'] = (int) $this->db->get()->row()->total;

        $stats['ready_to_pb'] = $stats['selesai'];

        // Pagination Config
        $this->load->library('pagination');
        $per_page = 50;
        $page = max(1, (int) ($this->input->get('page') ?: 1));
        $offset = ($page - 1) * $per_page;

        // Count Total Unique SPK for the current filter
        $this->db->select('COUNT(DISTINCT tbl_produksi.nospk) as total_filtered_spk');
        $this->db->from('tbl_produksi');
        $this->_apply_produksi_filters($filter_status_view, $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode, false, !empty($this->input->get('search_spk')));
        $total_spk = $this->db->get()->row()->total_filtered_spk;
        $stats['total_spk'] = $total_spk;

        // Setup pagination URLs
        $config['base_url'] = site_url('wip/produksi');
        $config['total_rows'] = $total_spk;
        $config['per_page'] = $per_page;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['use_page_numbers'] = TRUE;
        $config['full_tag_open'] = '<ul class="pagination pagination-sm m-0">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['total_pages'] = ceil($total_spk / $per_page);
        $data['current_page'] = $page;
        $data['total_products'] = $total_spk;
        $data['offset'] = $offset;
        $data['per_page'] = $per_page;

        // Get distinct nospk with limits
        $this->db->select('tbl_produksi.nospk');
        $this->db->from('tbl_produksi');
        $this->_apply_produksi_filters($filter_status_view, $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode, false, !empty($this->input->get('search_spk')));
        $this->db->group_by('tbl_produksi.nospk');
        if ($filter_status_view === 'ready_pb' || $filter_status_view === 'proses') {
            $this->db->order_by('MAX(tbl_produksi.update_time)', 'DESC');
        } elseif ($filter_status_view === 'pb' || $filter_status_view === 'konsumen') {
            $this->db->order_by('MAX(tbl_produksi.update_time)', 'DESC');
        } else {
            $this->db->order_by('MAX(tbl_produksi.produksi_id)', 'DESC');
        }
        $this->db->limit($per_page, $offset);
        $spk_list = $this->db->get()->result_array();
        $nospk_array = array_column($spk_list, 'nospk');
        if (!empty($nospk_array)) {
            $this->db->select('tbl_produksi.*, tbl_konsumen.konsumen_nama, tbl_product.product_nama, tbl_product.kategori_id, tbl_product.subkategori_id, tbl_product.tipe_id, tbl_kategori.kategori_nama, tbl_satuan.satuan_nama, tbl_perusahaan.nama_perusahaan, tbl_pemesanan.created_at as tgl_spk');
            $this->db->select('tbl_product.form_schema as product_schema, tbl_kategori.form_schema as kategori_schema, tbl_subkategori.form_schema as subkategori_schema');
            $this->db->select('tbl_harga_product.ket_1 as db_ket_1, tbl_harga_product.jenisprint_id as db_jenisprint_id');
            $this->db->select('tbl_jenis_transaksi.nama_jenis_transaksi, tbl_marketplace.marketplace_nama, dk.nama_lengkap as deskprint_nama');

            $this->db->from('tbl_produksi');
            $this->_apply_produksi_filters($filter_status_view, $active_operator, $is_admin, $filter_cabang, $perusahaan, $filter_metode, true, !empty($this->input->get('search_spk')));
            $this->db->join('users as deskprint_user', 'deskprint_user.user_id = tbl_pemesanan.deskprint_id', 'left');
            $this->db->join('tbl_karyawan as dk', 'dk.karyawan_id = deskprint_user.karyawan_id', 'left');
            $this->db->where_in('tbl_produksi.nospk', $nospk_array);

            // Order dynamically based on active view to show most recent activities first
            if ($filter_status_view === 'ready_pb') {
                $this->db->order_by('tbl_produksi.update_time', 'DESC');
            } elseif ($filter_status_view === 'proses') {
                $this->db->order_by('tbl_produksi.update_time', 'DESC');
            } elseif ($filter_status_view === 'pb') {
                $this->db->order_by('tbl_produksi.tgl_kirim_pb', 'DESC');
            } elseif ($filter_status_view === 'konsumen') {
                $this->db->order_by('tbl_produksi.update_time', 'DESC');
            } else {
                // Default (Pending)
                $this->db->order_by('tbl_produksi.produksi_id', 'DESC');
            }

            $raw_data = $this->db->get()->result();
        } else {
            $raw_data = [];
        }
        // [OPTIMIZATION] Pre-calculate all routing stats and operator mappings here
        // so that they are available for both grouping stats and the final view.
        if (!empty($raw_data)) {
            $p_ids = array_column($raw_data, 'produksi_id');

            // PREPARE PRODUCT TO PID MAP
            $pid_to_product = [];
            $unique_product_ids = [];
            foreach ($raw_data as $row) {
                $pid_to_product[$row->produksi_id] = $row->product_id;
                if (!isset($unique_product_ids[$row->product_id])) {
                    $unique_product_ids[$row->product_id] = true;
                }
            }
            $product_id_list = array_keys($unique_product_ids);

            // PRE-FETCH FALLBACK ROUTINGS (Avoid N+1 in loop)
            $fallback_routes_map = [];
            if (!empty($product_id_list)) {
                $fallback_r_data = $this->db->select('product_id, routing_urutan, routing_name, operator_id, is_finishing, bom_line_id')
                    ->from('routing')
                    ->where_in('product_id', $product_id_list)
                    ->get()->result();
                foreach ($fallback_r_data as $fr) {
                    $fallback_routes_map[$fr->product_id][$fr->routing_urutan] = $fr;
                }
            }

            // CHUNK FETCHING TO PREVENT QUERY LENGTH LIMITS
            $all_routing_steps = [];
            $p_ids_chunks = array_chunk($p_ids, 1000);
            foreach ($p_ids_chunks as $chunk) {
                $this->db->select('dp.*, r.routing_name, dp.status_produksi, m.mesin_nama, dp.lokasi_proses, dp.operator_id, r.operator_id AS routing_operator_id, dp.routing_urutan, r.is_finishing, r.bom_line_id');
                $this->db->from('detail_produksi dp');
                $this->db->join('routing r', 'r.routing_id = dp.routing_id', 'left');
                $this->db->join('tbl_mesin m', 'm.mesin_id = dp.mesin_id', 'left');
                $this->db->where_in('dp.produksi_id', $chunk);
                $this->db->order_by('dp.routing_urutan', 'ASC');
                $chunk_result = $this->db->get()->result();
                $all_routing_steps = array_merge($all_routing_steps, $chunk_result);
            }

            // Fetch all users to map operator_id to username efficiently
            // Fetch all karyawan to map operator_id to nama_lengkap efficiently
            $karyawan_data = $this->db->select('karyawan_id, nama_lengkap, divisi_id')
                ->from('tbl_karyawan')
                ->get()->result();

            $karyawan_to_user = [];
            foreach ($karyawan_data as $k) {
                $karyawan_to_user[$k->karyawan_id] = $k->nama_lengkap;
            }

            $divisi_data = $this->db->get('tbl_divisi')->result();
            $divisi_to_user = [];
            foreach ($divisi_data as $d) {
                $divisi_to_user[$d->divisi_id] = $d->divisi_nama;
            }

            $steps_map = [];
            $prod_stats_map = [];

            foreach ($all_routing_steps as $step) {
                $product_id = $pid_to_product[$step->produksi_id] ?? 0;

                if (empty($step->routing_name) && !empty($product_id)) {
                    $fallback_r = $fallback_routes_map[$product_id][$step->routing_urutan] ?? null;
                    if ($fallback_r) {
                        $step->routing_name = $fallback_r->routing_name;
                        $step->is_finishing = $fallback_r->is_finishing;
                        $step->bom_line_id = $fallback_r->bom_line_id;
                        if (empty($step->routing_operator_id)) {
                            $step->routing_operator_id = $fallback_r->operator_id;
                        }
                    } else {
                        $step->routing_name = "Proses " . ($step->routing_urutan ?: "");
                    }
                }
                $step->workflow_version = Production_workflow::version($step);
                if (Production_workflow::step_completed($step->status_produksi))
                    $step->status_produksi = '2';
                $steps_map[$step->produksi_id][] = $step;
            }

            foreach ($steps_map as $pid => $steps) {
                $total = count($steps);
                $done = 0;
                $current_step = null;
                $last_done_operator = null;
                $active_op_id = null;
                $op_cetak = [];
                $op_finishing = [];

                foreach ($steps as $s) {
                    if ($s->status_produksi == '2') {
                        $done++;
                        if (!empty($s->operator_id)) {
                            $last_done_operator = $s->operator_id;
                        }
                    } else {
                        if ($current_step === null) {
                            $current_step = $s;
                        }
                    }
                    if ($s->status_produksi == '1') {
                        if ($active_op_id === null) {
                            $active_op_id = $s->operator_id;
                        }
                    }

                    // Extract operator name if an operator is assigned, regardless of status
                    if (!empty($s->operator_id)) {
                        $op_name = $karyawan_to_user[$s->operator_id] ?? null;
                        $s->op_name = $op_name;
                        if ($op_name) {
                            // Check database flag or routing name keywords
                            $is_finishing_step = ($s->is_finishing == 1) ||
                                (stripos($s->routing_name, 'finish') !== false) ||
                                (stripos($s->routing_name, 'cutting') !== false) ||
                                (stripos($s->routing_name, 'potong') !== false) ||
                                (stripos($s->routing_name, 'jahit') !== false) ||
                                (stripos($s->routing_name, 'mata ayam') !== false) ||
                                (stripos($s->routing_name, 'laminasi') !== false) ||
                                (stripos($s->routing_name, 'pon') !== false) ||
                                (stripos($s->routing_name, 'kiss cut') !== false);

                            if ($is_finishing_step) {
                                if (!in_array($op_name, $op_finishing))
                                    $op_finishing[] = $op_name;
                            } else {
                                if (!in_array($op_name, $op_cetak))
                                    $op_cetak[] = $op_name;
                            }
                        }
                    }
                }

                $assigned_user = null;
                if ($active_op_id && isset($karyawan_to_user[$active_op_id])) {
                    $assigned_user = $karyawan_to_user[$active_op_id];
                } elseif ($last_done_operator && isset($karyawan_to_user[$last_done_operator])) {
                    $assigned_user = $karyawan_to_user[$last_done_operator];
                }


                $prod_stats_map[$pid] = [
                    'total_steps' => $total,
                    'done_steps' => $done,
                    'current_step_name' => $current_step ? $current_step->routing_name : null,
                    'current_step_urutan' => $current_step ? $current_step->routing_urutan : null,
                    'current_step_bom_line_id' => $current_step ? $current_step->bom_line_id : null,
                    'assigned_user' => $assigned_user,
                    'active_operator_id' => $active_op_id,
                    'op_cetak_name' => !empty($op_cetak) ? implode(', ', $op_cetak) : null,
                    'op_finishing_name' => !empty($op_finishing) ? implode(', ', $op_finishing) : null
                ];
            }

            foreach ($raw_data as $item) {
                $item->routing_steps = $steps_map[$item->produksi_id] ?? [];
                foreach ($item->routing_steps as $snapshot_step) {
                    if (!Production_workflow::step_completed($snapshot_step->status_produksi)) {
                        $item->workflow_step_id = $snapshot_step->detail_produksi_id;
                        $item->workflow_step_status = $snapshot_step->status_produksi;
                        $item->workflow_version = $snapshot_step->workflow_version;
                        $item->workflow_qty_output = (float) ($snapshot_step->qty_output ?? 0);
                        break;
                    }
                }

                $st = $prod_stats_map[$item->produksi_id] ?? null;

                if ($st) {
                    $item->total_steps = $st['total_steps'];
                    $item->done_steps = $st['done_steps'];
                    $item->current_step_name = $st['current_step_name'];
                    $item->current_step_urutan = $st['current_step_urutan'];
                    $item->current_step_bom_line_id = $st['current_step_bom_line_id'];
                    $item->assigned_user = $st['assigned_user'];
                    $item->active_operator_id = $st['active_operator_id'];
                    $item->op_cetak_name = $st['op_cetak_name'];
                    $item->op_finishing_name = $st['op_finishing_name'];
                } else {
                    $item->total_steps = 0;
                    $item->done_steps = 0;
                    $item->current_step_name = null;
                    $item->current_step_urutan = null;
                    $item->current_step_bom_line_id = null;
                    $item->assigned_user = null;
                    $item->active_operator_id = null;
                    $item->op_cetak_name = null;
                    $item->op_finishing_name = null;
                }

                // Override assigned_user for PB/Handover items by extracting from catatan
                $st_prod = (string) $item->status_produksi;
                if ($st_prod === 'on_costumer' && !empty($item->catatan)) {
                    if (preg_match('/\[(?:BULK )?HANDOVER\].*?Oleh:\s*([^|]+)/', $item->catatan, $matches)) {
                        $item->assigned_user = trim($matches[1]);
                    }
                }
            }
        }
        // DEBUGGING CODE (Hapus setelah selesai)
        // echo $this->db->last_query();
        // die();
        // END DEBUGGING

        // Grouping data berdasarkan nospk & Calculate Stats
        $grouped_data = [];


        if (!empty($raw_data)) {
            $unique_spks = [];
            foreach ($raw_data as $row) {
                $nospk = !empty($row->nospk) ? $row->nospk : 'NO SPK Undefined';
                $cat_id = $row->kategori_id ?: '0';

                // Extract and Merge all Schema parts (Cat -> SubCat -> Product overrides)
                $cat_sch = !empty($row->kategori_schema) ? json_decode($row->kategori_schema, true) : [];
                $sub_sch = !empty($row->subkategori_schema) ? json_decode($row->subkategori_schema, true) : [];
                $prd_sch = !empty($row->product_schema) ? json_decode($row->product_schema, true) : [];

                $merged_schema = [];
                if (is_array($cat_sch)) {
                    foreach ($cat_sch as $s)
                        if (isset($s['name']))
                            $merged_schema[$s['name']] = $s;
                }
                if (is_array($sub_sch)) {
                    foreach ($sub_sch as $s)
                        if (isset($s['name']))
                            $merged_schema[$s['name']] = $s;
                }
                if (is_array($prd_sch)) {
                    foreach ($prd_sch as $s)
                        if (isset($s['name']))
                            $merged_schema[$s['name']] = $s;
                }

                // Keep raw array for simpler checking later
                $row->merged_schema_assoc = $merged_schema;

                // Identify Machine Required (Patching Metadata for Older Schemas)
                $meta_info = !empty($row->metadata) ? json_decode($row->metadata, true) : [];
                $jenisprint_id = $meta_info['jenisprint_id'] ?? '0';
                $groupSelector = $jenisprint_id;

                $is_dtg = ($cat_id == 8 || ($cat_id == 5 && stripos($row->product_nama, 'DTG') !== false));

                // For Category 9 (DTF) and 5 (Textile), ket_1 is Roll/Fabric Width, NOT a machine ID.
                if (in_array($cat_id, [5, 9]) && !$is_dtg) {
                    $dtf_lebar = $meta_info['ket_1'] ?? $row->db_ket_1 ?? '0';
                    $groupSelector = 'W' . $dtf_lebar;
                    if (!isset($meta_info['ket_1']) && $row->db_ket_1) {
                        $meta_info['ket_1'] = $row->db_ket_1;
                        $row->metadata = json_encode($meta_info);
                    }
                } else {
                    // If memory metadata lacks 'jenisprint_id', try to recover from the Pricing/Product schema master
                    if ($jenisprint_id == '0' || empty($jenisprint_id)) {
                        if (!empty($row->db_jenisprint_id) && $row->db_jenisprint_id != '0') {
                            $jenisprint_id = $row->db_jenisprint_id;
                        } elseif (!empty($row->db_ket_1) && $row->db_ket_1 != '0') {
                            // Older schema uses ket_1 to identify printer dropdown options
                            $jenisprint_id = $row->db_ket_1;
                            $meta_info['ket_1'] = $jenisprint_id; // Also sync ket_1 so badges appear
                        }

                        // Re-inject so that all downstream Views (v_table_item, v_render_metadata) can simply read metadata['jenisprint_id'] natively
                        if ($jenisprint_id != '0') {
                            $meta_info['jenisprint_id'] = $jenisprint_id;
                            $row->metadata = json_encode($meta_info);

                            // Force a schema entry so v_render_metadata will loop over it and style it as a printer!
                            if (!isset($row->merged_schema_assoc['jenisprint_id'])) {
                                // If it was ket_1, migrate its label/options
                                if (isset($row->merged_schema_assoc['ket_1'])) {
                                    $legacySchema = $row->merged_schema_assoc['ket_1'];
                                    $legacySchema['name'] = 'jenisprint_id';
                                    $row->merged_schema_assoc['jenisprint_id'] = $legacySchema;
                                    unset($row->merged_schema_assoc['ket_1']);
                                } else {
                                    $row->merged_schema_assoc['jenisprint_id'] = [
                                        'name' => 'jenisprint_id',
                                        'label' => 'MESIN CETAK',
                                        'type' => 'text' // Will be overridden by DB map anyway
                                    ];
                                }
                            }
                        }
                    }
                    $groupSelector = $jenisprint_id;
                }

                // [MODIFIED] Logika Grouping: A3 dan Finishing digabung per SPK agar tidak "double"
                // Namun Large Format & DTF tetap dipisah per mesin/lebar bahan untuk efisiensi cetak.
                $is_granular_cat = in_array((int) $cat_id, [1, 2, 4, 5, 8, 9]); // ID Kategori Digital A3+, Large Format, Sublimasi, Textile, DTF

                if ($is_granular_cat) {
                    // Tetap split untuk Banner/LF agar operator tahu beda bahan/mesin
                    $group_key = $nospk . '_c' . $cat_id . '_grp' . $groupSelector;
                } else {
                    // Gabung untuk A3, Finishing, dsb agar satu baris per SPK
                    $group_key = $nospk;
                }

                if (!isset($grouped_data[$group_key])) {
                    $grouped_data[$group_key] = [];
                }
                $grouped_data[$group_key][] = $row;
            }
        }

        // Build Machine Printing Map for labels
        $mesin_data = $this->db->get('tbl_mesin_printing')->result();
        $mesin_map = [];
        foreach ($mesin_data as $m)
            $mesin_map[$m->id] = strtoupper($m->mesin_nama);
        $data['mesin_printing_map'] = $mesin_map;

        if ($current_operator_id !== null) {
            $data['is_operator_mode'] = true;
        }

        // Fetch all active operators for dropdown
        $data['tab_operators'] = $this->db->order_by('operator_id', 'ASC')->get('tbl_operator')->result();

        // Load all active categories to build schema map (required for metadata badges)
        $all_categories = $this->db->get_where('tbl_kategori', ['status' => '1'])->result();
        $schema_map = [];
        foreach ($all_categories as $cat) {
            $schema_map[$cat->kategori_id] = !empty($cat->form_schema) ? json_decode($cat->form_schema, true) : [];
        }
        $data['category_schemas'] = $schema_map;

        // Determine dynamic View Mode (Item View vs SPK View) and Default Category ID
        $is_material_view_default = false;
        $default_cat_id = 1;
        if ($active_operator !== 'all') {
            $is_material_view_default = true; // Force new UI for all operators

            $op_row = $this->db->get_where('tbl_operator', ['operator_id' => $active_operator])->row();
            if ($op_row && !empty($op_row->kategori_ids)) {
                $op_cats = explode(',', $op_row->kategori_ids);
                $default_cat_id = (int) $op_cats[0];
            }
        }
        $data['is_material_view_default'] = $is_material_view_default;
        $data['active_operator'] = $active_operator;

        $data['produksi_list'] = $grouped_data;
        $data['raw_items'] = $raw_data; // Flat list for item-based view
        $data['stats'] = $stats;

        // Fetch BOM info for raw_items (Efficient batching)
        if (!empty($raw_data)) {
            $product_ids = array_unique(array_column($raw_data, 'product_id'));
            $this->db->select('bl.bom_line_id, bh.bahan_nama, bl.quantity as bom_qty, bl.product_id, bl.satuan_id, ts.satuan_nama, tm.bahan_id, bl.bahan_id as bom_material_id, bl.panjang, bl.lebar, bl.harga_id');
            $this->db->from('bom_lines bl');
            $this->db->join('tbl_material tm', 'tm.material_id = bl.bahan_id', 'left');
            $this->db->join('tbl_bahan bh', 'bh.bahan_id = tm.bahan_id', 'left');
            $this->db->join('tbl_satuan ts', 'ts.satuan_id = bh.satuan_id', 'left');
            $this->db->where_in('bl.product_id', $product_ids);
            $this->db->order_by('bl.bom_line_id', 'ASC');
            $bom_data = $this->db->get()->result();

            $bom_map = [];
            $bom_lines_map = [];
            $priority_units = [6 => 3, 2 => 2, 1 => 1]; // Roll > Meter > Lembar

            foreach ($bom_data as $bd) {
                // Add to the list of all materials (will be filtered later per item)
                $bom_lines_map[$bd->product_id][] = $bd;
            }

            // [OPTIMIZATION] Pre-fetch Dynamic BOM Mapping to avoid N+1 queries in the loop
            $dynamic_boms_map = [];
            if (!empty($product_ids)) {
                $this->db->select('dm.*, b.bahan_nama, b.satuan_id, s.satuan_nama');
                $this->db->from('tbl_bom_dynamic_mapping dm');
                $this->db->join('tbl_bahan b', 'b.bahan_id = dm.bahan_id', 'left');
                $this->db->join('tbl_satuan s', 's.satuan_id = b.satuan_id', 'left');
                $this->db->where_in('dm.product_id', $product_ids);
                $dyn_bom_data = $this->db->get()->result();
                foreach ($dyn_bom_data as $dbom) {
                    $dynamic_boms_map[$dbom->product_id][] = $dbom;
                }
            }

            foreach ($data['raw_items'] as &$item) {
                // Filter BOM lines dynamically by this item's specific harga_id (Variasi) - WHOLE BOM OVERRIDE
                $filtered_bom_lines = [];
                if (isset($bom_lines_map[$item->product_id])) {
                    $specific_boms = [];
                    $global_boms = [];
                    foreach ($bom_lines_map[$item->product_id] as $bd) {
                        if ($bd->harga_id == $item->harga_id && $bd->harga_id !== null) {
                            $specific_boms[] = $bd;
                        } else if ($bd->harga_id === null) {
                            $global_boms[] = $bd;
                        }
                    }
                    $filtered_bom_lines = count($specific_boms) > 0 ? $specific_boms : $global_boms;
                }

                // [DYNAMIC BOM MAPPING INJECTION]
                if (!empty($item->metadata)) {
                    $meta = json_decode($item->metadata, true);
                    if (is_array($meta)) {
                        $dynamic_boms = isset($dynamic_boms_map[$item->product_id]) ? $dynamic_boms_map[$item->product_id] : [];

                        foreach ($dynamic_boms as $dbom) {
                            $meta_val = isset($meta[$dbom->attribute_key]) ? $meta[$dbom->attribute_key] : null;
                            if ($meta_val !== null && $meta_val == $dbom->attribute_value) {
                                // Calculate Qty
                                $base_qty = 1; // Default
                                if ($dbom->calc_type === 'area') {
                                    $p = isset($meta['panjang']) ? (float) $meta['panjang'] : (isset($item->panjang) ? (float) $item->panjang : 1);
                                    $l = isset($meta['lebar']) ? (float) $meta['lebar'] : (isset($item->lebar) ? (float) $item->lebar : 1);
                                    $base_qty = $p * $l;
                                } else if ($dbom->calc_type === 'sheets_plano' || $dbom->calc_type === 'fixed') {
                                    $base_qty = 1;
                                }

                                if (!empty($dbom->multiplier_key)) {
                                    if (is_numeric($dbom->multiplier_key)) {
                                        $base_qty *= (float) $dbom->multiplier_key;
                                    } else if (isset($meta[$dbom->multiplier_key])) {
                                        $base_qty *= (float) $meta[$dbom->multiplier_key];
                                    }
                                }

                                if ((float) $dbom->divider_factor > 0) {
                                    $base_qty /= (float) $dbom->divider_factor;
                                }

                                if ((float) $dbom->scrap_factor > 0) {
                                    $base_qty += ($base_qty * ((float) $dbom->scrap_factor / 100));
                                }

                                // Create pseudo bom_line
                                $pseudo_bom = new stdClass();
                                $pseudo_bom->bom_line_id = 'dyn_' . $dbom->id;
                                $pseudo_bom->bahan_nama = $dbom->bahan_nama;
                                $pseudo_bom->bom_qty = $base_qty;
                                $pseudo_bom->product_id = $item->product_id;
                                $pseudo_bom->satuan_id = $dbom->satuan_id;
                                $pseudo_bom->satuan_nama = $dbom->satuan_nama;
                                $pseudo_bom->bahan_id = $dbom->bahan_id;
                                $pseudo_bom->bom_material_id = $dbom->bahan_id;
                                $pseudo_bom->panjang = null;
                                $pseudo_bom->lebar = null;
                                $pseudo_bom->harga_id = null;
                                $pseudo_bom->is_dynamic = true;

                                $filtered_bom_lines[] = $pseudo_bom;
                            }
                        }
                    }
                }
                // [/DYNAMIC BOM MAPPING INJECTION]

                $item->bom_lines = $filtered_bom_lines;

                // Dynamically build the primary BOM for THIS item's variant
                $item_bom_primary = null;
                foreach ($filtered_bom_lines as $bd) {
                    if (!$item_bom_primary) {
                        $item_bom_primary = $bd;
                    } else {
                        $current_has_dim = ($item_bom_primary->panjang > 0 || $item_bom_primary->lebar > 0);
                        $new_has_dim = ($bd->panjang > 0 || $bd->lebar > 0);

                        $current_unit = $item_bom_primary->satuan_id;
                        $new_unit = $bd->satuan_id;

                        $current_prio = $priority_units[$current_unit] ?? 0;
                        $new_prio = $priority_units[$new_unit] ?? 0;

                        if (!$current_has_dim && $new_has_dim) {
                            $item_bom_primary = $bd;
                        } elseif ($current_has_dim === $new_has_dim && $new_prio > $current_prio) {
                            $item_bom_primary = $bd;
                        }
                    }
                }
                $item->bom = $item_bom_primary;

                // Inherit Standard Dimensions from BOM if empty in Transaction
                if (empty($item->panjang) || $item->panjang == 0) {
                    $item->panjang = isset($item->bom) ? (float) $item->bom->panjang : 0;
                }
                if (empty($item->lebar) || $item->lebar == 0) {
                    $item->lebar = isset($item->bom) ? (float) $item->bom->lebar : 0;
                }
            }

            // EXPLICITLY Apply to the Grouped List (View Layer assurance)
            foreach ($data['produksi_list'] as $gKey => &$gItems) {
                foreach ($gItems as &$gItem) {
                    // Match back from raw_items based on produksi_id
                    // Since raw_items is flat, we can find the item.
                    $mapped_item = null;
                    foreach ($data['raw_items'] as $raw) {
                        if ($raw->produksi_id == $gItem->produksi_id) {
                            $mapped_item = $raw;
                            break;
                        }
                    }
                    if ($mapped_item) {
                        $gItem->bom = $mapped_item->bom;
                        $gItem->bom_lines = $mapped_item->bom_lines;
                    } else {
                        $gItem->bom = null;
                        $gItem->bom_lines = [];
                    }
                    if (empty($gItem->panjang) || $gItem->panjang == 0) {
                        $gItem->panjang = isset($gItem->bom) ? (float) $gItem->bom->panjang : 0;
                    }
                    if (empty($gItem->lebar) || $gItem->lebar == 0) {
                        $gItem->lebar = isset($gItem->bom) ? (float) $gItem->bom->lebar : 0;
                    }
                }
            }

            // Fetch all routing steps for the items in the list (MOVED UP FOR OPTIMIZATION)
        }

        // Fetch Operators for dropdowns
        $this->db->select('tbl_karyawan.karyawan_id, tbl_karyawan.nama_lengkap');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id');
        $this->db->where_in('users.role_id', [4, 20]);
        $data['operators'] = $this->db->get()->result();

        $data['default_category_id'] = $default_cat_id;

        $data['current_operator_id'] = $this->get_operator_id_for_current_user();
        $data['active_operator'] = $active_operator;
        $data['is_super_admin'] = in_array((int) $this->session->userdata('level'), [1, 8, 17, 22], true);
        $data['group_mode'] = $this->input->get('group_mode') ?: ($_GET['group_mode'] ?? 'bahan');

        // Data for Branch Filter
        if ($data['is_super_admin']) {
            $data['cabang_list'] = $this->db->get('tbl_perusahaan')->result();
            $data['filter_cabang'] = $this->input->get('cabang') ?: 'all';
        }

        if ($this->input->get('iframe') == '1') {
            $this->load->view('layout_admin/head', $data);
            $this->load->view('admin/wip/produksi_list', $data);
            $this->load->view('layout_admin/alert', $data);
        } else {
            $this->load_admin_view('admin/wip/produksi_list', $data);
        }
    }

    /**
     * BATCH: Start ALL items in an SPK
     * Bypass button to start everything at once.
     */
    public function start_all_spk_items()
    {
        $this->_workflow_response(function () {
            return $this->_workflow_execute($this->_workflow_spk_ids($this->input->post('nospk', true)), 'start');
        });
    }

    public function finish_all_spk_items()
    {
        $this->_workflow_legacy_rejected();
    }

    public function revise_item_process()
    {
        $this->_workflow_legacy_rejected();
    }

    public function admin_save_item_edit()
    {
        $this->_workflow_response(function () {
            if (!in_array((int) $this->session->userdata('level'), [1, 17], true))
                throw new DomainException('Akses ditolak.');
            $id = $this->input->post('produksi_id', true);
            $data = $this->input->post(null, true);
            return $this->_workflow_execute([$id], 'report', function ($item, $step, $actor) use ($data) {
                if (!Production_workflow::pending($item->status_produksi))
                    throw new DomainException('Spesifikasi pekerjaan berjalan/selesai tidak dapat ditimpa. Ajukan revisi terpisah.');
                foreach ($this->wip_workflow->steps($item) as $history) {
                    if (!Production_workflow::pending($history->status_produksi) || !empty($history->waktu_mulai))
                        throw new DomainException('Item sudah memiliki riwayat pengerjaan; penimpaan ditolak.');
                }
                $updates = [];
                foreach (['qty', 'panjang', 'lebar'] as $field)
                    $updates[$field] = Production_workflow::number($data[$field] ?? null, $field);
                if ($updates['qty'] <= 0 || $updates['qty'] != floor($updates['qty']))
                    throw new DomainException('Qty order harus bilangan bulat positif.');
                if (!empty($data['product_id']) && (int) $data['product_id'] !== (int) $item->product_id)
                    throw new DomainException('Penggantian produk memerlukan SPK/revisi terpisah.');
                if (!empty($data['step_updates']))
                    throw new DomainException('Riwayat operator dan konsumsi bahan tidak dapat ditimpa melalui editor.');
                $updates['catatan'] = trim(($item->catatan ?? '') . "\n[ADMIN EDIT] " . trim((string) ($data['catatan'] ?? '')));
                $updates['update_time'] = date('Y-m-d H:i:s');
                if (!$this->db->where('produksi_id', $item->produksi_id)->update('tbl_produksi', $updates))
                    throw new RuntimeException('Gagal menyimpan spesifikasi.');
                if (!$this->db->insert('tbl_produksi_log', ['produksi_id' => $item->produksi_id, 'user_id' => $actor['user_id'], 'status_asal' => $item->status_produksi, 'status_tujuan' => $item->status_produksi, 'catatan' => '[ADMIN EDIT] Spesifikasi sebelum produksi; qty sebelumnya: ' . $item->qty]))
                    throw new RuntimeException('Gagal menyimpan audit.');
                return [];
            });
        });
    }

    public function batch_status_produksi()
    {
        $this->_workflow_response(function () {
            if ($this->input->post('status', true) !== 'proses')
                throw new DomainException('Batch hanya dapat memulai tahap; hasil dan PB memakai aksi terpisah.');
            $data = $this->input->post(null, true);
            return $this->_workflow_execute(Production_workflow::ids($this->input->post('ids', true)), 'start', function ($item, $step) use ($data) {
                return $this->_workflow_fields($item, $step, $data, false);
            });
        });
    }

    public function ajax_search_bahan()
    {
        $q = $this->input->get('q', true);
        $this->db->select('bahan_id, bahan_nama');
        $this->db->from('tbl_bahan');
        if ($q) {
            $this->db->like('bahan_nama', $q);
        }
        $this->db->limit(20);
        $res = $this->db->get()->result();
        echo json_encode($res);
    }

    /**
     * AJAX: Get Material Variants (Rolls) for a Base Material
     */
    public function ajax_get_material_variants()
    {
        $bahan_id = $this->input->get('bahan_id', true);
        if (!$bahan_id) {
            echo json_encode([]);
            return;
        }

        $this->db->select('
            m.material_id, 
            m.varian, 
            m.barcode, 
            SUM(COALESCE(sg.qty, 0)) as stok_aktual_base, 
            (SUM(COALESCE(sg.qty, 0)) / COALESCE(NULLIF(m.konversi, 0), 1)) as stok_aktual_gudang,
            s_prod.satuan_nama as satuan_produksi,
            s_gudang.satuan_nama as satuan_gudang
        ');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left');
        $this->db->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id AND sg.gudang_id = ' . (int) $this->session->userdata('perusahaan_id'), 'left');
        $this->db->join('tbl_satuan s_prod', 'b.satuan_id = s_prod.satuan_id', 'left');
        $this->db->join('tbl_satuan s_gudang', 'm.satuan_gudang_id = s_gudang.satuan_id', 'left');
        $this->db->where('m.bahan_id', $bahan_id);
        $this->db->where('m.status', '1');
        $this->db->group_by('m.material_id');
        $res = $this->db->get()->result();

        echo json_encode($res);
    }

    /**
     * LAPOR MASALAH / RIJEK
     */
    public function lapor_masalah()
    {
        $this->_workflow_response(function () {
            $nospk = $this->input->post('nospk', true);
            $lines = $this->input->post('items_rijek', true);
            $type = trim((string) $this->input->post('jenis_masalah', true));
            $reason = trim((string) $this->input->post('keterangan', true));
            if (!$nospk || !$type || !is_array($lines) || !$lines || !$this->input->post('pin_operator', true))
                throw new DomainException('Lengkapi item, jenis masalah, dan PIN operator.');
            $quantities = [];
            foreach ($lines as $line) {
                $id = $line['id'] ?? 0;
                if (isset($quantities[$id]))
                    throw new DomainException('Item laporan tidak boleh duplikat.');
                $quantities[$id] = Production_workflow::number($line['qty_rijek'] ?? 0, 'Qty rijek');
            }
            return $this->_workflow_execute(array_keys($quantities), 'report', function ($item, $step, $actor) use ($nospk, $quantities, $type, $reason) {
                if ($item->nospk !== $nospk)
                    throw new DomainException('Item bukan milik SPK yang dilaporkan.');
                $qty = $quantities[$item->produksi_id];
                if ($qty <= 0 || $qty > (float) $item->qty)
                    throw new DomainException('Qty rijek harus lebih dari nol dan tidak melebihi order.');
                $note = '[MASALAH] ' . date('d/m/Y H:i') . ' | Karyawan #' . $actor['employee_id'] . ' | Qty: ' . $qty . ' | Jenis: ' . $type . ' | ' . $reason;
                if ($this->input->post('is_reprint', true))
                    $note .= ' | Permintaan reprint; riwayat tahap dipertahankan, menunggu peninjauan supervisor';
                if (!$this->db->where('produksi_id', $item->produksi_id)->update('tbl_produksi', ['catatan' => trim(($item->catatan ?? '') . "\n" . $note), 'update_time' => date('Y-m-d H:i:s')]))
                    throw new RuntimeException('Gagal menyimpan masalah.');
                if (!$this->db->insert('tbl_produksi_log', ['produksi_id' => $item->produksi_id, 'user_id' => $actor['user_id'], 'status_asal' => $item->status_produksi, 'status_tujuan' => $item->status_produksi, 'catatan' => $note]))
                    throw new RuntimeException('Gagal menyimpan riwayat masalah.');
                return [];
            });
        });
    }

    public function batch_selesai_dan_stok()
    {
        $this->_workflow_response(function () {
            if (!$this->input->post('pin_operator', true))
                throw new DomainException('Masukkan PIN operator.');
            $ids = Production_workflow::ids($this->input->post('ids', true));
            $outcomes = $this->_workflow_map('outcomes');
            $material = $this->input->post('material_id', true);
            $usage = Production_workflow::number($this->input->post('total_usage', true) ?: 0, 'Total pemakaian bahan');
            if ($usage > 0 && !ctype_digit((string) $material))
                throw new DomainException('Pilih material pemakaian batch.');
            // Explicit allocations are in base units. Legacy batches use a documented quantity-based allocation.
            $weights = [];
            foreach ($ids as $id) {
                $item = $this->db->get_where('tbl_produksi', ['produksi_id' => $id])->row();
                if (!$item)
                    throw new DomainException('Item batch tidak ditemukan.');
                $weights[$id] = max(0, (float) $item->qty);
            }
            $total_weight = array_sum($weights);
            if ($usage > 0 && $total_weight <= 0)
                throw new DomainException('Qty batch tidak valid.');
            $last_id = end($ids);
            $allocated = 0;
            $machine = $this->input->post('mesin_id', true);
            return $this->_workflow_execute($ids, 'complete', function ($item, $step, $actor) use ($outcomes, $material, $usage, $weights, $total_weight, $last_id, &$allocated, $machine) {
                $id = $item->produksi_id;
                $outcome = $outcomes[$id] ?? null;
                if (!is_array($outcome) || !in_array($outcome['status'] ?? '', ['success', 'fail'], true))
                    throw new DomainException('Isi hasil setiap item batch.');
                $fields = $this->_workflow_fields($item, $step, [
                    'qty_output' => $outcome['qty_output'] ?? null,
                    'qty_missing' => $outcome['qty_missing'] ?? 0,
                    'mesin_id' => $machine
                ]);
                $failed = $outcome['status'] === 'fail';
                if ($failed && !trim((string) ($outcome['reason'] ?? '')))
                    throw new DomainException('Isi alasan hasil rijek.');
                $fields['_failed'] = $failed;
                $fields['_note'] = trim((string) ($outcome['reason'] ?? ''));
                $share = $usage > 0 ? ($id == $last_id ? $usage - $allocated : $usage * $weights[$id] / $total_weight) : 0;
                $allocated += $share;
                if ($share > 0)
                    $fields['_note'] .= '; alokasi bahan batch berdasarkan qty order: ' . $share;
                $this->_workflow_post_material($item, $step, $material, $share, $actor);
                $fields['qty_bahan_pakai'] = (float) ($step->qty_bahan_pakai ?? 0) + $share;
                if ($share > 0)
                    $fields['material_id'] = (int) $material;
                $secondary = [];
                foreach (($outcome['bom_usage'] ?? []) as $line) {
                    $mid = $line['bom_material_id'] ?? 0;
                    $qty = Production_workflow::number($line['qty'] ?? 0, 'Pemakaian aktual BOM');
                    if ($mid == $material || !$qty)
                        continue;
                    if (!ctype_digit((string) $mid) || (int) $mid < 1)
                        throw new DomainException('Material BOM tidak valid.');
                    $secondary[$mid] = ($secondary[$mid] ?? 0) + $qty;
                }
                ksort($secondary, SORT_NUMERIC);
                foreach ($secondary as $mid => $qty)
                    $this->_workflow_post_material($item, $step, $mid, $qty, $actor);
                $this->_workflow_require_material($step, $share + array_sum($secondary));
                // Completion and handover are separate actions; send_to_pb is not a status override.
                return $fields;
            });
        });
    }



    public function selesai_item($produksi_id)
    {
        $this->_workflow_legacy_rejected();
    }

    public function get_detail_produksi()
    {
        $produksi_id = $this->input->post('produksi_id', true);
        try {
            $this->wip_workflow->assert_access($data['produksi'], $this->wip_workflow->actor(null, $this->get_operator_id_for_current_user()));
        } catch (DomainException $e) {
            show_error($e->getMessage(), 403);
            return;
        }
        $operator_id = $this->get_operator_id_for_current_user();

        $data['detail_produksi'] = $this->Produksi_model->get_detail_produksi_by_id($produksi_id, $operator_id);
        $data['mesin'] = $this->db->get('tbl_mesin')->result();
        $data['perusahaan'] = $this->db->get('tbl_perusahaan')->result();
        $data['bahan_baku'] = $this->db->get('tbl_material')->result();

        $this->load_admin_view('admin/wip/wip_detail', $data);
    }

    public function detail($produksi_id)
    {
        $data['produksi'] = $this->Produksi_model->get_produksi_by_id($produksi_id);

        if (!$data['produksi']) {
            show_404();
        }

        $operator_id = $this->get_operator_id_for_current_user();

        $data['detail_produksi'] = $this->Produksi_model->get_detail_produksi_by_id($produksi_id, $operator_id);
        $data['mesin'] = $this->db->get('tbl_mesin')->result();
        $data['perusahaan'] = $this->db->get('tbl_perusahaan')->result();
        $data['bahan_baku'] = $this->db->get('tbl_material')->result();

        $this->load_admin_view('admin/wip/wip_detail', $data);
    }
    public function guide()
    {
        $this->load_admin_view('admin/wip/guide', []);
    }


    public function guide_revisi()
    {
        $this->load_admin_view('admin/wip/guide_revisi', []);
    }


    public function start_process()
    {
        $this->_workflow_detail_action('start');
    }

    public function complete_process()
    {
        $this->_workflow_detail_action('complete');
    }

    public function update_production_process()
    {
        $status = (string) $this->input->post('status_produksi', true);
        $actions = ['0' => 'cancel', '1' => 'start', '2' => 'complete'];
        if (!isset($actions[$status])) {
            $this->_workflow_legacy_rejected();
            return;
        }
        $this->_workflow_detail_action($actions[$status]);
    }

    public function move_to_pb()
    {
        $this->_workflow_response(function () {
            return $this->_workflow_execute([$this->input->post('produksi_id', true)], 'handover'); });
    }

    public function batch_move_to_pb()
    {
        $this->_workflow_response(function () {
            return $this->_workflow_execute(Production_workflow::ids($this->input->post('ids', true)), 'handover'); });
    }

    public function ajax_get_log()
    {
        $produksi_id = $this->input->post('produksi_id', true);
        if (empty($produksi_id)) {
            echo json_encode(['success' => false, 'data' => []]);
            return;
        }

        $logs = $this->Produksi_model->get_produksi_log($produksi_id);

        $html = '';
        if (empty($logs)) {
            $html = '<div class="text-center text-muted p-3"><small>Belum ada riwayat aktivitas.</small></div>';
        } else {
            $html .= '<ul class="timeline timeline-inverse">';
            foreach ($logs as $log) {
                // Konversi tanggal (asumsikan created_at berupa format Y-m-d H:i:s)
                $time_formatted = date('d M Y H:i', strtotime($log->created_at));

                $nama = htmlspecialchars($log->nama_lengkap ?? $log->username ?? 'Sistem');
                $catatan = nl2br(htmlspecialchars($log->catatan));

                // Ikon beda warna sesuai status
                $bg_color = 'bg-primary';
                $icon = 'fa-info';
                if ($log->status_tujuan == 'proses') {
                    $bg_color = 'bg-warning';
                    $icon = 'fa-cogs';
                } elseif ($log->status_tujuan == 'done') {
                    $bg_color = 'bg-success';
                    $icon = 'fa-check';
                } elseif ($log->status_tujuan == 'on_pb') {
                    $bg_color = 'bg-info';
                    $icon = 'fa-truck';
                }

                $html .= '
                <li>
                    <i class="fa ' . $icon . ' ' . $bg_color . '"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> ' . $time_formatted . '</span>
                        <h3 class="timeline-header"><a href="javascript:void(0)">' . $nama . '</a> merubah status</h3>
                        <div class="timeline-body">
                            ' . $catatan . '
                        </div>
                    </div>
                </li>';
            }
            $html .= '<li><i class="fa fa-clock-o bg-gray"></i></li>'; // Akhir timeline
            $html .= '</ul>';
        }

        echo json_encode(['success' => true, 'html' => $html]);
    }

    public function ajax_global_search()
    {
        $term = $this->input->get('q', true);
        if (empty($term)) {
            echo json_encode(['results' => []]);
            return;
        }

        $this->db->select('tbl_produksi.produksi_id, tbl_produksi.nospk, tbl_produksi.status_produksi, tbl_konsumen.konsumen_nama, tbl_product.product_nama, tbl_kategori.kategori_nama, tbl_perusahaan.nama_perusahaan');
        $this->db->from('tbl_produksi');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = tbl_produksi.nospk', 'left');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = pm.konsumen_id', 'left');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id', 'left');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_produksi.perusahaan_id', 'left');

        // Filter transactions from the last 7 days
        $this->db->where("pm.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)", NULL, FALSE);

        $this->db->group_start();
        $this->db->like('LOWER(tbl_produksi.nospk)', strtolower($term));
        $this->db->or_like('LOWER(tbl_konsumen.konsumen_nama)', strtolower($term));
        $this->db->group_end();

        // Prevent duplicate SPKs from showing multiple times (due to multiple products in one SPK)
        $this->db->group_by('tbl_produksi.nospk');

        $this->db->limit(30);
        $items = $this->db->get()->result();

        // Fetch direct consumer profile matches
        $this->db->select('konsumen_id, konsumen_nama');
        $this->db->from('tbl_konsumen');
        $this->db->like('LOWER(konsumen_nama)', strtolower($term));
        $this->db->limit(10);
        $consumers = $this->db->get()->result();

        $results = [];

        // Add a "Show All" option at the very top
        $results[] = [
            'id' => 'show_all_' . $term,
            'is_show_all' => true,
            'text' => 'Tampilkan semua hasil untuk "' . htmlspecialchars($term, ENT_QUOTES, 'UTF-8') . '"',
            'nospk' => $term // We pass the term here, so the frontend redirects using search_spk=term
        ];

        // Add Consumer Profile Suggestions
        foreach ($consumers as $cons) {
            $results[] = [
                'id' => 'cons_' . $cons->konsumen_id,
                'is_consumer' => true,
                'text' => $cons->konsumen_nama,
                'nospk' => $cons->konsumen_nama
            ];
        }

        foreach ($items as $item) {
            $status_text = 'ANTRIAN';
            $status_color = 'warning';
            if ($item->status_produksi == 'proses') {
                $status_text = 'PROSES';
                $status_color = 'primary';
            } elseif ($item->status_produksi == 'done' || $item->status_produksi == 'selesai') {
                $status_text = 'FINISHING/SELESAI';
                $status_color = 'success';
            } elseif ($item->status_produksi == 'on_pb' || $item->status_produksi == 'di_pb') {
                $status_text = 'DI PENGAMBILAN BARANG';
                $status_color = 'info';
            } elseif ($item->status_produksi == 'on_costumer') {
                $status_text = 'DISERAHKAN (KONSUMEN)';
                $status_color = 'secondary';
            }

            $nama_cabang = isset($item->nama_perusahaan) ? $item->nama_perusahaan : 'TGM ERP';
            $text = "[$item->nospk] $item->konsumen_nama - $item->product_nama | $item->kategori_nama ($nama_cabang) | $status_text";

            $results[] = [
                'id' => $item->produksi_id,
                'text' => $text,
                'nospk' => $item->nospk,
                'konsumen' => $item->konsumen_nama,
                'kategori' => $item->kategori_nama,
                'status' => $status_text,
                'status_color' => $status_color
            ];
        }

        echo json_encode(['results' => $results]);
    }
    public function tracking_spk()
    {
        $spk = $this->input->get('spk', true) ?: $this->input->post('spk', true);

        if ($spk) {
            $data['produksi'] = $this->Produksi_model->get_produksi_by_spk($spk);
            if ($data['produksi']) {
                $data['detail_produksi'] = $this->Produksi_model->get_detail_produksi_by_id($data['produksi']->produksi_id);
                $data['spk_found'] = true;
            } else {
                $data['spk_found'] = false;
                $data['error_message'] = 'SPK #' . $spk . ' tidak ditemukan.';
            }
        } else {
            $data['spk_found'] = false;
        }

        $data['search_spk'] = $spk;
        $this->load->view('public/spk_tracking', $data);
    }

    public function get_spk_status()
    {
        $spk = $this->input->post('spk', true);

        if (!$spk) {
            $response = ['success' => false, 'message' => 'SPK number is required'];
        } else {
            $produksi = $this->Produksi_model->get_produksi_by_spk($spk);

            if ($produksi) {
                $detail_produksi = $this->Produksi_model->get_detail_produksi_by_id($produksi->produksi_id);

                $total_routing = count($detail_produksi);
                $completed = 0;
                $in_progress = 0;
                $in_pb = 0;
                $delivered = 0;

                foreach ($detail_produksi as $d) {
                    $status = isset($d->status_produksi) ? (string) $d->status_produksi : '0';
                    if (in_array($status, ['2', '3', '4']))
                        $completed++;
                    if ($status === '1')
                        $in_progress++;
                    if ($status === '3')
                        $in_pb++;
                    if ($status === '4')
                        $delivered++;
                }

                $overall_progress = $total_routing > 0 ? round(($completed / $total_routing) * 100) : 0;

                $response = [
                    'success' => true,
                    'data' => [
                        'produksi' => $produksi,
                        'detail_produksi' => $detail_produksi,
                        'progress' => [
                            'total_routing' => $total_routing,
                            'completed' => $completed,
                            'in_progress' => $in_progress,
                            'in_pb' => $in_pb,
                            'delivered' => $delivered,
                            'overall_progress' => $overall_progress
                        ]
                    ]
                ];
            } else {
                $response = ['success' => false, 'message' => 'SPK #' . $spk . ' tidak ditemukan'];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function print_tag($produksi_id = null)
    {
        if (empty($produksi_id)) {
            show_404();
        }

        $row = $this->db->get_where('tbl_produksi', ['produksi_id' => $produksi_id])->row();

        if (!$row) {
            $row = $this->db->get_where('tbl_produksi', ['nospk' => $produksi_id])->row();
        }

        if (!$row) {
            show_404();
        }

        $nospk = $row->nospk;

        $this->db->select('tbl_produksi.*, tbl_product.product_nama, tbl_product.kategori_id, tbl_kategori.kategori_nama, tbl_konsumen.konsumen_nama, tbl_satuan.satuan_nama, (CASE WHEN tbl_product.kategori_id = 1 THEN tbl_harga_product.jenisprint_id ELSE tbl_harga_product.ket_1 END) as sisi, tbl_harga_product.ket_1 as db_ket_1, tbl_harga_product.jenisprint_id as db_jenisprint_id, tbl_perusahaan.nama_perusahaan as cabang_asal, tbl_perusahaan.tag_paper, tbl_perusahaan.tag_font_size, tbl_perusahaan.tag_font_family, tbl_perusahaan.tag_show_logo, tbl_perusahaan.tag_logo_width, tbl_perusahaan.tag_show_qr, tbl_perusahaan.tag_qr_width, tbl_perusahaan.tag_footer_note, tbl_perusahaan.tag_section_spacing, tbl_perusahaan.invoice_font_size, tbl_perusahaan.invoice_font_family, tbl_perusahaan.invoice_section_spacing, tbl_pemesanan.created_at as tgl_spk, tbl_pemesanan.status_pembayaran, dk.nama_lengkap as deskprint_nama, tbl_jenis_transaksi.nama_jenis_transaksi, tbl_pemesanan.resi_marketplace, tbl_product.form_schema as product_schema, tbl_kategori.form_schema as kategori_schema');
        $this->db->select('(SELECT mesin_nama FROM tbl_mesin m JOIN detail_produksi dp ON dp.mesin_id = m.mesin_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.mesin_id IS NOT NULL LIMIT 1) as mesin_nama');
        $this->db->select('(SELECT nama_lengkap FROM tbl_karyawan k JOIN detail_produksi dp ON dp.operator_id = k.karyawan_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.operator_id IS NOT NULL AND dp.status_produksi = \'2\' ORDER BY dp.detail_produksi_id DESC LIMIT 1) as nama_operator');
        $this->db->select('(SELECT (MAX(total_tagihan) - MAX(total_bayar)) FROM tbl_pembayaran WHERE nospk = tbl_produksi.nospk) as sisa_tagihan');
        $this->db->from('tbl_produksi');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id', 'left');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_produksi.konsumen_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id', 'left');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_produksi.perusahaan_id', 'left');
        $this->db->join('tbl_pemesanan', 'tbl_pemesanan.nospk = tbl_produksi.nospk', 'left');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi', 'left');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left');
        // Get Deskprint from SPK Number (2nd segment is user_id)
        $this->db->join('users u_dk', "SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_produksi.nospk, '-', 2), '-', -1) = u_dk.user_id", 'left', FALSE);
        $this->db->join('tbl_karyawan dk', 'dk.karyawan_id = u_dk.karyawan_id', 'left');
        $this->db->where('tbl_produksi.nospk', $nospk);

        $items = $this->db->get()->result();

        if (empty($items)) {
            show_404();
        }

        $data = [
            'items' => $items,
            'produksi' => $items[0],
            'print_time' => date('Y-m-d H:i:s'),
            'company_name' => 'TGM ERP'
        ];

        $this->load->view('admin/wip/print_tag', $data);
    }

    public function print_tag_ids()
    {
        $ids_str = $this->input->get('ids', true);
        if (empty($ids_str))
            show_404();

        $ids = explode(',', $ids_str);

        // Cari nospk unik dari ids yang diberikan
        $this->db->select('DISTINCT(nospk) as nospk');
        $this->db->from('tbl_produksi');
        $this->db->where_in('produksi_id', $ids);
        $spk_rows = $this->db->get()->result();

        $nospks = [];
        foreach ($spk_rows as $row) {
            $nospks[] = $row->nospk;
        }

        if (empty($nospks)) {
            show_404();
        }

        $this->db->select('tbl_produksi.*, tbl_product.product_nama, tbl_product.kategori_id, tbl_kategori.kategori_nama, tbl_konsumen.konsumen_nama, tbl_satuan.satuan_nama, (CASE WHEN tbl_product.kategori_id = 1 THEN tbl_harga_product.jenisprint_id ELSE tbl_harga_product.ket_1 END) as sisi, tbl_harga_product.ket_1 as db_ket_1, tbl_harga_product.jenisprint_id as db_jenisprint_id, tbl_perusahaan.nama_perusahaan as cabang_asal, tbl_perusahaan.tag_paper, tbl_perusahaan.tag_font_size, tbl_perusahaan.tag_font_family, tbl_perusahaan.tag_show_logo, tbl_perusahaan.tag_logo_width, tbl_perusahaan.tag_show_qr, tbl_perusahaan.tag_qr_width, tbl_perusahaan.tag_footer_note, tbl_perusahaan.tag_section_spacing, tbl_perusahaan.invoice_font_size, tbl_perusahaan.invoice_font_family, tbl_perusahaan.invoice_section_spacing, tbl_pemesanan.created_at as tgl_spk, tbl_pemesanan.status_pembayaran, dk.nama_lengkap as deskprint_nama, tbl_jenis_transaksi.nama_jenis_transaksi, tbl_pemesanan.resi_marketplace, tbl_product.form_schema as product_schema, tbl_kategori.form_schema as kategori_schema');
        $this->db->select('(SELECT mesin_nama FROM tbl_mesin m JOIN detail_produksi dp ON dp.mesin_id = m.mesin_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.mesin_id IS NOT NULL LIMIT 1) as mesin_nama');
        $this->db->select('(SELECT nama_lengkap FROM tbl_karyawan k JOIN detail_produksi dp ON dp.operator_id = k.karyawan_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.operator_id IS NOT NULL AND dp.status_produksi = \'2\' ORDER BY dp.detail_produksi_id DESC LIMIT 1) as nama_operator');
        $this->db->select('(SELECT (MAX(total_tagihan) - MAX(total_bayar)) FROM tbl_pembayaran WHERE nospk = tbl_produksi.nospk) as sisa_tagihan');
        $this->db->from('tbl_produksi');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id', 'left');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_produksi.konsumen_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id', 'left');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_produksi.perusahaan_id', 'left');
        $this->db->join('tbl_pemesanan', 'tbl_pemesanan.nospk = tbl_produksi.nospk', 'left');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi', 'left');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left');
        // Get Deskprint from SPK Number (2nd segment is user_id)
        $this->db->join('users u_dk', "SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_produksi.nospk, '-', 2), '-', -1) = u_dk.user_id", 'left', FALSE);
        $this->db->join('tbl_karyawan dk', 'dk.karyawan_id = u_dk.karyawan_id', 'left');
        $this->db->where_in('tbl_produksi.nospk', $nospks);

        $items = $this->db->get()->result();

        if (empty($items)) {
            show_404();
        }

        $data = [
            'items' => $items,
            'produksi' => $items[0],
            'print_time' => date('Y-m-d H:i:s'),
            'company_name' => 'TGM ERP'
        ];

        $this->load->view('admin/wip/print_tag', $data);
    }

    public function get_production_data()
    {
        $detail_produksi_id = $this->input->post('detail_produksi_id', true);

        if (!$detail_produksi_id) {
            $response = ['success' => false, 'message' => 'Detail produksi ID is required'];
        } else {
            $operator_id = $this->get_operator_id_for_current_user();

            $this->db->select('dp.*, m.varian as bahan_nama')
                ->from('detail_produksi dp')
                ->join('tbl_material m', 'm.material_id = dp.material_id', 'left')
                ->where('dp.detail_produksi_id', $detail_produksi_id);

            if ($operator_id !== null) {
                $this->db->where('dp.operator_id', $operator_id);
            }

            $data = $this->db->get()->row();

            if ($data) {
                $response = ['success' => true, 'data' => $data];
            } else {
                $response = ['success' => false, 'message' => 'Production data not found or access denied'];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function get_detail_for_print()
    {
        $detail_produksi_id = $this->input->post('detail_produksi_id', true);

        if (!$detail_produksi_id) {
            $response = ['success' => false, 'message' => 'Detail ID is required'];
        } else {
            $detail = $this->Produksi_model->get_detail_produksi_by_id_single($detail_produksi_id);

            if ($detail) {
                $response = ['success' => true, 'data' => $detail];
            } else {
                $response = ['success' => false, 'message' => 'Detail not found'];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function get_routing_data()
    {
        $detail_produksi_id = $this->input->post('detail_produksi_id', true);

        if (!$detail_produksi_id) {
            $response = ['success' => false, 'message' => 'Detail ID is required'];
        } else {
            $detail_data = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $detail_produksi_id])->row();

            if ($detail_data) {
                $routing_data = $this->db->get_where('routing', ['routing_id' => $detail_data->routing_id])->row();

                if ($routing_data) {
                    $response = [
                        'success' => true,
                        'data' => [
                            'mesin_id' => $routing_data->mesin_id,
                            'routing_name' => $routing_data->routing_name
                        ]
                    ];
                } else {
                    $response = ['success' => false, 'message' => 'Routing data not found'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Detail produksi not found'];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function get_bom_data()
    {
        $detail_produksi_id = $this->input->post('detail_produksi_id', true);

        if (!$detail_produksi_id) {
            $response = ['success' => false, 'message' => 'Detail ID is required'];
        } else {
            $detail_data = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $detail_produksi_id])->row();

            if ($detail_data) {
                $produksi_data = $this->db->get_where('tbl_produksi', ['produksi_id' => $detail_data->produksi_id])->row();

                if ($produksi_data) {
                    $bom_data = $this->db->select('bl.*, m.varian as material_name')
                        ->from('bom_lines bl')
                        ->join('tbl_material m', 'm.material_id = bl.material_id', 'left')
                        ->where('bl.product_id', $produksi_data->product_id)
                        ->where('bl.routing_id', $detail_data->routing_id)
                        ->get()->row();

                    if ($bom_data) {
                        $response = [
                            'success' => true,
                            'data' => [
                                'material_id' => $bom_data->material_id,
                                'material_name' => $bom_data->material_name,
                                'qty_required' => $bom_data->qty_required
                            ]
                        ];
                    } else {
                        $response = ['success' => false, 'message' => 'BOM data not found'];
                    }
                } else {
                    $response = ['success' => false, 'message' => 'Production data not found'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Detail produksi not found'];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    private function update_warehouse_stock($produksi_id, $detail_id, $material_id, $stock_reduction, $lokasi_proses = null, $override_karyawan_id = null)
    {
        try {
            // 1. Get material and conversion info
            // Join with tbl_bahan and tbl_satuan to handle units correctly
            $this->db->select('m.*, sg.satuan_nama as satuan_gudang_nama, sp.satuan_nama as satuan_produksi_nama');
            $this->db->from('tbl_material m');
            $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left');
            $this->db->join('tbl_satuan sg', 'sg.satuan_id = m.satuan_gudang_id', 'left');
            $this->db->join('tbl_satuan sp', 'sp.satuan_id = b.satuan_id', 'left');
            $this->db->where('m.material_id', $material_id);
            $material_info = $this->db->get()->row();

            if (!$material_info) {
                return false;
            }

            // Conversion factor (e.g., 500 if 1 RIM = 500 Sheets)
            $konversi = (float) $material_info->konversi;
            if ($konversi == 0)
                $konversi = 1; // Preserve the legacy default without changing material data.
            if ($konversi < 0)
                throw new DomainException('Konversi satuan material tidak valid.');

            // Calculate reduction in Warehouse Unit (Satuan Gudang)
            // If production uses 50 sheets and conversion is 500, deduction is 50/500 = 0.1 RIM
            $warehouse_reduction = (float) $stock_reduction / $konversi;

            $produksi = $this->db->get_where('tbl_produksi', ['produksi_id' => $produksi_id])->row();
            if (!$produksi)
                return false;

            // Gudang taken from: 1. $lokasi_proses, 2. $produksi->lokasi_cetak, 3. $produksi->perusahaan_id, 4. fallback 1
            $gudang_id = $lokasi_proses ?: (($produksi->lokasi_cetak ?? 0) ?: ($produksi->perusahaan_id ?? 0));
            if (!$gudang_id)
                throw new DomainException('Lokasi gudang produksi belum ditentukan.');

            // 2. USE STOCK ENGINE (BEST PRACTICE)
            $nospk = $produksi->nospk;
            $sat_prod = $material_info->satuan_produksi_nama ?: 'unit';
            $sat_gudang = $material_info->satuan_gudang_nama ?: 'unit';

            // Note includes conversion for transparency
            $catatan = "Produksi SPK: $nospk. Pakai: $stock_reduction $sat_prod (Step: $detail_id)";
            if ($konversi != 1) {
                $rounded_reduction = round($warehouse_reduction, 4);
                $catatan .= " [Konversi: $rounded_reduction $sat_gudang]";
            }

            // Get operator_id from detail_produksi if available
            $karyawan_id = $override_karyawan_id;
            if (!$karyawan_id && $detail_id > 0) {
                $detail_prod = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $detail_id])->row();
                if ($detail_prod && $detail_prod->operator_id) {
                    $karyawan_id = $detail_prod->operator_id;
                }
            }

            return $this->M_gudang->update_stock_with_log(
                $material_id,
                $gudang_id,
                -$warehouse_reduction, // Negative value for deduction
                'Produksi',
                $produksi_id,
                $catatan,
                NULL, // rak_id
                $karyawan_id
            );

        } catch (DomainException $e) {
            throw $e;
        } catch (Exception $e) {
            log_message('error', 'Error in update_warehouse_stock: ' . $e->getMessage());
            return false;
        }
    }


    public function get_spk_details_ajax()
    {
        // [FIX] Call this FIRST to prevent Query Builder state reset during query construction
        $current_operator_id = $this->get_operator_id_for_current_user();

        $nospk = $this->input->post('nospk', true);
        $cat_id = $this->input->post('kategori_id', true);

        $this->db->select('tbl_produksi.*, tbl_product.product_nama, tbl_product.kategori_id, tbl_satuan.satuan_nama, (CASE WHEN tbl_product.kategori_id = 1 THEN tbl_harga_product.jenisprint_id ELSE tbl_harga_product.ket_1 END) as sisi, tbl_harga_product.jenisprint_id, pm.status_pembayaran');
        $this->db->from('tbl_produksi');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id', 'left');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id', 'left');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = tbl_produksi.nospk', 'left');
        $this->db->where('tbl_produksi.nospk', $nospk);

        if ($cat_id) {
            $is_granular_cat = in_array((int) $cat_id, [2, 5, 8, 9]);
            if ($is_granular_cat) {
                $this->db->where('tbl_product.kategori_id', $cat_id);
            } else {
                // Untuk A3 dan Finishing, tampilkan semua item non-granular dalam satu modal
                $this->db->where_not_in('tbl_product.kategori_id', [2, 5, 8, 9]);
            }
        }

        $this->db->where_not_in('tbl_produksi.status_produksi', ['on_pb', 'on_costumer']);

        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');

        // $current_operator_id is now calculated at the top
        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->group_start();
            $this->db->where('tbl_produksi.lokasi_cetak', $perusahaan);
            $this->db->or_group_start();
            $this->db->where("(tbl_produksi.lokasi_cetak IS NULL OR tbl_produksi.lokasi_cetak = '' OR tbl_produksi.lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('tbl_produksi.perusahaan_id', $perusahaan);
            $this->db->group_end();
            $this->db->group_end();
        }

        $produksi_items = $this->db->get()->result();

        if (!empty($produksi_items) && $produksi_items[0]->status_pembayaran !== 'paid') {
            echo json_encode(['success' => false, 'message' => 'SPK belum divalidasi pembayarannya.']);
            return;
        }

        foreach ($produksi_items as $key => $item) {
            $this->db->select('ts.satuan_nama, bh.bahan_nama, bl.quantity as bom_qty, bl.bahan_id, bl.harga_id');
            $this->db->from('bom_lines bl');
            $this->db->join('tbl_bahan bh', 'bh.bahan_id = bl.bahan_id', 'left');
            $this->db->join('tbl_satuan ts', 'ts.satuan_id = bh.satuan_id', 'left');
            $this->db->where('bl.product_id', $item->product_id);
            $this->db->order_by('bl.bom_line_id', 'ASC');
            $all_boms = $this->db->get()->result();

            $specific_boms = [];
            $global_boms = [];
            foreach ($all_boms as $b) {
                if ($b->harga_id == $item->harga_id && $b->harga_id !== null) {
                    $specific_boms[] = $b;
                } else if ($b->harga_id === null) {
                    $global_boms[] = $b;
                }
            }
            $bom_res = count($specific_boms) > 0 ? $specific_boms : $global_boms;

            $item->bom = !empty($bom_res) ? $bom_res[0] : null;
            $item->bom_lines = $bom_res;

            $primary_bom = $this->db->query("SELECT bom_line_id FROM bom_lines WHERE product_id = ? AND (harga_id IS NULL OR harga_id = ?) AND bahan_id IS NOT NULL ORDER BY harga_id DESC, bom_line_id ASC LIMIT 1", [(int) $item->product_id, (int) $item->harga_id])->row();
            $fallback_bom_line_id = $primary_bom ? $primary_bom->bom_line_id : 0;

            $this->db->select('dp.*, r.routing_name, m.mesin_nama, o.operator_nama, tm.bahan_id as routing_material_id, ts.satuan_nama as routing_material_satuan');
            $this->db->from('detail_produksi dp');
            $this->db->join('routing r', 'dp.routing_id = r.routing_id', 'left');
            $this->db->join('bom_lines bl1', 'bl1.bom_line_id = r.bom_line_id', 'left');
            $this->db->join('bom_lines bl2', 'bl2.bom_line_id = ' . $fallback_bom_line_id, 'left');
            $this->db->join('tbl_material tm', 'tm.material_id = COALESCE(bl1.bahan_id, bl2.bahan_id)', 'left');
            $this->db->join('tbl_bahan bh', 'bh.bahan_id = tm.bahan_id', 'left');
            $this->db->join('tbl_satuan ts', 'ts.satuan_id = bh.satuan_id', 'left');
            $this->db->join('tbl_mesin m', 'm.mesin_id = dp.mesin_id', 'left');
            $this->db->join('tbl_operator o', 'o.operator_id = dp.operator_id', 'left');
            $this->db->where('dp.produksi_id', $item->produksi_id);
            $this->db->order_by('dp.routing_urutan', 'ASC');
            $this->db->order_by('dp.detail_produksi_id', 'ASC');

            $routing = $this->db->get()->result();

            // [Unique Fix] Detect Finishing/Laminasi - Consistent with Control Center
            $this->db->select('pp.finishing_id');
            $this->db->from('tbl_pembayaran_produk pp');
            $this->db->where('pp.nospk', $item->nospk);
            $this->db->where('pp.product_id', $item->product_id);
            $this->db->limit(1);
            $pp = $this->db->get()->row();

            if (!$pp || empty($pp->finishing_id)) {
                $this->db->select('pd.finishing_id');
                $this->db->from('tbl_pemesanan_detail pd');
                $this->db->where('pd.nospk', $item->nospk);
                $this->db->where('pd.product_id', $item->product_id);
                $this->db->limit(1);
                $pp = $this->db->get()->row();
            }

            if ($pp && !empty($pp->finishing_id)) {
                $finishing_name = '';
                $f_prod = $this->db->get_where('tbl_product', ['product_id' => $pp->finishing_id])->row();
                if ($f_prod) {
                    $finishing_name = $f_prod->product_nama;
                } else {
                    if ($this->db->table_exists('tbl_finishing')) {
                        $f_tbl = $this->db->get_where('tbl_finishing', ['id' => $pp->finishing_id])->row();
                        if ($f_tbl)
                            $finishing_name = $f_tbl->nama_finishing ?? $f_tbl->finishing_nama ?? 'FINISHING';
                    }
                }

                if (!empty($finishing_name) && is_array($routing) && count($routing) > 1) {
                    $last_idx = count($routing) - 1;
                    $routing[$last_idx]->routing_name = $finishing_name . " (FINISHING)";
                }
            }

            foreach ($routing as $route_step) {
                $route_step->workflow_version = Production_workflow::version($route_step);
            }
            $produksi_items[$key]->routing = $routing;
            $item->total_steps = count($routing);
            $item->done_steps = 0;
            foreach ($routing as $route_step) {
                if (Production_workflow::step_completed($route_step->status_produksi)) {
                    $item->done_steps++;
                    continue;
                }
                if (!isset($item->workflow_step_id)) {
                    $item->workflow_step_id = $route_step->detail_produksi_id;
                    $item->workflow_version = $route_step->workflow_version;
                    $item->workflow_qty_output = (float) ($route_step->qty_output ?? 0);
                    $item->current_step_urutan = $route_step->routing_urutan;
                    $item->current_step_name = $route_step->routing_name;
                }
            }

        }

        echo json_encode([
            'items' => $produksi_items,
            'mesin' => $this->db->get_where('tbl_mesin', ['status' => '1'])->result(),
            'operator' => $this->db->get('tbl_operator')->result()
        ]);
    }

    public function get_production_control_json()
    {
        $produksi_id = $this->input->post('produksi_id', true);
        if (!$produksi_id) {
            echo json_encode(['success' => false, 'message' => 'Produksi ID is required']);
            return;
        }

        $this->load->model('Produksi_model');
        $operator_id = $this->get_operator_id_for_current_user();

        $produksi = $this->Produksi_model->get_produksi_by_id($produksi_id);
        if (!$produksi) {
            echo json_encode(['success' => false, 'message' => 'Produksi not found']);
            return;
        }

        // Security Check: Match branch/lokasi_cetak logic
        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = (int) $this->session->userdata('level');

        $can_access = false;

        // Allow Super Admin / Manager
        if (in_array((int) $level, [1, 17], true)) {
            $can_access = true;
        } else {
            // Standard Branch Check
            if ($produksi->lokasi_cetak == $perusahaan) {
                $can_access = true;
            } elseif (empty($produksi->lokasi_cetak) || $produksi->lokasi_cetak == '0') {
                if ($produksi->perusahaan_id == $perusahaan) {
                    $can_access = true;
                }
            }
        }

        if (!$can_access) {
            echo json_encode(['success' => false, 'message' => 'Akses ditolak. Item ini bukan milik cabang Anda.']);
            return;
        }

        $routing = $this->wip_workflow->steps($produksi);

        foreach ($routing as $route_step) {
            $route_step->workflow_version = Production_workflow::version($route_step);
        }
        $machines = $this->db->get('tbl_mesin')->result();
        $locations = $this->db->get('tbl_perusahaan')->result();
        $materials = $this->db->get('tbl_material')->result();

        echo json_encode([
            'success' => true,
            'data' => [
                'produksi' => $produksi,
                'routing' => $routing,
                'machines' => $machines,
                'locations' => $locations,
                'materials' => $materials
            ]
        ]);
    }

    public function update_manual_dimension_ajax()
    {
        $this->_workflow_response(function () {
            $id = $this->input->post('produksi_id', true);
            return $this->_workflow_execute([$id], 'report', function ($item, $step, $actor) {
                if (!Production_workflow::pending($item->status_produksi))
                    throw new DomainException('Ukuran pekerjaan yang telah dimulai tidak dapat ditimpa.');
                foreach ($this->wip_workflow->steps($item) as $history) {
                    if (!empty($history->waktu_mulai))
                        throw new DomainException('Item memiliki riwayat pengerjaan; ukuran tidak dapat ditimpa.');
                }
                $meta = json_decode($item->metadata ?? '', true);
                if (!is_array($meta))
                    $meta = [];
                $meta['panjang'] = Production_workflow::number($this->input->post('panjang', true), 'Panjang');
                $meta['lebar'] = Production_workflow::number($this->input->post('lebar', true), 'Lebar');
                if (!$this->db->where('produksi_id', $item->produksi_id)->update('tbl_produksi', ['metadata' => json_encode($meta)]))
                    throw new RuntimeException('Gagal menyimpan ukuran.');
                return [];
            });
        });
    }

    public function proses_item($produksi_id = null)
    {
        $this->_workflow_legacy_rejected();
    }

    public function selesai_spk($nospk = null)
    {
        $this->_workflow_legacy_rejected();
    }

    public function selesai_spk_ajax()
    {
        $this->_workflow_response(function () {
            if (!$this->input->post('pin_operator', true))
                throw new DomainException('Masukkan PIN operator.');
            $ids = $this->_workflow_spk_ids($this->input->post('nospk', true), $this->input->post('cat', true), 'proses');
            $results = $this->_workflow_map('outcomes');
            return $this->_workflow_execute($ids, 'complete', function ($item, $step, $actor) use ($results) {
                // SPK quick completion is for operations without a material posting requirement.
                if (!empty($step->bom_line_id))
                    throw new DomainException('Tahap ini memerlukan pencatatan bahan. Gunakan Catat Hasil per item.');
                return $this->_workflow_fields($item, $step, $results[$item->produksi_id] ?? []);
            });
        });
    }

    public function process_all_ready_to_pb_ajax()
    {
        $this->_workflow_response(function () {
            $cat = (int) $this->input->post('cat_id', true);
            if (!$cat || !$this->input->post('pin_operator', true))
                throw new DomainException('Kategori dan PIN wajib diisi.');
            $branch = (int) $this->session->userdata('perusahaan_id');
            $admin = in_array((int) $this->session->userdata('level'), [1, 17], true);
            $this->db->select('p.produksi_id')->from('tbl_produksi p')->join('tbl_product pr', 'pr.product_id = p.product_id')->where('p.status_produksi', 'done');
            if ($this->wip_quality->installed())
                $this->db->where("NOT EXISTS (SELECT 1 FROM tbl_wip_qc q WHERE q.produksi_id = p.produksi_id AND q.status <> 'passed')", null, false);
            if (in_array($cat, [2, 5, 8, 9], true))
                $this->db->where('pr.kategori_id', $cat);
            else
                $this->db->where_not_in('pr.kategori_id', [2, 5, 8, 9]);
            if (!$admin)
                $this->db->where('COALESCE(NULLIF(p.lokasi_cetak, 0), p.perusahaan_id) =', $branch, false);
            $ids = array_column($this->db->order_by('p.produksi_id')->limit(500)->get()->result_array(), 'produksi_id');
            return $this->_workflow_execute($ids, 'handover');
        });
    }

    public function control_center($param = null)
    {
        if (!$param)
            redirect('wip/produksi');

        // 1. Determine Identity (ID or NoSPK)
        // Try to find by ID first to get the NoSPK, or assumme param is NoSPK? 
        // Safer: Treat as ID first.
        $trigger_item = $this->db->get_where('tbl_produksi', ['produksi_id' => $param])->row();

        $nospk = $trigger_item ? $trigger_item->nospk : $param;

        // 2. Fetch ALL Items in this SPK
        $this->db->select('p.*, pr.product_nama, pr.kategori_id, pr.tipe_id, pr.satuan_id, k.kategori_nama, s.satuan_nama, ko.konsumen_nama, (CASE WHEN pr.kategori_id = 1 THEN hp.jenisprint_id ELSE hp.ket_1 END) as sisi, hp.jenisprint_id');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_product pr', 'pr.product_id = p.product_id', 'left');
        $this->db->join('tbl_kategori k', 'k.kategori_id = pr.kategori_id', 'left'); // Added Kategori Name
        $this->db->join('tbl_satuan s', 's.satuan_id = pr.satuan_id', 'left');
        $this->db->join('tbl_konsumen ko', 'ko.konsumen_id = p.konsumen_id', 'left');
        $this->db->join('tbl_harga_product hp', 'hp.harga_id = p.harga_id', 'left');
        $this->db->where('p.nospk', $nospk);

        // Access Control (Perusahaan / Branch)
        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->group_start();
            $this->db->where('p.lokasi_cetak', $perusahaan);
            $this->db->or_group_start();
            $this->db->where("(p.lokasi_cetak IS NULL OR p.lokasi_cetak = '' OR p.lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('p.perusahaan_id', $perusahaan);
            $this->db->group_end();
            $this->db->group_end();
        }
        $this->db->group_by('p.produksi_id');

        $items = $this->db->get()->result();

        if (empty($items)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Data SPK tidak ditemukan atau akses ditolak.</div>');
            redirect('wip/produksi');
        }

        // 3. Process Each Item (Metadata & Routing)
        foreach ($items as &$item) {
            // A. Metadata / Dynamic Labels
            $this->db->select('form_schema');
            $cat = $this->db->get_where('tbl_kategori', ['kategori_id' => $item->kategori_id])->row();
            $schema = ($cat && !empty($cat->form_schema)) ? json_decode($cat->form_schema, true) : [];

            $item->label_ket_1 = "SISI";
            $item->val_ket_1 = $item->sisi;
            $item->label_jenisprint = "JENIS PRINT";
            $item->val_jenisprint = "";

            if (!empty($schema)) {
                foreach ($schema as $field) {
                    if ($field['name'] == 'ket_1') {
                        $item->label_ket_1 = strtoupper($field['label']);
                        if (isset($field['options'][$item->sisi])) {
                            $item->val_ket_1 = $field['options'][$item->sisi];
                        }
                    }
                    if ($field['name'] == 'jenisprint_id') {
                        $item->label_jenisprint = strtoupper($field['label']);
                        if (isset($field['options'][$item->jenisprint_id])) {
                            $item->val_jenisprint = $field['options'][$item->jenisprint_id];
                        }
                    }
                }
            }

            // Banner Override
            $prod = $this->db->get_where('tbl_product', ['product_id' => $item->product_id])->row();
            if ($prod && $prod->subkategori_id == 13) {
                $item->label_ket_1 = "PILIHAN MATA";
                $mata = $this->db->get_where('tbl_mata_cutting', ['id' => $item->sisi])->row();
                if ($mata) {
                    $item->val_ket_1 = $mata->mata_nama;
                }
            }

            // A2. Get primary BOM line for fallback if routing lacks it
            $primary_bom = $this->db->query("SELECT bom_line_id FROM bom_lines WHERE product_id = ? AND (harga_id IS NULL OR harga_id = ?) AND bahan_id IS NOT NULL ORDER BY harga_id DESC, bom_line_id ASC LIMIT 1", [(int) $item->product_id, (int) $item->harga_id])->row();
            $fallback_bom_line_id = $primary_bom ? $primary_bom->bom_line_id : 0;

            // B. Fetch Routing Steps for THIS specific item
            $this->db->select('dp.*, r.routing_name, COALESCE(r.routing_urutan, dp.routing_urutan) as routing_urutan, m.mesin_nama, k.nama_lengkap as operator_nama, 
                COALESCE(bl1.quantity, bl2.quantity) as step_bom_qty, 
                tm.bahan_id as bom_bahan_id, 
                bh.bahan_nama as bom_bahan_nama, 
                ts.satuan_nama as bom_bahan_satuan,
                COALESCE(bl1.panjang, bl2.panjang) as bom_p, 
                COALESCE(bl1.lebar, bl2.lebar) as bom_l');
            $this->db->from('detail_produksi dp');
            $this->db->join('routing r', 'dp.routing_id = r.routing_id', 'left');
            $this->db->join('tbl_mesin m', 'm.mesin_id = dp.mesin_id', 'left');
            $this->db->join('tbl_karyawan k', 'k.karyawan_id = dp.operator_id', 'left');

            // Join with BOM and Material to get Material Name and Base Bahan ID
            $this->db->join('bom_lines bl1', 'bl1.bom_line_id = r.bom_line_id', 'left');
            $this->db->join('bom_lines bl2', 'bl2.bom_line_id = ' . $fallback_bom_line_id, 'left');
            $this->db->join('tbl_material tm', 'tm.material_id = COALESCE(bl1.bahan_id, bl2.bahan_id)', 'left');
            $this->db->join('tbl_bahan bh', 'bh.bahan_id = tm.bahan_id', 'left');
            $this->db->join('tbl_satuan ts', 'ts.satuan_id = bh.satuan_id', 'left');

            $this->db->where('dp.produksi_id', $item->produksi_id);
            $this->db->group_by('dp.detail_produksi_id'); // Just in case
            $this->db->order_by('r.routing_urutan', 'ASC');
            $this->db->order_by('dp.detail_produksi_id', 'ASC'); // Stable sort

            $steps = $this->db->get()->result();
            // Ensure routing_name fallback
            foreach ($steps as &$s) {
                if (empty($s->routing_name)) {
                    $fallback_r = $this->db->get_where('routing', ['product_id' => $item->product_id, 'routing_urutan' => $s->routing_urutan])->row();
                    if ($fallback_r) {
                        $s->routing_name = $fallback_r->routing_name;
                        // Recover BOM details if they were missing due to missing routing_id
                        if (empty($s->bom_bahan_id) && !empty($fallback_r->bom_line_id)) {
                            $this->db->select('tm.bahan_id, bh.bahan_nama, ts.satuan_nama, bl.quantity, bl.panjang, bl.lebar');
                            $this->db->from('bom_lines bl');
                            $this->db->join('tbl_material tm', 'tm.material_id = bl.bahan_id', 'left');
                            $this->db->join('tbl_bahan bh', 'bh.bahan_id = tm.bahan_id', 'left');
                            $this->db->join('tbl_satuan ts', 'ts.satuan_id = bh.satuan_id', 'left');
                            $this->db->where('bl.bom_line_id', $fallback_r->bom_line_id);
                            $fallback_bom = $this->db->get()->row();

                            if ($fallback_bom) {
                                $s->bom_bahan_id = $fallback_bom->bahan_id;
                                $s->bom_bahan_nama = $fallback_bom->bahan_nama;
                                $s->bom_bahan_satuan = $fallback_bom->satuan_nama;
                                $s->step_bom_qty = $fallback_bom->quantity;
                                $s->bom_p = $fallback_bom->panjang;
                                $s->bom_l = $fallback_bom->lebar;
                            }
                        }
                    } else {
                        $s->routing_name = "Proses " . ($s->routing_urutan ?: "");
                    }
                }
            }
            $item->routing_steps = is_array($steps) ? $steps : [];

            // C. Calculate Progress State
            $item->current_step_urutan = 0;
            // Use existing status if valid, OR calculate based on steps
            // We'll respect DB status but augment with step info

            $total_s = count($item->routing_steps);
            $done_s = 0;
            $foundCurrent = false;

            foreach ($item->routing_steps as $step) {
                if ($step->status_produksi == '2') {
                    $done_s++;
                } elseif ($step->status_produksi == '1') {
                    $item->status_produksi = 'proses'; // Force update memory state
                    $item->current_step_urutan = $step->routing_urutan;
                    $foundCurrent = true;
                } elseif (($step->status_produksi === '0' || $step->status_produksi === '') && !$foundCurrent && $done_s == ($step->routing_urutan - 1)) {
                    // Next pending
                    $item->current_step_urutan = $step->routing_urutan - 1;
                }
            }

            // Logic Check: If all steps done, ensure item is marked done in view logic
            if ($done_s == $total_s && $total_s > 0) {
                $item->is_fully_done = true;
            } else {
                $item->is_fully_done = false;
            }
        }

        foreach ($items as $item) {
            foreach (($item->routing_steps ?? []) as $snapshot_step) {
                $snapshot_step->workflow_version = Production_workflow::version($snapshot_step);
                if (!isset($item->workflow_step_id) && !Production_workflow::step_completed($snapshot_step->status_produksi)) {
                    $item->workflow_step_id = $snapshot_step->detail_produksi_id;
                    $item->workflow_step_status = $snapshot_step->status_produksi;
                    $item->workflow_version = $snapshot_step->workflow_version;
                    $item->workflow_qty_output = (float) ($snapshot_step->qty_output ?? 0);
                }
                if (Production_workflow::step_completed($snapshot_step->status_produksi))
                    $snapshot_step->status_produksi = '2';
            }
        }
        $data['items'] = $items;
        $data['spk_header'] = $items[0]; // Use first item for common headers
        $data['qc_passed_count'] = 0; // Placeholder

        // Fetch Material List (Common for all) using raw query to prevent active-record bugs with CONCAT/IF
        $sqlBahan = "
            SELECT 
                m.material_id AS bahan_id, 
                m.bahan_id AS raw_bahan_id, 
                IF(m.varian = '' OR m.varian IS NULL OR b.bahan_nama = m.varian, 
                   COALESCE(b.bahan_nama, m.varian, 'Bahan Tanpa Nama'), 
                   CONCAT_WS(' - ', NULLIF(b.bahan_nama, ''), NULLIF(m.varian, ''))
                ) AS bahan_nama, 
                sg.qty AS stok, 
                s.satuan_nama AS satuan 
            FROM tbl_material m
            LEFT JOIN tbl_bahan b ON m.bahan_id = b.bahan_id
            LEFT JOIN tbl_stok_gudang sg ON m.material_id = sg.material_id AND sg.gudang_id = 1
            LEFT JOIN tbl_satuan s ON m.satuan_gudang_id = s.satuan_id
            WHERE m.status = '1'
            ORDER BY b.bahan_nama ASC, m.varian ASC
        ";
        $data['bahan_list'] = $this->db->query($sqlBahan)->result();

        // Determine target branch for operator list
        $target_branch = $this->session->userdata('perusahaan_id');
        if ($target_branch == 1 && !empty($items) && isset($items[0]->perusahaan_id)) {
            $target_branch = $items[0]->perusahaan_id;
        }

        $data['operators'] = $this->db->select('k.karyawan_id, k.nama_lengkap')
            ->from('tbl_karyawan k')
            ->join('users u', 'k.karyawan_id = u.karyawan_id', 'inner')
            ->where('k.perusahaan_id', $target_branch)
            ->where('u.role_id', 4) // Operator Role
            ->order_by('k.nama_lengkap', 'ASC')
            ->get()->result();

        $this->load_admin_view('admin/wip/control_center', $data);
    }



    public function submit_step_result()
    {
        $this->_workflow_detail_action('complete');
    }

    public function advance_step_ajax()
    {
        $this->_workflow_advance();
    }

    public function get_machines_ajax()
    {
        $stepName = strtolower($this->input->get('stepName') ?? '');
        $catId = $this->input->get('catId');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        $is_finishing = (strpos($stepName, 'finish') !== false || strpos($stepName, 'potong') !== false || strpos($stepName, 'packing') !== false);
        $kategori_mesin = $is_finishing ? 'Finishing' : 'Produksi';
        $is_mandatory = !$is_finishing; // Mandatory if it's production/printing

        // Exception: Merchandise (catId = 10) does not require a machine
        if ($catId !== null && (int) $catId === 10) {
            $is_mandatory = false;
        }

        // Use selected items, including their print location, for both admins and operators.
        // PIN identifies the worker; it does not relocate a job or a machine.
        try {
            $locations = [(int) $perusahaan_id];
            $ids = $this->input->get('ids');
            if ($ids !== null) {
                $ids = Production_workflow::ids($ids);
                $actor = $this->wip_workflow->actor();
                $locations = [];
                foreach ($ids as $id) {
                    $item = $this->db->get_where('tbl_produksi', ['produksi_id' => $id])->row();
                    $this->wip_workflow->assert_access($item, $actor);
                    $locations[] = Production_workflow::production_location($item);
                }
            }
        } catch (DomainException $e) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'machines' => [],
                'is_mandatory' => $is_mandatory,
                'message' => $e->getMessage(),
                'user_mesin_ids' => []
            ]));
        }
        $machines = $this->db->select('mesin_id, mesin_nama, kategori, mesin_keterangan, perusahaan_id')
            ->from('tbl_mesin')->where('status', '1')->where('kategori', $kategori_mesin)
            ->get()->result_array();
        $machines = array_values(array_filter($machines, function ($machine) use ($locations) {
            return Production_workflow::machine_matches_locations((object) $machine, $locations);
        }));

        // Optional fine-tuning based on catId for Produksi
        if (!$is_finishing && !empty($machines)) {
            $filtered = [];
            foreach ($machines as $m) {
                $ket = strtolower($m['mesin_keterangan']);
                $match = false;
                if ($catId == 1 && strpos($ket, 'a3') !== false)
                    $match = true;
                elseif ($catId == 2 && (strpos($ket, 'outdoor') !== false || strpos($ket, 'indoor') !== false))
                    $match = true;
                elseif ($catId == 3 && (strpos($ket, 'flatbed') !== false || strpos($ket, 'uv') !== false))
                    $match = true;
                elseif ($catId == 4 && strpos($ket, 'sublim') !== false)
                    $match = true;
                elseif ($catId == 5 && (strpos($ket, 'tekstil') !== false || strpos($ket, 'textile') !== false))
                    $match = true;
                elseif ($catId == 6 && (strpos($ket, 'laser') !== false || strpos($ket, 'cutting') !== false))
                    $match = true;
                elseif ($catId == 8 && strpos($ket, 'dtg') !== false)
                    $match = true;

                // If it matches perfectly, add it.
                if ($match) {
                    $filtered[] = $m;
                }
            }
            // If the fine-tuned list is not empty, use it, otherwise fall back to all category machines
            if (!empty($filtered)) {
                $machines = $filtered;
            }
        }

        $user_id = $this->session->userdata('id_user') ?: $this->session->userdata('id');
        $user_data = $this->db->get_where('users', ['user_id' => $user_id])->row();
        $user_mesin_ids = [];
        if ($user_data && !empty($user_data->mesin_ids)) {
            $user_mesin_ids = explode(',', $user_data->mesin_ids);
        }

        echo json_encode([
            'machines' => $machines,
            'is_mandatory' => $is_mandatory,
            'kategori_mesin' => $kategori_mesin,
            'user_mesin_ids' => $user_mesin_ids
        ]);
    }

    /**
     * DEDICATED Function for Large Format (Avoid touching A3+)
     */
    public function advance_step_lf_ajax()
    {
        $this->_workflow_advance();
    }





    public function proses_routing($detail_id = null)
    {
        $this->_workflow_legacy_rejected();
    }

    public function selesai_routing($detail_id = null)
    {
        $this->_workflow_legacy_rejected();
    }

    public function kirim_pb_spk($nospk = null)
    {
        $this->_workflow_response(function () use ($nospk) {
            $nospk = $nospk ?: $this->input->post('nospk', true);
            return $this->_workflow_execute($this->_workflow_spk_ids($nospk, $this->input->post('cat', true), 'done'), 'handover');
        });
    }

    public function verify_operator_pin()
    {
        $operator_id = $this->input->post('operator_id', true);
        $pin = $this->input->post('pin', true);

        if (!$operator_id || !$pin) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            return;
        }

        // Check if PIN column exists first to avoid DB error 500
        if (!$this->db->field_exists('pin', 'tbl_operator')) {
            echo json_encode(['success' => false, 'message' => 'Kolom PIN belum ada di database. Hubungi IT Admin.']);
            return;
        }

        $op = $this->db->get_where('tbl_operator', ['operator_id' => $operator_id, 'pin' => $pin])->row();

        if ($op) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'PIN Operator Salah!']);
        }
    }

    // =========================================================================
    // CUSTOMER VIEW MIRROR
    // =========================================================================

    // Fitur untuk melihat tampilan tracking persis seperti yang dilihat konsumen
    public function check_client_view($nospk = null)
    {
        // 1. Jika SPK kosong, cek apakah ada input POST
        if ($nospk === null) {
            $nospk = $this->input->post('spk', true);
            if ($nospk) {
                // Redirect agar URL cantik dan sesuai method mirror
                redirect('wip/check_client_view/' . urlencode($nospk));
                return;
            }
        }

        // 2. Jika masih kosong, tampilkan FORM INPUT
        if (empty($nospk)) {
            $data['title'] = "Cek Pesanan (Mirror Konsumen)";
            // Kita bisa reuse view form sederhana atau buat inline view di sini jika sangat simpel
            // Tapi lebih baik buat view terpisah agar rapi. 
            // Untuk efisiensi, saya akan load view admin_template dengan konten form.
            $this->load_admin_view('admin/wip/form_tracking_consumer', $data);
            return;
        }

        $this->load->model('M_transaksi');

        // Use logic from Cekpesanan controller
        $pembayaran_data = $this->M_transaksi->getcekpesanan($nospk)->row();

        if (!$pembayaran_data) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">SPK <b>' . $nospk . '</b> Tidak Ditemukan!</div>');
            redirect('wip/check_client_view');
            return;
        }

        $perusahaan_id = isset($pembayaran_data->perusahaan_id) ? $pembayaran_data->perusahaan_id : 1;
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row_array();

        $data = [
            'function' => 'Cek Pesanan (Admin View)',
            'title' => 'Cek Pesanan - Client View Mirror',
            'nospk' => $nospk,
            'perusahaan' => $perusahaan,
            'pembayaran' => $pembayaran_data,
            'detailProduk' => (function () use ($nospk) {
                $items = $this->M_transaksi->getDetailPesan($nospk)->result();
                foreach ($items as &$item) {
                    if (!empty($item->metadata)) {
                        $meta = json_decode($item->metadata, true);
                        if (isset($meta['unit_price'])) {
                            $item->unit_price = $meta['unit_price'];
                        }
                    }
                }
                return $items;
            })(),
            'detailBayar' => $this->M_transaksi->getDetailPembayaran($nospk)->result(),
            'cekflow' => $this->M_transaksi->getcekflow($nospk)->row_array(),
            'rekening' => $this->db->get('tbl_rekening')->result(),
            'is_admin_preview' => true
        ];

        // Load exact views as Cekpesanan controller
        $this->load->view('layout_admin/head', $data);
        $this->load->view('pesanan/cekpesanan', $data);
        $this->load->view('layout_admin/footer');
    }

    /**
     * Display Invoice View for Iframe (No Auto Print)
     * URL: wip/view_spk_invoice/INV~2023~001 (Safe URL)
     */
    public function view_spk_invoice($nospk = '')
    {
        // Fallback for query string (optional) or segment
        if (empty($nospk)) {
            $nospk = $this->input->get('nospk', true);
        }

        if (empty($nospk)) {
            show_error('No SPK provided', 400);
            return;
        }

        // Decode: Replace SAFE_SEPARATOR (e.g. ~) back to slash /
        $nospk = urldecode($nospk);
        $nospk = str_replace('~', '/', $nospk);

        $this->load->model('M_pembayaran');

        $pembayaran = $this->M_pembayaran->get_pembayaran_by_nospk($nospk);
        if (!$pembayaran) {
            show_error('Invoice not found', 404);
            return;
        }

        $pemesanan = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row_array();
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $pemesanan['perusahaan_id'] ?? 1])->row_array();

        $detailBayar = $this->M_pembayaran->get_pembayaran_detail($pembayaran->pembayaran_id);
        $detailProduk = $this->M_pembayaran->get_pembayaran_produk($nospk);

        $data = [
            'perusahaan' => $perusahaan,
            'pembayaran' => $pembayaran,
            'detailBayar' => $detailBayar,
            'detailProduk' => $detailProduk,
        ];

        // Load view as string to manipulate
        $view_html = $this->load->view('admin/transaksi/cetak_invoice_ulang', $data, TRUE);

        // Remove auto-print
        $view_html = str_replace('onload="window.print();"', '', $view_html);

        // Add style to center it in iframe if needed, but preserve thermal width
        // The original view has body { width: 76mm; margin: 0 auto; } which centers it already.
        // We just ensure background is nice.
        $style_override = '<style>
            body { 
                background-color: #f8f9fc !important; 
                margin-top: 20px !important;
                margin-bottom: 20px !important;
                box-shadow: 0 0 15px rgba(0,0,0,0.1);
                min-height: 100vh;
            }
        </style>';

        // Inject override before </head>
        $view_html = str_replace('</head>', $style_override . '</head>', $view_html); // Assuming head exists, or just prepend

        // If view doesn't have head (it might not), just prepend. 
        // Checking view content... it starts with <style>. So we prepend.
        echo $style_override . $view_html;
    }



    protected function get_operator_id_for_current_user()
    {
        $user_id = $this->session->userdata('id_user') ?: $this->session->userdata('id');
        $role_id = (int) $this->session->userdata('level');
        $username = $this->session->userdata('username');

        // 1. Super Admin / Owner / Developer Bypass
        // Aligned with other access checks in Wip.php (1=Admin, 17=Owner)
        if (in_array($role_id, [1, 17], true)) {
            return null; // See ALL
        }

        // 2. Priority: Check user-specific operator_id from 'users' table
        // This handles cases where division is generic (3) but user is specific (1 = A3+)
        $user_data = $this->db->get_where('users', ['user_id' => $user_id])->row();
        if ($user_data && !empty($user_data->operator_id)) {
            // Verify this operator ID exists in tbl_operator
            $op_exists = $this->db->get_where('tbl_operator', ['operator_id' => $user_data->operator_id])->row();
            if ($op_exists) {
                return $user_data->operator_id;
            }
        }

        // 3. Fallback: Check if User's Division is an Operator (Legacy / Default)
        $this->db->select('k.divisi_id');
        $this->db->from('users u');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id');
        $this->db->where('u.user_id', $user_id);
        $res = $this->db->get()->row();

        if ($res && $res->divisi_id) {
            // Check if this division exists as an operator role
            $is_operator = $this->db->get_where('tbl_operator', ['operator_id' => $res->divisi_id])->row();
            if ($is_operator) {
                return $res->divisi_id;
            }
        }

        return null;
    }



    /**
     * Helper: Process Stock Deduction based on BOM
     * Called when a production step is marked as finished
     */


    public function execute_rip_batch_ajax()
    {
        $this->_workflow_legacy_rejected();
    }


}
