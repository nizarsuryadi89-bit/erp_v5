<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pb extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Produksi_model');
        $this->load->model('M_gudang');
    }

    public function index()
    {
        $data['detail_pb'] = $this->Produksi_model->get_detail_pb();
        $data['detail_handover'] = $this->Produksi_model->get_detail_handover();
        $data['list_cabang'] = $this->db->get('tbl_perusahaan')->result();

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

    public function guide()
    {
        $this->load_admin_view('admin/pb/guide', []);
    }

    public function handover_to_customer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nospk', 'Nomor SPK', 'required');
        $this->form_validation->set_rules('pin', 'PIN Petugas', 'required');

        if ($this->form_validation->run() === FALSE) {
            $response = ['success' => false, 'message' => validation_errors()];
        } else {
            $nospk = $this->input->post('nospk', true);
            $pin = $this->input->post('pin', true);
            $produksi_ids = $this->input->post('produksi_ids', true); // Array of IDs
            $via = $this->input->post('pengambilan_via', true);
            $note = $this->input->post('pengambilan_note', true);
            $foto_name = null;

            // 0. Verify PIN Petugas
            $officer_name = $this->_verify_pin_and_get_name($pin);
            if (!$officer_name) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'PIN Petugas Salah atau Tidak Ditemukan!']);
                return;
            }

            if (empty($produksi_ids)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tidak ada item yang dipilih untuk diserahkan']);
                return;
            }
            // ... (keep previous photo upload logic)
            // Handle Foto Upload
            if (!empty($_FILES['foto_handover']['name'])) {
                $upload_path = FCPATH . 'uploads/handover/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }

                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'gif|jpg|png|jpeg|GIF|JPG|PNG|JPEG';
                $config['max_size'] = 20480; // 20MB
                $config['file_name'] = 'HO_' . $nospk . '_' . time();

                $this->load->library('upload');
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('foto_handover')) {
                    $error = $this->upload->display_errors('', '');
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Gagal upload foto: ' . $error]);
                    return;
                } else {
                    $upload_data = $this->upload->data();
                    $foto_name = $upload_data['file_name'];
                }
            }

            $handover_note = "[HANDOVER] " . date('d/m/Y H:i') . " | Oleh: " . $officer_name . " | Via: " . ($via ?: '-') . " | Note: " . ($note ?: '-');

            $this->db->trans_start();

            // 1. Dapatkan detail produksi yang dipilih
            try {
                $this->load->model('Wip_workflow_model', 'wip_workflow');
                $productions = $this->wip_workflow->customer_handover_items($produksi_ids, $this->wip_workflow->actor(), $nospk);
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false,
                    'message' => $e instanceof DomainException ? $e->getMessage() : 'Serah terima gagal divalidasi.']));
                return;
            }

            if (empty($productions)) {
                $response = ['success' => false, 'message' => 'Item produksi tidak ditemukan'];
            } else {
                // 2. Update status di detail_produksi
                $this->db->where_in('produksi_id', $produksi_ids);
                $this->db->where_in('status_produksi', ['on_pb', 'done']);
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
                if ($this->db->trans_status() === FALSE) {
                    $response = ['success' => false, 'message' => 'Gagal update database'];
                } else {
                    $response = ['success' => true, 'message' => 'Item berhasil diserahkan oleh ' . $officer_name];
                    
                    // --- KIRIM WA OTOMATIS (PENGAMBILAN) ---
                    $this->load->helper('whatsapp');
                    $pemesanan = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row();
                    if ($pemesanan) {
                        $kons = $this->db->get_where('tbl_konsumen', ['konsumen_id' => $pemesanan->konsumen_id])->row();
                        if ($kons && !empty($kons->konsumen_nohp)) {
                            $wa_msg = "*PESANAN TELAH DIAMBIL*\n\n";
                            $wa_msg .= "Halo *" . $kons->konsumen_nama . "*,\n";
                            $wa_msg .= "Terima kasih! Pesanan Anda dengan No. SPK *$nospk* telah kami serahkan.\n";
                            $wa_msg .= "Diserahkan oleh: *$officer_name*\n";
                            $wa_msg .= "Via: *" . ($via ?: '-') . "*\n\n";
                            $link_cek = base_url('cekpesanan?spk=' . urlencode(encrypt_spk($nospk)));
                            $wa_msg .= "Cek Invoice Anda: $link_cek\n\n";
                            $wa_msg .= "Terima kasih telah mempercayakan cetakan Anda kepada kami.\n\n";
                            $wa_msg .= "_Pesan otomatis via ERP TGM Print_";
                            
                            send_wa_notif($kons->konsumen_nohp, $wa_msg, 'notif_pengambilan');
                        }
                    }
                    // ---------------------------------------
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function bulk_handover_to_customer()
    {
        $ids_raw = $this->input->post('ids', true); // Legacy if needed
        $nospks = $this->input->post('nospks', true); // New: preferred for full SPK handover
        $via = $this->input->post('pengambilan_via', true);
        $note = $this->input->post('pengambilan_note', true);
        $pin = $this->input->post('pin', true);
        $foto_name = null;

        if (empty($pin)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'PIN Petugas wajib diisi!']);
            return;
        }

        // 0. Verify PIN Petugas
        $officer_name = $this->_verify_pin_and_get_name($pin);
        if (!$officer_name) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'PIN Petugas Salah atau Tidak Ditemukan!']);
            return;
        }

        // EXPANSION LOGIC: If nospks provided, fetch all associated production IDs that are currently 'on_pb'
        $ids = is_array($ids_raw) ? $ids_raw : [];
        if (!empty($nospks) && is_array($nospks)) {
            $expanded = $this->db->select('produksi_id')
                ->where_in('nospk', $nospks)
                ->where_in('status_produksi', ['on_pb', 'done'])
                ->get('tbl_produksi')->result();
            foreach ($expanded as $row) {
                if (!in_array($row->produksi_id, $ids)) {
                    $ids[] = $row->produksi_id;
                }
            }
        }

        if (empty($ids)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Tidak ada item yang ditemukan untuk diserahkan.']);
            return;
        }

        // Handle Foto Upload
        if (!empty($_FILES['foto_handover']['name'])) {
            $upload_path = FCPATH . 'uploads/handover/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg|GIF|JPG|PNG|JPEG';
            $config['max_size'] = 20480; // 20MB
            $config['file_name'] = 'BULK_HO_' . time();

            $this->load->library('upload');
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('foto_handover')) {
                $error = $this->upload->display_errors('', '');
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal upload foto: ' . $error]);
                return;
            } else {
                $upload_data = $this->upload->data();
                $foto_name = $upload_data['file_name'];
            }
        }

        $handover_note = "[BULK HANDOVER] " . date('d/m/Y H:i') . " | Oleh: " . $officer_name . " | Via: " . ($via ?: '-') . " | Note: " . ($note ?: '-');

        $this->db->trans_start();

        // 1. Dapatkan catatan saat ini
        try {
            $this->load->model('Wip_workflow_model', 'wip_workflow');
            $productions = $this->wip_workflow->customer_handover_items($ids, $this->wip_workflow->actor());
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false,
                'message' => $e instanceof DomainException ? $e->getMessage() : 'Serah terima gagal divalidasi.']));
            return;
        }

        // 2. Update status di detail_produksi
        $this->db->where_in('produksi_id', $ids);
        $this->db->where_in('status_produksi', ['on_pb', 'done']);
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
        if ($this->db->trans_status() === FALSE) {
            $response = ['success' => false, 'message' => 'Gagal bulk update database'];
        } else {
            $response = ['success' => true, 'message' => count($ids) . ' item berhasil diserahkan oleh ' . $officer_name];
            
            // --- KIRIM WA OTOMATIS (PENGAMBILAN BULK) ---
            if (!empty($nospks) && is_array($nospks)) {
                $this->load->helper('whatsapp');
                // Get unique konsumen from these SPKs
                $this->db->select('tbl_pemesanan.nospk, tbl_konsumen.konsumen_nohp, tbl_konsumen.konsumen_nama');
                $this->db->from('tbl_pemesanan');
                $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id');
                $this->db->where_in('tbl_pemesanan.nospk', $nospks);
                $pemesanans = $this->db->get()->result();

                foreach ($pemesanans as $pem) {
                    if (!empty($pem->konsumen_nohp)) {
                        $wa_msg = "*PESANAN TELAH DIAMBIL*\n\n";
                        $wa_msg .= "Halo *" . $pem->konsumen_nama . "*,\n";
                        $wa_msg .= "Terima kasih! Pesanan Anda dengan No. SPK *$pem->nospk* telah kami serahkan.\n";
                        $wa_msg .= "Diserahkan oleh: *$officer_name*\n";
                        $wa_msg .= "Via: *" . ($via ?: '-') . "*\n\n";
                        $link_cek = base_url('cekpesanan?spk=' . urlencode(encrypt_spk($pem->nospk)));
                        $wa_msg .= "Cek Invoice Anda: $link_cek\n\n";
                        $wa_msg .= "Terima kasih telah mempercayakan cetakan Anda kepada kami.\n\n";
                        $wa_msg .= "_Pesan otomatis via ERP TGM Print_";
                        
                        send_wa_notif($pem->konsumen_nohp, $wa_msg, 'notif_pengambilan');
                    }
                }
            }
            // --------------------------------------------
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    private function _verify_pin_and_get_name($pin)
    {
        if (empty($pin))
            return false;

        $this->db->select('nama_lengkap');
        $this->db->where('pin_produksi', $pin);
        $res = $this->db->get('tbl_karyawan')->row();

        return $res ? $res->nama_lengkap : false;
    }

    public function get_pb_verify_details_ajax()
    {
        $nospk = $this->input->post('nospk', true);

        $this->db->select('p.*, pr.product_nama, pr.kategori_id, s.satuan_nama, pm.status_pembayaran, hp.detail_product, hp.ket_1 as sisi');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_product pr', 'pr.product_id = p.product_id', 'left');
        $this->db->join('tbl_satuan s', 's.satuan_id = pr.satuan_id', 'left');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk', 'left');
        $this->db->join('tbl_harga_product hp', 'hp.harga_id = p.harga_id', 'left');
        $this->db->where('p.nospk', $nospk);

        $items = $this->db->get()->result();

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
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'success' => false, 'message' => 'Hasil produksi historis tidak dapat ditimpa. Koreksi memerlukan transaksi revisi terpisah.'
        ]));
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

        $this->db->select('tbl_produksi.*, tbl_product.product_nama, tbl_product.kategori_id, tbl_kategori.kategori_nama, tbl_konsumen.konsumen_nama, tbl_satuan.satuan_nama, tbl_harga_product.detail_product, tbl_harga_product.ket_1 as sisi, tbl_perusahaan.nama_perusahaan as cabang_asal, tbl_perusahaan.tag_paper, tbl_perusahaan.tag_font_size, tbl_perusahaan.tag_font_family, tbl_perusahaan.tag_show_logo, tbl_perusahaan.tag_logo_width, tbl_perusahaan.tag_show_qr, tbl_perusahaan.tag_qr_width, tbl_perusahaan.tag_footer_note, tbl_perusahaan.tag_section_spacing, tbl_perusahaan.invoice_font_size, tbl_perusahaan.invoice_font_family, tbl_perusahaan.invoice_section_spacing, tbl_pemesanan.created_at as tgl_spk, tbl_pemesanan.status_pembayaran, dk.nama_lengkap as deskprint_nama');
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
        $ids_str = $this->input->get('ids', true);
        if (empty($ids_str))
            show_404();

        $ids = explode(',', $ids_str);

        $this->db->select('tbl_produksi.*, tbl_product.product_nama, tbl_product.kategori_id, tbl_kategori.kategori_nama, tbl_konsumen.konsumen_nama, tbl_satuan.satuan_nama, tbl_harga_product.detail_product, tbl_harga_product.ket_1 as sisi, tbl_perusahaan.nama_perusahaan as cabang_asal, tbl_perusahaan.tag_paper, tbl_perusahaan.tag_font_size, tbl_perusahaan.tag_font_family, tbl_perusahaan.tag_show_logo, tbl_perusahaan.tag_logo_width, tbl_perusahaan.tag_show_qr, tbl_perusahaan.tag_qr_width, tbl_perusahaan.tag_footer_note, tbl_perusahaan.tag_section_spacing, tbl_perusahaan.invoice_font_size, tbl_perusahaan.invoice_font_family, tbl_perusahaan.invoice_section_spacing, tbl_pemesanan.created_at as tgl_spk, tbl_pemesanan.status_pembayaran, dk.nama_lengkap as deskprint_nama');
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

    public function reproduce_rijek_ajax()
    {
        $response = ['success' => false, 'message' => 'Aksi harus dikirim melalui POST.'];
        if (strtolower($this->input->method()) !== 'post') {
            $this->output->set_status_header(405)->set_header('Allow: POST');
        } else {
            try {
                $nospk = $this->input->post('nospk', true);
                $reason = trim((string) $this->input->post('reason', true));
                $pin = $this->input->post('pin', true);
                if (!$nospk || !$reason || !$this->_verify_pin_privileged($pin)) throw new DomainException('SPK, alasan, dan PIN supervisor yang valid wajib diisi.');
                $this->load->model('Wip_workflow_model', 'wip_workflow');
                $ids = array_column($this->db->select('produksi_id')->where('nospk', $nospk)->get('tbl_produksi')->result_array(), 'produksi_id');
                $actor = $this->wip_workflow->actor($pin);
                $response = $this->wip_workflow->execute($ids, 'report', $actor, [], [], function ($item, $step, $actor) use ($reason) {
                    $note = '[MASALAH] [PB] Permintaan produksi ulang: ' . $reason;
                    if (!$this->db->where('produksi_id', $item->produksi_id)->update('tbl_produksi', ['catatan' => trim(($item->catatan ?? '') . "\n" . $note), 'update_time' => date('Y-m-d H:i:s')])) throw new RuntimeException('Gagal menyimpan laporan.');
                    if (!$this->db->insert('tbl_produksi_log', ['produksi_id' => $item->produksi_id, 'user_id' => $actor['user_id'], 'status_asal' => $item->status_produksi, 'status_tujuan' => $item->status_produksi, 'catatan' => $note])) throw new RuntimeException('Gagal menyimpan riwayat.');
                    return [];
                });
                if ($response['success']) $response['message'] = 'Permintaan produksi ulang dicatat untuk ditinjau supervisor. Riwayat dan status tahap tidak direset.';
                $response['new_nospk'] = $nospk;
            } catch (Exception $e) {
                $response = ['success' => false, 'message' => $e instanceof DomainException ? $e->getMessage() : 'Gagal mencatat permintaan.'];
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    private function _verify_pin_privileged($pin)
    {
        if (empty($pin))
            return false;

        $this->db->select('k.nama_lengkap, u.role_id');
        $this->db->from('tbl_karyawan k');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id', 'inner');
        $this->db->where('k.pin_produksi', $pin);
        // Level IDs for Admin (1) and Owners (8, 17) based on Dashboard configuration
        $this->db->where_in('u.role_id', [1, 8, 17]);

        $res = $this->db->get()->row();

        return $res ? $res->nama_lengkap : false;
    }
}
