<?php
// File: backend/pembeli/status_pesanan_api.php
// (VERSI FINAL - DISESUAIKAN DENGAN NAMA TABEL ANDA)

session_start();
include '../koneksi/koneksi.php';
header('Content-Type: application/json');

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk melihat status pesanan.']);
    exit;
}

$id_user_login = $_SESSION['id_user'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_my_orders':
        try {
            // (BENAR) Menggunakan tabel `orders` Anda (plural)
            $query = "SELECT id, total_harga, status, tanggal_pesan 
                      FROM `orders` 
                      WHERE user_id = ? 
                      AND status IN ('pending', 'proses', 'ready')
                      ORDER BY tanggal_pesan DESC";
                      
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $id_user_login);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $orders = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $orders[] = $row;
            }
            echo json_encode(['success' => true, 'orders' => $orders]);
            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'get_order_detail':
        try {
            if (!isset($_GET['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID Pesanan tidak ada.']);
                exit;
            }
            $order_id = $_GET['id'];

            // (PERBAIKAN KUNCI ADA DI SINI)
            $query = "SELECT oi.jumlah, oi.subtotal, m.nama_menu
                      FROM order_item oi  /* <-- (DIUBAH) Menjadi 'order_item' (singular) */
                      JOIN menu m ON oi.menu_id = m.id  /* (DIUBAH) Menjadi 'm.id' (bukan m.id_menu) */
                      JOIN `orders` o ON oi.order_id = o.id /* (BENAR) Menggunakan 'orders' (plural) */
                      WHERE oi.order_id = ? AND o.user_id = ?";
            
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ii', $order_id, $id_user_login);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $items = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $items[] = $row;
            }
            echo json_encode(['success' => true, 'items' => $items]);
            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
}

mysqli_close($conn);
?>