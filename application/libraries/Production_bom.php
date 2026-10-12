<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Resolve stock variants once, identically for cashier deductions and WIP forms. */
class Production_bom
{
    private $db;
    private $products = [], $prices = [], $ordinary = [], $dynamic = [], $materials = [];

    public function __construct($params = [])
    {
        $this->db = $params['db'] ?? get_instance()->db;
    }

    public function warm($ids)
    {
        $ids = array_values(array_filter(array_unique(array_map('intval', $ids)), function ($id) {
            return $id > 0 && !array_key_exists($id, $this->products);
        }));
        if (!$ids) return;
        foreach ($ids as $id) {
            $this->products[$id] = null; $this->prices[$id] = $this->ordinary[$id] = $this->dynamic[$id] = [];
        }
        foreach ($this->db->where_in('product_id', $ids)->get('tbl_product')->result_array() as $p) $this->products[$p['product_id']] = $p;
        foreach (['tbl_harga_product' => 'prices', 'bom_lines' => 'ordinary', 'tbl_bom_dynamic_mapping' => 'dynamic'] as $table => $property) {
            if (!$this->db->table_exists($table)) continue;
            foreach ($this->db->where_in('product_id', $ids)->get($table)->result_array() as $r) $this->{$property}[$r['product_id']][] = $r;
        }
        $refs = [];
        foreach ($ids as $id) foreach (array_merge($this->ordinary[$id], $this->dynamic[$id]) as $r) if (!empty($r['bahan_id'])) $refs[] = (int)$r['bahan_id'];
        $refs = array_values(array_unique($refs));
        if (!$refs) return;
        // Older mappings stored the parent bahan_id; load that family's variants too.
        $rows = $this->db->select('m.*, b.bahan_nama, b.satuan_id, s.satuan_nama')
            ->from('tbl_material m')->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left')
            ->join('tbl_satuan s', 's.satuan_id = b.satuan_id', 'left')
            ->group_start()->where_in('m.material_id', $refs)->or_where_in('m.bahan_id', $refs)->group_end()->get()->result_array();
        foreach ($rows as $r) $this->materials[$r['material_id']] = $r;
    }

    public function resolve($item)
    {
        $id = (int)$item->product_id;
        $this->warm([$id]);
        return self::select($item, $this->products[$id] ?? [], $this->prices[$id], $this->ordinary[$id], $this->dynamic[$id], $this->materials);
    }

