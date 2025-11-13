<?php 
    include 'navbar.html';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/pembeli_navbar.css">
    <title>Detail Pesanan Anda</title>
    <link rel="stylesheet" href="../assets/css/detail_pesanan.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>☕ Rincian Pesanan</h2>
        <p class="subtitle">Periksa kembali pesanan Anda sebelum melakukan pembayaran</p>

        <table class="order-table">
            <thead>
                <tr>
                    <th>Nama Item</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody id="cart-detail-list">
            </tbody>
        </table>

        <div class="order-summary">
            <div class="summary-row">
                <span>Total Pesanan (Jenis Item)</span>
                <span id="total-distinct-items">0</span>
            </div>
            <div class="summary-row">
                <span>Jumlah Pesanan (Total Qty)</span>
                <span id="total-quantity">0</span>
            </div>
            <div class="grand-total">
                <span>Total Harga</span>
                <span id="grand-total">Rp 0</span>
            </div>
        </div>

        <div class="confirmation-form">
            <div class="form-group">
                <label for="nama_pemesan">Nama Pemesan</label>
                <input type="text" id="nama_pemesan" placeholder="Masukkan nama Anda..." required>
            </div>
            <button class="btn-submit-order" id="kirim-ke-kasir">Kirim ke Kasir untuk Bayar</button>
        </div>
    </div>

    <script>
        let groupedCartData = {};
        let grandTotalData = 0;

        document.addEventListener('DOMContentLoaded', () => {
            const simpleCart = JSON.parse(sessionStorage.getItem('cart')) || [];
            const tableBody = document.getElementById('cart-detail-list');

            if (simpleCart.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 30px; color: #999;">Keranjang Anda kosong</td></tr>';
                document.getElementById('total-distinct-items').innerText = 0;
                document.getElementById('total-quantity').innerText = 0;
                document.getElementById('grand-total').innerText = 'Rp 0';
                document.querySelector('.confirmation-form').style.display = 'none';
                return;
            }

            const groupedCart = {};
            let totalQuantity = 0;
            let grandTotal = 0;

            simpleCart.forEach(item => {
                const itemName = item.name;
                const itemPrice = item.price;
                const itemId = item.id;

                if (groupedCart[itemName]) {
                    groupedCart[itemName].quantity += 1;
                    groupedCart[itemName].subtotal += itemPrice;
                } else {
                    groupedCart[itemName] = {
                        id: itemId,
                        price: itemPrice, 
                        quantity: 1,
                        subtotal: itemPrice
                    };
                }
                totalQuantity += 1;
                grandTotal += itemPrice;
            });

            groupedCartData = groupedCart;
            grandTotalData = grandTotal;

            tableBody.innerHTML = ''; 
            Object.keys(groupedCart).forEach(itemName => {
                const item = groupedCart[itemName];
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${itemName}</td>
                    <td class="text-right">Rp ${item.price.toLocaleString()}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-right">Rp ${item.subtotal.toLocaleString()}</td>
                `;
                tableBody.appendChild(row);
            });

            document.getElementById('total-distinct-items').innerText = Object.keys(groupedCart).length;
            document.getElementById('total-quantity').innerText = totalQuantity;
            document.getElementById('grand-total').innerText = `Rp ${grandTotal.toLocaleString()}`;
        });

        document.getElementById('kirim-ke-kasir').addEventListener('click', () => {
            const namaPemesan = document.getElementById('nama_pemesan').value;

            if (namaPemesan.trim() === '') {
                alert('Harap masukkan nama Anda!');
                return;
            }

            const dataToSubmit = {
                nama_pemesan: namaPemesan,
                total_harga: grandTotalData,
                keranjang: groupedCartData
            };

            fetch('../../backend/pembeli/proses_pesanan.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataToSubmit)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pesanan Anda berhasil dikirim ke kasir! Silakan lakukan pembayaran.');
                    sessionStorage.removeItem('cart');
                    window.location.href = 'katalog.php';
                } else {
                    alert('Gagal mengirim pesanan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
            });
        });
    </script>
</body>
</html>