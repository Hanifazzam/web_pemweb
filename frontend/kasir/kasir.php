<?php
// include 'navbar_kasir.html'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Kasir - Pesanan Masuk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/kasir.css">
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-cash-register"></i>Dasbor Kasir</h1>

        <div class="status-navbar">
            <button class="status-btn active" onclick="fetchOrders('pending')">
                <i class="fas fa-clock"></i>Pending
            </button>
            <button class="status-btn" onclick="fetchOrders('proses')">
                <i class="fas fa-spinner"></i>Proses
            </button>
            <button class="status-btn" onclick="fetchOrders('ready')">
                <i class="fas fa-check-circle"></i>Ready
            </button>
            <button class="status-btn" onclick="fetchOrders('selesai')">
                <i class="fas fa-check-double"></i>Selesai
            </button>
            <button class="status-btn" onclick="fetchOrders('dibatalkan')">
                <i class="fas fa-times-circle"></i>Dibatalkan
            </button>
        </div>

        <div class="dashboard">
            <div class="order-list-container">
                <h2 id="order-list-title"><i class="fas fa-list-alt"></i>Menunggu Pembayaran ('pending')</h2>
                <div class="order-list" id="daftar-pesanan">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Memuat pesanan...</p>
                    </div>
                </div>
            </div>

            <div class="order-detail-container">
                <h2><i class="fas fa-receipt"></i>Detail Pesanan</h2>
                <div class="order-detail" id="detail-pesanan">
                    <div class="order-detail-placeholder">
                        <i class="fas fa-clipboard-list"></i>
                        <p>Pilih pesanan dari daftar di sebelah kiri<br>untuk melihat detailnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const orderListDiv = document.getElementById('daftar-pesanan');
        const orderDetailDiv = document.getElementById('detail-pesanan');
        const api_url = '../../backend/kasir/kasir_api.php';
        
        let currentActiveOrderId = null;
        let currentStatus = 'pending';

        async function fetchOrders(status) {
            if (status) {
                currentStatus = status;
                document.getElementById('order-list-title').innerHTML = `<i class="fas fa-list-alt"></i>Daftar Pesanan '${status}'`;
                
                document.querySelectorAll('.status-btn').forEach(btn => {
                    if (btn.innerText.toLowerCase().includes(status)) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }
            
            try {
                const response = await fetch(`${api_url}?action=get_orders_by_status&status=${currentStatus}`);
                const data = await response.json();
                
                orderListDiv.innerHTML = '';

                if (data.success && data.orders.length > 0) {
                    data.orders.forEach(order => {
                        const orderEl = document.createElement('div');
                        orderEl.className = 'order-item';
                        orderEl.dataset.id = order.id;
                        
                        orderEl.innerHTML = `
                            <h4><i class="fas fa-user-circle"></i>${order.nama_pemesan} <span style="opacity:0.7">(ID: ${order.id})</span></h4>
                            <p><i class="fas fa-money-bill-wave"></i> Total: Rp ${parseFloat(order.total_harga).toLocaleString()}</p>
                            <p><i class="fas fa-clock"></i> Waktu: ${new Date(order.tanggal_pesan).toLocaleTimeString()}</p>
                        `;
                        
                        orderEl.addEventListener('click', () => showOrderDetail(order.id));
                        
                        if (order.id == currentActiveOrderId) {
                            orderEl.classList.add('active');
                        }
                        orderListDiv.appendChild(orderEl);
                    });
                } else {
                    orderListDiv.innerHTML = '<div class="no-orders"><i class="fas fa-inbox"></i><p>Tidak ada pesanan.</p></div>';
                }
            } catch (error) {
                console.error('Error fetching orders:', error);
                orderListDiv.innerHTML = '<div class="no-orders"><i class="fas fa-exclamation-triangle"></i><p>Gagal memuat pesanan.</p></div>';
            }
        }

        async function showOrderDetail(orderId) {
            currentActiveOrderId = orderId;
            fetchOrders(currentStatus);
            
            orderDetailDiv.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Memuat detail...</p></div>';
            
            try {
                const response = await fetch(`${api_url}?action=get_order_detail&id=${orderId}`);
                const data = await response.json();

                if (!data.success || data.items.length === 0) {
                    orderDetailDiv.innerHTML = '<div class="order-detail-placeholder"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat detail atau pesanan ini kosong.</p></div>';
                    return;
                }
                
                let tableHTML = `
                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th>Nama Menu</th>
                                <th>Jumlah</th>
                                <th class="text-right">Subtotal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                data.items.forEach(item => {
                    tableHTML += `
                        <tr>
                            <td>${item.nama_menu}</td>
                            <td class="text-center">${item.qty}</td>
                            <td class="text-right">Rp ${parseFloat(item.subtotal).toLocaleString()}</td>
                        </tr>
                    `;
                });
                tableHTML += `</tbody></table>`;
                
                let buttonsHTML = '<div class="action-buttons">';
                if (currentStatus !== 'selesai' && currentStatus !== 'dibatalkan') {
                    if (currentStatus === 'pending') {
                        buttonsHTML += `<button class="btn-proses" onclick="updateStatus(${orderId}, 'proses')"><i class="fas fa-play"></i>Tandai 'Proses'</button>`;
                    }
                    if (currentStatus === 'proses') {
                        buttonsHTML += `<button class="btn-ready" onclick="updateStatus(${orderId}, 'ready')"><i class="fas fa-check"></i>Tandai 'Ready'</button>`;
                    }
                    if (currentStatus === 'ready') {
                        buttonsHTML += `<button class="btn-selesai" onclick="updateStatus(${orderId}, 'selesai')"><i class="fas fa-check-double"></i>Tandai 'Selesai'</button>`;
                    }
                    buttonsHTML += `<button class="btn-batal" onclick="updateStatus(${orderId}, 'dibatalkan')"><i class="fas fa-times"></i>Batalkan Pesanan</button>`;
                } else {
                    buttonsHTML += `<p><i class="fas fa-info-circle"></i> Pesanan ini sudah <strong>${currentStatus}</strong>.</p>`;
                }
                buttonsHTML += '</div>';
                
                orderDetailDiv.innerHTML = tableHTML + buttonsHTML;
                
            } catch (error) {
                console.error('Error fetching detail:', error);
                orderDetailDiv.innerHTML = '<div class="order-detail-placeholder"><i class="fas fa-exclamation-triangle"></i><p>Gagal memuat detail pesanan.</p></div>';
            }
        }
        
        async function updateStatus(orderId, newStatus) {
            if (!confirm(`Anda yakin ingin mengubah status pesanan ID: ${orderId} menjadi '${newStatus}'?`)) {
                return;
            }
            
            try {
                const response = await fetch(`${api_url}?action=update_order_status&id=${orderId}&status=${newStatus}`);
                const data = await response.json();
                
                if (data.success) {
                    alert('Status berhasil diperbarui!');
                    orderDetailDiv.innerHTML = '<div class="order-detail-placeholder"><i class="fas fa-check-circle"></i><p>Status diperbarui. Pilih pesanan lain.</p></div>';
                    currentActiveOrderId = null;
                    fetchOrders(currentStatus);
                } else {
                    alert('Gagal memperbarui status: ' + data.message);
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('Gagal memperbarui status.');
            }
        }

        fetchOrders('pending');
        
        setInterval(() => {
            fetchOrders(currentStatus);
        }, 5000);
    </script>
</body>
</html>