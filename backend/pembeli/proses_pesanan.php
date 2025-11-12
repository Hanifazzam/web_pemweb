<?php
// (DIUBAH) Mulai session di atas untuk mengambil user_id
session_start();
include '../koneksi/koneksi.php'; 

header('Content-Type: application/json');

// Ambil ID user dari session.
$id_user_login = null;
if (isset($_SESSION['id_user'])) {
    $id_user_login = $_SESSION['id_user'];
}

// Ambil data JSON yang dikirim oleh JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Validasi data dasar
if (empty($data['nama_pemesan']) || empty($data['keranjang']) || !isset($data['total_harga'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
    exit;
}

$nama_pemesan = $data['nama_pemesan']; // "Budi"
$total_harga = $data['total_harga'];   // 50000
$keranjang = $data['keranjang'];     // { "Kentang": { id: 2, ... } }

// Mulai Transaksi Database
mysqli_begin_transaction($conn);

try {
    // 1. Masukkan ke tabel `orders` (plural)
    
    // (DIUBAH) Menggunakan nama tabel 'orders' dan memasukkan 'user_id'
    $query_pesanan = "INSERT INTO `orders` (user_id, nama_pemesan, total_harga, status) VALUES (?, ?, ?, 'pending')";
    
    $stmt_pesanan = mysqli_prepare($conn, $query_pesanan);
    if (!$stmt_pesanan) {
        throw new Exception("Gagal menyiapkan query orders: " . mysqli_error($conn));
    }
    
    // (DIUBAH) Tipe data bind menjadi 'isd' (integer, string, double)
    mysqli_stmt_bind_param($stmt_pesanan, 'isd', 
        $id_user_login,
        $nama_pemesan, 
        $total_harga
    );
    mysqli_stmt_execute($stmt_pesanan);

    // Ambil ID dari pesanan yang baru saja dibuat
    $id_pesanan_baru = mysqli_insert_id($conn);
    
    if ($id_pesanan_baru == 0) {
        throw new Exception('Gagal mendapatkan ID pesanan baru.');
    }
    
    // 2. Siapkan query untuk `order_items`
    // (Nama tabel 'order_items' Anda sudah benar)
    $query_detail = "INSERT INTO order_item (order_id, menu_id, jumlah, subtotal) VALUES (?, ?, ?, ?)";
    
    $stmt_detail = mysqli_prepare($conn, $query_detail);
    if (!$stmt_detail) {
        throw new Exception('Gagal menyiapkan query order_items: ' . mysqli_error($conn));
    }

    // 3. Loop melalui keranjang dan masukkan ke `order_items`
    foreach ($keranjang as $nama_menu => $item) {
        $menu_id = $item['id'];
        $qty = $item['quantity'];
        $subtotal = $item['subtotal'];
        
        mysqli_stmt_bind_param($stmt_detail, 'iidd', 
            $id_pesanan_baru, // order_id
            $menu_id,         // menu_id
            $qty,             // jumlah
            $subtotal         // subtotal
        );
        mysqli_stmt_execute($stmt_detail);
    }

    // Jika semua berhasil, commit transaksinya
    mysqli_commit($conn);
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Jika ada satu saja yang gagal, rollback (batalkan) semua
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Tutup statement dan koneksi
if (isset($stmt_pesanan)) mysqli_stmt_close($stmt_pesanan);
if (isset($stmt_detail)) mysqli_stmt_close($stmt_detail);
mysqli_close($conn);

?>