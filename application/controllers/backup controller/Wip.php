<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wip extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Produksi_model');
        $this->load->model('M_gudang');
    }

    // =========================================================================
    // MAIN PAGES
    // =========================================================================

    public function index()
    {
        try {
            $operator_id = $this->get_operator_id_for_current_user();

            $data['produksi'] = $this->Produksi_model->get_all_produksi($operator_id);
            $data['summary'] = $this->Produksi_model->get_summary($operator_id);

            // Ensure valid arrays
            if (!is_array($data['produksi']))
                $data['produksi'] = [];
            if (!is_array($data['summary']))
                $data['summary'] = ['total' => 0, 'selesai' => 0, 'proses' => 0, 'terlambat' => 0];

            $this->load_admin_view('admin/wip/wip_tracking', $data);
        } catch (Exception $e) {
            log_message('error', 'WIP Index - Error: ' . $e->getMessage());
            $data['produksi'] = [];
            $data['summary'] = ['total' => 0, 'selesai' => 0, 'proses' => 0, 'terlambat' => 0];
            $this->load_admin_view('admin/wip/wip_tracking', $data);
        }
    }

    public function produksi()
    {
        // Create operator ID variable early to avoid DB Builder collision
        $current_operator_id = $this->get_operator_id_for_current_user();

        // Ambil semua data dari tbl_produksi
        $this->db->select('tbl_produksi.*, tbl_konsumen.konsumen_nama, tbl_product.product_nama, tbl_product.kategori_id, tbl_product.tipe_id, tbl_kategori.kategori_nama, tbl_satuan.satuan_nama, tbl_perusahaan.nama_perusahaan, tbl_pemesanan.created_at as tgl_spk');
        $this->db->select('(SELECT r.routing_name FROM detail_produksi dp JOIN routing r ON r.routing_id = dp.routing_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.status_produksi != \'2\' ORDER BY r.routing_urutan ASC LIMIT 1) as current_step_name');
        $this->db->select('(SELECT u.username FROM detail_produksi dp JOIN tbl_operator o ON dp.operator_id = o.operator_id JOIN tbl_karyawan k ON k.divisi_id = o.operator_id JOIN users u ON u.karyawan_id = k.karyawan_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.status_produksi != \'2\' ORDER BY dp.routing_urutan ASC LIMIT 1) as assigned_user');
        $this->db->select('(SELECT r.routing_urutan FROM detail_produksi dp JOIN routing r ON r.routing_id = dp.routing_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.status_produksi != \'2\' ORDER BY r.routing_urutan ASC LIMIT 1) as current_step_urutan');
        $this->db->select('(SELECT COUNT(*) FROM detail_produksi WHERE produksi_id = tbl_produksi.produksi_id) as total_steps');
        $this->db->select('(SELECT COUNT(*) FROM detail_produksi WHERE produksi_id = tbl_produksi.produksi_id AND status_produksi = \'2\') as done_steps');
        $this->db->from('tbl_produksi');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_produksi.konsumen_id', 'left');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id', 'left');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_produksi.perusahaan_id', 'left');
        $this->db->join('tbl_pemesanan', 'tbl_pemesanan.nospk = tbl_produksi.nospk', 'left');

        // BEST PRACTICE: Filter by tipe_id. 
        // 1: Barang Produksi, 2: Jasa Produksi (Include in WIP)
        // 3: Barang Jadi, 4: Jasa Layanan/Desain (Exclude from PRODUCTION WIP)
        $this->db->where_in('tbl_product.tipe_id', [1, 2]);


        // [REFACTORED] OPERATOR ACCESS POLICY:
        // All operators can VIEW all production items for workflow transparency and coordination.
        // However, they can only EXECUTE steps that match their PIN (verified at action time).
        // This prevents information silos while maintaining security through PIN verification.
        // No filtering by $current_operator_id here - let them see the full production floor.


        // Filter data: Exclude on_costumer and ONLY show PAID/CREDIT orders (exclude pending)
        // [MODIFIED] Include 'on_pb' items if they were sent TODAY so operators can see status difference.
        $this->db->group_start();
        $this->db->where('tbl_produksi.status_produksi !=', 'on_pb');
        $this->db->or_where('DATE(tbl_produksi.tgl_kirim_pb)', date('Y-m-d'));
        $this->db->group_end();

        $this->db->where('tbl_produksi.status_produksi !=', 'on_costumer');
        $this->db->where('tbl_produksi.status_produksi IS NOT NULL');
        $this->db->where('tbl_pemesanan.status_pembayaran', 'paid');

        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');

        // FILTER: Apply STRICT Location Filtering for ALL users (Operators & Admins) except Owner/SuperAdmin.
        // This ensures operators only separate jobs produced at their current branch location.
        // Previously, operators bypassed this check, causing cross-branch mixing.
        if (!in_array((int) $level, [1, 17], true)) {
            // Updated Logic: Filter by 'lokasi_cetak' if set, otherwise 'perusahaan_id'
            // This allows operators to process items assigned to their location (lokasi_cetak)
            // even if the SPK originated from another branch.
            $this->db->group_start();
            $this->db->where('tbl_produksi.lokasi_cetak', $perusahaan);
            $this->db->or_group_start();
            $this->db->where("(tbl_produksi.lokasi_cetak IS NULL OR tbl_produksi.lokasi_cetak = '' OR tbl_produksi.lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('tbl_produksi.perusahaan_id', $perusahaan);
            $this->db->group_end();
            $this->db->group_end();
        }

        // Order by update_time DESC to show most recently updated items first
        $this->db->order_by('tbl_produksi.update_time', 'DESC');

        // Remove SQL group_by to ensure all items are fetched for PHP grouping
        $this->db->group_by('tbl_produksi.produksi_id'); // Ensure full item fetching

        $raw_data = $this->db->get()->result();
        // DEBUGGING CODE (Hapus setelah selesai)
        // echo $this->db->last_query();
        // die();
        // END DEBUGGING

        // Grouping data berdasarkan nospk & Calculate Stats
        $grouped_data = [];
        $stats = [
            'total_spk' => 0,
            'antrian' => 0,
            'produksi' => 0,
            'selesai' => 0,
            'ready_to_pb' => 0
        ];

        if (!empty($raw_data)) {
            $unique_spks = [];
            foreach ($raw_data as $row) {
                $nospk = !empty($row->nospk) ? $row->nospk : 'NO SPK Undefined';
                $cat_id = $row->kategori_id ?: '0';

                // Composite key to separate categories within the same SPK
                // This satisfies the user request: "setiap kategori dipecah... jangan ada produk yg terkumpul di 1 kategori"
                $group_key = $nospk . '_c' . $cat_id;

                if (!isset($grouped_data[$group_key])) {
                    $grouped_data[$group_key] = [];
                }
                $grouped_data[$group_key][] = $row;

                if (!isset($unique_spks[$nospk])) {
                    $stats['total_spk']++;
                    $unique_spks[$nospk] = true;
                }

                // Simple item stats
                $st = (string) $row->status_produksi;
                if (in_array($st, ['approved', 'pending', '', '0']))
                    $stats['antrian']++;
                elseif ($st === 'proses')
                    $stats['produksi']++;
                elseif ($st === 'done')
                    $stats['selesai']++;
            }
            // Secondary Pass: Count Ready to PB groups
            foreach ($grouped_data as $group_key => $items) {
                $hasDone = false;
                $hasActiveQueue = false;

                foreach ($items as $it) {
                    $st = $it->status_produksi;
                    if (in_array($st, ['approved', 'pending', 'proses'])) {
                        $hasActiveQueue = true;
                        break;
                    }
                    if ($st === 'done') {
                        $hasDone = true;
                    }
                }

                if ($hasDone && !$hasActiveQueue) {
                    $stats['ready_to_pb']++;
                }
            }
        }

        $data['tab_categories'] = $this->db->query("SELECT * FROM tbl_kategori WHERE status = '1' ORDER BY kategori_urutan ASC")->result();

        // Build schema map for dynamic labels in the view
        $schema_map = [];
        foreach ($data['tab_categories'] as $cat) {
            $schema_map[$cat->kategori_id] = !empty($cat->form_schema) ? json_decode($cat->form_schema, true) : [];
        }
        $data['category_schemas'] = $schema_map;

        $data['produksi_list'] = $grouped_data;
        $data['raw_items'] = $raw_data; // Flat list for item-based view
        $data['stats'] = $stats;

        // Fetch BOM info for raw_items (Efficient batching)
        if (!empty($raw_data)) {
            $product_ids = array_unique(array_column($raw_data, 'product_id'));
            $this->db->select('bh.bahan_nama, bl.quantity as bom_qty, bl.product_id, bl.satuan_id, bl.bahan_id, bl.panjang, bl.lebar');
            $this->db->from('bom_lines bl');
            $this->db->join('tbl_bahan bh', 'bh.bahan_id = bl.bahan_id', 'left');
            $this->db->where_in('bl.product_id', $product_ids);
            $bom_data = $this->db->get()->result();

            $bom_map = [];
            $priority_units = [6 => 3, 2 => 2, 1 => 1]; // Roll > Meter > Lembar

            foreach ($bom_data as $bd) {
                if (!isset($bom_map[$bd->product_id])) {
                    $bom_map[$bd->product_id] = $bd;
                } else {
                    $current_unit = $bom_map[$bd->product_id]->satuan_id;
                    $new_unit = $bd->satuan_id;

                    $current_prio = $priority_units[$current_unit] ?? 0;
                    $new_prio = $priority_units[$new_unit] ?? 0;

                    if ($new_prio > $current_prio) {
                        $bom_map[$bd->product_id] = $bd;
                    }
                }
            }

            foreach ($data['raw_items'] as &$item) {
                $item->bom = $bom_map[$item->product_id] ?? null;
            }

            // Fetch all routing steps for the items in the list
            $p_ids = array_column($raw_data, 'produksi_id');
            if (!empty($p_ids)) {
                $this->db->select('dp.produksi_id, r.routing_name, dp.status_produksi, m.mesin_nama, dp.lokasi_proses');
                $this->db->from('detail_produksi dp');
                $this->db->join('routing r', 'r.routing_id = dp.routing_id');
                $this->db->join('tbl_mesin m', 'm.mesin_id = dp.mesin_id', 'left');
                $this->db->where_in('dp.produksi_id', $p_ids);
                $this->db->order_by('r.routing_urutan', 'ASC');
                $all_routing_steps = $this->db->get()->result();

                $steps_map = [];
                foreach ($all_routing_steps as $step) {
                    $steps_map[$step->produksi_id][] = $step;
                }

                foreach ($data['raw_items'] as &$item) {
                    $item->routing_steps = $steps_map[$item->produksi_id] ?? [];
                }
            }
        }

        // Fetch Operators for dropdowns
        $this->db->select('tbl_karyawan.karyawan_id, tbl_karyawan.nama_lengkap');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id');
        $this->db->where_in('users.role_id', [4, 20]);
        $data['operators'] = $this->db->get()->result();

        // Determine Default Category Focus based on User
        // 1=A3+, 2=Large Format, 3=Finishing, etc.
        $default_category = 1; // Default global
        if ($current_operator_id !== null) {
            $username = $this->session->userdata('username');
            if (strpos($username, 'lf') !== false || strpos($username, 'large') !== false) {
                $default_category = 2;
            } elseif (strpos($username, 'fn') !== false || strpos($username, 'finish') !== false) {
                $default_category = 3;
            }
        }
        $data['default_category_id'] = $default_category;

        $this->load_admin_view('admin/wip/produksi_list', $data);
    }

    /**
     * BATCH: Start ALL items in an SPK
     * Bypass button to start everything at once.
     */
    public function start_all_spk_items()
    {
        $nospk = $this->input->post('nospk');

        if (empty($nospk)) {
            echo json_encode(['success' => false, 'message' => 'No SPK Invalid']);
            return;
        }

        $operator_id = $this->get_operator_id_for_current_user();

        $this->db->trans_start();

        // 1. Get all items for this SPK
        $items = $this->db->get_where('tbl_produksi', ['nospk' => $nospk])->result();
        $count = 0;

        foreach ($items as $item) {
            // Only process items that are NOT done.
            if ($item->status_produksi == 'done')
                continue;

            // Update Header Status if needed
            if ($item->status_produksi != 'proses') {
                $this->db->where('produksi_id', $item->produksi_id);
                $this->db->update('tbl_produksi', [
                    'status_produksi' => 'proses',
                    'update_time' => date('Y-m-d H:i:s')
                ]);
            }

            // Find First Pending Step (status '0')
            $this->db->select('detail_produksi_id');
            $this->db->where('produksi_id', $item->produksi_id);
            $this->db->where('status_produksi', '0');
            $this->db->order_by('routing_urutan', 'ASC');
            $this->db->limit(1);
            $step = $this->db->get('detail_produksi')->row();

            if ($step) {
                // Start this step
                $update_step = [
                    'status_produksi' => '1', // In Progress
                    'waktu_mulai' => date('Y-m-d H:i:s'),
                ];
                if ($operator_id) {
                    $update_step['operator_id'] = $operator_id;
                }

                $this->db->where('detail_produksi_id', $step->detail_produksi_id);
                $this->db->update('detail_produksi', $update_step);
                $count++;
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal memproses data.']);
        } else {
            $res = ['success' => true, 'message' => "$count item dimulai."];
            echo json_encode($res);
        }
    }

    /**
     * BATCH: Finish ALL active items in an SPK
     * Auto-completes current running steps.
     */
    public function finish_all_spk_items()
    {
        $nospk = $this->input->post('nospk');
        $operator_id = $this->get_operator_id_for_current_user();

        if (empty($nospk)) {
            echo json_encode(['success' => false, 'message' => 'No SPK Invalid']);
            return;
        }

        $this->db->trans_start();

        // Get active items
        $items = $this->db->get_where('tbl_produksi', ['nospk' => $nospk, 'status_produksi' => 'proses'])->result();
        $count = 0;

        foreach ($items as $item) {
            // Find currently RUNNING step (status '1')
            $this->db->select('detail_produksi_id, routing_urutan');
            $this->db->where('produksi_id', $item->produksi_id);
            $this->db->where('status_produksi', '1');
            $step = $this->db->get('detail_produksi')->row();

            if ($step) {
                // Finish this step
                $update_step = [
                    'status_produksi' => '2',
                    'waktu_selesai' => date('Y-m-d H:i:s'),
                    // Assume Output = Qty Order (Auto)
                    'qty_output' => $item->qty,
                    'operator_id' => $operator_id ?: null
                ];
                $this->db->where('detail_produksi_id', $step->detail_produksi_id);
                $this->db->update('detail_produksi', $update_step);

                // Check if there are more pending steps
                $remaining = $this->db->where('produksi_id', $item->produksi_id)
                    ->where('status_produksi', '0')
                    ->count_all_results('detail_produksi');

                if ($remaining == 0) {
                    // All done
                    $this->db->where('produksi_id', $item->produksi_id);
                    $this->db->update('tbl_produksi', [
                        'status_produksi' => 'done',
                        'tgl_selesai' => date('Y-m-d H:i:s')
                    ]);
                }
                $count++;
            }
        }

        $this->db->trans_complete();

        echo json_encode(['success' => true, 'message' => "$count item diselesaikan (Auto)."]);
    }


    public function revise_item_process()
    {
        $produksi_id = $this->input->post('produksi_id');
        $target_step_id = $this->input->post('target_step_id');
        $qty_revisi = (float) $this->input->post('qty_revisi');
        $alasan = $this->input->post('alasan_revisi');

        // 0. Get Basic Info 
        $item_info = $this->db->get_where('tbl_produksi', ['produksi_id' => $produksi_id])->row();
        if (!$item_info) {
            echo json_encode(['success' => false, 'message' => 'Item tidak ditemukan.']);
            return;
        }

        $this->db->trans_start();

        // 1. Determine Target Step
        if ($target_step_id) {
            $target_step = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $target_step_id])->row();
        } else {
            // Fallback to last finished step
            $target_step = $this->db->select('dp.*')
                ->from('detail_produksi dp')
                ->where('dp.produksi_id', $produksi_id)
                ->where('dp.status_produksi', '2')
                ->order_by('detail_produksi_id', 'DESC')
                ->limit(1)
                ->get()->row();
        }

        if (!$target_step) {
            // If no steps are done, maybe it's already in progress or newly started
            $target_step = $this->db->select('dp.*')
                ->from('detail_produksi dp')
                ->where('dp.produksi_id', $produksi_id)
                ->where('dp.status_produksi', '1')
                ->order_by('detail_produksi_id', 'ASC')
                ->limit(1)
                ->get()->row();
        }

        if (!$target_step) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada tahap yang bisa direvisi.']);
            return;
        }

        // 2. Revert Main Item Status (tbl_produksi)
        $update_payload = [
            'status_produksi' => 'proses',
            'tgl_selesai' => NULL,
            'update_time' => date('Y-m-d H:i:s')
        ];

        $rev_note = "\n[REVISI " . date('d/m H:i') . "]: Mundur ke tahap " . ($target_step->routing_id ?: $target_step->detail_produksi_id) . ($qty_revisi > 0 ? " dengan rijek $qty_revisi lembar." : ".");
        if ($alasan) {
            $rev_note .= " ALASAN: " . $alasan;
        }
        $this->db->set('catatan', "CONCAT(COALESCE(catatan, ''), " . $this->db->escape($rev_note) . ")", FALSE);
        $this->db->where('produksi_id', $produksi_id);
        $this->db->update('tbl_produksi', $update_payload);

        // 3. Status Transitions for Steps
        $all_steps = $this->db->get_where('detail_produksi', ['produksi_id' => $produksi_id])->result();

        foreach ($all_steps as $s) {
            if ($s->detail_produksi_id == $target_step->detail_produksi_id) {
                // This is our re-entry point
                $this->db->where('detail_produksi_id', $s->detail_produksi_id);
                $this->db->update('detail_produksi', [
                    'status_produksi' => '1',
                    'waktu_selesai' => NULL,
                    'qty_bahan_rusak' => (float) ($s->qty_bahan_rusak ?? 0) + $qty_revisi
                ]);

                // Stock Reduction
                if ($qty_revisi > 0) {
                    $bom = $this->db->select('bl.bahan_id, bl.quantity as bom_qty')
                        ->from('detail_produksi dp')
                        ->join('routing r', 'dp.routing_id = r.routing_id', 'left')
                        ->join('bom_lines bl', 'r.bom_line_id = bl.bom_line_id', 'left')
                        ->where('dp.detail_produksi_id', $s->detail_produksi_id)
                        ->get()->row();

                    if ($bom && $bom->bahan_id) {
                        $total_red = $qty_revisi * (float) ($bom->bom_qty ?: 1);
                        $this->update_warehouse_stock($produksi_id, $s->detail_produksi_id, $bom->bahan_id, $total_red);
                    }
                }
            } elseif ($s->detail_produksi_id > $target_step->detail_produksi_id) {
                // Future steps reset
                $this->db->where('detail_produksi_id', $s->detail_produksi_id);
                $this->db->update('detail_produksi', [
                    'status_produksi' => '0',
                    'waktu_mulai' => NULL,
                    'waktu_selesai' => NULL,
                    'qty_bahan_pakai' => 0,
                    'qty_bahan_rusak' => 0
                ]);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal memproses revisi database.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Status item berhasil direvisi.']);
        }
    }

    public function admin_save_item_edit()
    {
        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $pid = $this->input->post('produksi_id');
        $qty = (float) $this->input->post('qty');
        $panjang = (float) $this->input->post('panjang');
        $lebar = (float) $this->input->post('lebar');
        $catatan = $this->input->post('catatan');
        $product_id = $this->input->post('product_id');
        $step_updates = $this->input->post('step_updates');

        if (!$pid) {
            echo json_encode(['success' => false, 'message' => 'ID Produksi tidak valid.']);
            return;
        }

        $this->db->trans_start();

        $data_item = [
            'qty' => $qty,
            'panjang' => $panjang,
            'lebar' => $lebar,
            'catatan' => $catatan,
            'update_time' => date('Y-m-d H:i:s')
        ];

        if ($product_id) {
            $data_item['product_id'] = $product_id;
        }

        $this->db->where('produksi_id', $pid);
        $this->db->update('tbl_produksi', $data_item);

        // 2. Update Production Steps (detail_produksi)
        if (!empty($step_updates)) {
            $steps = json_decode($step_updates, true);
            if (is_array($steps)) {
                foreach ($steps as $s) {
                    $this->db->where('detail_produksi_id', $s['id']);
                    $this->db->update('detail_produksi', [
                        'material_id' => $s['material_id'] ?: NULL,
                        'operator_id' => $s['operator_id'] ?: NULL,
                        'qty_bahan_pakai' => (float) $s['pakai'],
                        'qty_bahan_rusak' => (float) $s['rusak'],
                        'qty_missing' => (float) $s['missing']
                    ]);
                }
            }
        }

        // Logging
        $log_note = "\n[ADMIN EDIT " . date('d/m H:i') . "]: Penyesuaian data item & tahapan oleh Admin.";
        $this->db->set('catatan', "CONCAT(COALESCE(catatan, ''), " . $this->db->escape($log_note) . ")", FALSE);
        $this->db->where('produksi_id', $pid);
        $this->db->update('tbl_produksi');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan penyesuaian.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Data item & tahapan berhasil disesuaikan.']);
        }
    }

    public function batch_status_produksi()
    {
        $ids = $this->input->post('ids');
        $status = $this->input->post('status');

        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            return;
        }

        $this->db->trans_start();

        // 1. Update Global Status
        $this->db->where_in('produksi_id', $ids);
        $this->db->update('tbl_produksi', [
            'status_produksi' => $status,
            'update_time' => date('Y-m-d H:i:s')
        ]);

        // 2. Sync with Control Center (detail_produksi) 
        // If status is 'proses', mark the FIRST pending step as 'In Progress' (1)
        if ($status === 'proses') {
            foreach ($ids as $pid) {
                // Find first pending step (status '0') ordered by routing sequence
                $this->db->select('detail_produksi_id');
                $this->db->from('detail_produksi');
                $this->db->where('produksi_id', $pid);
                $this->db->where('status_produksi', '0');
                $this->db->order_by('routing_urutan', 'ASC');
                $this->db->limit(1);
                $step = $this->db->get()->row();

                if ($step) {
                    $this->db->where('detail_produksi_id', $step->detail_produksi_id);
                    $this->db->update('detail_produksi', [
                        'status_produksi' => '1',
                        'waktu_mulai' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        $this->db->trans_complete();

        echo json_encode(['success' => $this->db->trans_status()]);
    }

    /**
     * AJAX: Search Base Materials
     */
    public function ajax_search_bahan()
    {
        $q = $this->input->get('q');
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
        $bahan_id = $this->input->get('bahan_id');
        if (!$bahan_id) {
            echo json_encode([]);
            return;
        }

        $this->db->select('m.material_id, m.varian, m.barcode, COALESCE(sg.qty, 0) as stok_aktual, s.satuan_nama');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id', 'left');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->where('m.bahan_id', $bahan_id);
        $this->db->where('m.status', '1');
        $res = $this->db->get()->result();

        echo json_encode($res);
    }

    /**
     * LAPOR MASALAH / RIJEK
     */
    public function lapor_masalah()
    {
        $nospk = $this->input->post('nospk');
        $items_rijek = $this->input->post('items_rijek'); // Array of objects [{id: 1, qty_rijek: 5}, ...]
        $jenis = $this->input->post('jenis_masalah');
        $keterangan = $this->input->post('keterangan');
        $is_reprint = $this->input->post('is_reprint'); // 1 or 0
        $pin = $this->input->post('pin_operator');

        if (empty($nospk) || empty($jenis) || empty($items_rijek) || empty($pin)) {
            echo json_encode(['status' => false, 'message' => 'Data tidak lengkap (Pilih item & Masukkan PIN)']);
            return;
        }

        // Verify PIN (Filter by Company)
        $this->db->where('pin_produksi', $pin);
        $this->db->where('perusahaan_id', $this->session->userdata('perusahaan_id'));
        $operator = $this->db->get('tbl_karyawan')->row();
        if (!$operator) {
            echo json_encode(['status' => false, 'message' => 'PIN Operator tidak valid!']);
            return;
        }
        $operator_name = $operator->nama_lengkap;

        $this->db->trans_start();

        $processed_count = 0;

        foreach ($items_rijek as $rijek) {
            $pid = $rijek['id'] ?? 0;
            $qty_rusak = $rijek['qty_rijek'] ?? 0;

            if (!$pid)
                continue;

            // Get Current Item Data
            $item = $this->db->get_where('tbl_produksi', ['produksi_id' => $pid])->row();
            if (!$item)
                continue;

            $log_note = "\n[MASALAH] " . date('d/m/Y H:i') . " | Oleh: " . $operator_name .
                " (PIN: $pin) | Qty Rusak: $qty_rusak | Jenis: $jenis | Ket: $keterangan";

            $update_data = [
                'catatan' => ($item->catatan ?? '') . $log_note,
                'update_time' => date('Y-m-d H:i:s')
            ];

            // Jika Reprint diminta -> Reset status ke 'approved' (Antrian Awal)
            if ($is_reprint == 1) {
                $update_data['status_produksi'] = 'approved';

                if ($this->db->field_exists('is_reprint', 'tbl_produksi')) {
                    $update_data['is_reprint'] = 1;
                }

                // Reset semua steps di detail_produksi
                $this->db->where('produksi_id', $pid);
                $this->db->update('detail_produksi', [
                    'status_produksi' => '0', // Reset ke pending
                    'waktu_mulai' => null,
                    'waktu_selesai' => null,
                    'operator_id' => null,
                    'mesin_id' => null
                ]);
            }

            $this->db->where('produksi_id', $pid);
            $this->db->update('tbl_produksi', $update_data);
            $processed_count++;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => false, 'message' => 'Gagal menyimpan laporan']);
        } else {
            echo json_encode(['status' => true, 'message' => "$processed_count Item ditandai bermasalah"]);
        }
    }

    /**
     * BATCH: Finish Jobs and Deduct Material Stock
     */
    public function batch_selesai_dan_stok()
    {
        $ids = $this->input->post('ids');
        $outcomes = $this->input->post('outcomes'); // Array: [id => ['status' => 'success/fail', 'reason' => '...']]
        $material_id = $this->input->post('material_id');
        $pin = $this->input->post('pin_operator');
        $total_usage = (float) $this->input->post('total_usage'); // in Meters

        if (empty($pin)) {
            echo json_encode(['success' => false, 'message' => 'Masukkan PIN Operator untuk verifikasi.']);
            return;
        }

        // Verify PIN (Filter by Company)
        $this->db->where('pin_produksi', $pin);
        $this->db->where('perusahaan_id', $this->session->userdata('perusahaan_id'));
        $operator = $this->db->get('tbl_karyawan')->row();
        if (!$operator) {
            echo json_encode(['success' => false, 'message' => 'PIN Operator tidak valid!']);
            return;
        }
        $operator_id = $operator->karyawan_id;

        // Validation: If no material_id provided, it's considered a "Finishing Only" batch (no stock deduction)
        $is_finishing_only = empty($material_id) || $material_id == 'null';

        if (empty($ids) || (!$is_finishing_only && $total_usage <= 0)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            return;
        }

        $this->db->trans_start();

        foreach ($ids as $pid) {
            $this->_ensure_detail_produksi($pid); // INITIALIZE STEPS IF MISSING
            $outcome = $outcomes[$pid] ?? ['status' => 'success', 'reason' => ''];

            if ($outcome['status'] === 'fail') {
                // REJECT/REDO LOGIC: Revert EVERYTHING for this job
                $reason = ($outcome['reason'] ?: 'Rijek Produksi / Misprint');

                // 1. Get current data for logging
                $curr = $this->db->get_where('tbl_produksi', ['produksi_id' => $pid])->row();
                $new_note = trim(($curr->catatan ?? '') . "\n[RIJEK] " . date('d/m/Y H:i') . " | Alasan: " . $reason);

                // 2. Revert Global Status to 'approved' (Antrian)
                $update_header = [
                    'status_produksi' => 'approved',
                    'catatan' => $new_note,
                    'update_time' => date('Y-m-d H:i:s')
                ];

                // Add is_reprint if column exists
                if ($this->db->field_exists('is_reprint', 'tbl_produksi')) {
                    $update_header['is_reprint'] = 1;
                }

                $this->db->where('produksi_id', $pid);
                $this->db->update('tbl_produksi', $update_header);

                // 3. Clear timestamps and revert ALL steps to pending (0)
                // This ensures it starts from Step 1 again
                $this->db->where('produksi_id', $pid);
                $this->db->update('detail_produksi', [
                    'status_produksi' => '0',
                    'waktu_mulai' => null,
                    'waktu_selesai' => null,
                    'operator_id' => null,
                    'mesin_id' => null,
                    'alasan_missing' => $reason,
                    'qty_missing' => 1
                ]);
            } else {
                // SUCCESS LOGIC: Step-Aware Completion
                // ... (Logic Sukses Tetap Sama)
                // 1. Identify current active/pending step
                $this->db->select('detail_produksi_id');
                $this->db->where('produksi_id', $pid);
                $this->db->where_in('status_produksi', ['0', '1']);
                $this->db->order_by('routing_urutan', 'ASC');
                $this->db->order_by('detail_produksi_id', 'ASC');
                $current_step = $this->db->get('detail_produksi')->row();

                if ($current_step) {
                    // Close this specific step
                    $this->db->where('detail_produksi_id', $current_step->detail_produksi_id);
                    $this->db->update('detail_produksi', [
                        'status_produksi' => '2',
                        'operator_id' => $operator_id,
                        'waktu_selesai' => date('Y-m-d H:i:s')
                    ]);
                }

                // 2. Check if any steps remain after this update
                $remaining = $this->db->where('produksi_id', $pid)
                    ->where_in('status_produksi', ['0', '1'])
                    ->count_all_results('detail_produksi');

                // 3. Update Global Status
                if ($remaining > 0) {
                    // If finishing steps remain, keep global status as 'proses'
                    $this->db->where('produksi_id', $pid);
                    $this->db->update('tbl_produksi', [
                        'status_produksi' => 'proses'
                    ]);
                } else {
                    // Fully done
                    $this->db->where('produksi_id', $pid);
                    $this->db->update('tbl_produksi', [
                        'status_produksi' => 'done',
                        'tgl_selesai' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        // 3. Reduce Stock (Deduct regardless of success/fail, but ONLY if it's a printing batch)
        if (!$is_finishing_only) {
            $ref_id = $ids[0];
            $this->update_warehouse_stock($ref_id, 0, $material_id, $total_usage);
        }

        $this->db->trans_complete();

        echo json_encode(['success' => $this->db->trans_status()]);
    }

    public function selesai_item($produksi_id)
    {
        if (empty($produksi_id)) {
            redirect('wip/produksi');
        }

        $data = [
            'status_produksi' => 'done',
            'tgl_selesai' => date('Y-m-d H:i:s')
        ];

        $this->db->where('produksi_id', $produksi_id);
        $update = $this->db->update('tbl_produksi', $data);

        if ($update) {
            $this->session->set_flashdata('message', '<div class="alert alert-success">Produksi selesai! Barang dipindahkan ke PB.</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Gagal memperbarui status.</div>');
        }

        redirect('wip/produksi');
    }

    public function get_detail_produksi()
    {
        $produksi_id = $this->input->post('produksi_id');
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

    public function get_pengambilan_barang()
    {
        $this->load->model('Produksi_model');
        // Unused variables (produksi_pb, produksi_handover) removed to speed up execution
        $data['detail_pb'] = $this->Produksi_model->get_detail_pb();
        $data['detail_handover'] = $this->Produksi_model->get_detail_handover();

        // Attach breakdown for better UX using batched query (Optimized from N+1)
        if (!empty($data['detail_pb'])) {
            $nospk_list = array_map(function ($row) {
                return $row->nospk;
            }, $data['detail_pb']);

            $breakdowns = $this->Produksi_model->get_qty_breakdown_batched($nospk_list);

            foreach ($data['detail_pb'] as $key => $val) {
                $data['detail_pb'][$key]->breakdown = isset($breakdowns[$val->nospk]) ? $breakdowns[$val->nospk] : [];
            }
        }

        $this->load_admin_view('admin/wip/pengambilan_barang', $data);
    }

    public function start_process()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('detail_produksi_id', 'Detail Produksi ID', 'required|numeric');
        $this->form_validation->set_rules('routing_urutan', 'Routing Urutan', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $response = ['success' => false, 'message' => validation_errors()];
        } else {
            $detail_produksi_id = $this->input->post('detail_produksi_id');

            $update_data = [
                'status_produksi' => '1',
                'waktu_mulai' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ];

            $this->db->where('detail_produksi_id', $detail_produksi_id);
            $updated = $this->db->update('detail_produksi', $update_data);

            if ($updated) {
                $response = ['success' => true, 'message' => 'Process started successfully'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to start process'];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function complete_process()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('detail_produksi_id', 'Detail Produksi ID', 'required|numeric');
        $this->form_validation->set_rules('qty_output', 'Qty Output', 'required|numeric');
        $this->form_validation->set_rules('qty_missing', 'Qty Missing', 'numeric');
        $this->form_validation->set_rules('panjang_output', 'Panjang Output', 'numeric');
        $this->form_validation->set_rules('lebar_output', 'Lebar Output', 'numeric');
        $this->form_validation->set_rules('mesin_id', 'Mesin ID', 'required|numeric');
        $this->form_validation->set_rules('material_id', 'Material ID', 'numeric');
        $this->form_validation->set_rules('lokasi_proses', 'Lokasi Cetak', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $response = ['success' => false, 'message' => validation_errors()];
        } else {
            $detail_produksi_id = $this->input->post('detail_produksi_id');
            $qty_output = $this->input->post('qty_output');
            $qty_missing = $this->input->post('qty_missing') ?: 0;
            $panjang_output = $this->input->post('panjang_output');
            $lebar_output = $this->input->post('lebar_output');
            $alasan_missing = $this->input->post('alasan_missing');

            $detail_data = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $detail_produksi_id])->row();

            if (!$detail_data) {
                $response = ['success' => false, 'message' => 'Detail produksi not found'];
            } else {
                $this->db->trans_start();

                try {
                    $update_data = [
                        'qty_output' => $qty_output,
                        'qty_missing' => $qty_missing,
                        'panjang_output' => $panjang_output,
                        'lebar_output' => $lebar_output,
                        'alasan_missing' => $alasan_missing,
                        'mesin_id' => $this->input->post('mesin_id'),
                        'material_id' => $this->input->post('material_id') ?: null,
                        'lokasi_proses' => $this->input->post('lokasi_proses'),
                        'status_produksi' => '2', // Selesai Di Produksi
                        'waktu_selesai' => date('Y-m-d H:i:s'),
                        'update_time' => date('Y-m-d H:i:s')
                    ];

                    $this->db->where('detail_produksi_id', $detail_produksi_id);
                    $this->db->update('detail_produksi', $update_data);

                    // Stock reduction
                    $stock_reduction = $this->calculate_stock_reduction($detail_data, $qty_output, $panjang_output, $lebar_output);

                    if ($stock_reduction > 0) {
                        $material_id = $this->input->post('material_id') ?: $detail_data->material_id;
                        $this->update_warehouse_stock($detail_data->produksi_id, $detail_produksi_id, $material_id, $stock_reduction);
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $response = ['success' => false, 'message' => 'Failed to complete process and update stock'];
                    } else {
                        $response = ['success' => true, 'message' => 'Process completed and stock updated successfully'];
                    }

                } catch (Exception $e) {
                    $this->db->trans_rollback();
                    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function update_production_process()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('detail_produksi_id', 'Detail Produksi ID', 'required|numeric');
        $this->form_validation->set_rules('status_produksi', 'Status Produksi', 'required|in_list[0,1,2,3]');
        $this->form_validation->set_rules('mesin_id', 'Mesin ID', 'required|numeric');
        $this->form_validation->set_rules('lokasi_proses', 'Lokasi Proses', 'required|numeric');
        // Optional fields validation
        $this->form_validation->set_rules('material_id', 'Material ID', 'numeric');
        $this->form_validation->set_rules('qty_input', 'Qty Input', 'numeric');
        $this->form_validation->set_rules('qty_output', 'Qty Output', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            $response = ['success' => false, 'message' => validation_errors()];
        } else {
            $detail_produksi_id = $this->input->post('detail_produksi_id');
            $status_produksi = $this->input->post('status_produksi');

            // Collect optional data
            $qty_input = $this->input->post('qty_input') ?: null;
            $qty_output = $this->input->post('qty_output') ?: null;
            $qty_missing = $this->input->post('qty_missing') ?: 0;
            $panjang_output = $this->input->post('panjang_output') ?: null;
            $lebar_output = $this->input->post('lebar_output') ?: null;
            $lokasi_proses = $this->input->post('lokasi_proses') ?: null;
            $alasan_missing = $this->input->post('alasan_missing') ?: null;
            $material_id = $this->input->post('material_id') ?: null;

            $current_data = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $detail_produksi_id])->row();

            if (!$current_data) {
                $response = ['success' => false, 'message' => 'Detail produksi not found'];
            } else {
                $this->db->trans_start();

                try {
                    $update_data = [
                        'status_produksi' => $status_produksi,
                        'mesin_id' => $this->input->post('mesin_id'),
                        'update_time' => date('Y-m-d H:i:s')
                    ];

                    if ($qty_input !== null)
                        $update_data['qty_input'] = $qty_input;
                    if ($qty_output !== null)
                        $update_data['qty_output'] = $qty_output;
                    if ($qty_missing !== null)
                        $update_data['qty_missing'] = $qty_missing;
                    if ($panjang_output !== null)
                        $update_data['panjang_output'] = $panjang_output;
                    if ($lebar_output !== null)
                        $update_data['lebar_output'] = $lebar_output;
                    if ($lokasi_proses !== null)
                        $update_data['lokasi_proses'] = $lokasi_proses;
                    if ($alasan_missing !== null)
                        $update_data['alasan_missing'] = $alasan_missing;
                    if ($material_id !== null)
                        $update_data['material_id'] = $material_id == '0' ? null : $material_id;

                    // Set status dependent timestamps
                    if ($status_produksi == '1' && $current_data->status_produksi != '1') {
                        $update_data['waktu_mulai'] = date('Y-m-d H:i:s');
                    } elseif (in_array($status_produksi, ['2', '3']) && !in_array($current_data->status_produksi, ['2', '3'])) {
                        $update_data['waktu_selesai'] = date('Y-m-d H:i:s');
                    }

                    $this->db->where('detail_produksi_id', $detail_produksi_id);
                    $this->db->update('detail_produksi', $update_data);

                    // Update stock if completed
                    if (in_array($status_produksi, ['2', '3']) && $qty_output && $panjang_output && $lebar_output) {
                        $stock_reduction = $this->calculate_stock_reduction($current_data, $qty_output, $panjang_output, $lebar_output);
                        if ($stock_reduction > 0) {
                            $material_id = $this->input->post('material_id') ?: $current_data->material_id;
                            $this->update_warehouse_stock($current_data->produksi_id, $detail_produksi_id, $material_id, $stock_reduction);
                        }
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $response = ['success' => false, 'message' => 'Failed to update production process'];
                    } else {
                        $response = ['success' => true, 'message' => 'Production process updated successfully'];
                    }

                } catch (Exception $e) {
                    $this->db->trans_rollback();
                    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function handover_to_customer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nospk', 'Nomor SPK', 'required');

        if ($this->form_validation->run() === FALSE) {
            $response = ['success' => false, 'message' => validation_errors()];
        } else {
            $nospk = $this->input->post('nospk');
            $produksi_ids = $this->input->post('produksi_ids'); // Array of IDs
            $via = $this->input->post('pengambilan_via');
            $note = $this->input->post('pengambilan_note');
            $foto_name = null;

            if (empty($produksi_ids)) {
                echo json_encode(['success' => false, 'message' => 'Tidak ada item yang dipilih untuk diserahkan']);
                return;
            }

            // Handle Foto Upload
            if (!empty($_FILES['foto_handover']['name'])) {
                $config['upload_path'] = FCPATH . 'uploads/handover/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg|GIF|JPG|PNG|JPEG';
                $config['max_size'] = 20480; // 20MB
                $config['file_name'] = 'HO_' . $nospk . '_' . time();

                $this->load->library('upload');
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('foto_handover')) {
                    $error = $this->upload->display_errors('', '');
                    echo json_encode(['success' => false, 'message' => 'Gagal upload foto: ' . $error]);
                    return;
                } else {
                    $upload_data = $this->upload->data();
                    $foto_name = $upload_data['file_name'];
                }
            }

            $handover_note = "[HANDOVER] " . date('d/m/Y H:i') . " | Via: " . ($via ?: '-') . " | Note: " . ($note ?: '-');

            $this->db->trans_start();

            // 1. Dapatkan detail produksi yang dipilih
            $productions = $this->db->select('produksi_id, catatan, nospk')
                ->where_in('produksi_id', $produksi_ids)
                ->get('tbl_produksi')->result();

            if (empty($productions)) {
                $response = ['success' => false, 'message' => 'Item produksi tidak ditemukan'];
            } else {
                // 2. Update status di detail_produksi
                $this->db->where_in('produksi_id', $produksi_ids);
                $this->db->where('status_produksi', 'on_pb');
                $this->db->update('detail_produksi', [
                    'status_produksi' => 'on_costumer',
                    'update_time' => date('Y-m-d H:i:s'),
                    'waktu_selesai' => date('Y-m-d H:i:s')
                ]);

                // 3. Update header tbl_produksi
                foreach ($productions as $p) {
                    $curr_catatan = $p->catatan ?? '';
                    $new_catatan = trim($curr_catatan . "\n" . $handover_note);

                    $update_data = [
                        'catatan' => $new_catatan,
                        'status_produksi' => 'on_costumer',
                        'update_time' => date('Y-m-d H:i:s')
                    ];

                    if ($foto_name) {
                        $update_data['foto_handover'] = $foto_name;
                    }

                    $this->db->where('produksi_id', $p->produksi_id);
                    $this->db->update('tbl_produksi', $update_data);
                }

                $this->db->trans_complete();
                $response = ($this->db->trans_status() === FALSE)
                    ? ['success' => false, 'message' => 'Gagal update database']
                    : ['success' => true, 'message' => 'Item berhasil diserahkan'];
            }
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function bulk_handover_to_customer()
    {
        $ids_raw = $this->input->post('ids'); // Legacy if needed
        $nospks = $this->input->post('nospks'); // New: preferred for full SPK handover
        $via = $this->input->post('pengambilan_via');
        $note = $this->input->post('pengambilan_note');
        $foto_name = null;

        // EXPANSION LOGIC: If nospks provided, fetch all associated production IDs that are currently 'on_pb'
        $ids = is_array($ids_raw) ? $ids_raw : [];
        if (!empty($nospks) && is_array($nospks)) {
            $expanded = $this->db->select('produksi_id')
                ->where_in('nospk', $nospks)
                ->where('status_produksi', 'on_pb')
                ->get('tbl_produksi')->result();
            foreach ($expanded as $row) {
                if (!in_array($row->produksi_id, $ids)) {
                    $ids[] = $row->produksi_id;
                }
            }
        }

        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada item yang ditemukan untuk diserahkan.']);
            return;
        }

        // Handle Foto Upload
        if (!empty($_FILES['foto_handover']['name'])) {
            $config['upload_path'] = FCPATH . 'uploads/handover/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|GIF|JPG|PNG|JPEG';
            $config['max_size'] = 20480; // 20MB
            $config['file_name'] = 'BULK_HO_' . time();

            $this->load->library('upload');
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('foto_handover')) {
                $error = $this->upload->display_errors('', '');
                echo json_encode(['success' => false, 'message' => 'Gagal upload foto: ' . $error]);
                return;
            } else {
                $upload_data = $this->upload->data();
                $foto_name = $upload_data['file_name'];
            }
        }

        $handover_note = "[BULK HANDOVER] " . date('d/m/Y H:i') . " | Via: " . ($via ?: '-') . " | Note: " . ($note ?: '-');

        $this->db->trans_start();

        // 1. Dapatkan catatan saat ini
        $productions = $this->db->select('produksi_id, catatan')
            ->where_in('produksi_id', $ids)
            ->get('tbl_produksi')->result();

        // 2. Update status di detail_produksi
        $this->db->where_in('produksi_id', $ids);
        $this->db->where('status_produksi', 'on_pb');
        $this->db->update('detail_produksi', [
            'status_produksi' => 'on_costumer',
            'update_time' => date('Y-m-d H:i:s'),
            'waktu_selesai' => date('Y-m-d H:i:s')
        ]);

        // 3. Update header tbl_produksi
        foreach ($productions as $p) {
            $curr_catatan = $p->catatan ?? '';
            $new_catatan = trim($curr_catatan . "\n" . $handover_note);

            $update_data = [
                'catatan' => $new_catatan,
                'status_produksi' => 'on_costumer',
                'update_time' => date('Y-m-d H:i:s')
            ];

            if ($foto_name) {
                $update_data['foto_handover'] = $foto_name;
            }

            $this->db->where('produksi_id', $p->produksi_id);
            $this->db->update('tbl_produksi', $update_data);
        }

        $this->db->trans_complete();
        $response = ($this->db->trans_status() === FALSE)
            ? ['success' => false, 'message' => 'Gagal bulk update database']
            : ['success' => true, 'message' => count($ids) . ' item berhasil diserahkan'];

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function move_to_pb()
    {
        $id = $this->input->post('produksi_id');
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
            return;
        }

        $this->db->trans_start();

        // Ambil data catatan lama
        $this->db->where('produksi_id', $id);
        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->group_start();
            $this->db->where('lokasi_cetak', $perusahaan);
            $this->db->or_group_start();
            $this->db->where("(lokasi_cetak IS NULL OR lokasi_cetak = '' OR lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('perusahaan_id', $perusahaan);
            $this->db->group_end();
            $this->db->group_end();
        }
        $curr = $this->db->get('tbl_produksi')->row();

        if (!$curr) {
            echo json_encode(['success' => false, 'message' => 'Akses ditolak atau data tidak ditemukan']);
            return;
        }
        $new_note = trim(($curr->catatan ?? '') . "\n[PB] " . date('d/m/Y H:i') . " | Dikirim ke Hub Pengambilan Barang");

        $this->db->where('produksi_id', $id);
        $this->db->update('tbl_produksi', [
            'status_produksi' => 'on_pb',
            'tgl_kirim_pb' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
            'catatan' => $new_note
        ]);

        $this->db->where('produksi_id', $id);
        $this->db->update('detail_produksi', [
            'status_produksi' => 'on_pb',
            'update_time' => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $response = ['success' => false, 'message' => 'Gagal memindahkan status'];
        } else {
            $response = ['success' => true, 'message' => 'Berhasil dipindahkan ke PB Hub'];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function batch_move_to_pb()
    {
        $ids = $this->input->post('ids');
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            return;
        }

        $this->db->trans_start();
        $timestamp = date('Y-m-d H:i:s');
        $note_date = date('d/m/Y H:i');

        foreach ($ids as $id) {
            // Get current note for append
            $curr = $this->db->select('catatan')->get_where('tbl_produksi', ['produksi_id' => $id])->row();
            $new_note = trim(($curr->catatan ?? '') . "\n[PB] " . $note_date . " | Batch Handover ke PB");

            $this->db->where('produksi_id', $id);
            $this->db->update('tbl_produksi', [
                'status_produksi' => 'on_pb',
                'tgl_kirim_pb' => $timestamp,
                'update_time' => $timestamp,
                'catatan' => $new_note
            ]);

            $this->db->where('produksi_id', $id);
            $this->db->update('detail_produksi', [
                'status_produksi' => 'on_pb',
                'update_time' => $timestamp
            ]);
        }

        $this->db->trans_complete();

        echo json_encode(['success' => $this->db->trans_status()]);
    }

    public function tracking_spk()
    {
        $spk = $this->input->get('spk') ?: $this->input->post('spk');

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
        $spk = $this->input->post('spk');

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

        $this->db->select('tbl_produksi.*, tbl_product.product_nama, tbl_product.kategori_id, tbl_kategori.kategori_nama, tbl_konsumen.konsumen_nama, tbl_satuan.satuan_nama, tbl_harga_product.ket_1 as sisi, tbl_perusahaan.nama_perusahaan as cabang_asal, tbl_pemesanan.created_at as tgl_spk, tbl_pemesanan.status_pembayaran, dk.nama_lengkap as deskprint_nama');
        $this->db->select('(SELECT mesin_nama FROM tbl_mesin m JOIN detail_produksi dp ON dp.mesin_id = m.mesin_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.mesin_id IS NOT NULL LIMIT 1) as mesin_nama');
        $this->db->select('(SELECT nama_lengkap FROM tbl_karyawan k JOIN detail_produksi dp ON dp.operator_id = k.karyawan_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.operator_id IS NOT NULL AND dp.status_produksi = \'2\' LIMIT 1) as nama_operator');
        $this->db->from('tbl_produksi');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id', 'left');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_produksi.konsumen_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id', 'left');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_produksi.perusahaan_id', 'left');
        $this->db->join('tbl_pemesanan', 'tbl_pemesanan.nospk = tbl_produksi.nospk', 'left');
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
        $ids_str = $this->input->get('ids');
        if (empty($ids_str))
            show_404();

        $ids = explode(',', $ids_str);

        $this->db->select('tbl_produksi.*, tbl_product.product_nama, tbl_product.kategori_id, tbl_kategori.kategori_nama, tbl_konsumen.konsumen_nama, tbl_satuan.satuan_nama, tbl_harga_product.ket_1 as sisi, tbl_perusahaan.nama_perusahaan as cabang_asal, tbl_pemesanan.created_at as tgl_spk, tbl_pemesanan.status_pembayaran, dk.nama_lengkap as deskprint_nama');
        $this->db->select('(SELECT mesin_nama FROM tbl_mesin m JOIN detail_produksi dp ON dp.mesin_id = m.mesin_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.mesin_id IS NOT NULL LIMIT 1) as mesin_nama');
        $this->db->select('(SELECT nama_lengkap FROM tbl_karyawan k JOIN detail_produksi dp ON dp.operator_id = k.karyawan_id WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.operator_id IS NOT NULL AND dp.status_produksi = \'2\' LIMIT 1) as nama_operator');
        $this->db->from('tbl_produksi');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id', 'left');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_produksi.konsumen_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id', 'left');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_produksi.perusahaan_id', 'left');
        $this->db->join('tbl_pemesanan', 'tbl_pemesanan.nospk = tbl_produksi.nospk', 'left');
        // Get Deskprint from SPK Number (2nd segment is user_id)
        $this->db->join('users u_dk', "SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_produksi.nospk, '-', 2), '-', -1) = u_dk.user_id", 'left', FALSE);
        $this->db->join('tbl_karyawan dk', 'dk.karyawan_id = u_dk.karyawan_id', 'left');
        $this->db->where_in('tbl_produksi.produksi_id', $ids);

        $data = [
            'items' => $this->db->get()->result(),
            'print_time' => date('Y-m-d H:i:s'),
            'company_name' => 'TGM ERP'
        ];

        $this->load->view('admin/wip/print_tag', $data);
    }

    public function get_production_data()
    {
        $detail_produksi_id = $this->input->post('detail_produksi_id');

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
        $detail_produksi_id = $this->input->post('detail_produksi_id');

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
        $detail_produksi_id = $this->input->post('detail_produksi_id');

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
        $detail_produksi_id = $this->input->post('detail_produksi_id');

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

    private function calculate_stock_reduction($detail_data, $qty_output, $panjang_output, $lebar_output)
    {
        try {
            $produksi_data = $this->db->get_where('tbl_produksi', ['produksi_id' => $detail_data->produksi_id])->row();
            $routing_data = $this->db->get_where('routing', ['routing_id' => $detail_data->routing_id])->row();

            if (!$produksi_data || !$routing_data) {
                return 0;
            }

            $output_area = $panjang_output * $lebar_output;
            $total_output_area = $output_area * $qty_output;

            $fields = $this->db->list_fields('bom_lines');
            $has_routing_id = in_array('routing_id', $fields);

            if ($has_routing_id) {
                $bom_data = $this->db->get_where('bom_lines', [
                    'product_id' => $produksi_data->product_id,
                    'routing_id' => $detail_data->routing_id
                ])->row();

                if ($bom_data) {
                    $consumption_rate = isset($bom_data->qty_required) ? $bom_data->qty_required : 1;
                    return $total_output_area * $consumption_rate;
                }
            }

            return $qty_output * $output_area;

        } catch (Exception $e) {
            log_message('error', 'Error in calculate_stock_reduction: ' . $e->getMessage());
            return 0;
        }
    }

    private function update_warehouse_stock($produksi_id, $detail_id, $material_id, $stock_reduction)
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
            $konversi = (float) ($material_info->konversi ?: 1);

            // Calculate reduction in Warehouse Unit (Satuan Gudang)
            // If production uses 50 sheets and conversion is 500, deduction is 50/500 = 0.1 RIM
            $warehouse_reduction = (float) $stock_reduction / $konversi;

            $produksi = $this->db->get_where('tbl_produksi', ['produksi_id' => $produksi_id])->row();
            if (!$produksi)
                return false;

            // Gudang taken from SPK print location
            $gudang_id = $produksi->lokasi_cetak ?: 1;

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

            $this->M_gudang->update_stock_with_log(
                $material_id,
                $gudang_id,
                -$warehouse_reduction, // Negative value for deduction
                'Produksi',
                $produksi_id,
                $catatan
            );

            return true;
        } catch (Exception $e) {
            log_message('error', 'Error in update_warehouse_stock: ' . $e->getMessage());
            return false;
        }
    }

    public function get_pb_verify_details_ajax()
    {
        $nospk = $this->input->post('nospk');

        $this->db->select('p.*, pr.product_nama, s.satuan_nama, pm.status_pembayaran');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_product pr', 'pr.product_id = p.product_id', 'left');
        $this->db->join('tbl_satuan s', 's.satuan_id = pr.satuan_id', 'left');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk', 'left');
        $this->db->where('p.nospk', $nospk);

        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->where('p.perusahaan_id', $perusahaan);
        }

        $items = $this->db->get()->result();

        // Payment validation check removed to allow "Proceed Anyway" from frontend.
        // Frontend handles the warning (handover function).
        /*
        if (!empty($items) && $items[0]->status_pembayaran !== 'paid') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'SPK belum divalidasi pembayarannya oleh Kasir.']);
            return;
        }
        */

        foreach ($items as $item) {
            $this->db->select('detail_produksi_id, qty_output');
            $this->db->where('produksi_id', $item->produksi_id);
            $this->db->order_by('routing_urutan', 'DESC');
            $this->db->order_by('detail_produksi_id', 'DESC');
            $this->db->limit(1);
            $last_step = $this->db->get('detail_produksi')->row();
            // Return ID for correction purpose
            $item->last_detail_id = $last_step ? $last_step->detail_produksi_id : null;
            // Fallback: If qty_output is 0, use the ordered qty (qty)
            $item->qty_final = ($last_step && $last_step->qty_output > 0) ? $last_step->qty_output : $item->qty;

            // Get all routing status for this item
            $this->db->select('r.routing_name, dp.status_produksi');
            $this->db->from('detail_produksi dp');
            $this->db->join('routing r', 'r.routing_id = dp.routing_id', 'left');
            $this->db->where('dp.produksi_id', $item->produksi_id);
            $this->db->order_by('dp.routing_urutan', 'ASC');
            $item->flow = $this->db->get()->result();
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'items' => $items]);
    }

    public function update_output_qty_ajax()
    {
        $level = $this->session->userdata('level');
        // Only Admin (1), Manager (17) or special level (21)
        if (!in_array((int) $level, [1, 17, 21], true)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk koreksi.']);
            return;
        }

        $id = $this->input->post('detail_id');
        $qty = $this->input->post('qty');

        if (!$id || $qty === null) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
            return;
        }

        $this->db->where('detail_produksi_id', $id);
        $update = $this->db->update('detail_produksi', ['qty_output' => $qty]);

        header('Content-Type: application/json');
        if ($update) {
            echo json_encode(['success' => true, 'message' => 'Output berhasil dikoreksi.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui database.']);
        }
    }

    public function get_spk_details_ajax()
    {
        // [FIX] Call this FIRST to prevent Query Builder state reset during query construction
        $current_operator_id = $this->get_operator_id_for_current_user();

        $nospk = $this->input->post('nospk');
        $cat_id = $this->input->post('kategori_id');

        $this->db->select('tbl_produksi.*, tbl_product.product_nama, tbl_product.kategori_id, tbl_satuan.satuan_nama, tbl_harga_product.ket_1 as sisi, tbl_harga_product.jenisprint_id, pm.status_pembayaran');
        $this->db->from('tbl_produksi');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id', 'left');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id', 'left');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = tbl_produksi.nospk', 'left');
        $this->db->where('tbl_produksi.nospk', $nospk);

        if ($cat_id) {
            $this->db->where('tbl_product.kategori_id', $cat_id);
        }

        $this->db->where_not_in('tbl_produksi.status_produksi', ['on_pb', 'on_costumer']);

        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');

        // $current_operator_id is now calculated at the top
        if ($current_operator_id === null && !in_array((int) $level, [1, 17], true)) {
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
            $this->db->select('bh.bahan_nama, bl.quantity as bom_qty, bl.bahan_id');
            $this->db->from('bom_lines bl');
            $this->db->join('tbl_bahan bh', 'bh.bahan_id = bl.bahan_id', 'left');
            $this->db->where('bl.product_id', $item->product_id);
            $item->bom = $this->db->get()->row();

            $this->db->select('dp.*, r.routing_name, m.mesin_nama, o.operator_nama, bl.bahan_id as routing_material_id');
            $this->db->from('detail_produksi dp');
            $this->db->join('routing r', 'dp.routing_id = r.routing_id', 'left');
            $this->db->join('bom_lines bl', 'r.bom_line_id = bl.bom_line_id', 'left');
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

            if (empty($routing)) {
                $this->db->where('product_id', $item->product_id);
                $this->db->order_by('routing_urutan', 'ASC');
                $this->db->order_by('routing_id', 'ASC');
                $master_routing = $this->db->get('routing')->result();

                if (!empty($master_routing)) {
                    foreach ($master_routing as $mr) {
                        $this->db->insert('detail_produksi', [
                            'produksi_id' => $item->produksi_id,
                            'routing_id' => $mr->routing_id,
                            'routing_urutan' => $mr->routing_urutan,
                            'operator_id' => $mr->operator_id,
                            'status_produksi' => '0',
                        ]);
                    }

                    $this->db->select('dp.*, r.routing_name, m.mesin_nama, o.operator_nama, bl.bahan_id as routing_material_id');
                    $this->db->from('detail_produksi dp');
                    $this->db->join('routing r', 'dp.routing_id = r.routing_id', 'left');
                    $this->db->join('bom_lines bl', 'r.bom_line_id = bl.bom_line_id', 'left');
                    $this->db->join('tbl_mesin m', 'm.mesin_id = dp.mesin_id', 'left');
                    $this->db->join('tbl_operator o', 'o.operator_id = dp.operator_id', 'left');
                    $this->db->where('dp.produksi_id', $item->produksi_id);
                    $this->db->order_by('dp.routing_urutan', 'ASC');
                    $this->db->order_by('dp.detail_produksi_id', 'ASC');
                    $routing = $this->db->get()->result();
                }
            }

            $produksi_items[$key]->routing = $routing;
        }

        echo json_encode([
            'items' => $produksi_items,
            'mesin' => $this->db->get_where('tbl_mesin', ['status' => '1'])->result(),
            'operator' => $this->db->get('tbl_operator')->result()
        ]);
    }

    public function get_production_control_json()
    {
        $produksi_id = $this->input->post('produksi_id');
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
        $can_access = false;

        // Allow Super Admin / Manager
        if (in_array((int) $level, [1, 17], true)) {
            $can_access = true;
        }
        // Allow specific Operator (Cross-branch access)
        elseif ($operator_id !== null) {
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

        $routing = $this->Produksi_model->get_detail_produksi_by_id($produksi_id, $operator_id);

        // If no routing yet, try to initialize it (matching get_spk_details_ajax logic)
        if (empty($routing)) {
            $this->db->where('product_id', $produksi->product_id);
            $this->db->order_by('routing_urutan', 'ASC');
            $master_routing = $this->db->get('routing')->result();

            if (!empty($master_routing)) {
                foreach ($master_routing as $mr) {
                    $this->db->insert('detail_produksi', [
                        'produksi_id' => $produksi_id,
                        'routing_id' => $mr->routing_id,
                        'routing_urutan' => $mr->routing_urutan,
                        'operator_id' => $mr->operator_id,
                        'status_produksi' => '0',
                    ]);
                }
                $routing = $this->Produksi_model->get_detail_produksi_by_id($produksi_id, $operator_id);
            }
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
        $pid = $this->input->post('produksi_id');
        $p = $this->input->post('panjang');
        $l = $this->input->post('lebar');

        if (empty($pid)) {
            echo json_encode(['success' => false, 'message' => 'ID Produksi kosong.']);
            return;
        }

        $prod = $this->db->get_where('tbl_produksi', ['produksi_id' => $pid])->row();
        if (!$prod) {
            echo json_encode(['success' => false, 'message' => 'Item tidak ditemukan.']);
            return;
        }

        $meta = !empty($prod->metadata) ? json_decode($prod->metadata, true) : [];
        $meta['panjang'] = (float) $p;
        $meta['lebar'] = (float) $l;

        $this->db->where('produksi_id', $pid);
        $res = $this->db->update('tbl_produksi', ['metadata' => json_encode($meta)]);

        echo json_encode(['success' => $res]);
    }

    public function proses_item($produksi_id = null)
    {
        if (empty($produksi_id))
            redirect('wip/produksi');

        $this->db->where('produksi_id', $produksi_id);
        $this->db->update('tbl_produksi', [
            'status_produksi' => 'proses'
        ]);

        $this->session->set_flashdata('message', '<div class="alert alert-success">Item produksi dimulai.</div>');
        redirect('wip/produksi');
    }

    public function selesai_spk($nospk = null)
    {
        if (empty($nospk))
            redirect('wip/produksi');

        $data = [
            'status_produksi' => 'done',
            'tgl_selesai' => date('Y-m-d H:i:s')
        ];

        $cat_id = $this->input->get('cat');

        if ($cat_id) {
            // Generate subquery for CI3
            $subquery = $this->db->select('tp.produksi_id')
                ->from('tbl_produksi tp')
                ->join('tbl_product tpr', 'tp.product_id = tpr.product_id')
                ->where('tp.nospk', $nospk)
                ->where('tpr.kategori_id', $cat_id)
                ->get_compiled_select();

            $this->db->where('status_produksi', 'proses');
            $this->db->where("produksi_id IN ($subquery)", NULL, FALSE);
        } else {
            $this->db->where('status_produksi', 'proses');
            $this->db->where('nospk', $nospk);
        }

        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->group_start();
            $this->db->where('lokasi_cetak', $perusahaan);
            $this->db->or_group_start();
            $this->db->where("(lokasi_cetak IS NULL OR lokasi_cetak = '' OR lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('perusahaan_id', $perusahaan);
            $this->db->group_end();
            $this->db->group_end();
        }

        $this->db->update('tbl_produksi', $data);

        $this->session->set_flashdata('message', '<div class="alert alert-success">SPK ' . $nospk . ' selesai. Barang dipindahkan ke PB.</div>');
        redirect('wip/produksi');
    }

    public function selesai_spk_ajax()
    {
        $nospk = $this->input->post('nospk');
        $cat_id = $this->input->post('cat');
        $pin = $this->input->post('pin_operator');

        if (empty($nospk) || empty($pin)) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
            return;
        }

        // Verify PIN (Filter by Company)
        $this->db->where('pin_produksi', $pin);
        $this->db->where('perusahaan_id', $this->session->userdata('perusahaan_id'));
        $operator = $this->db->get('tbl_karyawan')->row();
        if (!$operator) {
            echo json_encode(['success' => false, 'message' => 'PIN Operator tidak valid!']);
            return;
        }

        // 1. Cari Item yang akan diupdate (Select First)
        $this->db->select('tbl_produksi.produksi_id');
        $this->db->from('tbl_produksi');

        if ($cat_id) {
            $this->db->join('tbl_product pr', 'pr.product_id = tbl_produksi.product_id');
            $this->db->where('pr.kategori_id', $cat_id);
        }

        $this->db->where('tbl_produksi.nospk', $nospk);
        $this->db->where('tbl_produksi.status_produksi', 'proses');

        // Access Control (Perusahaan / Branch)
        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');

        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->group_start();
            $this->db->where('tbl_produksi.lokasi_cetak', $perusahaan);
            $this->db->or_group_start();
            $this->db->where("(tbl_produksi.lokasi_cetak IS NULL OR tbl_produksi.lokasi_cetak = '' OR tbl_produksi.lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('tbl_produksi.perusahaan_id', $perusahaan);
            $this->db->group_end();
            $this->db->group_end();
        }

        $items = $this->db->get()->result();

        if (empty($items)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada item "Proses" yang dapat diselesaikan.']);
            return;
        }

        $ids = array_column($items, 'produksi_id');

        // 2. Transaction Update
        $this->db->trans_start();

        $update_data = [
            'status_produksi' => 'done',
            'tgl_selesai' => date('Y-m-d H:i:s')
            // Optional: 'operator_selesai' => $operator->karyawan_id
        ];

        $this->db->where_in('produksi_id', $ids);
        $this->db->update('tbl_produksi', $update_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->output->set_status_header(500);
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status produksi.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'SPK ' . $nospk . ' berhasil diselesaikan oleh ' . $operator->nama_lengkap]);
        }
    }

    public function process_all_ready_to_pb_ajax()
    {
        $cat_id = $this->input->post('cat_id');
        $pin = $this->input->post('pin_operator');

        if (empty($cat_id) || empty($pin)) {
            echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap.']);
            return;
        }

        // 1. Verify PIN & Perusahaan
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $operator = $this->db->get_where('tbl_karyawan', [
            'pin_produksi' => $pin,
            'perusahaan_id' => $perusahaan_id
        ])->row();

        if (!$operator) {
            echo json_encode(['success' => false, 'message' => 'PIN tidak valid atau akses ditolak!']);
            return;
        }

        // 2. Query Optimal: Mencari nospk yang punya item 'done' dan TIDAK punya item 'pending/proses'
        // Logic ini harus identik dengan cara kita menghitung $stats['ready_to_pb'] di index dashboard.
        $this->db->select('tp.nospk');
        $this->db->from('tbl_produksi tp');
        $this->db->join('tbl_product pr', 'pr.product_id = tp.product_id');

        $this->db->where('pr.kategori_id', $cat_id);
        $this->db->where_in('pr.tipe_id', [1, 2]); // Samakan dengan tipe_id di list utama

        // Filter Lokasi (Samakan dengan logic list utama)
        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->group_start();
            $this->db->where('tp.lokasi_cetak', $perusahaan_id);
            $this->db->or_group_start();
            $this->db->where("(tp.lokasi_cetak IS NULL OR tp.lokasi_cetak = '' OR tp.lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('tp.perusahaan_id', $perusahaan_id);
            $this->db->group_end();
            $this->db->group_end();
        }

        $this->db->group_by('tp.nospk');

        // KRITERIA SIAP PB:
        // 1. Minimal ada 1 item yang statusnya 'done'
        $this->db->having('SUM(CASE WHEN tp.status_produksi = "done" THEN 1 ELSE 0 END) > 0', NULL, FALSE);
        // 2. TIDAK ADA item yang statusnya masih 'pending' atau 'proses' (harus tuntas)
        $this->db->having('SUM(CASE WHEN tp.status_produksi IN ("pending", "approved", "proses", "0", "") THEN 1 ELSE 0 END) = 0', NULL, FALSE);

        $valid_spks = $this->db->get()->result_array();

        if (empty($valid_spks)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada SPK (Category ID: ' . $cat_id . ') yang siap dikirim. Pastikan semua item di grup ini sudah berstatus DONE (100%).']);
            return;
        }

        $list_nospk = array_column($valid_spks, 'nospk');

        // 3. Update Batch dalam Transaksi Atomic
        $this->db->trans_start();

        // Cari ID semua item yang terlibat
        $this->db->select('tp.produksi_id');
        $this->db->from('tbl_produksi tp');
        $this->db->join('tbl_product pr', 'pr.product_id = tp.product_id');
        $this->db->where_in('tp.nospk', $list_nospk);
        $this->db->where('pr.kategori_id', $cat_id);
        $this->db->where('tp.status_produksi', 'done');
        $ids_to_update = array_column($this->db->get()->result_array(), 'produksi_id');

        if (!empty($ids_to_update)) {
            $timestamp = date('Y-m-d H:i:s');

            // Update Tabel Utama
            $this->db->where_in('produksi_id', $ids_to_update);
            $this->db->update('tbl_produksi', [
                'status_produksi' => 'on_pb',
                'tgl_kirim_pb' => $timestamp
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal memproses pengiriman batch.']);
        } else {
            echo json_encode([
                'success' => true,
                'message' => count($list_nospk) . ' SPK (' . count($ids_to_update) . ' Item) berhasil dikirim ke PB.',
                'count' => count($list_nospk)
            ]);
        }
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
        $this->db->select('p.*, pr.product_nama, pr.kategori_id, pr.tipe_id, pr.satuan_id, k.kategori_nama, s.satuan_nama, ko.konsumen_nama, hp.ket_1 as sisi, hp.jenisprint_id');
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

            // B. Fetch Routing Steps for THIS specific item
            $this->db->select('dp.*, r.routing_name, r.routing_urutan, m.mesin_nama, k.nama_lengkap as operator_nama, bl.quantity as step_bom_qty, bl.bahan_id as bom_bahan_id, bh.bahan_nama as bom_bahan_nama, bl.panjang as bom_p, bl.lebar as bom_l');
            $this->db->from('detail_produksi dp');
            $this->db->join('routing r', 'dp.routing_id = r.routing_id', 'left');
            $this->db->join('tbl_mesin m', 'm.mesin_id = dp.mesin_id', 'left');
            $this->db->join('tbl_karyawan k', 'k.karyawan_id = dp.operator_id', 'left');
            // Join with BOM and Bahan to get Material Name
            $this->db->join('bom_lines bl', 'bl.product_id = ' . (int) $item->product_id . ' AND bl.bahan_id IS NOT NULL', 'left');
            $this->db->join('tbl_bahan bh', 'bh.bahan_id = bl.bahan_id', 'left');
            $this->db->where('dp.produksi_id', $item->produksi_id);
            $this->db->group_by('dp.detail_produksi_id'); // PREVENT DUPLICATES if multiple BOM lines exist
            $this->db->order_by('r.routing_urutan', 'ASC');
            $this->db->order_by('dp.detail_produksi_id', 'ASC'); // Stable sort

            $steps = $this->db->get()->result();
            // Ensure routing_name fallback
            foreach ($steps as &$s) {
                if (empty($s->routing_name))
                    $s->routing_name = "Proses " . ($s->routing_urutan ?: "");
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
                } elseif ($step->status_produksi == '0' && !$foundCurrent && $done_s == ($step->routing_urutan - 1)) {
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

        $data['items'] = $items;
        $data['spk_header'] = $items[0]; // Use first item for common headers
        $data['qc_passed_count'] = 0; // Placeholder

        // Fetch Material List (Common for all)
        $data['bahan_list'] = $this->db->select('m.material_id as bahan_id, m.bahan_id as raw_bahan_id, m.varian as bahan_nama, sg.qty as stok, s.satuan_nama as satuan')
            ->from('tbl_material m')
            ->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id AND sg.gudang_id = 1', 'left')
            ->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left')
            ->where('m.status', '1') // Active materials only
            ->order_by('m.varian', 'ASC')
            ->get()->result();

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
        $detail_id = $this->input->post('detail_id');
        $produksi_id = $this->input->post('produksi_id');
        $op_id = $this->input->post('operator_id');

        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            $check = $this->db->select('produksi_id')
                ->from('tbl_produksi')
                ->where('produksi_id', $produksi_id)
                ->group_start()
                ->where('lokasi_cetak', $perusahaan)
                ->or_group_start()
                ->where("(lokasi_cetak IS NULL OR lokasi_cetak = '' OR lokasi_cetak = '0')", NULL, FALSE)
                ->where('perusahaan_id', $perusahaan)
                ->group_end()
                ->group_end()
                ->get()->num_rows();

            if ($check == 0) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Akses ditolak: Branch tidak sesuai.</div>');
                redirect('wip/produksi');
                return;
            }
        }

        // Fetch existing data for additive logic
        $existing = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $detail_id])->row();

        $qty_pakai_input = (float) $this->input->post('qty_bahan_pakai');
        $qty_rusak_input = (float) $this->input->post('qty_bahan_rusak');

        // Final values for DB (Add to existing if they are not NULL)
        $new_qty_pakai = (float) ($existing->qty_bahan_pakai ?? 0) + $qty_pakai_input;
        $new_qty_rusak = (float) ($existing->qty_bahan_rusak ?? 0) + $qty_rusak_input;

        $data_update = [
            'status_produksi' => '2',
            'qty_output' => $this->input->post('qty_output'),
            'qty_missing' => $this->input->post('qty_failed'),
            'qty_bahan_pakai' => $new_qty_pakai,
            'qty_bahan_rusak' => $new_qty_rusak,
            'panjang_output' => $this->input->post('panjang_output') ?: 0,
            'lebar_output' => $this->input->post('lebar_output') ?: 0,
            'material_id' => $this->input->post('material_id'),
            'operator_id' => !empty($op_id) ? $op_id : $existing->operator_id,
            'alasan_missing' => $this->input->post('alasan_failed'),
            'catatan' => $this->input->post('catatan'),
            'waktu_selesai' => date('Y-m-d H:i:s')
        ];

        $this->db->trans_start();

        $this->db->where('detail_produksi_id', $detail_id);
        $this->db->update('detail_produksi', $data_update);

        // REAL-TIME STOCK REDUCTION (Only for the DELTA/INPUT part)
        $material_id = $this->input->post('material_id');
        if ($material_id && ($qty_pakai_input > 0 || $qty_rusak_input > 0)) {
            $delta_reduced = $qty_pakai_input + $qty_rusak_input;
            $this->update_warehouse_stock($produksi_id, $detail_id, $material_id, $delta_reduced);
        }
    }

    /**
     * AJAX: Advance Production    /**
     * Handles 'Start' and 'Finish' actions for routing steps
     */
    public function advance_step_ajax()
    {
        // Enforce JSON check or login check
        if (!$this->input->is_ajax_request())
            show_404();

        $produksi_id = $this->input->post('produksi_id');

        // SAFETY CHECK: Ensure this legacy function is NOT used for Large Format (Category 2)
        // This prevents regressions in A3+ when LF logic is modified elsewhere.
        $check = $this->db->select('kategori_id')->from('tbl_product pr')->join('tbl_produksi p', 'p.product_id = pr.product_id')->where('p.produksi_id', $produksi_id)->get()->row();
        if ($check && $check->kategori_id == 2) {
            echo json_encode(['success' => false, 'message' => 'System Routing Error: LF items must use their own dedicated processor.']);
            return;
        }

        // Get Inputs
        $action_type = $this->input->post('type'); // 'start' or 'next'

        // Verify PIN if provided
        $pin = $this->input->post('pin_operator');
        $operator_id = null;
        if (!empty($pin)) {
            $this->db->where('pin_produksi', $pin);
            $this->db->where('perusahaan_id', $this->session->userdata('perusahaan_id'));
            $operator = $this->db->get('tbl_karyawan')->row();
            if (!$operator) {
                echo json_encode(['success' => false, 'message' => 'PIN Operator tidak valid!']);
                return;
            }
            $operator_id = $operator->karyawan_id;
        }

        // If no operator/PIN provided for starting, we should probably reject it now as per new policy
        if (!$operator_id && $action_type === 'start') {
            echo json_encode(['success' => false, 'message' => 'Harap masukkan PIN Operator untuk memulai.']);
            return;
        }

        if (!$produksi_id) {
            echo json_encode(['success' => false, 'message' => 'ID Produksi Invalid']);
            return;
        }

        $this->db->trans_start();

        // 1. Get current steps
        $this->db->select('*');
        $this->db->from('detail_produksi');
        $this->db->where('produksi_id', $produksi_id);
        $this->db->order_by('routing_urutan', 'ASC');
        $steps = $this->db->get()->result();

        // CASE A: SINGLE STEP ITEM (No Routing)
        if (empty($steps)) {
            $this->db->where('produksi_id', $produksi_id);
            $item = $this->db->get('tbl_produksi')->row();

            if ($item) {
                if ($item->status_produksi == 'pending' || $item->status_produksi == 'approved' || $item->status_produksi == '') {
                    $this->db->where('produksi_id', $produksi_id);
                    $this->db->update('tbl_produksi', ['status_produksi' => 'proses', 'update_time' => date('Y-m-d H:i:s')]);
                } elseif ($item->status_produksi == 'proses') {
                    $this->db->where('produksi_id', $produksi_id);
                    $this->db->update('tbl_produksi', ['status_produksi' => 'done', 'update_time' => date('Y-m-d H:i:s')]);
                }
            }
            $this->db->trans_complete();
            echo json_encode(['success' => true]);
            return;
        }

        // CASE B: MULTI-STEP ROUTING
        $currentInProcess = null;
        $nextPending = null;

        foreach ($steps as $s) {
            if ($s->status_produksi == '1') {
                $currentInProcess = $s;
            }
            if ($s->status_produksi == '0' && !$nextPending) {
                $nextPending = $s;
            }
        }

        // ACTION LOGIC based on 'type' parameter or auto-detect
        if ($action_type === 'next' && $currentInProcess) {
            // FINISH CURRENT STEP
            $qty_pakai_input = (float) $this->input->post('qty_pakai');
            $qty_rusak_input = (float) $this->input->post('qty_rusak');
            $bahan_id = $this->input->post('bahan_id'); // Get Selected Material ID

            // Additive logic for reprint/revision
            $new_qty_pakai = (float) ($currentInProcess->qty_bahan_pakai ?? 0) + $qty_pakai_input;
            $new_qty_rusak = (float) ($currentInProcess->qty_bahan_rusak ?? 0) + $qty_rusak_input;

            $this->db->where('detail_produksi_id', $currentInProcess->detail_produksi_id);
            $this->db->update('detail_produksi', [
                'status_produksi' => '2',
                'waktu_selesai' => date('Y-m-d H:i:s'),
                'qty_bahan_pakai' => $new_qty_pakai,
                'qty_bahan_rusak' => $new_qty_rusak,
                'qty_output' => $qty_pakai_input, // Record the actual output quantity
                'operator_id' => !empty($operator_id) ? $operator_id : $currentInProcess->operator_id,
                'material_id' => !empty($bahan_id) ? $bahan_id : NULL
            ]);

            // Stock Deduction (Only for A3+ logic here in this function)
            if (!empty($bahan_id) && ($qty_pakai_input > 0 || $qty_rusak_input > 0)) {
                $delta_reduced = $qty_pakai_input + $qty_rusak_input;
                $this->update_warehouse_stock($produksi_id, $currentInProcess->detail_produksi_id, $bahan_id, $delta_reduced);
            }

            // Check if this was the last step
            $remaining = 0;
            foreach ($steps as $s) {
                if ($s->detail_produksi_id !== $currentInProcess->detail_produksi_id && $s->status_produksi != '2') {
                    $remaining++;
                }
            }

            if ($remaining === 0) {
                $this->db->where('produksi_id', $produksi_id);
                $this->db->update('tbl_produksi', [
                    'status_produksi' => 'done',
                    'update_time' => date('Y-m-d H:i:s')
                ]);
            } else {
                $this->db->where('produksi_id', $produksi_id);
                $this->db->update('tbl_produksi', ['status_produksi' => 'proses', 'update_time' => date('Y-m-d H:i:s')]);
            }

        } elseif ($action_type === 'start' && $nextPending) {
            // START NEXT PENDING STEP
            $mesin_id = $this->input->post('mesin_id');
            $lokasi_proses = $this->input->post('lokasi_proses');

            $update_step = [
                'status_produksi' => '1',
                'waktu_mulai' => date('Y-m-d H:i:s'),
                'operator_id' => $operator_id
            ];

            if ($mesin_id)
                $update_step['mesin_id'] = $mesin_id;
            if ($lokasi_proses)
                $update_step['lokasi_proses'] = $lokasi_proses;

            $this->db->where('detail_produksi_id', $nextPending->detail_produksi_id);
            $this->db->update('detail_produksi', $update_step);

            $this->db->where('produksi_id', $produksi_id);
            $this->db->update('tbl_produksi', ['status_produksi' => 'proses', 'update_time' => date('Y-m-d H:i:s')]);

        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Database Error']);
        } else {
            echo json_encode(['success' => true]);
        }
    }

    public function get_machines_ajax()
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $this->db->select('mesin_id, mesin_nama, kategori');
        $this->db->from('tbl_mesin');
        $this->db->where('status', '1');
        // Include global machines (perusahaan_id 0) and branch-specific machines
        $this->db->group_start();
        $this->db->where('perusahaan_id', $perusahaan_id);
        $this->db->or_where('perusahaan_id', 0);
        $this->db->group_end();

        $machines = $this->db->get()->result_array();
        echo json_encode($machines);
    }

    /**
     * DEDICATED Function for Large Format (Avoid touching A3+)
     */
    public function advance_step_lf_ajax()
    {
        if (!$this->input->is_ajax_request())
            show_404();

        $produksi_id = $this->input->post('produksi_id');
        $action_type = $this->input->post('type');
        $pin = $this->input->post('pin_operator');

        $operator_id = null;
        if (!empty($pin)) {
            $operator = $this->db->get_where('tbl_karyawan', [
                'pin_produksi' => $pin,
                'perusahaan_id' => $this->session->userdata('perusahaan_id')
            ])->row();

            if (!$operator) {
                echo json_encode(['success' => false, 'message' => 'PIN Operator tidak valid!']);
                return;
            }
            $operator_id = $operator->karyawan_id;
        }

        if (!$operator_id && $action_type === 'start') {
            echo json_encode(['success' => false, 'message' => 'Harap masukkan PIN Operator untuk memulai.']);
            return;
        }

        $this->db->trans_start();

        $this->db->select('*')->from('detail_produksi')->where('produksi_id', $produksi_id)->order_by('routing_urutan', 'ASC');
        $steps = $this->db->get()->result();

        $currentInProcess = null;
        $nextPending = null;
        foreach ($steps as $s) {
            if ($s->status_produksi == '1')
                $currentInProcess = $s;
            if ($s->status_produksi == '0' && !$nextPending)
                $nextPending = $s;
        }

        if ($action_type === 'next' && $currentInProcess) {
            $qty_pakai_input = (float) $this->input->post('qty_pakai');
            $qty_rusak_input = (float) $this->input->post('qty_rusak');
            $bahan_id = $this->input->post('bahan_id');

            $new_qty_pakai = (float) ($currentInProcess->qty_bahan_pakai ?? 0) + $qty_pakai_input;
            $new_qty_rusak = (float) ($currentInProcess->qty_bahan_rusak ?? 0) + $qty_rusak_input;

            $this->db->where('detail_produksi_id', $currentInProcess->detail_produksi_id);
            $this->db->update('detail_produksi', [
                'status_produksi' => '2',
                'waktu_selesai' => date('Y-m-d H:i:s'),
                'qty_bahan_pakai' => $new_qty_pakai,
                'qty_bahan_rusak' => $new_qty_rusak,
                'qty_output' => $qty_pakai_input, // Record the actual output quantity
                'operator_id' => !empty($operator_id) ? $operator_id : $currentInProcess->operator_id,
                'material_id' => !empty($bahan_id) ? $bahan_id : NULL
            ]);

            // [REFACTORED] FLEXIBLE UNIT SYSTEM: Check product unit type
            if (!empty($bahan_id) && ($qty_pakai_input > 0 || $qty_rusak_input > 0)) {
                // Get product info to determine unit type
                $prod_info = $this->db->select('p.*, pr.product_nama, s.satuan_nama, s.satuan_id')
                    ->from('tbl_produksi p')
                    ->join('tbl_product pr', 'pr.product_id = p.product_id')
                    ->join('tbl_satuan s', 's.satuan_id = pr.satuan_id', 'left')
                    ->where('p.produksi_id', $produksi_id)
                    ->get()->row();

                if ($prod_info) {
                    $unit_type = strtolower($prod_info->satuan_nama ?? 'meter');

                    // Determine if this is a PCS-based or Meter-based product
                    $is_pcs_based = in_array($unit_type, ['pcs', 'unit', 'lembar', 'set']);

                    if ($is_pcs_based) {
                        // PCS-based: Calculate area from dimensions (P x L x Qty)
                        $this->update_warehouse_stock_lf_pcs($produksi_id, $currentInProcess->detail_produksi_id, $bahan_id, ($qty_pakai_input + $qty_rusak_input), $prod_info);
                    } else {
                        // Meter-based: Direct meter usage
                        $this->update_warehouse_stock_lf($produksi_id, $currentInProcess->detail_produksi_id, $bahan_id, ($qty_pakai_input + $qty_rusak_input));
                    }
                }
            }

            // Finalize SPK Status
            $rem = $this->db->where(['produksi_id' => $produksi_id, 'status_produksi !=' => '2'])->count_all_results('detail_produksi');
            if ($rem === 0) {
                $this->db->where('produksi_id', $produksi_id)->update('tbl_produksi', [
                    'status_produksi' => 'done',
                    'update_time' => date('Y-m-d H:i:s')
                ]);
            } else {
                $this->db->where('produksi_id', $produksi_id)->update('tbl_produksi', [
                    'status_produksi' => 'proses',
                    'update_time' => date('Y-m-d H:i:s')
                ]);
            }
        } elseif ($action_type === 'start' && $nextPending) {
            $mesin_id = $this->input->post('mesin_id');
            $lokasi_proses = $this->input->post('lokasi_proses');

            $update_step = [
                'status_produksi' => '1',
                'waktu_mulai' => date('Y-m-d H:i:s'),
                'operator_id' => $operator_id
            ];

            if ($mesin_id)
                $update_step['mesin_id'] = $mesin_id;
            if ($lokasi_proses)
                $update_step['lokasi_proses'] = $lokasi_proses;

            $this->db->where('detail_produksi_id', $nextPending->detail_produksi_id)->update('detail_produksi', $update_step);
            $this->db->where('produksi_id', $produksi_id)->update('tbl_produksi', [
                'status_produksi' => 'proses',
                'update_time' => date('Y-m-d H:i:s')
            ]);
        }

        $this->db->trans_complete();
        echo json_encode(['success' => ($this->db->trans_status() !== FALSE)]);
    }

    /**
     * DEDICATED Warehouse Stock Update for Large Format (Meter based)
     */
    private function update_warehouse_stock_lf($produksi_id, $detail_id, $material_id, $stock_reduction)
    {
        // 1. Get material info specifically for LF logic
        $this->load->model('M_gudang');
        $mat = $this->db->select('m.*, s.satuan_nama as satuan_prod')
            ->from('tbl_material m')
            ->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left')
            ->join('tbl_satuan s', 'b.satuan_id = s.satuan_id', 'left')
            ->where('m.material_id', $material_id)
            ->get()->row();

        if (!$mat)
            return false;

        $konversi = (float) $mat->konversi ?: 1;
        $wh_reduction = (float) $stock_reduction / $konversi;

        $p = $this->db->get_where('tbl_produksi', ['produksi_id' => $produksi_id])->row();
        if (!$p)
            return false;

        $gudang_id = $p->lokasi_cetak ?: 1;
        $catatan = "Produksi LF SPK: $p->nospk. Pakai: $stock_reduction " . ($mat->satuan_prod ?: 'm') . " (Step: $detail_id)";

        return $this->M_gudang->update_stock_with_log(
            $material_id,
            $gudang_id,
            -$wh_reduction,
            'Produksi',
            $produksi_id,
            $catatan
        );
    }

    /**
     * [NEW] Warehouse Stock Update for PCS-based Large Format Products
     * Calculates area from dimensions (P x L x Qty) then deducts from warehouse
     * 
     * @param int $produksi_id Production ID
     * @param int $detail_id Detail production step ID
     * @param int $material_id Material/Roll ID to deduct from
     * @param float $qty_input Quantity in PCS (from operator input)
     * @param object $prod_info Product information including dimensions
     */
    private function update_warehouse_stock_lf_pcs($produksi_id, $detail_id, $material_id, $qty_input, $prod_info)
    {
        // 1. Get dimensions from item or BOM
        $panjang = (float) ($prod_info->panjang ?: 0);
        $lebar = (float) ($prod_info->lebar ?: 0);
        $qty = (int) ($prod_info->qty ?: 1);

        // If dimensions missing, try to get from BOM
        if ($panjang == 0 || $lebar == 0) {
            $bom = $this->db->select('panjang, lebar')
                ->from('bom_lines')
                ->where('product_id', $prod_info->product_id)
                ->get()->row();

            if ($bom) {
                $panjang = $panjang ?: (float) $bom->panjang;
                $lebar = $lebar ?: (float) $bom->lebar;
            }
        }

        // 2. Calculate actual area usage in m²
        // For PCS products: Area = P x L x Qty
        $area_per_unit = $panjang * $lebar;
        $total_area_m2 = $area_per_unit * $qty;

        // 3. If operator input differs from order qty (waste/reprint), use operator input
        if ($qty_input > 0 && $qty_input != $qty) {
            $total_area_m2 = $area_per_unit * $qty_input;
        }

        // 4. Deduct from warehouse using meter-based logic
        if ($total_area_m2 > 0) {
            $this->load->model('M_gudang');
            $mat = $this->db->select('m.*, s.satuan_nama as satuan_prod')
                ->from('tbl_material m')
                ->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left')
                ->join('tbl_satuan s', 'b.satuan_id = s.satuan_id', 'left')
                ->where('m.material_id', $material_id)
                ->get()->row();

            if (!$mat)
                return false;

            $konversi = (float) $mat->konversi ?: 1;
            $wh_reduction = (float) $total_area_m2 / $konversi;

            $gudang_id = $prod_info->lokasi_cetak ?: 1;
            $catatan = "Produksi LF (PCS) SPK: {$prod_info->nospk}. {$prod_info->product_nama}. Area: {$total_area_m2}m² ({$panjang}x{$lebar}x{$qty} pcs). Step: $detail_id";

            return $this->M_gudang->update_stock_with_log(
                $material_id,
                $gudang_id,
                -$wh_reduction,
                'Produksi',
                $produksi_id,
                $catatan
            );
        }

        return false;
    }



    public function proses_routing($detail_id = null)
    {
        if (empty($detail_id))
            redirect('wip/produksi');

        $mesin_id = $this->input->get('mesin_id');
        $operator_id = $this->input->get('operator_id');
        $redir = $this->input->get('redir');

        // Security Check: Verify branch access
        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            $check = $this->db->select('dp.detail_produksi_id')
                ->from('detail_produksi dp')
                ->join('tbl_produksi p', 'p.produksi_id = dp.produksi_id')
                ->where('dp.detail_produksi_id', $detail_id)
                ->group_start()
                ->where('p.lokasi_cetak', $perusahaan)
                ->or_group_start()
                ->where("(p.lokasi_cetak IS NULL OR p.lokasi_cetak = '' OR p.lokasi_cetak = '0')", NULL, FALSE)
                ->where('p.perusahaan_id', $perusahaan)
                ->group_end()
                ->group_end()
                ->get()->num_rows();

            if ($check == 0) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Akses ditolak: Item ini bukan milik cabang Anda.</div>');
                redirect('wip/produksi');
                return;
            }
        }

        $update_data = [
            'status_produksi' => '1',
            'waktu_mulai' => date('Y-m-d H:i:s')
        ];
        if ($mesin_id)
            $update_data['mesin_id'] = $mesin_id;
        if ($operator_id)
            $update_data['operator_id'] = $operator_id;

        $this->db->where('detail_produksi_id', $detail_id);
        $this->db->update('detail_produksi', $update_data);

        $detail = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $detail_id])->row();
        if ($detail) {
            $this->db->where('produksi_id', $detail->produksi_id);
            $this->db->update('tbl_produksi', ['status_produksi' => 'proses']);
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success">Tahapan produksi dimulai.</div>');
        if ($redir) {
            redirect('wip/control_center/' . $detail->produksi_id);
        } else {
            redirect('wip/produksi');
        }
    }

    public function selesai_routing($detail_id = null)
    {
        if (empty($detail_id))
            redirect('wip/produksi');

        $redir = $this->input->get('redir');

        $this->db->where('detail_produksi_id', $detail_id);
        $this->db->update('detail_produksi', [
            'status_produksi' => '2',
            'waktu_selesai' => date('Y-m-d H:i:s')
        ]);

        $detail = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $detail_id])->row();
        if ($detail) {
            $produksi_id = $detail->produksi_id;
            $all_steps = $this->db->get_where('detail_produksi', ['produksi_id' => $produksi_id])->result();
            $is_all_done = true;
            foreach ($all_steps as $step) {
                if ($step->status_produksi != '2') {
                    $is_all_done = false;
                    break;
                }
            }
            if ($is_all_done && count($all_steps) > 0) {
                $this->db->where('produksi_id', $produksi_id);
                $this->db->update('tbl_produksi', ['status_produksi' => 'done', 'tgl_selesai' => date('Y-m-d H:i:s')]);
            }
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success">Tahapan produksi diselesaikan.</div>');
        if ($redir && $detail) {
            redirect('wip/control_center/' . $detail->produksi_id);
        } else {
            redirect('wip/produksi');
        }
    }

    public function kirim_pb_spk($nospk = null)
    {
        if (empty($nospk)) {
            redirect('wip/produksi');
        }

        $data = [
            'status_produksi' => 'on_pb',
            'tgl_kirim_pb' => date('Y-m-d H:i:s')
        ];

        $cat_id = $this->input->get('cat');

        if ($cat_id) {
            // Generate subquery subquery
            $subquery = $this->db->select('tp.produksi_id')
                ->from('tbl_produksi tp')
                ->join('tbl_product tpr', 'tp.product_id = tpr.product_id')
                ->where('tp.nospk', $nospk)
                ->where('tpr.kategori_id', $cat_id)
                ->get_compiled_select();

            $this->db->where('status_produksi', 'done');
            $this->db->where("produksi_id IN ($subquery)", NULL, FALSE);
        } else {
            $this->db->where('status_produksi', 'done');
            $this->db->where('nospk', $nospk);
        }

        $this->db->update('tbl_produksi', $data);

        $this->db->select('tp.produksi_id');
        $this->db->from('tbl_produksi tp');
        $this->db->where('tp.nospk', $nospk);
        if ($cat_id) {
            $this->db->join('tbl_product tpr', 'tp.product_id = tpr.product_id', 'left');
            $this->db->where('tpr.kategori_id', $cat_id);
        }
        $prod_ids = array_column($this->db->get()->result_array(), 'produksi_id');

        if (!empty($prod_ids)) {
            $this->db->where_in('produksi_id', $prod_ids);
            $this->db->update('detail_produksi', ['status_produksi' => 'on_pb']);
        }

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('message', '<div class="alert alert-success">SPK ' . $nospk . ' berhasil dikirim ke Packing Barang (PB).</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-warning">Tidak ada item berstatus selesai yang dipindahkan.</div>');
        }

        redirect('wip/produksi');
    }
    public function verify_operator_pin()
    {
        $operator_id = $this->input->post('operator_id');
        $pin = $this->input->post('pin');

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
            $nospk = $this->input->post('spk');
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
            'detailProduk' => $this->M_transaksi->getDetailPesan($nospk)->result(),
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
            $nospk = $this->input->get('nospk');
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

    private function _ensure_detail_produksi($pid)
    {
        $check = $this->db->get_where('detail_produksi', ['produksi_id' => $pid])->num_rows();
        if ($check > 0)
            return;

        $p = $this->db->get_where('tbl_produksi', ['produksi_id' => $pid])->row();
        if (!$p)
            return;

        $master = $this->db->where('product_id', $p->product_id)
            ->order_by('routing_urutan', 'ASC')
            ->order_by('routing_id', 'ASC')
            ->get('routing')->result();

        if (!empty($master)) {
            foreach ($master as $mr) {
                $this->db->insert('detail_produksi', [
                    'produksi_id' => $pid,
                    'routing_id' => $mr->routing_id,
                    'routing_urutan' => $mr->routing_urutan,
                    'operator_id' => $mr->operator_id,
                    'status_produksi' => '0',
                ]);
            }
        }
    }

    /**
     * Helper to get Operator ID for WIP Filtering
     * Added here to ensure availability even if MY_Controller is not updated on server
     */
    protected function get_operator_id_for_current_user()
    {
        $user_id = $this->session->userdata('id');
        $role_id = (int) $this->session->userdata('level');
        $username = $this->session->userdata('username');

        // 1. Super Admin / Owner / Developer Bypass
        // Aligned with other access checks in Wip.php (1=Admin, 17=Owner)
        if ($username === 'nizarsuryadi' || in_array($role_id, [1, 17], true)) {
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
    private function _process_stock_deduction($detail_id)
    {
        // 1. Get Detail Info & BOM Mapping
        $this->db->select('dp.produksi_id, dp.routing_id, r.bom_line_id');
        $this->db->from('detail_produksi dp');
        $this->db->join('routing r', 'r.routing_id = dp.routing_id');
        $this->db->where('dp.detail_produksi_id', $detail_id);
        $detail = $this->db->get()->row();

        if (!$detail || empty($detail->bom_line_id))
            return;

        // 2. Get Production Data (Qty to produce)
        $prod = $this->db->get_where('tbl_produksi', ['produksi_id' => $detail->produksi_id])->row();
        if (!$prod)
            return;

        // 3. Get BOM Line Data (Material Usage per Unit)
        $bom = $this->db->get_where('bom_lines', ['bom_line_id' => $detail->bom_line_id])->row();
        if (!$bom)
            return;

        // 3. Calculate Total Usage
        $qty_usage = (float) $bom->quantity * (float) $prod->qty;

        // 4. Deduct Stock (Using Model if available, or direct DB)
        // Check if M_gudang has exact function, else direct update
        // We need to find `material_id` from `bahan_id` first.
        $material = $this->db->get_where('tbl_material', ['bahan_id' => $bom->bahan_id])->row();

        if ($material) {
            // DEDUCT FROM TEKNISI/PRODUCTION WAREHOUSE OR GENERAL?
            // For now, simple deduction from 'tbl_stok_gudang' where material_id matches
            // Ideally, we deduct from a specific roll/batch, but here we just take the first available or aggregate.

            // Logic: Find stock record, decrease qty.
            // Priority: Gudang Produksi (if concept exists) -> Gudang Utama.

            // Simple Implementation: Reduce from total stock (First found record)
            $stok = $this->db->order_by('qty', 'DESC')->get_where('tbl_stok_gudang', ['material_id' => $material->material_id])->row();

            if ($stok) {
                $new_qty = $stok->qty - $qty_usage;
                $this->db->where('id_stok', $stok->id_stok);
                $this->db->update('tbl_stok_gudang', ['qty' => $new_qty]);

                // Log Transaction (Optional but recommended)
                /*
                $this->db->insert('tbl_log_stok', [
                    'material_id' => $material->material_id,
                    'qty' => $qty_usage,
                    'tipe' => 'out',
                    'keterangan' => 'Produksi WIP #' . $prod->nospk,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                */
            }
        }
    }

    /**
     * [NEW] RIP Batch Execution for Large Format
     * Processes multiple items at once, marks RIP steps as complete
     * 
     * @return void response with success status and processed items
     */
    public function execute_rip_batch_ajax()
    {
        if (!$this->input->is_ajax_request())
            show_404();

        $item_ids = $this->input->post('item_ids'); // Array of produksi_id
        $operator_pin = $this->input->post('pin_operator');

        // Verify PIN
        $operator = $this->db->get_where('tbl_karyawan', [
            'pin_produksi' => $operator_pin,
            'perusahaan_id' => $this->session->userdata('perusahaan_id')
        ])->row();

        if (!$operator) {
            echo json_encode(['success' => false, 'message' => 'PIN Operator tidak valid!']);
            return;
        }

        if (empty($item_ids) || !is_array($item_ids)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada item yang dipilih']);
            return;
        }

        $this->db->trans_start();

        $total_usage = 0;
        $processed_items = [];
        $failed_items = [];

        foreach ($item_ids as $pid) {
            // Get item details
            $item = $this->db->select('p.*, pr.product_nama, pr.satuan_id, s.satuan_nama')
                ->from('tbl_produksi p')
                ->join('tbl_product pr', 'pr.product_id = p.product_id')
                ->join('tbl_satuan s', 's.satuan_id = pr.satuan_id', 'left')
                ->where('p.produksi_id', $pid)
                ->get()->row();

            if (!$item) {
                $failed_items[] = "Item ID $pid tidak ditemukan";
                continue;
            }

            // Find RIP step in detail_produksi
            $rip_step = $this->db->select('dp.*')
                ->from('detail_produksi dp')
                ->join('routing r', 'r.routing_id = dp.routing_id')
                ->where('dp.produksi_id', $pid)
                ->where('r.routing_name LIKE', '%RIP%')
                ->where('dp.status_produksi', '0') // Pending only
                ->get()->row();

            if (!$rip_step) {
                $failed_items[] = "{$item->product_nama}: RIP step tidak ditemukan atau sudah selesai";
                continue;
            }

            // Calculate material usage for estimation
            $panjang = (float) ($item->panjang ?: 0);
            $lebar = (float) ($item->lebar ?: 0);
            $qty = (int) ($item->qty ?: 1);

            // Get BOM if dimensions missing
            if ($panjang == 0 || $lebar == 0) {
                $bom = $this->db->select('panjang, lebar')
                    ->from('bom_lines')
                    ->where('product_id', $item->product_id)
                    ->get()->row();

                if ($bom) {
                    $panjang = $panjang ?: (float) $bom->panjang;
                    $lebar = $lebar ?: (float) $bom->lebar;
                }
            }

            $usage = $panjang * $lebar * $qty;
            $total_usage += $usage;

            // Mark RIP step as complete
            $this->db->where('detail_produksi_id', $rip_step->detail_produksi_id);
            $this->db->update('detail_produksi', [
                'status_produksi' => '2', // Done
                'waktu_mulai' => date('Y-m-d H:i:s'),
                'waktu_selesai' => date('Y-m-d H:i:s'),
                'operator_id' => $operator->karyawan_id,
                'qty_bahan_pakai' => $usage,
                'catatan' => 'Batch RIP - Auto calculated: ' . number_format($usage, 2) . ' m²'
            ]);

            // Update main produksi status to 'proses' (RIP done, ready for print)
            $this->db->where('produksi_id', $pid);
            $this->db->update('tbl_produksi', [
                'status_produksi' => 'proses',
                'update_time' => date('Y-m-d H:i:s')
            ]);

            $processed_items[] = [
                'name' => $item->product_nama,
                'qty' => $qty,
                'area' => round($usage, 2),
                'unit' => $item->satuan_nama
            ];
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memproses RIP batch. Transaksi database error.'
            ]);
        } else {
            $response = [
                'success' => true,
                'message' => count($processed_items) . ' item berhasil di-RIP',
                'total_items' => count($processed_items),
                'total_usage_m2' => round($total_usage, 2),
                'items' => $processed_items
            ];

            if (!empty($failed_items)) {
                $response['warnings'] = $failed_items;
            }

            echo json_encode($response);
        }
    }

}
