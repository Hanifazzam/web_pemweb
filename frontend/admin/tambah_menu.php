<?php
include '../../backend/koneksi/koneksi.php';

// Ambil semua kategori untuk dropdown
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($conn, $query_kategori);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Menu - Cafe AL</title>
  <link rel="stylesheet" href="../assets/css/tambah_menu.css">
</head>
<body>
  <div class="form-wrap">
    <h1>Tambah Menu Baru</h1>
    <p class="subtitle">Lengkapi form berikut untuk menambahkan menu baru</p>

    <form action="../../backend/admin/tambah_menu.php" method="POST" enctype="multipart/form-data" id="menuForm">
      
      <div class="form-row">
        <div class="col">
          <label>Nama Menu</label>
          <input type="text" name="nama_menu" placeholder="Contoh: Cappuccino" required>
        </div>
        <div class="col">
          <label>Harga</label>
          <input type="number" name="harga" placeholder="25000" required min="0">
        </div>
      </div>

      <div class="form-row">
        <div class="col">
          <label>Kategori</label>
          <select name="id_kategori" id="id_kategori" required>
            <option value="" disabled selected>-- Pilih Kategori --</option>
            <?php
            // Loop melalui hasil kueri kategori dan buat <option>
            if (mysqli_num_rows($result_kategori) > 0) {
                while ($kategori = mysqli_fetch_assoc($result_kategori)) {
                    echo '<option value="' . $kategori['id_kategori'] . '">' 
                       . htmlspecialchars($kategori['nama_kategori']) 
                       . '</option>';
                }
            } else {
                echo '<option value="" disabled>Belum ada kategori</option>';
            }
            ?>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="col">
          <label>Deskripsi</label>
          <textarea name="deskripsi" placeholder="Tuliskan deskripsi menu yang menggugah selera..."></textarea>
        </div>
      </div>

      <div class="form-row">
        <div class="col">
          <label>Gambar Menu</label>
          <input type="file" name="gambar" accept="image/*" required id="fileInput">
          <div class="file-preview" id="preview"></div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary">Simpan Menu</button>
        <a href="data_menu.php" class="btn-secondary">Kembali</a>
      </div>
    </form>
  </div>

  <script>
    // Preview gambar sebelum upload
    document.getElementById('fileInput').addEventListener('change', function(e) {
      const file = e.target.files[0];
      const preview = document.getElementById('preview');
      
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        }
        reader.readAsDataURL(file);
      } else {
        preview.innerHTML = '';
      }
    });

    // Validasi form (DIPERBARUI)
    document.getElementById('menuForm').addEventListener('submit', function(e) {
      const nama = document.querySelector('input[name="nama_menu"]').value;
      const harga = document.querySelector('input[name="harga"]').value;
      
      // (BARU) Ambil nilai kategori
      const kategori = document.getElementById('id_kategori').value;

      if (!nama.trim()) {
        alert('Nama menu tidak boleh kosong!');
        e.preventDefault();
        return;
      }
      
      if (harga <= 0) {
        alert('Harga harus lebih dari 0!');
        e.preventDefault();
        return;
      }

      // (BARU) Validasi kategori
      if (!kategori) { // Cek jika nilainya masih "" (kosong)
        alert('Anda harus memilih kategori!');
        e.preventDefault();
        return;
      }
    });
  </script>
</body>
</html>