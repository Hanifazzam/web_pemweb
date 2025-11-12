<?php
include '../koneksi/koneksi.php';
session_start();

if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pembeli'){
    die("Akses ditolak.");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id_menu = $_POST['id_menu'];
    $jumlah  = $_POST['jumlah'];
    $id_pembeli = $_SESSION['id_user']; // gunakan id_user dari session

    // ... lanjutkan proses order seperti sebelumnya
}
?>
