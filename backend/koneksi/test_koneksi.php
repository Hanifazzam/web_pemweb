<?php
include 'koneksi.php';

if ($conn) {
  echo "✅ Koneksi berhasil!";
} else {
  echo "❌ Gagal koneksi: " . mysqli_connect_error();
}
?>
