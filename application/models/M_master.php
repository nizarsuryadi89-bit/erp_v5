<?php
/**
 * M_master Model
 * 
 * @method CI_DB_result getData(string $table = 'tbl_product')
 */
class M_master extends CI_model
{
    public function getBahanbakuBelumDipakai()
    {
        $this->db->select('*');
        $this->db->from('tbl_bahan');
        $this->db->where_not_in('bahanbaku_id', $this->db->select('bahanbaku_id')->from('tbl_bahan'), false);
        return $this->db->get();
    }

    public function get_menu_with_submenu()
    {
        $this->db->select('menu.id as menu_id, menu.alias, menu.icon, submenu.nama_submenu, submenu.link_submenu');
        $this->db->from('menu');
        $this->db->join('submenu', 'submenu.menu_id = menu.id');
        // $this->db->where('submenu.is_active', 1);
        $this->db->order_by('menu.id, submenu.id');
        $query = $this->db->get();
        $results = $query->result();

        // Struktur nested
        $menus = [];
        foreach ($results as $row) {
            if (!isset($menus[$row->menu_id])) {
                $menus[$row->menu_id] = (object) [
                    'alias' => $row->alias,
                    'icon' => $row->icon,
                    'submenu' => []
                ];
            }
            $menus[$row->menu_id]->submenu[] = [
                'title' => $row->title,
                'url' => $row->url,
                'icon' => $row->sub_icon
            ];
        }

        return $menus;
    }

    public function search_by_keyword($keyword)
    {
        $this->db->select('konsumen_id, konsumen_nama');
        $this->db->like('konsumen_nama', $keyword);
        $this->db->limit(10);
        $query = $this->db->get('tbl_konsumen');

        return $query->result();
    }

    public function tampilDataMesin()
    {
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_mesin.perusahaan_id', 'left');
        $this->db->order_by('tbl_mesin.mesin_id', 'DESC');
        return $this->db->get('tbl_mesin');
    }



    public function search_product($keyword)
    {
        $this->db->select('product_id, product_nama');
        $this->db->like('product_nama', $keyword);
        $query = $this->db->get('tbl_product'); // sesuaikan nama tabel
        return $query->result();
    }

    public function search_product_by_metode($keyword, $metode_transaksi)
    {
        $this->db->select('tbl_product.product_id, tbl_product.product_nama, tbl_kategori.kategori_nama');
        $this->db->from('tbl_product');
        $this->db->join('tbl_kategori', 'tbl_product.kategori_id = tbl_kategori.kategori_id', 'left');

        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('tbl_product.product_nama', $keyword);
            $this->db->or_like('tbl_product.product_id', $keyword);
            $this->db->group_end();
        }

        // Semua produk (marketplace & offline) dapat ditransaksikan di semua
        // jenis transaksi (offline, marketplace, WA/email) — tidak ada filter detail_id.
        // PENGECUALIAN: Jangan tampilkan produk yang dibuat via Transaksi Manual
        $this->db->group_start();
        $this->db->where('tbl_product.product_deskripsi !=', 'Manual entry via POS');
        $this->db->or_where('tbl_product.product_deskripsi IS NULL');
        $this->db->group_end();

        $this->db->order_by('tbl_kategori.kategori_id', 'ASC');
        $this->db->order_by('tbl_product.product_id', 'ASC');
        $this->db->limit(30);

