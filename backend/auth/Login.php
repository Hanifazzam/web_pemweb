<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_set_cookie_params(['path'=>'/pemweb/']);
session_start();

header('Content-Type: application/json');
include '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Metode request tidak diperbolehkan']);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(empty($email) || empty($password)){
    echo json_encode(['status'=>'error','message'=>'Email dan password wajib diisi']);
    exit;
}

// Ambil user dari DB
$stmt = $conn->prepare("SELECT id, nama, role_id, password FROM users WHERE email = ?");
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    echo json_encode(['status'=>'error','message'=>'Email atau password salah']);
    exit;
}

$user = $result->fetch_assoc();

// Periksa hash password
if(!password_verify($password, $user['password'])){
    echo json_encode(['status'=>'error','message'=>'Email atau password salah']);
    exit;
}

// Login sukses
$_SESSION['user_id'] = $user['id'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['role_id'] = $user['role_id'];

// Role mapping
$roleMap = [1=>'admin',2=>'pembeli',3=>'kurir',4=>'kasir'];
$role = $roleMap[$user['role_id']] ?? 'pembeli';

echo json_encode(['status'=>'success','message'=>'Login berhasil!','role'=>$role]);
exit;
?>
