<?php include 'navbar.html'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - Cafe AL</title>
  <link rel="stylesheet" href="../assets/css/dashboard_admin.css">
  <link rel="stylesheet" href="../assets/css/admin_navbar.css">

  <style>
    
  </style>
</head>
<body>
  <div class="container">
    <h1>Dashboard Admin</h1>
    <p class="subtitle">Kelola dan pantau pendapatan cafe Anda</p>
    <div class="controls">
      <label for="periode">📅 Lihat Pendapatan:</label>
      <select id="periode">
        <option value="harian">Per Hari</option>
        <option value="mingguan">Per Minggu</option>
        <option value="bulanan">Per Bulan</option>
        <option value="tahunan">Per Tahun</option>
      </select>
    </div>

    <div class="stat-grid">
      <div class="card">
        <h3>Total Pendapatan</h3>
        <div class="value" id="totalPendapatan">
          <span class="loading"></span>
        </div>
      </div>
    </div>

    <div class="chart-wrap">
      <h3>Grafik Pendapatan (Coming Soon)</h3>
      <canvas id="chartPendapatan" height="80"></canvas>
    </div>
  </div>

  <script>
    async function updatePendapatan() {
      const periode = document.getElementById('periode').value;
      const valueEl = document.getElementById('totalPendapatan');
      
      // Tampilkan loading
      valueEl.innerHTML = '<span class="loading"></span>';
      
      try {
        const response = await fetch(`../../backend/admin/dashboard.php?periode=${periode}`);
        const data = await response.json();
        
        // Animasi update nilai
        setTimeout(() => {
          valueEl.textContent = 'Rp ' + Number(data.total).toLocaleString('id-ID');
        }, 300);
      } catch (error) {
        console.error('Gagal mengambil data:', error);
        valueEl.textContent = 'Error';
      }
    }

    document.getElementById('periode').addEventListener('change', updatePendapatan);
    
    // Load data pertama kali
    updatePendapatan();
  </script>
</body>
</html>