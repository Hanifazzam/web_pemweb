<?php
include __DIR__ . '/../koneksi/koneksi.php';
session_start();

// cek session pembeli
if(!isset($_SESSION['id_user'])) $_SESSION['id_user'] = 1;
if(!isset($_SESSION['role'])) $_SESSION['role'] = 'pembeli';

$sql = "SELECT 
            id AS id_menu,  /* <-- INI PERUBAHAN KUNCINYA */
            nama_menu,
            deskripsi,
            harga,
            gambar,
            id_kategori
        FROM 
            menu";
$result = $conn->query($sql);
if(!$result){
    die("Query gagal: " . $conn->error);
}

$menu = [];
while($row = $result->fetch_assoc()){
    $menu[] = $row;
}