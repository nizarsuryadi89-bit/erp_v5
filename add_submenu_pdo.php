<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=tgm_erp', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Find Transaksi menu ID
    $stmt = $db->query("SELECT id_menu FROM menu WHERE link = 'transaksi' LIMIT 1");
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($menu) {
        $menu_id = $menu['id_menu'];
        
        // Check if submenu exists
        $check = $db->prepare("SELECT id FROM submenu WHERE link_submenu = 'papan_sortir' AND id_menu = ?");
        $check->execute([$menu_id]);
        
        if ($check->rowCount() == 0) {
            $db->prepare("INSERT INTO submenu (id_menu, nama_submenu, link_submenu, urutan) VALUES (?, 'Papan Sortir (Logistik)', 'papan_sortir', 99)")
               ->execute([$menu_id]);
            echo "Submenu 'Papan Sortir' added to Transaksi menu.\n";
        } else {
            echo "Submenu already exists.\n";
        }
    } else {
        echo "Error: Menu 'transaksi' not found.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
