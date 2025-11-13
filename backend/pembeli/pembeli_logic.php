<?php
// Batasi waktu eksekusi biar tidak hang lama
ini_set('max_execution_time', 10);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/../koneksi/koneksi.php';
session_start();

// Cek koneksi ke database
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Cek session pembeli
if (!isset($_SESSION['id_user'])) $_SESSION['id_user'] = 1;
if (!isset($_SESSION['role'])) $_SESSION['role'] = 'pembeli';

// Jalankan query untuk ambil menu
$sql = "SELECT 
            id AS id_menu,
            nama_menu,
            deskripsi,
            harga,
            gambar,
            id_kategori
        FROM menu";

$result = $conn->query($sql);

// Jika query gagal, tampilkan pesan error
if (!$result) {
    die("Query gagal: " . $conn->error);
}

// Buat array untuk menampung semua data menu
$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = [
        'id' => $row['id_menu'],
        'nama_menu' => $row['nama_menu'],
        'deskripsi' => $row['deskripsi'],
        'harga' => $row['harga'],
        'gambar' => $row['gambar'],
        'kategori' => $row['id_kategori']
    ];
}

// Tutup koneksi agar tidak menggantung
$conn->close();
?>
