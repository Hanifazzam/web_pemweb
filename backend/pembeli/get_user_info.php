<?php
// File: backend/pembeli/get_user_info.php

session_start();
include '../koneksi/koneksi.php';

// Atur header sebagai JSON
header('Content-Type: application/json');

// Siapkan balasan default
$response = [
    'success' => false,
    'nama' => 'Tamu', // Default jika tidak ada yang login
    'is_guest' => true
];

if (isset($_SESSION['id_user'])) {
    $id_user_login = $_SESSION['id_user'];
    
    // Asumsi: tabel 'users', kolom 'id_user', kolom 'nama'
    $query_user = "SELECT nama FROM users WHERE id_user = ?";
    $stmt_user = mysqli_prepare($conn, $query_user);
    
    if ($stmt_user) {
        mysqli_stmt_bind_param($stmt_user, 'i', $id_user_login);
        mysqli_stmt_execute($stmt_user);
        $result_user = mysqli_stmt_get_result($stmt_user);
        
        if ($row_user = mysqli_fetch_assoc($result_user)) {
            // Sukses! User ditemukan
            $response['success'] = true;
            $response['nama'] = $row_user['nama'];
            $response['is_guest'] = false; // Ini bukan tamu
        } else {
            // ID di session, tapi tidak ada di DB? Aneh, tapi kita tangani.
            $response['message'] = 'User ID in session not found in database.';
        }
        mysqli_stmt_close($stmt_user);
    } else {
        $response['message'] = 'Database query failed.';
    }
} else {
    // Tidak ada session id_user, dia adalah Tamu
    $response['message'] = 'User not logged in.';
}

mysqli_close($conn);

// Kirim balasan JSON
echo json_encode($response);
?>