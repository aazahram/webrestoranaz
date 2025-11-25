<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin_login.php");
    exit;
}

include "../db/koneksi.php";

$orders = mysqli_query($conn, "
    SELECT o.*, c.nama as nama_customer 
    FROM orders o 
    LEFT JOIN customers c ON o.customer_id = c.id 
    ORDER BY o.tanggal DESC, o.created_at DESC
");

// Hitung total pendapatan
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) as total FROM orders"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin RasaRasa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <span>🍽️</span>
                <div>
                    <div>RasaRasa</div>
                    <div class="tagline">Panel Administrator</div>
                </div>
            </a>
            <nav>
                <a href="index.php"><button class="nav-btn">Dashboard</button></a>
                <a href="menu.php"><button class="nav-btn">Kelola Menu</button></a>
                <a href="reservasi.php"><button class="nav-btn">Reservasi</button></a>
                <a href="pesanan.php"><button class="nav-btn active">Pesanan</button></a>
                <a href="ulasan.php"><button class="nav-btn">Ulasan</button></a>
                <a href="../logout.php"><button class="nav-btn">Logout (<?= $_SESSION['nama'] ?>)</button></a>
            </nav>
        </div>
    </header>

    <div class="container-admin">
        <div class="page-header">
            <h2>Kelola Pesanan</h2>
            <p>Lihat semua pesanan yang telah dibuat oleh pelanggan</p>
        </div>

        <!-- Statistik Pesanan -->
        <div class="stats-cards">
            <div class="stat-card large">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <h3>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>
            <div class="stat-card large">
                <div class="stat-icon">📦</div>
                <div class="stat-info">
                    <h3><?= mysqli_num_rows($orders) ?></h3>
                    <p>Total Pesanan</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Daftar Pesanan</h3>
                <span class="badge"><?= mysqli_num_rows($orders) ?> pesanan</span>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($orders) > 0): ?>
                    <div class="orders-list">
                        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-info">
                                        <h4>Order #<?= $order['order_id'] ?></h4>
                                        <div class="order-meta">
                                            <span class="customer">Oleh: <?= $order['nama_customer'] ?></span>
                                            <span class="date">Tanggal: <?= $order['tanggal'] ?></span>
                                            <span class="time">Dibuat: <?= date('H:i', strtotime($order['created_at'])) ?></span>
                                        </div>
                                    </div>
                                    <div class="order-total">
                                        <strong>Rp <?= number_format($order['total'], 0, ',', '.') ?></strong>
                                    </div>
                                </div>

                                <div class="order-items">
                                    <h5>Items:</h5>
                                    <?php
                                    $items = mysqli_query($conn, "
                                        SELECT oi.*, m.nama as menu_nama 
                                        FROM order_items oi 
                                        LEFT JOIN menu m ON oi.menu_id = m.id 
                                        WHERE oi.order_id='{$order['order_id']}'
                                    ");
                                    ?>
                                    <table class="items-table">
                                        <thead>
                                            <tr>
                                                <th>Menu</th>
                                                <th>Harga</th>
                                                <th>Qty</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($item = mysqli_fetch_assoc($items)): ?>
                                            <tr>
                                                <td><?= $item['menu_nama'] ?: $item['nama_menu'] ?></td>
                                                <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                                <td><?= $item['jumlah'] ?></td>
                                                <td><strong>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></strong></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Belum ada pesanan yang dibuat.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>