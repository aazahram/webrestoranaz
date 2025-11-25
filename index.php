<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../user_login.php");
    exit;
}

include "../db/koneksi.php";

$menu = mysqli_query($conn, "SELECT * FROM menu");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - RasaRasa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <span>🍽️</span>
                <div>
                    <div>RasaRasa</div>
                    <div class="tagline">Restoran Terbaik Anda</div>
                </div>
            </a>
            <nav>
                <a href="index.php"><button class="nav-btn active">Pesan Menu</button></a>
                <a href="reservasi.php"><button class="nav-btn">Reservasi</button></a>
                <a href="riwayat.php"><button class="nav-btn">Riwayat</button></a>
                <a href="ulasan.php"><button class="nav-btn">Ulasan</button></a>
                <a href="../logout.php"><button class="nav-btn">Logout (<?= $_SESSION['nama'] ?>)</button></a>
            </nav>
        </div>
    </header>

    <div class="atas">
        <h2>Menu Restoran RasaRasa</h2>
        <p class="welcome-text">Halo, <?= $_SESSION['nama'] ?>! Selamat berbelanja 🛒</p>
        <div class="pilihanmakanan">
            <button class="filter active" data-category="semua">Semua menu</button>
            <button class="filter" data-category="makanan">Makanan</button>
            <button class="filter" data-category="minuman">Minuman</button>
            <button class="filter" data-category="dessert">Dessert</button>
        </div>
    </div>

    <div class="allcontent">
        <div class="menu-container">
            <?php while ($row = mysqli_fetch_assoc($menu)): ?>
            <div class="menu-card" data-category="<?= $row['kategori'] ?>">
                <div class="menu-image">
                    <img src="<?= $row['gambar'] ?: 'https://via.placeholder.com/200' ?>" 
                        alt="<?= $row['nama'] ?>" style="width: 100%; height: 180px; object-fit: cover; border-radius: 10px;">
                </div>

                <div class="menu-info">
                    <h3><?= $row['nama'] ?></h3>
                    <p><?= $row['deskripsi'] ?></p>
                    <div class="price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                    <button onclick="tambahKeranjang(<?= $row['id'] ?>, '<?= $row['nama'] ?>', <?= $row['harga'] ?>)">
                        + Tambah keranjang
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <div class="keranjang">
            <h2>🛒 Keranjang Belanja</h2>
            <div id="keranjang-items" class="keranjang-items">
                <p class="empty-cart">Keranjang masih kosong</p>
            </div>
            <div class="cart-total">
                <strong>Total: Rp <span id="total-harga">0</span></strong>
            </div>
            <button class="checkout-btn" onclick="checkout()">💳 Pesan Sekarang</button>
            <button class="clear-btn" onclick="kosongkanKeranjang()">🗑️ Kosongkan Keranjang</button>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>