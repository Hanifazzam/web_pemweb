<?php
include '../koneksi/koneksi.php';

// cek POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ambil input
    $nama = $_POST['nama_menu'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $id_kategori = $_POST['id_kategori'];

    // upload gambar
    $file_name = ''; // Inisialisasi
    if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] == 0) {
        $target_dir = "../../frontend/assets/images/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_type, $allowed_types)) {
            echo "<script>alert('Hanya file gambar (JPG, JPEG, PNG, GIF) yang diperbolehkan!'); history.back();</script>";
            exit;
        }

        if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            echo "<script>alert('Gagal mengupload gambar!'); history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Error: Gambar tidak ditemukan atau gagal diupload.'); history.back();</script>";
        exit;
    }

    // simpan ke DB (prepared)
    $query = "INSERT INTO menu (nama_menu, harga, deskripsi, gambar, id_kategori)
              VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        echo "<script>alert('Gagal menyiapkan statement: " . mysqli_error($conn) . "'); history.back();</script>";
        exit;
    }

    // tipe params: s = string, d = double, i = int
    mysqli_stmt_bind_param($stmt, "sdssi", $nama, $harga, $deskripsi, $file_name, $id_kategori);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Menu berhasil ditambahkan!'); window.location.href='../../frontend/admin/data_menu.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan ke database: " . mysqli_stmt_error($stmt) . "'); history.back();</script>";
    }

    mysqli_stmt_close($stmt);

} else {
    echo "<script>alert('Metode tidak valid!'); history.back();</script>";
}

// tutup koneksi
mysqli_close($conn);
?>