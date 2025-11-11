<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // ✅ tampilkan error di browser

include '../koneksi/koneksi.php';
header('Content-Type: application/json');

if (!$conn) {
  echo json_encode(['status' => 'error', 'message' => 'Gagal koneksi ke database!']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  $query = "SELECT u.id, u.nama, u.email, u.password, r.nama_role
            FROM user u
            JOIN role r ON u.role_id = r.id
            WHERE u.email = '$email'";

  $result = mysqli_query($conn, $query);

  // Tambahkan debug
  if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Query gagal: ' . mysqli_error($conn)]);
    exit;
  }

  if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    if (password_verify($password, $user['password'])) {
      echo json_encode([
        'status' => 'success',
        'message' => 'Login berhasil!',
        'role' => $user['nama_role']
      ]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Password salah!']);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan!']);
  }
} else {
  echo json_encode(['status' => 'error', 'message' => 'Metode tidak valid!']);
}
?>
