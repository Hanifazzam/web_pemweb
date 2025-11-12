<?php include 'navbar.html'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan Anda</title>
    <link rel="stylesheet" href="../assets/css/pembeli_navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5e6d3 0%, #d7ccc8 100%);
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(139, 69, 19, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(101, 67, 33, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        
        .container { 
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(101, 67, 33, 0.15);
            position: relative;
            z-index: 1;
        }
        
        h2 { 
            font-family: 'Playfair Display', serif;
            text-align: center;
            color: #5d4037;
            font-size: 2.5em;
            margin-bottom: 15px;
            font-weight: 700;
            position: relative;
            padding-bottom: 20px;
        }
        
        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #8B4513, transparent);
        }
        
        .subtitle {
            text-align: center;
            color: #795548;
            font-size: 0.95em;
            margin-bottom: 40px;
            font-weight: 300;
        }
        
        .order-table { 
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 30px;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .order-table thead {
            background: linear-gradient(135deg, #6d4c41 0%, #5d4037 100%);
            color: white;
        }
        
        .order-table th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 500;
            font-size: 0.9em;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .order-table td { 
            padding: 16px 20px;
            border-bottom: 1px solid #efebe9;
            background: white;
            transition: background-color 0.3s;
        }
        
        .order-table tbody tr:hover td {
            background-color: #fef5e7;
        }
        
        .order-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .order-table th:nth-child(2), .order-table td:nth-child(2) { width: 140px; }
        .order-table th:nth-child(3), .order-table td:nth-child(3) { width: 80px; }
        .order-table th:nth-child(4), .order-table td:nth-child(4) { width: 140px; }
        
        .order-summary { 
            background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 30px;
        }
        
        .summary-row { 
            display: flex;
            justify-content: space-between;
            font-size: 1em;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #d7ccc8;
            color: #5d4037;
        }
        
        .summary-row:last-child { 
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .summary-row span:first-child {
            font-weight: 400;
        }
        
        .summary-row span:last-child {
            font-weight: 600;
        }
        
        .grand-total { 
            background: linear-gradient(135deg, #8B4513 0%, #6d4c41 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-top: 20px;
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .grand-total span:first-child {
            font-size: 1.1em;
            font-weight: 500;
        }
        
        .grand-total span:last-child { 
            font-size: 1.8em;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
        }
        
        .confirmation-form { 
            margin-top: 40px;
            border-top: 2px solid #efebe9;
            padding-top: 35px;
        }
        
        .form-group { 
            margin-bottom: 25px;
        }
        
        .form-group label { 
            display: block;
            font-weight: 500;
            margin-bottom: 10px;
            color: #5d4037;
            font-size: 0.95em;
        }
        
        .form-group input[type="text"] { 
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #d7ccc8;
            border-radius: 10px;
            font-size: 1em;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            background: white;
        }
        
        .form-group input[type="text"]:focus { 
            outline: none;
            border-color: #8B4513;
            box-shadow: 0 0 0 4px rgba(139, 69, 19, 0.1);
        }
        
        .btn-submit-order { 
            background: linear-gradient(135deg, #8B4513 0%, #6d4c41 100%);
            color: white;
            padding: 18px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.3);
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-submit-order::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-submit-order:hover::before {
            left: 100%;
        }
        
        .btn-submit-order:hover { 
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(139, 69, 19, 0.4);
        }
        
        .btn-submit-order:active {
            transform: translateY(0);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 2em;
            }
            
            .order-table th,
            .order-table td {
                padding: 12px 10px;
                font-size: 0.9em;
            }
            
            .grand-total {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
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