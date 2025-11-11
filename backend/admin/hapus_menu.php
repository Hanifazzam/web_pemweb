<?php
include '../koneksi/koneksi.php';

// Pastikan parameter id dikirim
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil nama file gambar dulu biar bisa dihapus juga
    $query_gambar = "SELECT gambar FROM menu WHERE id = $id";
    $result_gambar = mysqli_query($conn, $query_gambar);
    $data = mysqli_fetch_assoc($result_gambar);
    $gambar = $data['gambar'];

    // Hapus data dari database
    $query = "DELETE FROM menu WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        // Hapus file gambar dari folder jika ada
        if (!empty($gambar) && file_exists("../../frontend/assets/images/$gambar")) {
            unlink("../../frontend/assets/images/$gambar");
        }

        echo "<script>alert('Menu berhasil dihapus!'); window.location.href='../../frontend/admin/data_menu.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($conn) . "'); history.back();</script>";
    }
} else {
    echo "<script>alert('ID menu tidak ditemukan!'); history.back();</script>";
}
?>
