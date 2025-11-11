<?php
include '../koneksi/koneksi.php';
header('Content-Type: application/json');

// aktifkan debug sementara
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = 2; // default: pembeli

    // cek email sudah ada belum
    $cek = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email sudah terdaftar!']);
        exit;
    }

    // simpan ke database
    $query = "INSERT INTO user (nama, email, no_hp, password, role_id)
              VALUES ('$nama', '$email', '$no_hp', '$password', '$role_id')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Pendaftaran berhasil!']);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan data ke database: ' . mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak valid!']);
}
?>
