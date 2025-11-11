<?php
include '../koneksi/koneksi.php';

// Pastikan form dikirim via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_menu'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    // Folder tempat menyimpan gambar
    $target_dir = "../../frontend/assets/images/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["gambar"]["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validasi file
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_type, $allowed_types)) {
        echo "<script>alert('Hanya file gambar (JPG, JPEG, PNG, GIF) yang diperbolehkan!'); history.back();</script>";
        exit;
    }

    // Pindahkan file ke folder tujuan
    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
        // Simpan ke database
        $gambar = $file_name;
        $query = "INSERT INTO menu (nama_menu, harga, deskripsi, gambar)
                  VALUES ('$nama', '$harga', '$deskripsi', '$gambar')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Menu berhasil ditambahkan!'); window.location.href='../../frontend/admin/data_menu.html';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan ke database: " . mysqli_error($conn) . "'); history.back();</script>";
        }
    } else {
        echo "<script>alert('Gagal mengupload gambar!'); history.back();</script>";
    }
} else {
    echo "<script>alert('Metode tidak valid!'); history.back();</script>";
}
?>
