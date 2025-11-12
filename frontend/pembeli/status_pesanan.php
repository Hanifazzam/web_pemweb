<?php 
session_start();
// Cek jika belum login, tendang ke halaman login
// if (!isset($_SESSION['id_user'])) {
//     header('Location: login.php'); // (Sesuaikan dengan halaman login Anda)
//     exit;
// }
include 'navbar.html'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pesanan Saya</title>
    <link rel="stylesheet" href="../assets/css/pembeli_navbar.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 20px auto; }
        h1 { text-align: center; }

        .order-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden; /* Penting untuk status bar */
        }
        
        /* Garis status di atas kartu */
        .status-bar {
            padding: 4px;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 0.8em;
            text-transform: uppercase;
        }
        .status-pending { background-color: #ffc107; color: #333; } /* Oranye */
        .status-proses { background-color: #007bff; } /* Biru */
        .status-ready { background-color: #28a745; } /* Hijau */

        .order-content { padding: 20px; }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .order-header h3 { margin: 0; }
        .order-header span { font-weight: bold; font-size: 1.1em; }
        
        .order-items-list {
            list-style: none; padding: 0; margin-top: 10px;
            display: none; /* Sembunyi by default */
        }
        .order-items-list li {
            background: #f9f9f9;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
        }
        .btn-toggle-detail {
            background: none; border: 1px solid #ccc;
            padding: 5px 10px; border-radius: 5px; cursor: pointer;
            font-size: 0.9em;
        }
        .loading, .no-orders { text-align: center; padding: 30px; color: #777; font-size: 1.2em; }

        /* (BARU) Animasi Notifikasi */
        @keyframes highlight-ready {
            0% { background-color: #28a745; box-shadow: 0 0 15px #28a745; }
            50% { background-color: #34ce57; box-shadow: 0 0 30px #34ce57; }
            100% { background-color: #28a745; box-shadow: 0 0 15px #28a745; }
        }
        .status-ready.notify {
            animation: highlight-ready 1.5s ease-in-out;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Pesanan Aktif Saya</h1>
        <div id="order-tracking-container">
            <div class="loading">Memuat pesanan Anda...</div>
        </div>
    </div>

    <script>
        const orderContainer = document.getElementById('order-tracking-container');
        const api_url = '../../backend/pembeli/status_pesanan_api.php';
        
        // (BARU) Variabel untuk menyimpan status lama
        let previousOrderStatuses = {};
        // (BARU) Audio untuk notifikasi
        const notifSound = new Audio('../assets/sounds/notif.mp3'); // (Pastikan Anda punya file ini)

        // Fungsi utama untuk mengambil daftar pesanan
        async function fetchMyOrders() {
            try {
                const response = await fetch(`${api_url}?action=get_my_orders`);
                const data = await response.json();

                if (!data.success) {
                    orderContainer.innerHTML = `<div class="no-orders">${data.message}</div>`;
                    return;
                }

                if (data.orders.length === 0) {
                    orderContainer.innerHTML = '<div class="no-orders">Tidak ada pesanan aktif.</div>';
                    return;
                }

                orderContainer.innerHTML = ''; // Kosongkan container
                
                let newStatuses = {}; // Objek untuk menyimpan status baru

                data.orders.forEach(order => {
                    const orderCard = document.createElement('div');
                    orderCard.className = 'order-card';
                    orderCard.dataset.id = order.id;
                    
                    const oldStatus = previousOrderStatuses[order.id];
                    const newStatus = order.status;
                    newStatuses[order.id] = newStatus; // Simpan status baru
                    
                    let statusBarClass = `status-${newStatus}`;
                    
                    // (LOGIKA NOTIFIKASI)
                    if (oldStatus && newStatus !== oldStatus && newStatus === 'ready') {
                        // Status baru saja berubah menjadi 'ready'!
                        statusBarClass += ' notify'; // Tambah kelas animasi
                        notifSound.play(); // Mainkan suara
                        
                        // Tampilkan alert (opsional)
                        alert(`Pesanan #${order.id} SUDAH READY! Silakan ambil di kasir.`);
                    }

                    orderCard.innerHTML = `
                        <div class="status-bar ${statusBarClass}">
                            Status: ${order.status}
                        </div>
                        <div class="order-content">
                            <div class="order-header">
                                <div>
                                    <h3>Pesanan #${order.id}</h3>
                                    <small>${new Date(order.tanggal_pesan).toLocaleString('id-ID')}</small>
                                </div>
                                <span>Rp ${parseFloat(order.total_harga).toLocaleString()}</span>
                            </div>
                            <button class="btn-toggle-detail" onclick="toggleDetail(this, ${order.id})">Lihat Detail</button>
                            <ul class="order-items-list" id="items-for-${order.id}">
                                <li>Memuat item...</li>
                            </ul>
                        </div>
                    `;
                    orderContainer.appendChild(orderCard);
                });
                
                // Update status lama dengan status baru untuk pengecekan berikutnya
                previousOrderStatuses = newStatuses;

            } catch (error) {
                console.error('Error fetching orders:', error);
                orderContainer.innerHTML = '<div class="no-orders">Gagal memuat pesanan.</div>';
            }
        }

        // Fungsi untuk mengambil dan menampilkan detail item
        async function toggleDetail(button, orderId) {
            const itemList = document.getElementById(`items-for-${orderId}`);
            const isHidden = itemList.style.display === 'none' || itemList.style.display === '';

            if (isHidden) {
                // Tampilkan dan muat data
                itemList.style.display = 'block';
                button.innerText = 'Sembunyikan Detail';
                
                try {
                    const response = await fetch(`${api_url}?action=get_order_detail&id=${orderId}`);
                    const data = await response.json();
                    
                    if (data.success && data.items.length > 0) {
                        itemList.innerHTML = ''; // Kosongkan
                        data.items.forEach(item => {
                            itemList.innerHTML += `
                                <li>
                                    <span>${item.nama_menu} (x${item.jumlah})</span>
                                    <span>Rp ${parseFloat(item.subtotal).toLocaleString()}</span>
                                </li>
                            `;
                        });
                    } else {
                        itemList.innerHTML = '<li>Gagal memuat item.</li>';
                    }
                } catch (error) {
                    itemList.innerHTML = '<li>Error: Gagal memuat item.</li>';
                }
            } else {
                // Sembunyikan
                itemList.style.display = 'none';
                button.innerText = 'Lihat Detail';
            }
        }

        // --- Inisialisasi ---
        fetchMyOrders(); // Panggil pertama kali
        
        // Panggil ulang setiap 5 detik untuk auto-refresh
        setInterval(fetchMyOrders, 5000); 
    </script>
</body>
</html>