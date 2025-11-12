<?php
// File: backend/kasir/kasir_api.php
// (VERSI DIPERBARUI DENGAN FILTER STATUS)

include '../koneksi/koneksi.php';
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    // (DIUBAH) Mengganti nama aksi agar lebih fleksibel
    case 'get_orders_by_status':
        getOrdersByStatus($conn);
        break;
        
    case 'get_order_detail':
        getOrderDetail($conn);
        break;
        
    case 'update_order_status':
        updateOrderStatus($conn);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
}

mysqli_close($conn);

// --- FUNGSI-FUNGSI ---

// (DIUBAH) Fungsi ini sekarang dinamis berdasarkan status
function getOrdersByStatus($conn) {
    // Ambil status dari URL, default-nya 'pending' jika tidak ada
    $status = isset($_GET['status']) ? $_GET['status'] : 'pending';

    // Validasi status untuk keamanan
    $allowed_statuses = ['pending', 'proses', 'ready', 'selesai', 'dibatalkan'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid.']);
        return;
    }

    // Kueri sekarang menggunakan parameter (?)
    $query = "SELECT id, nama_pemesan, total_harga, status, tanggal_pesan 
              FROM `orders` 
              WHERE status = ?  /* <-- (DIUBAH) */
              ORDER BY tanggal_pesan ASC";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Query getOrders gagal: ' . mysqli_error($conn)]);
        return;
    }

    mysqli_stmt_bind_param($stmt, 's', $status);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    echo json_encode(['success' => true, 'orders' => $orders]);
    mysqli_stmt_close($stmt);
}

// (Fungsi ini tidak berubah)
function getOrderDetail($conn) {
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID Pesanan tidak ada.']);
        return;
    }
    $order_id = $_GET['id'];
    
    $query = "SELECT * FROM order_item WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    echo json_encode(['success' => true, 'items' => $items]);
    mysqli_stmt_close($stmt);
}

// (Fungsi ini tidak berubah)
function updateOrderStatus($conn) {
    if (!isset($_GET['id']) || !isset($_GET['status'])) {
        echo json_encode(['success' => false, 'message' => 'ID atau Status tidak ada.']);
        return;
    }
    
    $order_id = $_GET['id'];
    $new_status = $_GET['status'];
    
    $allowed_statuses = ['proses', 'ready', 'selesai', 'dibatalkan'];
    if (!in_array($new_status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid.']);
        return;
    }

    $query = "UPDATE `orders` SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $new_status, $order_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update status.']);
    }
    mysqli_stmt_close($stmt);
}
?>