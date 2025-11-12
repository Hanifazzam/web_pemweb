<?php
// include 'navbar_kasir.html'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Kasir - Pesanan Masuk</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        h1 { text-align: center; }

        /* (BARU) CSS untuk Navbar Status */
        .status-navbar {
            display: flex;
            justify-content: center;
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 5px;
            margin: 0 auto 20px auto;
            max-width: 1000px;
        }
        .status-btn {
            background-color: transparent;
            border: none;
            padding: 12px 18px;
            font-size: 1em;
            font-weight: bold;
            color: #555;
            cursor: pointer;
            border-radius: 6px;
            transition: background-color 0.3s, color 0.3s;
        }
        .status-btn:hover {
            background-color: #ddd;
        }
        .status-btn.active {
            background-color: #8B4513; /* Warna Coklat */
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .dashboard { display: flex; max-width: 1400px; margin: auto; gap: 20px; }
        
        /* Kolom Kiri: Daftar Pesanan */
        .order-list-container {
            flex: 1; min-width: 350px;
        }
        /* (CSS lain tidak berubah) */
        .order-list {
            background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-height: 80vh; overflow-y: auto;
        }
        .order-item {
            padding: 15px; border-bottom: 1px solid #eee; cursor: pointer;
            transition: background-color 0.2s;
        }
        .order-item:hover { background-color: #f9f9f9; }
        .order-item.active { background-color: #8B4513; color: white; }
        .order-item h4 { margin: 0 0 5px 0; }
        .order-item p { margin: 0; font-size: 0.9em; opacity: 0.8; }
        .loading, .no-orders { text-align: center; padding: 20px; color: #777; }

        /* Kolom Kanan: Detail Pesanan (CSS tidak berubah) */
        .order-detail-container { flex: 2; }
        .order-detail {
            background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px; min-height: 400px;
        }
        .order-detail-placeholder {
            display: flex; align-items: center; justify-content: center;
            height: 400px; color: #aaa; font-size: 1.2em; text-align: center;
        }
        .detail-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .detail-table th, .detail-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .detail-table th { background-color: #f9f9f9; }
        .detail-table .text-right { text-align: right; }
        .detail-table .text-center { text-align: center; }
        .action-buttons { margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
        .action-buttons button {
            padding: 10px 15px; border: none; border-radius: 5px; font-size: 1em;
            cursor: pointer; transition: background-color 0.3s;
        }
        .btn-proses { background-color: #007bff; color: white; }
        .btn-ready { background-color: #ffc107; color: black; }
        .btn-selesai { background-color: #28a745; color: white; }
        .btn-batal { background-color: #dc3545; color: white; }
    </style>
</head>
<body>

    <h1>Dasbor Kasir</h1>

    <div class="status-navbar">
        <button class="status-btn active" onclick="fetchOrders('pending')">Pending</button>
        <button class="status-btn" onclick="fetchOrders('proses')">Proses</button>
        <button class="status-btn" onclick="fetchOrders('ready')">Ready</button>
        <button class="status-btn" onclick="fetchOrders('selesai')">Selesai</button>
        <button class="status-btn" onclick="fetchOrders('dibatalkan')">Dibatalkan</button>
    </div>

    <div class="dashboard">
        <div class="order-list-container">
            <h2 id="order-list-title">Menunggu Pembayaran ('pending')</h2>
            <div class="order-list" id="daftar-pesanan">
                <div class="loading">Memuat pesanan...</div>
            </div>
        </div>

        <div class="order-detail-container">
            <h2>Detail Pesanan</h2>
            <div class="order-detail" id="detail-pesanan">
                <div class="order-detail-placeholder">
                    <p>Pilih pesanan dari daftar di sebelah kiri untuk melihat detailnya.</p>
                </div>
            </div>
        </div>
        
    </div>

    <script>
        const orderListDiv = document.getElementById('daftar-pesanan');
        const orderDetailDiv = document.getElementById('detail-pesanan');
        const api_url = '../../backend/kasir/kasir_api.php';
        
        let currentActiveOrderId = null; // ID pesanan yang sedang dilihat
        let currentStatus = 'pending'; // (BARU) Status tab yang sedang aktif

        // (DIUBAH) Mengganti nama fetchPendingOrders -> fetchOrders
        // Fungsi ini sekarang dinamis berdasarkan status
        async function fetchOrders(status) {
            
            // 1. Update status global & UI
            if (status) {
                currentStatus = status; // Set status global
                
                // Update judul H2
                document.getElementById('order-list-title').innerText = `Daftar Pesanan '${status}'`;
                
                // Update tombol aktif
                document.querySelectorAll('.status-btn').forEach(btn => {
                    // Cek jika teks tombol = status yang diklik
                    if (btn.innerText.toLowerCase() === status) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }
            
            // 2. Ambil data dari API
            try {
                // (DIUBAH) URL sekarang dinamis
                const response = await fetch(`${api_url}?action=get_orders_by_status&status=${currentStatus}`);
                const data = await response.json();
                
                orderListDiv.innerHTML = ''; // Kosongkan daftar

                if (data.success && data.orders.length > 0) {
                    data.orders.forEach(order => {
                        const orderEl = document.createElement('div');
                        orderEl.className = 'order-item';
                        orderEl.dataset.id = order.id;
                        
                        orderEl.innerHTML = `
                            <h4>${order.nama_pemesan} (ID: ${order.id})</h4>
                            <p>Total: Rp ${parseFloat(order.total_harga).toLocaleString()}</p>
                            <p>Waktu: ${new Date(order.tanggal_pesan).toLocaleTimeString()}</p>
                        `;
                        
                        orderEl.addEventListener('click', () => showOrderDetail(order.id));
                        
                        if (order.id == currentActiveOrderId) {
                            orderEl.classList.add('active');
                        }
                        orderListDiv.appendChild(orderEl);
                    });
                } else {
                    orderListDiv.innerHTML = '<div class="no-orders">Tidak ada pesanan.</div>';
                }
            } catch (error) {
                console.error('Error fetching orders:', error);
                orderListDiv.innerHTML = '<div class="no-orders">Gagal memuat pesanan.</div>';
            }
        }

        // (DIUBAH) Fungsi ini sekarang me-refresh tab yang benar
        async function showOrderDetail(orderId) {
            currentActiveOrderId = orderId;
            fetchOrders(currentStatus); // Refresh daftar untuk menyorot item
            
            orderDetailDiv.innerHTML = '<div class="loading">Memuat detail...</div>';
            
            try {
                const response = await fetch(`${api_url}?action=get_order_detail&id=${orderId}`);
                const data = await response.json();

                if (!data.success || data.items.length === 0) {
                    orderDetailDiv.innerHTML = '<div class="order-detail-placeholder">Gagal memuat detail atau pesanan ini kosong.</div>';
                    return;
                }
                
                let tableHTML = `
                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th>Menu ID</th>
                                <th>Jumlah</th>
                                <th class="text-right">Subtotal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                data.items.forEach(item => {
                    tableHTML += `
                        <tr>
                            <td>${item.menu_id}</td>
                            <td class="text-center">${item.jumlah}</td>
                            <td class="text-right">Rp ${parseFloat(item.subtotal).toLocaleString()}</td>
                        </tr>
                    `;
                });
                tableHTML += `</tbody></table>`;
                
                // (DIUBAH) Tombol 'Selesai' dan 'Batal' hanya muncul jika status belum selesai/batal
                let buttonsHTML = '<div class="action-buttons">';
                if (currentStatus !== 'selesai' && currentStatus !== 'dibatalkan') {
                    if (currentStatus === 'pending') {
                        buttonsHTML += `<button class="btn-proses" onclick="updateStatus(${orderId}, 'proses')">Tandai 'Proses'</button>`;
                    }
                    if (currentStatus === 'proses') {
                        buttonsHTML += `<button class="btn-ready" onclick="updateStatus(${orderId}, 'ready')">Tandai 'Ready'</button>`;
                    }
                    if (currentStatus === 'ready') {
                         buttonsHTML += `<button class="btn-selesai" onclick="updateStatus(${orderId}, 'selesai')">Tandai 'Selesai' (Selesai)</button>`;
                    }
                    buttonsHTML += `<button class="btn-batal" onclick="updateStatus(${orderId}, 'dibatalkan')">Batalkan Pesanan</button>`;
                } else {
                    buttonsHTML += `<p>Pesanan ini sudah <strong>${currentStatus}</strong>.</p>`;
                }
                buttonsHTML += '</div>';
                
                orderDetailDiv.innerHTML = tableHTML + buttonsHTML;
                
            } catch (error) {
                console.error('Error fetching detail:', error);
                orderDetailDiv.innerHTML = '<div class="order-detail-placeholder">Gagal memuat detail pesanan.</div>';
            }
        }
        
        // (DIUBAH) Fungsi ini sekarang me-refresh tab yang benar
        async function updateStatus(orderId, newStatus) {
            if (!confirm(`Anda yakin ingin mengubah status pesanan ID: ${orderId} menjadi '${newStatus}'?`)) {
                return;
            }
            
            try {
                const response = await fetch(`${api_url}?action=update_order_status&id=${orderId}&status=${newStatus}`);
                const data = await response.json();
                
                if (data.success) {
                    alert('Status berhasil diperbarui!');
                    orderDetailDiv.innerHTML = '<div class="order-detail-placeholder"><p>Status diperbarui. Pilih pesanan lain.</p></div>';
                    currentActiveOrderId = null;
                    fetchOrders(currentStatus); // Muat ulang tab saat ini
                } else {
                    alert('Gagal memperbarui status: ' + data.message);
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('Gagal memperbarui status.');
            }
        }

        // --- Inisialisasi ---
        // (DIUBAH) Muat tab 'pending' saat halaman dibuka
        fetchOrders('pending');
        
        // (DIUBAH) Auto-refresh sekarang me-refresh tab yang sedang aktif
        setInterval(() => {
            fetchOrders(currentStatus);
        }, 5000); // Setiap 5 detik
    </script>

</body>
</html>