    private static function json($value)
    {
        if (is_array($value)) return $value;
        $decoded = json_decode((string)$value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private static function label($value)
    {
        return is_scalar($value) ? strtolower(trim(preg_replace('/\s+/u', ' ', (string)$value))) : '';
    }

    /** Option IDs and display labels are compared as strings, never PHP loose equality. */
    public static function attribute($key, $order, $price, $schema)
    {
        $base = preg_replace('/_id$/', '', $key);
        $aliases = array_unique([$key, $base, $base . '_id']);
        $field = null;
        foreach ($schema as $f) if (in_array($f['name'] ?? '', $aliases, true)) { $field = $f; break; }
        // Legacy order forms put the size selector in ket_1 instead of ukuran.
        if ($field && in_array($base, ['ukuran', 'size'], true)) $aliases[] = 'ket_1';
        foreach ([$order, $price] as $metadata) {
            foreach ($aliases as $alias) {
                if (!array_key_exists($alias, $metadata) || !is_scalar($metadata[$alias]) || trim((string)$metadata[$alias]) === '') continue;
                $raw = (string)$metadata[$alias];
                $options = $field['options'] ?? [];
                if (array_key_exists($raw, $options)) return self::label($options[$raw]);
                return self::label($raw);
            }
        }
        return '';
    }

    private static function material($id, $value, $materials, $dynamic)
    {
        if (isset($materials[$id])) return $materials[$id];
        // Only rescue a missing material ID when one parent-family variant matches exactly.
        $matches = [];
        if ($dynamic) foreach ($materials as $m) {
            if ((int)$m['bahan_id'] !== (int)$id) continue;
            $variant = self::label($m['varian'] ?? '');
            $suffix = self::label($value);
            if ($suffix !== '' && ($variant === $suffix || substr($variant, -strlen(' ' . $suffix)) === ' ' . $suffix)) $matches[] = $m;
        }
        if (count($matches) === 1) return $matches[0];
        throw new DomainException('Material BOM tidak valid atau ambigu (ID ' . (int)$id . '). Periksa mapping varian stok.');
    }

    private static function number($value, $name)
    {
        if (!is_numeric($value) || !is_finite((float)$value) || (float)$value < 0) throw new DomainException($name . ' BOM tidak valid.');
        return (float)$value;
    }

    private static function dynamic_quantity($row, $item, $meta)
    {
        $p = self::number($item->panjang ?? $meta['panjang'] ?? 0, 'Panjang');
        $l = self::number($item->lebar ?? $meta['lebar'] ?? 0, 'Lebar');
        switch ($row['calc_type'] ?? 'fixed') {
            case 'area': $qty = $p * $l; break;
            case 'linear_meter': $qty = $p; break;
            case 'fixed': case 'sheets_plano': $qty = 1; break;
            default: throw new DomainException('Jenis perhitungan BOM dinamis tidak valid.');
        }
        $key = $row['multiplier_key'] ?? '';
        if ($key !== '' && $key !== null) {
            if (!is_numeric($key) && !isset($meta[$key])) throw new DomainException('Pengali BOM belum terisi: ' . $key . '.');
            $qty *= self::number(is_numeric($key) ? $key : $meta[$key], 'Pengali');
        }
        $divider = self::number($row['divider_factor'] ?? 1, 'Pembagi');
        if (!$divider) throw new DomainException('Pembagi BOM harus lebih dari nol.');
        return $qty / $divider * (1 + self::number($row['scrap_factor'] ?? 0, 'Waste') / 100);
    }

    public static function select($item, $product, $prices, $ordinary, $dynamic, $materials)
    {
        $schema = self::json($product['form_schema'] ?? '');
        $order = self::json($item->metadata ?? '');
        $price = [];
        foreach ($prices as $r) if ((int)$r['harga_id'] === (int)($item->harga_id ?? 0)) { $price = self::json($r['metadata'] ?? ''); break; }
        $shirt_color = null;
        if (preg_match('/\[(Hitam|Putih|Warna Lain)\]\s*$/i', $product['product_nama'] ?? '', $match) &&
            preg_match('/\bkaos\b/i', $product['product_nama'] ?? '')) {
            $shirt_color = self::label($match[1]);
            $ordered_color = self::label($order['warna'] ?? $order['jenisprint'] ?? '');
            if (in_array($ordered_color, ['hitam', 'putih', 'warna lain'], true) && $ordered_color !== $shirt_color) {
                throw new DomainException('Warna pesanan kaos berbeda dari produk yang dipilih. Periksa warna dan pilih produk kaos yang sesuai sebelum mengurangi stok.');
            }
        }
        $specific = []; $global = [];
        foreach ($ordinary as $r) {
            if (empty($r['harga_id'])) $global[] = $r;
            elseif (!empty($item->harga_id) && (int)$r['harga_id'] === (int)$item->harga_id) $specific[] = $r;
        }
        // A sibling price tier of the same variant can provide its complete BOM.
        if (!$specific && $price) {
            foreach ($prices as $r) {
                $candidate = self::json($r['metadata'] ?? '');
                if (!$candidate) continue;
                $candidate_sorted = $candidate; $price_sorted = $price;
                ksort($candidate_sorted); ksort($price_sorted);
                $same = $schema ? true : json_encode($candidate_sorted) === json_encode($price_sorted);
                foreach ($schema as $f) {
                    if (empty($f['options'])) continue;
                    $key = $f['name'];
                    if (self::attribute($key, $order, $price, $schema) !== self::attribute($key, [], $candidate, $schema)) { $same = false; break; }
                }
                if ($same) foreach ($ordinary as $line) if ((int)($line['harga_id'] ?? 0) === (int)$r['harga_id']) $specific[] = $line;
                if ($specific) break; // Do not consume identical BOMs from multiple price tiers.
            }
        }
        $selected = []; $families = []; $keys = [];
        foreach ($dynamic as $r) {
            if (!empty($schema)) {
                $base = preg_replace('/_id$/', '', $r['attribute_key']);
                $inSchema = false;
                foreach ($schema as $f) {
                    $fBase = preg_replace('/_id$/', '', $f['name'] ?? '');
                    if (($f['name'] ?? '') === $r['attribute_key'] || $fBase === $base) {
                        $inSchema = true;
                        break;
                    }
                }
                if (!$inSchema) continue;
            }
            $keys[$r['attribute_key']][] = $r;
        }
        foreach ($keys as $key => $rows) {
            $value = self::attribute($key, $order, $price, $schema);
            if ($value === '') throw new DomainException('Pilihan variasi BOM belum terisi: ' . $key . '.');
            $matching = array_filter($rows, function ($r) use ($value) { return self::label($r['attribute_value']) === $value; });
            if (!$matching) {
                // A valid schema option may intentionally have no consumption (e.g. no lamination).
                $optional = false;
                foreach ($schema as $f) if (preg_replace('/_id$/', '', $f['name'] ?? '') === preg_replace('/_id$/', '', $key)) {
                    foreach (($f['options'] ?? []) as $label) if (self::label($label) === $value) $optional = true;
                }
                if (!$optional) throw new DomainException('Mapping BOM untuk ' . $key . ' = ' . $value . ' tidak ditemukan.');
            }
            foreach ($matching as $r) {
                $m = self::material($r['bahan_id'], $r['attribute_value'], $materials, true);
                if ($shirt_color !== null && preg_match('/\b(Hitam|Putih|Warna Lain)\s+(?:S|M|L|XL|[2-5]XL)\s*$/i', $m['varian'] ?? '', $match) && self::label($match[1]) !== $shirt_color) {
                    throw new DomainException('Warna material BOM kaos tidak sesuai produk. Periksa mapping ukuran ' . $r['attribute_value'] . '.');
                }
                $families[(int)$m['bahan_id']] = true;
                $qty = self::dynamic_quantity($r, $item, array_replace($price, $order));
                $selected[] = self::line($r, $m, $qty, true);
            }
        }
        foreach (($specific ?: $global) as $r) {
            if (empty($r['bahan_id'])) continue; // Jasa-only BOM lines have no physical stock.
            $m = self::material($r['bahan_id'], '', $materials, false);
            if (isset($families[(int)$m['bahan_id']])) continue; // Variant mapping replaces that family's generic BOM.
            $qty = self::number($r['quantity'] ?? 0, 'Jumlah');
            $selected[] = self::line($r, $m, $qty, false);
        }
        return $selected;
    }

    private static function line($row, $material, $qty, $dynamic)
    {
        $line = (object)$row;
        $line->bom_line_id = $dynamic ? 'dyn_' . $row['id'] : $row['bom_line_id'];
        $line->bom_qty = $qty;
        // WIP estimates dynamic usage per item and multiplies by ordered quantity.
        if ($dynamic) $line->quantity = $qty;
        $line->bom_material_id = (int)$material['material_id'];
        $line->bahan_id = (int)$material['bahan_id'];
        $line->bahan_nama = ($material['varian'] ?? '') ?: ($material['bahan_nama'] ?? '');
        $line->satuan_id = $material['satuan_id'] ?? null;
        $line->satuan_nama = $material['satuan_nama'] ?? 'unit';
        $line->konversi = $material['konversi'] ?? 1;
        $line->panjang = $row['panjang'] ?? null; $line->lebar = $row['lebar'] ?? null;
        $line->is_dynamic = $dynamic;
        return $line;
    }

    public static function usage($line, $item)
    {
        $qty = self::number($item->qty ?? 0, 'Qty pesanan') * self::number($line->bom_qty, 'Jumlah');
        if (!$line->is_dynamic) {
            switch ($line->calc_type ?? 'fixed') {
                case 'area': $qty *= self::number($item->panjang ?? 0, 'Panjang') * self::number($item->lebar ?? 0, 'Lebar'); break;
                case 'length': case 'linear_meter': $qty *= self::number($item->panjang ?? 0, 'Panjang'); break;
                case 'weight': $qty *= self::number($item->berat ?? 0, 'Berat'); break;
                case 'fixed': case 'sheets_plano': break;
                default: throw new DomainException('Jenis perhitungan BOM tidak valid.');
            }
            $qty *= 1 + self::number($line->scrap_factor ?? 0, 'Waste') / 100;
        }
        if (!is_finite($qty)) throw new DomainException('Jumlah pemakaian BOM terlalu besar.');
        if ($qty <= 0 && (float)$line->bom_qty > 0 && (float)($item->qty ?? 0) > 0) throw new DomainException('Dimensi atau jumlah pemakaian BOM belum terisi.');
        return $qty;
    }
}
