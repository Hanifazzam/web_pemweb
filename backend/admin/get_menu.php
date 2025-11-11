<?php

include '../koneksi/koneksi.php';
header('Content-Type: application/json');

$result = mysqli_query($conn, "SELECT * FROM menu ORDER BY id DESC");
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}

echo json_encode($data);
?>
