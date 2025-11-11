<?php
// 1️⃣ Hubungkan ke database
include '../../backend/koneksi/koneksi.php';

// 2️⃣ Jalankan query untuk ambil semua menu
$query = "SELECT * FROM menu ORDER BY id DESC";
$result = mysqli_query($conn, $query);

// 3️⃣ Jika query gagal, tampilkan error
if (!$result) {
  die("Query gagal: " . mysqli_error($conn));
}
?>

<?php include 'navbar.html'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Menu - Cafe AL</title>
  <link rel="stylesheet" href="../assets/css/data_menu.css">
  <link rel="stylesheet" href="../assets/css/admin_navbar.css">
</head>
<body>
  <div class="container">
    <h1>Data Menu</h1>
    <p class="subtitle">Kelola menu cafe Anda dengan mudah</p>

    <div class="add-row">
      <a href="tambah_menu.html">Tambah Menu</a>
    </div>

    <div class="table-card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Menu</th>
            <th>Harga</th>
            <th>Deskripsi</th>
            <th>Gambar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $count = 0;
          while($row = mysqli_fetch_assoc($result)) { 
            $count++;
          ?>
          <tr>
            <td><strong>#<?= $row['id']; ?></strong></td>
            <td><strong><?= htmlspecialchars($row['nama_menu']); ?></strong></td>
            <td><strong style="color: #8b4513;">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></strong></td>
            <td><?= htmlspecialchars($row['deskripsi']); ?></td>
            <td>
              <?php if ($row['gambar']): ?>
                <img src="../assets/images/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama_menu']); ?>" width="80">
              <?php else: ?>
                <span style="color: #999;">Tidak ada gambar</span>
              <?php endif; ?>
            </td>
            <td class="actions">
              <a href="../../backend/admin/edit_menu.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
              <a href="../../backend/admin/hapus_menu.php?id=<?= $row['id']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus menu ini?')">Hapus</a>
            </td>
          </tr>
          <?php } ?>
          
          <?php if ($count === 0): ?>
          <tr>
            <td colspan="6" class="empty-state">
              <strong>Belum ada menu</strong><br>
              Tambahkan menu pertama Anda sekarang!
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>