<?php include '../../backend/pembeli/pembeli_logic.php'; ?>
<?php include 'navbar.html'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Katalog Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/katalog.css">
</head>
<body>
    <h2>☕ Katalog Menu Cafe AL</h2>

    <div id="cart">
        <i class="fas fa-shopping-cart"></i>
        <span id="cart-count">0</span>
    </div>

    <div id="category-btn">
        <i class="fas fa-filter"></i>
    </div>

    <div id="category-dropdown">
        <h4>Kategori Menu</h4>
        <ul id="category-list">
            <li><a href="#" onclick="filterMenu('semua', 'Semua')">Semua</a></li>
            <li><a href="#" onclick="filterMenu(1, 'Makanan')">Makanan</a></li>
            <li><a href="#" onclick="filterMenu(2, 'Cemilan')">Cemilan</a></li>
            <li><a href="#" onclick="filterMenu(3, 'Coffee')">Coffee</a></li>
            <li><a href="#" onclick="filterMenu(4, 'Non-Coffee')">Non-Coffee</a></li>
        </ul>
    </div>

    <div id="cart-items">
        <h4>Isi Keranjang</h4>
        <ul id="cart-list"></ul>
    </div>
    
    <div class="grid-container">
        <?php foreach($menu as $item): ?>
        <div class="card" data-id-kategori="<?= htmlspecialchars($item['kategori']) ?>"> 
            <?php if(!empty($item['gambar'])): ?>
            <img src="../../frontend/assets/images/<?= htmlspecialchars($item['gambar']) ?>" 
                alt="<?= htmlspecialchars($item['nama_menu']) ?>" 
                loading="lazy">
            <?php endif; ?>
            <div class="card-content">
                <div class="card-title"><?= $item['nama_menu'] ?></div>
                <div class="card-desc"><?= $item['deskripsi'] ?></div>
                <div class="card-price">Rp <?= number_format($item['harga'],0,',','.') ?></div>
                <button onclick="addToCart(<?= $item['id'] ?>, '<?= htmlspecialchars($item['nama_menu'], ENT_QUOTES) ?>', <?= $item['harga'] ?>)">Pesan</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
        // (DIUBAH) filterMenu sekarang menutup dropdown
        function filterMenu(idKategori, namaKategori) {
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                const cardKategoriId = card.getAttribute('data-id-kategori'); 
                let showCard = (idKategori === 'semua') || (idKategori == cardKategoriId);
                if (showCard) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // (BARU) Tutup dropdown setelah memilih
            document.getElementById('category-dropdown').style.display = 'none';
        }

        // (BARU) Fungsi untuk toggle dropdown kategori
        function toggleCategoryMenu() {
            const categoryDropdown = document.getElementById('category-dropdown');
            if (categoryDropdown.style.display === 'block') {
                categoryDropdown.style.display = 'none';
            } else {
                categoryDropdown.style.display = 'block';
                // Sembunyikan keranjang jika terbuka, agar tidak tumpang tindih
                document.getElementById('cart-items').style.display = 'none';
            }
        }
        
        // (BARU) Event listener untuk tombol kategori baru
        document.getElementById('category-btn').addEventListener('click', toggleCategoryMenu);


        // (Logika Keranjang Tidak Berubah)
        let cart = JSON.parse(sessionStorage.getItem('cart')) || [];

        function addToCart(id, name, price) {
            cart.push({ id: id, name: name, price: price });
            sessionStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();
        }
        
        function updateCartDisplay() {
            document.getElementById('cart-count').innerText = cart.length;
            const cartList = document.getElementById('cart-list');
            cartList.innerHTML = ''; 

            if (cart.length === 0) {
                cartList.innerHTML = '<li style="text-align: center; color: #999;">Keranjang kosong</li>';
                return;
            }

            const simpleGroup = {};
            cart.forEach(item => {
                simpleGroup[item.name] = (simpleGroup[item.name] || 0) + 1;
            });

            Object.keys(simpleGroup).forEach(name => {
                let li = document.createElement('li');
                li.innerText = `${name} (x${simpleGroup[name]})`;
                cartList.appendChild(li);
            });
        }

        // (DIUBAH) Event listener keranjang sekarang juga menutup dropdown kategori
        document.getElementById('cart').addEventListener('click', () => {
            // (BARU) Tutup dropdown kategori
            document.getElementById('category-dropdown').style.display = 'none';
            
            // Logika lama Anda untuk membuka/menutup keranjang
            const cartItems = document.getElementById('cart-items');
            if (cartItems.style.display === 'block') {
                cartItems.style.display = 'none';
            } else {
                cartItems.style.display = 'block';
                updateCartDisplay(); 
            }
        });

        // (BARU) Sembunyikan dropdown kategori saat halaman dimuat
        document.getElementById('category-dropdown').style.display = 'none';
        
        // Panggil update keranjang saat halaman dimuat
        updateCartDisplay();
    </script>
</body>
</html>