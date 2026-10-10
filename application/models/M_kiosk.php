<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_kiosk extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_transaksi');
        $this->ensure_order_table();
    }

    public function ensure_order_table()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS tbl_pemesanan_mandiri (
            id INT AUTO_INCREMENT PRIMARY KEY,
            no_ref VARCHAR(20) NOT NULL,
            konsumen_id INT,
            konsumen_nama VARCHAR(100),
            no_wa VARCHAR(20),
            perusahaan_id INT,
            product_id INT,
            harga_id INT,
            qty DECIMAL(10,2),
            panjang DECIMAL(10,2),
            lebar DECIMAL(10,2),
            total_harga DECIMAL(15,2),
            catatan TEXT,
            status VARCHAR(20) DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(no_ref),
            INDEX(konsumen_id),
            INDEX(status)
        )");

        $fields = $this->db->field_data('tbl_pemesanan_mandiri');
        $columns = array();
        foreach ($fields as $field) {
            $columns[] = $field->name;
        }

        if (!in_array('konsumen_id', $columns, true)) {
            $this->db->query("ALTER TABLE tbl_pemesanan_mandiri ADD COLUMN konsumen_id INT AFTER no_ref");
            $this->db->query("ALTER TABLE tbl_pemesanan_mandiri ADD INDEX(konsumen_id)");
        }

        if (!in_array('konsumen_nama', $columns, true)) {
            $this->db->query("ALTER TABLE tbl_pemesanan_mandiri ADD COLUMN konsumen_nama VARCHAR(100) AFTER konsumen_id");
        }

        if (!in_array('no_wa', $columns, true)) {
            $this->db->query("ALTER TABLE tbl_pemesanan_mandiri ADD COLUMN no_wa VARCHAR(20) AFTER konsumen_nama");
        }
    }

    /**
     * Generate a unique reference number for Kiosk order
     * Format: K [YYMM] [4-digit sequence]
     */
    public function generate_ref()
    {
        $prefix = 'K' . date('ym');
        $this->db->like('no_ref', $prefix, 'after');
        $this->db->order_by('no_ref', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get('tbl_pemesanan_mandiri')->row();

        if ($last) {
            $seq = (int) substr($last->no_ref, 5) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get active products for kiosk selection
     */
    public function get_products()
    {
        $this->db->select('p.product_id, p.product_nama, k.kategori_nama, s.satuan_nama');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_kategori k', 'p.kategori_id = k.kategori_id', 'left');
        $this->db->join('tbl_satuan s', 'p.satuan_id = s.satuan_id', 'left');
        $this->db->join('tbl_harga_product hp', 'hp.product_id = p.product_id');
        $this->db->group_by('p.product_id');
        $this->db->order_by('k.kategori_nama', 'ASC');
        $this->db->order_by('p.product_nama', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get pricing options for a product
     */
    public function get_product_variants($product_id)
    {
        $this->db->where('product_id', $product_id);
        return $this->db->get('tbl_harga_product')->result();
    }

    /**
     * Save BATCH order (Cart)
     * Reuses existing no_ref if customer has pending orders (submitted status)
     */
    public function save_batch_order($items)
    {
        if (empty($items)) {
            return false;
        }

        // Check if this customer already has a pending no_ref (submitted status)
        $konsumen_id = $items[0]['konsumen_id'];
        $this->db->select('no_ref');
        $this->db->where('konsumen_id', $konsumen_id);
        $this->db->where('status', 'submitted'); // Only submitted (awaiting validation)
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $existing = $this->db->get('tbl_pemesanan_mandiri')->row();

        // Use existing ref if found, otherwise generate new
        if ($existing && !empty($existing->no_ref)) {
            // Extract base ref (remove -1, -2 suffix if exists)
            $base_ref = preg_replace('/-\d+$/', '', $existing->no_ref);

            // Get max index for this ref
            $this->db->select('no_ref');
            $this->db->like('no_ref', $base_ref, 'after');
            $this->db->order_by('no_ref', 'DESC');
            $this->db->limit(1);
            $last_item = $this->db->get('tbl_pemesanan_mandiri')->row();

            if ($last_item) {
                // Extract index from K26020001-5 format
                preg_match('/-(\d+)$/', $last_item->no_ref, $matches);
                $i = $matches ? intval($matches[1]) + 1 : 1;
            } else {
                $i = 1;
            }
        } else {
            // Generate new ref for new customer session
            $base_ref = $this->generate_ref();
            $i = 1;
        }

        // Insert items with sequential indexing
        foreach ($items as &$item) {
            $item['no_ref'] = $base_ref . '-' . $i;
            $this->db->insert('tbl_pemesanan_mandiri', $item);
            $i++;
        }

        return $base_ref;
    }

    /**
     * Get draft by Ref ID (Returns Array of Items)
     */
    public function get_by_ref($no_ref)
    {
        $this->db->select('pm.*, p.product_nama, hp.detail_product, hp.harga_aktif');
        $this->db->from('tbl_pemesanan_mandiri pm');
        $this->db->join('tbl_product p', 'pm.product_id = p.product_id', 'left');
        $this->db->join('tbl_harga_product hp', 'pm.harga_id = hp.harga_id', 'left');
        // Use LIKE to match REF-1, REF-2, etc.
        $this->db->like('pm.no_ref', $no_ref, 'after');
        return $this->db->get()->result(); // Now returns multiple rows
    }
}
