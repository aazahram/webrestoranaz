<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../user_login.php");
    exit;
}

include "../db/koneksi.php";

$customer_id = $_SESSION['user_id'];

// Debug: Tampilkan informasi user
echo "<!-- Debug: User ID = " . $customer_id . " -->";
echo "<!-- Debug: User Name = " . $_SESSION['nama'] . " -->";

// Ambil data pesanan dari database
$orders = mysqli_query($conn, "
    SELECT o.* 
    FROM orders o 
    WHERE o.customer_id = '$customer_id' 
    ORDER BY o.tanggal DESC, o.created_at DESC
");

// Debug: Cek apakah query berhasil
if (!$orders) {
    echo "<!-- Debug: Query Error: " . mysqli_error($conn) . " -->";
} else {
    echo "<!-- Debug: Found " . mysqli_num_rows($orders) . " orders -->";
}

// Debug: Tampilkan data orders jika ada
if (mysqli_num_rows($orders) > 0) {
    while ($order = mysqli_fetch_assoc($orders)) {
        echo "<!-- Debug: Order ID: " . $order['order_id'] . ", Total: " . $order['total'] . " -->";
    }
    // Reset pointer untuk loop berikutnya
    mysqli_data_seek($orders, 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - RasaRasa</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .pesanan-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid #eaeaea;
        }
        
        h2 {
            margin-left: 80px;
            margin-top: 20px;
            color: #333;
        }
        
        .pesanan-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .pesanan-items {
            margin-bottom: 15px;
        }
        
        .item {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 8px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .cetak-btn, .hapus-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .cetak-btn {
            background: #28a745;
            color: white;
        }
        
        .cetak-btn:hover {
            background: #218838;
        }
        
        .hapus-btn {
            background: #dc3545;
            color: white;
        }
        
        .hapus-btn:hover {
            background: #c82333;
        }
        
        .kosong {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-size: 16px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 20px 80px;
        }
        
        .order-id {
            font-weight: bold;
            color: #333;
            font-size: 18px;
        }
        
        .order-date {
            color: #666;
            font-size: 14px;
        }
        
        .order-total {
            color: #ff6347;
            font-size: 20px;
            font-weight: bold;
        }

        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 80px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
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

    <h2>Riwayat Pesanan</h2>

    <!-- Debug Information (akan muncul di source code) -->
    <div class="debug-info">
        <strong>Debug Information:</strong><br>
        User ID: <?= $customer_id ?><br>
        User Name: <?= $_SESSION['nama'] ?><br>
        Orders Found: <?= mysqli_num_rows($orders) ?><br>
        <?php if (mysqli_num_rows($orders) > 0): ?>
            <?php 
            $order_count = 1;
            while ($order = mysqli_fetch_assoc($orders)): 
            ?>
                Order <?= $order_count ?>: <?= $order['order_id'] ?> - Rp <?= number_format($order['total'], 0, ',', '.') ?><br>
            <?php 
                $order_count++;
            endwhile; 
            mysqli_data_seek($orders, 0); // Reset pointer
            ?>
        <?php else: ?>
            No orders found in database for this user.
        <?php endif; ?>
    </div>
    
    <div style="margin: 0 80px;">
        <?php if (mysqli_num_rows($orders) > 0): ?>
            <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                <div class="pesanan-card">
                    <div class="pesanan-header">
                        <div>
                            <div class="order-id">Order #<?= $order['order_id'] ?></div>
                            <div class="order-date">Tanggal: <?= $order['tanggal'] ?></div>
                            <small>Dibuat: <?= date('H:i', strtotime($order['created_at'])) ?> WIB</small>
                        </div>
                        <div class="order-total">
                            Rp <?= number_format($order['total'], 0, ',', '.') ?>
                        </div>
                    </div>

                    <div class="pesanan-items">
                        <?php
                        // Ambil items untuk pesanan ini
                        $items_query = mysqli_query($conn, "
                            SELECT oi.*, m.nama as menu_nama 
                            FROM order_items oi 
                            LEFT JOIN menu m ON oi.menu_id = m.id 
                            WHERE oi.order_id = '{$order['order_id']}'
                        ");
                        
                        if (mysqli_num_rows($items_query) > 0):
                            while ($item = mysqli_fetch_assoc($items_query)):
                        ?>
                                <div class="item">
                                    <span><?= $item['menu_nama'] ?: $item['nama_menu'] ?> x<?= $item['jumlah'] ?></span>
                                    <span>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></span>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="color: #666; font-style: italic;">Tidak ada item ditemukan untuk pesanan ini.</div>
                        <?php endif; ?>
                    </div>

                    <div class="btn-group">
                        <button class="cetak-btn" onclick="cetakStruk('<?= $order['order_id'] ?>')">
                            🖨️ Cetak Struk
                        </button>
                        <button class="hapus-btn" onclick="hapusPesanan('<?= $order['id'] ?>', '<?= $order['order_id'] ?>')">
                            🗑️ Hapus
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="kosong">
                <p>📭 Belum ada pesanan</p>
                <p style="margin-top: 10px; font-size: 14px;">
                    <a href="index.php" style="color: #ff6347; text-decoration: none; font-weight: bold;">
                        ➡️ Pesan menu sekarang
                    </a>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function cetakStruk(orderId) {
            // Buka window baru untuk cetak struk
            const printWindow = window.open('cetak_struk.php?order_id=' + orderId, '_blank');
        }
        
        function hapusPesanan(orderId, orderCode) {
            if (confirm('Yakin ingin menghapus pesanan ' + orderCode + '?')) {
                // Redirect ke hapus_pesanan.php
                window.location.href = 'hapus_pesanan.php?id=' + orderId;
            }
        }
    </script>
</body>
</html>