        $query = $this->db->get();
        return $query->result();
    }

    public function search_public_prices($keyword = '', $limit = 12)
    {
        $selected_category = (int) $this->input->get('category');
        $this->db->select('
            p.product_id,
            p.product_nama,
            p.product_img,
            k.kategori_nama,
            k.kategori_id,
            MIN(hp.harga_1) as min_price,
            MAX(hp.harga_1) as max_price,
            COUNT(hp.harga_id) as variant_count
        ');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_harga_product hp', 'hp.product_id = p.product_id');
        $this->db->join('tbl_kategori k', 'k.kategori_id = p.kategori_id', 'left');
        $this->db->where('hp.is_active', '1');

        if ($selected_category > 0) {
            $this->db->where('p.kategori_id', $selected_category);
        }

        if ($keyword !== '') {
            $this->db->group_start();
            $this->db->like('p.product_nama', $keyword);
            $this->db->or_like('hp.detail_product', $keyword);
            $this->db->or_like('k.kategori_nama', $keyword);
            $this->db->group_end();
        }

        $this->db->group_by(array('p.product_id', 'p.product_nama', 'k.kategori_nama'));
        $this->db->order_by('p.product_nama', 'ASC');
        $this->db->limit((int) $limit);

        $products = $this->db->get()->result_array();

        foreach ($products as &$product) {
            $this->db->select('detail_product, harga_1, qty_min, qty_max, ket_1, jenisprint_id');
            $this->db->from('tbl_harga_product');
            $this->db->where('product_id', $product['product_id']);
            $this->db->where('is_active', '1');
            $this->db->order_by('harga_1', 'ASC');
            $this->db->order_by('qty_min', 'ASC');
            $this->db->limit(3);
            $product['samples'] = $this->db->get()->result_array();
        }
        unset($product);

        return $products;
    }

    public function get_public_categories()
    {
        $this->db->select('
            k.kategori_id,
            k.kategori_nama,
            k.kategori_deskripsi,
            COUNT(DISTINCT p.product_id) as total_products
        ');
        $this->db->from('tbl_kategori k');
        $this->db->join('tbl_product p', 'p.kategori_id = k.kategori_id', 'left');
        $this->db->join('tbl_harga_product hp', 'hp.product_id = p.product_id AND hp.is_active = "1"', 'left');
        $this->db->where('k.status', '1');
        $this->db->group_by(array('k.kategori_id', 'k.kategori_nama', 'k.kategori_deskripsi'));
        $this->db->having('COUNT(DISTINCT hp.harga_id) >', 0);
        $this->db->order_by('k.kategori_urutan', 'ASC');
        $this->db->order_by('k.kategori_nama', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_public_order_options($limit = 200)
    {
        $selected_category = (int) $this->input->get('category');

        $this->db->select('
            hp.harga_id,
            hp.product_id,
            hp.detail_product,
            hp.harga_1,
            hp.qty_min,
            hp.qty_max,
            p.product_nama,
            p.product_img,
            p.kategori_id,
            k.kategori_nama,
            s.satuan_nama,
            sd.side_name
        ');
        $this->db->from('tbl_harga_product hp');
        $this->db->join('tbl_product p', 'p.product_id = hp.product_id');
        $this->db->join('tbl_kategori k', 'k.kategori_id = p.kategori_id', 'left');
        $this->db->join('tbl_satuan s', 's.satuan_id = p.satuan_id', 'left');
        $this->db->join('tbl_side sd', 'sd.side_id = hp.ket_1', 'left');
        $this->db->where('hp.is_active', '1');
        $this->db->where('hp.harga_1 >', 0);

        if ($selected_category > 0) {
            $this->db->where('p.kategori_id', $selected_category);
        }

        $this->db->order_by('k.kategori_nama', 'ASC');
        $this->db->order_by('p.product_nama', 'ASC');
        $this->db->order_by('hp.harga_1', 'ASC');
        $this->db->limit((int) $limit);

        return $this->db->get()->result_array();
    }


    public function getprodukbyid($id)
    {
        return $this->db->get_where('tbl_product', array('product_id' => $id));
    }

    public function delete_product($id)
    {
        return $this->db->delete('tbl_product', ['product_id' => $id]);
    }

    public function delete_skala_harga($product_id)
    {
        return $this->db->delete('tbl_harga_product', ['product_id' => $product_id]);
    }

    public function delete_routing($product_id)
    {
        return $this->db->delete('routing', ['product_id' => $product_id]);
    }



    function getprodukbykode($kode)
    {
        $this->db->where('tbl_product.product_id', $kode);
        $this->db->join('tbl_subkategori', 'tbl_subkategori.subkategori_id = tbl_product.subkategori_id');
        $this->db->join('tbl_kategori', 'tbl_product.kategori_id = tbl_kategori.kategori_id');
        // $this->db->join('tbl_bahan', 'tbl_product.bahan_id = tbl_bahan.bahan_id');
        $this->db->join('tbl_satuan', 'tbl_product.satuan_id = tbl_satuan.satuan_id');
        $this->db->join('tbl_type', 'tbl_type.tipe_id = tbl_product.tipe_id');
        $this->db->order_by('tbl_product.kategori_id', 'asc');
        $this->db->order_by('tbl_product.subkategori_id', 'asc');
        return $this->db->get('tbl_product');
    }

    function getmenu($menu)
    {
        $this->db->where('menu.link', $menu);
        $this->db->where('submenu.status', 1); // Only Active Submenus
        $this->db->join('menu', 'menu.id = submenu.id_menu');
        $this->db->order_by('submenu.urutan', 'asc'); // Order by Sequence
        return $this->db->get('submenu');
    }

    public function tampilsemuadata($table, $order_by = null)
    {
        if ($order_by) $this->db->order_by($order_by, 'ASC');
        return $this->db->get($table);
    }

    function aksi_edit($tbl, $data, $where)
    {
        $this->db->where($where);
        return $this->db->update($tbl, $data);
    }

    function getproductorderby($orderby)
    {
        $this->db->order_by($orderby, 'asc');
        return $this->db->get('tbl_product');
    }

    function getrouting()
    {
        $this->db->query("SELECT * FROM `routing` a inner join tbl_product b 
                                on a.product_id=b.product_id inner join tbl_mesin c 
                                    on a.mesin_id = c.mesin_id");
        return $this->db->get();
    }

    function getbom($orderby)
    {
        $this->db->join('tbl_product', 'tbl_product.product_id = bom_lines.product_id');
        $this->db->order_by($orderby, 'asc');
        return $this->db->get('bom_lines');
    }

    function getbomkode($orderby, $kode)
    {
        $this->db->select('bom_lines.*, tbl_material.varian, tbl_bahan.bahan_nama, tbl_product.product_nama, tbl_satuan.satuan_nama');
        $this->db->from('bom_lines');
        $this->db->join('tbl_material', 'tbl_material.material_id = bom_lines.bahan_id', 'left');
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_material.bahan_id', 'left');
        $this->db->join('tbl_product', 'tbl_product.product_id = bom_lines.product_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = bom_lines.satuan_id', 'left');
        $this->db->where('bom_lines.product_id', $kode);
        return $this->db->get();
    }

    function getbombyproduct($kode)
    {
        $this->db->where('bom_lines.product_id', $kode);
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = bom_lines.bahan_id');
        $this->db->join('tbl_product', 'tbl_product.product_id = bom_lines.product_id');
        return $this->db->get('bom_lines');
    }

    function getbombykode($kode)
    {
        $this->db->where('tbl_product.product_id', $kode);
        $this->db->join('tbl_product', 'tbl_product.product_id = bom_lines.product_id');
        $this->db->order_by('bom_line_id', 'asc');
        $this->db->limit('1');
        return $this->db->get('bom_lines');
    }

    function getroutingkode($orderby, $kode)
    {
        $this->db->where('tbl_product.product_id', $kode);
        $this->db->join('tbl_product', 'tbl_product.product_id = routing.product_id');
        $this->db->order_by($orderby, 'asc');
        return $this->db->get('routing');
    }

    function getRoutingproduct($kode)
    {
        $this->db->where('produksi_id', $kode);
        $this->db->join('tbl_produksi', 'tbl_produksi.product_id = routing.product_id');
        return $this->db->get('routing');
    }

    function getRoutingproductnext($kode)
    {
        $this->db->where('product_id', $kode);
        $this->db->where('routing_urutan', 2);
        return $this->db->get('routing');
    }

    function routingbykode($kode, $harga_id = null)
    {
        if ($harga_id !== null) {
            $this->db->where('product_id', $kode);
            $this->db->where('harga_id', $harga_id);
            $count = $this->db->count_all_results('routing');
            
            if ($count > 0) {
                $this->db->where('routing.harga_id', $harga_id);
            } else {
                $this->db->where('routing.harga_id IS NULL');
            }
        } else {
            $this->db->where('routing.harga_id IS NULL');
        }

        $this->db->where('tbl_product.product_id', $kode);
        $this->db->join('tbl_operator', 'tbl_operator.operator_id = routing.operator_id');
        $this->db->join('tbl_product', 'tbl_product.product_id = routing.product_id');
        $this->db->join('bom_lines', 'bom_lines.bom_line_id = routing.bom_line_id', 'left');
        return $this->db->get('routing');
    }


    function getroutingbykode($kode)
    {
        return $this->db->get_where('routing', array('routing_id' => $kode));
    }


    function getworkcenters()
    {
        return $this->db->get('workcenters');
    }

    function steprouting($kode)
    {

        $this->db->where('routing_steps.routing_id', $kode);
        $this->db->join('workcenters', 'workcenters.workcenter_id = routing_steps.workcenter_id');
        $this->db->join('routing', 'routing.routing_id = routing_steps.routing_id');
        return $this->db->get('routing_steps');
    }

    function get_karyawan()
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // $this->db->select('k.*, j.nama_jabatan, j.gaji_pokok, o.operator_nama,p.nama_perusahaan');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('tbl_operator o', 'o.operator_id = k.divisi_id', 'left');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id', 'left');
        
        // Exclude dummy accounts (machines/terminals)
        $this->db->where("k.nik NOT LIKE 'STATION%'");
        $this->db->where("k.nik NOT LIKE 'TERM%'");
        $this->db->where('k.nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('k.nama_lengkap NOT LIKE', '%A3+%');

        // $this->db->join('tbl_perusahaan p', 'p.id_perusahaan = k.perusahaan_id', 'left');


        return $this->db->get();
    }

    function getdivisikaryawan($jabatan)
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $this->db->select('k.*, j.nama_jabatan, p.nama_perusahaan');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');

        $this->db->join('tbl_perusahaan p', 'p.id_perusahaan = k.perusahaan_id', 'left');
        $this->db->where('k.jabatan_id', $jabatan); // Jabatan 
        return $this->db->get();

        return $this->db->get();
    }

    function getjkkaryawan($jk)
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $this->db->select('k.*, j.nama_jabatan, p.nama_perusahaan');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');

        $this->db->join('tbl_perusahaan p', 'p.id_perusahaan = k.perusahaan_id', 'left');
        $this->db->where('k.jenis_kelamin', $jk); // Jabatan 
        return $this->db->get();

        return $this->db->get();
    }


    function get_lembur()
    {
        // $karyawan           = $this->session->userdata('karyawan_id');

        $this->db->select('*');
        $this->db->from('tbl_karyawan a');
        $this->db->join('tbl_lembur b', 'a.karyawan_id = b.karyawan_id');
        // $this->db->where('a.karyawan_id', $karyawan);

        // Filter bulan dan tahun ini (pakai fungsi MySQL MONTH() dan YEAR())
        $this->db->where('MONTH(b.tanggal)', date('m'));
        $this->db->where('YEAR(b.tanggal)', date('Y'));
        return $this->db->get();
    }

    function get_lembur_filter($bulan, $tahun)
    {
        $this->db->select('a.nama_lengkap, a.foto, a.nik, a.jabatan_id, b.*');
        $this->db->from('tbl_karyawan a');
        $this->db->join('tbl_lembur b', 'a.karyawan_id = b.karyawan_id');
        $this->db->where('MONTH(b.tanggal)', $bulan);
        $this->db->where('YEAR(b.tanggal)', $tahun);
        $this->db->order_by('b.tanggal', 'DESC');
        $this->db->order_by('b.jam_mulai', 'ASC');
        return $this->db->get();
    }

    function get_lembur_by_date($tanggal)
    {
        $this->db->select('a.nama_lengkap, a.foto, b.*');
        $this->db->from('tbl_karyawan a');
        $this->db->join('tbl_lembur b', 'a.karyawan_id = b.karyawan_id');

        // Logic HR: Tampilkan semua yang PENDING (dari hari manapun) 
        // ATAU semua data di tanggal yang dipilih user
        $this->db->group_start();
        $this->db->where('b.status', 'menunggu');
        $this->db->or_where('b.tanggal', $tanggal);
        $this->db->group_end();

        $this->db->order_by('b.tanggal', 'DESC');
        $this->db->order_by('b.jam_mulai', 'ASC');
        return $this->db->get();
    }


    function karyawan_lembur()
    {
        $karyawan = $this->session->userdata('karyawan_id');

        $this->db->select('*');
        $this->db->from('tbl_karyawan a');
        $this->db->join('tbl_lembur b', 'a.karyawan_id = b.karyawan_id');
        $this->db->where('a.karyawan_id', $karyawan);

        // Filter bulan dan tahun ini (pakai fungsi MySQL MONTH() dan YEAR())
        $this->db->where('MONTH(b.tanggal)', date('m'));
        $this->db->where('YEAR(b.tanggal)', date('Y'));
        return $this->db->get();

    }

    function jml_jam_lembur()
    {
        $karyawan = $this->session->userdata('karyawan_id');

        $this->db->select('a.*, ROUND(SUM(TIME_TO_SEC(TIMEDIFF(jam_selesai, jam_mulai)))/3600, 2) AS total_jam_lembur');
        $this->db->from('tbl_karyawan a');
        $this->db->join('tbl_lembur b', 'a.karyawan_id = b.karyawan_id');
        $this->db->where('a.karyawan_id', $karyawan);
        $this->db->where('b.status', 'disetujui');

        // Filter bulan dan tahun ini (pakai fungsi MySQL MONTH() dan YEAR())
        $this->db->where('MONTH(b.tanggal)', date('m'));
        $this->db->where('YEAR(b.tanggal)', date('Y'));
        $this->db->group_by('a.karyawan_id');

        return $this->db->get();
    }

    function jml_spk($user_id, $start_date = null, $end_date = null)
    {
        $this->db->select('COUNT(*) as jum');
        $this->db->from('tbl_pemesanan');
        $this->db->where('deskprint_id', $user_id);
        if ($start_date && $end_date) {
            $this->db->where('created_at >=', $start_date . ' 00:00:00');
            $this->db->where('created_at <=', $end_date . ' 23:59:59');
        }
        return $this->db->get();
    }

    function get_karyawan_jabatan($jabatan)
    {

        $this->db->select('k.*, j.nama_jabatan');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'j.jabatan_id = k.jabatan_id', 'left');
        $this->db->join('users u', 'k.karyawan_id = u.karyawan_id');
        $this->db->where('k.jabatan_id', $jabatan);
        return $this->db->get();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('tbl_karyawan', ['karyawan_id' => $id])->row();
    }

    public function update($id, $data)
    {
        $this->db->where('karyawan_id', $id);
        return $this->db->update('tbl_karyawan', $data);
    }

    public function get_all_jabatan()
    {
        return $this->db->get('tbl_jabatan')->result();
    }
    function getkaryawan($table, $order)
    {

        $this->db->join('tbl_divisi', 'tbl_divisi.divisi_id=tbl_karyawan.divisi_id');
        $this->db->order_by($order, 'ASC');
        return $this->db->get($table);
    }

    function getTampil($table, $order)
    {

        // $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan ='.$table.'.perusahaan_id');
        // $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id ='.$table.'.karyawan_id');
        $this->db->join('user_roles', 'user_roles.user_id =' . $table . '.user_id');
        $this->db->join('roles', 'roles.role_id =user_roles.role_id');
        return $this->db->get($table);
    }

    function getUser()
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        $this->db->select('users.*, tbl_perusahaan.nama_perusahaan, tbl_karyawan.nama_lengkap as nama_karyawan, roles.role_name');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id');

        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->order_by('tbl_perusahaan.id_perusahaan', 'asc');
        $this->db->order_by('roles.no_urut', 'asc');

        return $this->db->get('users');
    }


    function tampilData($table, $order)
    {
        $this->db->where('status', '1');
        $this->db->order_by($order, 'DESC');

        return $this->db->get($table);
    }

    public function addData($table, $data)
    {
        return $this->db->insert($table, $data);
    }

    function updateData($table, $id, $update)
    {
        $this->db->where($id);
        return $this->db->update($table, $update);
    }

    function joinKategori($table1, $table2, $order)
    {

        $this->db->join($table2, $table1 . '.kategori_id = ' . $table2 . '.kategori_id');
        $this->db->order_by($order, 'ASC');
        return $this->db->get($table1);
    }

    function getDatanonorder($table)
    {
        return $this->db->get($table);
    }

    public function getData($table = 'tbl_product')
    {
        return $this->db->get($table);
    }

    function getDataBahan()
    {

        $this->db->join('tbl_satuan as satuan_produk', 'tbl_bahan.satuan_id = satuan_produk.satuan_id');
        $this->db->select('tbl_bahan.*, satuan_produk.satuan_nama as satuan_nama');
        $this->db->order_by('bahan_id', 'asc');
        return $this->db->get('tbl_bahan');
    }


    function getDataProduct($filters = [])
    {
        try {
            $this->db->select('
                    tbl_product.*,
                    tbl_kategori.kategori_nama,
                    tbl_kategori.form_schema,
                    tbl_subkategori.subkategori_nama,
                    tbl_subkategori.subkategori_config,
                    tbl_satuan.satuan_nama,
                    tbl_type.tipe_nama,
                    tbl_side.side_name,
                    tbl_metode.metode_nama
                ');

            $this->db->join('tbl_kategori', 'tbl_product.kategori_id = tbl_kategori.kategori_id', 'left');
            $this->db->join('tbl_subkategori', 'tbl_product.subkategori_id = tbl_subkategori.subkategori_id', 'left');
            $this->db->join('tbl_satuan', 'tbl_product.satuan_id = tbl_satuan.satuan_id', 'left');
            $this->db->join('tbl_type', 'tbl_product.tipe_id = tbl_type.tipe_id', 'left');
            $this->db->join('tbl_side', 'tbl_product.side_id = tbl_side.side_id', 'left');
            $this->db->join('tbl_metode', 'tbl_product.detail_id = tbl_metode.metode_id', 'left');

            // Apply Filters
            if (!empty($filters['q'])) {
                $this->db->group_start();
                $this->db->like('tbl_product.product_nama', $filters['q']);
                $this->db->or_like('tbl_product.product_id', $filters['q']);
                $this->db->group_end();
            }
            if (!empty($filters['kategori'])) {
                $this->db->where('tbl_product.kategori_id', $filters['kategori']);
            }
            if (!empty($filters['metode'])) {
                $this->db->where('tbl_product.detail_id', $filters['metode']);
            }

            $this->db->order_by('tbl_product.kategori_id', 'asc');
            $this->db->order_by('tbl_product.subkategori_id', 'asc');
            $this->db->order_by('tbl_product.product_nama', 'asc');

            return $this->db->get('tbl_product');
        } catch (Exception $e) {
            return $this->db->get('tbl_product');
        }
    }

    public function getProductByIdComplete($product_id)
    {
        $has_prod_schema = $this->db->field_exists('form_schema', 'tbl_product');
        $has_cat_schema = $this->db->field_exists('form_schema', 'tbl_kategori');
        $has_sub_schema = $this->db->field_exists('form_schema', 'tbl_subkategori');

        $this->db->select('`tbl_product`.*');
        $this->db->select($has_prod_schema ? '`tbl_product`.`form_schema` as `product_form_schema`' : 'NULL as `product_form_schema`');
        $this->db->select($has_cat_schema ? '`tbl_kategori`.`form_schema` as `category_form_schema`' : 'NULL as `category_form_schema`');
        $this->db->select($has_sub_schema ? '`tbl_subkategori`.`form_schema` as `subcategory_form_schema`' : 'NULL as `subcategory_form_schema`');

        $this->db->select('
            `tbl_kategori`.`kategori_nama`,
            `tbl_subkategori`.`subkategori_nama`,
            `tbl_subkategori`.`subkategori_config`,
            `tbl_satuan`.`satuan_nama`,
            `tbl_type`.`tipe_nama`,
            `tbl_side`.`side_name`,
            `tbl_metode`.`metode_nama`
        ');
        $this->db->join('tbl_kategori', 'tbl_product.kategori_id = tbl_kategori.kategori_id', 'left');
        $this->db->join('tbl_subkategori', 'tbl_product.subkategori_id = tbl_subkategori.subkategori_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_product.satuan_id = tbl_satuan.satuan_id', 'left');
        $this->db->join('tbl_type', 'tbl_product.tipe_id = tbl_type.tipe_id', 'left');
        $this->db->join('tbl_side', 'tbl_product.side_id = tbl_side.side_id', 'left');
        $this->db->join('tbl_metode', 'tbl_product.detail_id = tbl_metode.metode_id', 'left');
        $this->db->where('tbl_product.product_id', $product_id);
        return $this->db->get('tbl_product')->row_array();
    }

    // Function untuk pagination dengan limit dan offset
    function getDataProductWithPagination($limit = null, $offset = null, $filters = [])
    {
        try {
            $this->db->select('
                    tbl_product.*,
                    tbl_product.form_schema as product_schema,
                    tbl_kategori.kategori_nama,
                    tbl_kategori.form_schema as kategori_schema,
                    tbl_subkategori.subkategori_nama,
                    tbl_subkategori.form_schema as subkategori_schema,
                    tbl_satuan.satuan_nama,
                    tbl_type.tipe_nama,
                    tbl_side.side_name,
                    tbl_metode.metode_nama,
                    0 as min_price,
                    0 as max_price,
                    0 as calculated_hpp
                ');

            $this->db->join('tbl_kategori', 'tbl_product.kategori_id = tbl_kategori.kategori_id', 'left');
            $this->db->join('tbl_subkategori', 'tbl_product.subkategori_id = tbl_subkategori.subkategori_id', 'left');
            $this->db->join('tbl_satuan', 'tbl_product.satuan_id = tbl_satuan.satuan_id', 'left');
            $this->db->join('tbl_type', 'tbl_product.tipe_id = tbl_type.tipe_id', 'left');
            $this->db->join('tbl_side', 'tbl_product.side_id = tbl_side.side_id', 'left');
            $this->db->join('tbl_metode', 'tbl_product.detail_id = tbl_metode.metode_id', 'left');

            // PENGECUALIAN: Jangan tampilkan produk yang dibuat via Transaksi Manual
            $this->db->group_start();
            $this->db->where('tbl_product.product_deskripsi !=', 'Manual entry via POS');
            $this->db->or_where('tbl_product.product_deskripsi IS NULL');
            $this->db->group_end();

            // Apply Filters
            if (!empty($filters['q'])) {
                $this->db->group_start();
                $this->db->like('tbl_product.product_nama', $filters['q']);
                $this->db->or_like('tbl_product.product_id', $filters['q']);
                $this->db->group_end();
            }
            if (!empty($filters['kategori'])) {
                $this->db->where('tbl_product.kategori_id', $filters['kategori']);
            }
            if (!empty($filters['metode'])) {
                $this->db->where('tbl_product.detail_id', $filters['metode']);
            }
            if (!empty($filters['starts_with'])) {
                if ($filters['starts_with'] === '0-9') {
                    $this->db->where("tbl_product.product_nama REGEXP '^[0-9]'", null, false);
                } else {
                    $this->db->like('tbl_product.product_nama', $filters['starts_with'], 'after');
                }
            }
            if (!empty($filters['status'])) {
                $has_harga = 'EXISTS (SELECT 1 FROM tbl_harga_product h WHERE h.product_id = tbl_product.product_id)';
                $has_bom = 'EXISTS (SELECT 1 FROM bom_lines b WHERE b.product_id = tbl_product.product_id)';
                $has_routing = 'EXISTS (SELECT 1 FROM routing r WHERE r.product_id = tbl_product.product_id)';

                if ($filters['status'] === 'complete') {
                    $this->db->where("{$has_harga} AND {$has_bom} AND {$has_routing}", null, false);
                } elseif ($filters['status'] === 'partial') {
                    $this->db->where("(($has_harga OR $has_bom OR $has_routing) AND NOT ($has_harga AND $has_bom AND $has_routing))", null, false);
                } elseif ($filters['status'] === 'empty') {
                    $this->db->where("(NOT {$has_harga} AND NOT {$has_bom} AND NOT {$has_routing})", null, false);
                } elseif ($filters['status'] === 'no_bom') {
                    $this->db->where("NOT {$has_bom}", null, false);
                } elseif ($filters['status'] === 'no_routing') {
                    $this->db->where("NOT {$has_routing}", null, false);
                }
            }

            $sortDirection = (!empty($filters['sort']) && strtolower($filters['sort']) === 'desc') ? 'desc' : 'asc';
            $orderBy = !empty($filters['orderby']) ? $filters['orderby'] : 'product_nama';

            if ($orderBy === 'product_id') {
                $this->db->order_by('tbl_product.product_id', $sortDirection);
            } else {
                $this->db->order_by('tbl_product.product_nama', $sortDirection);
            }
            $this->db->order_by('tbl_product.kategori_id', 'asc');
            $this->db->order_by('tbl_product.subkategori_id', 'asc');

            if ($limit !== null) {
                $this->db->limit($limit, $offset);
            }

            return $this->db->get('tbl_product');
        } catch (Exception $e) {
            if ($limit !== null) {
                $this->db->limit($limit, $offset);
            }
            return $this->db->get('tbl_product');
        }
    }



    function getProduct($kode)
    {
        $has_prod_schema = $this->db->field_exists('form_schema', 'tbl_product');
        $has_cat_schema = $this->db->field_exists('form_schema', 'tbl_kategori');
        $has_sub_schema = $this->db->field_exists('form_schema', 'tbl_subkategori');

        $this->db->select('`tbl_product`.*');
        $this->db->select($has_prod_schema ? '`tbl_product`.`form_schema` as `product_form_schema`' : 'NULL as `product_form_schema`');
        $this->db->select($has_cat_schema ? '`tbl_kategori`.`form_schema` as `category_form_schema`' : 'NULL as `category_form_schema`');
        $this->db->select($has_sub_schema ? '`tbl_subkategori`.`form_schema` as `subcategory_form_schema`' : 'NULL as `subcategory_form_schema`');

        $this->db->select('
                `tbl_kategori`.`kategori_nama`,
                `tbl_subkategori`.`subkategori_nama`,
                `tbl_satuan`.`satuan_nama`,
                `tbl_type`.`tipe_nama`,
                `tbl_side`.`side_name`,
                `tbl_metode`.`metode_nama`
            ');
        $this->db->where('tbl_product.product_id', $kode);
        $this->db->join('tbl_kategori', 'tbl_product.kategori_id = tbl_kategori.kategori_id', 'left');
        $this->db->join('tbl_subkategori', 'tbl_product.subkategori_id = tbl_subkategori.subkategori_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_product.satuan_id = tbl_satuan.satuan_id', 'left');
        $this->db->join('tbl_type', 'tbl_product.tipe_id = tbl_type.tipe_id', 'left');
        $this->db->join('tbl_side', 'tbl_product.side_id = tbl_side.side_id', 'left');
        $this->db->join('tbl_metode', 'tbl_product.detail_id = tbl_metode.metode_id', 'left');
        return $this->db->get('tbl_product');
    }

    // Helper method untuk mendapatkan produk aktif saja
    function getActiveProducts()
    {
        return $this->getDataProduct(['metode' => '1']);
    }

    // Helper method untuk mendapatkan produk berdasarkan kategori
    function getProductsByCategory($kategori_id)
    {
        return $this->getDataProduct(['kategori' => $kategori_id]);
    }

    // Helper method untuk mencari produk
    function searchProducts($search_term)
    {
        return $this->getDataProduct(['q' => $search_term]);
    }

    function get_product_by_id($id)
    {
        $this->db->where('product_id', $id);
        return $this->db->get('tbl_product');
    }

    function get_produk_by_bahan($id)
    {
        $this->db->select('tbl_product.*, tbl_bahan.bahan_nama, tbl_satuan.satuan_nama');
        $this->db->from('tbl_product');
        $this->db->join('bom_lines', 'bom_lines.product_id = tbl_product.product_id');
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = bom_lines.bahan_id');
        $this->db->join('tbl_satuan', 'tbl_product.satuan_id = tbl_satuan.satuan_id');
        $this->db->where('bom_lines.bahan_id', $id);
        return $this->db->get();
    }

    function getHarga($id)
    {
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_harga_product.product_id');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id');
        $this->db->join('tbl_range', 'tbl_range.range_id = tbl_harga_product.range_id', 'left');
        $this->db->where('tbl_harga_product.product_id', $id);
        return $this->db->get('tbl_harga_product');
    }


    function get_kategori()
    {
        $this->db->select('*');
        $this->db->order_by('kategori_nama', 'asc');
        return $this->db->get('tbl_kategori');
    }

    function get_subkategori($kategori_id = null)
    {
        if ($kategori_id && $this->db->field_exists('kategori_id', 'tbl_subkategori')) {
            $this->db->where('kategori_id', $kategori_id);
        }
        $this->db->order_by('subkategori_nama', 'asc');
        return $this->db->get('tbl_subkategori');
    }

    function getUrutan($table, $order)
    {
        $this->db->order_by($order, 'DESC');
        return $this->db->get($table);
    }

    function updateHarga($table, $id, $update)
    {
        $this->db->where($id);
        return $this->db->update($table, $update);
    }

    function cekjml($table, $field, $id)
    {
        $this->db->where($field, $id);
        return $this->db->get($table);
    }





    // public function getUser(){
    //     $user = $this->session->userdata('id');
    //     $this->db->where('user_id', $user); 
    //     $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = tbl_user.karyawan_id');
    //     $this->db->join('tbl_divisi', 'tbl_divisi.divisi_id = tbl_user.divisi_id');
    //     return $this->db->get('tbl_user');
    // }

    public function getAllUser()
    {
        $current_user = $this->session->userdata('username');
        $where_clause = "";
        if ($current_user !== 'nizarsuryadi') {
            $where_clause = " WHERE b.username != 'nizarsuryadi'";
        }

        return $this->db->query("SELECT b.*, c.role_name as role_nama, b.active as status 
            FROM users b 
            LEFT JOIN roles c ON b.role_id = c.role_id 
            $where_clause");
    }



    public function getPerusahaan()
    {
        return $this->db->get('tbl_perusahaan');
    }



    public function cekProduct($subkategori, $satuan)
    {
        // $this->db->where('perusahaan_id', $perusahaan);
        $this->db->where('subkategori_id', $subkategori);
        // $this->db->where('bahan_id', $bahanid);
        $this->db->where('satuan_id', $satuan);
        return $this->db->get('tbl_product');
    }

    function jml_detail($product)
    {
        $this->db->where('product_id', $product);
        return $this->db->get('tbl_harga_product');
    }

    function jml_routing($product)
    {
        $this->db->where('product_id', $product);
        return $this->db->get('routing');
    }

    function jml_bom($product)
    {
        $this->db->where('product_id', $product);
        return $this->db->get('bom_lines');
    }

    function getDataProductMarketplace($metode_pesan)
    {
        // Hanya ambil produk marketplace berdasarkan tipe_id
        // Asumsikan tipe_id = 2 adalah untuk produk marketplace
        $this->db->where('tbl_product.detail_id', $metode_pesan);

        return $this->db->get('tbl_product');
    }

    public function get_karyawan_by_nik($nik)
    {
        $this->db->where('nik', $nik);
        return $this->db->get('tbl_karyawan')->row();
    }
}

?>
