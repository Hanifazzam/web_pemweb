<?php
include '../includes/auth_check.php';
include '../includes/role_check.php';
include '../koneksi/koneksi.php';
header('Content-Type: application/json');

$periode = $_GET['periode'] ?? 'harian';

// Sesuaikan nama kolom dan tabel sesuai database kamu
// misal: kolom total_harga dan tanggal_pesanan
switch ($periode) {
  case 'harian':
    $query = "SELECT SUM(total_harga) AS total FROM orders WHERE DATE(tanggal_pesanan) = CURDATE() AND status='selesai'";
    break;
  case 'mingguan':
    $query = "SELECT SUM(total_harga) AS total FROM orders WHERE YEARWEEK(tanggal_pesanan) = YEARWEEK(CURDATE()) AND status='selesai'";
    break;
  case 'bulanan':
    $query = "SELECT SUM(total_harga) AS total FROM orders WHERE MONTH(tanggal_pesanan) = MONTH(CURDATE()) AND YEAR(tanggal_pesanan) = YEAR(CURDATE()) AND status='selesai'";
    break;
  case 'tahunan':
    $query = "SELECT SUM(total_harga) AS total FROM orders WHERE YEAR(tanggal_pesanan) = YEAR(CURDATE()) AND status='selesai'";
    break;
  default:
    $query = "SELECT SUM(total_harga) AS total FROM orders WHERE DATE(tanggal_pesanan) = CURDATE() AND status='selesai'";
    break;
}

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$total = $data['total'] ?? 0;

echo json_encode(['total' => $total]);
?>
