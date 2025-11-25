<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../user_login.php");
    exit;
}

include "../db/koneksi.php";

$order_id = mysqli_real_escape_string($conn, $_GET['order_id']);
$customer_id = $_SESSION['user_id'];

// Ambil data pesanan
$order_query = mysqli_query($conn, "  
    SELECT o.*, c.nama as customer_nama 
    FROM orders o 
    LEFT JOIN customers c ON o.customer_id = c.id 
    WHERE o.order_id = '$order_id' AND o.customer_id = '$customer_id'
");

if (mysqli_num_rows($order_query) == 0) {
    die("Pesanan tid+`ak ditemukan!");
}

$order = mysqli_fetch_assoc($order_query);

// Ambil items pesanan
$items_query = mysqli_query($conn, "
    SELECT oi.*, m.nama as menu_nama 
    FROM order_items oi 
    LEFT JOIN menu m ON oi.menu_id = m.id 
    WHERE oi.order_id = '$order_id'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan - RasaRasa</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.3;
        }
        
        .struk-container {
            max-width: 300px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        
        .restaurant-name {
            font-weight: bold;
            font-size: 18px;
        }
        
        .tagline {
            font-size: 12px;
        }
        
        .order-info {
            margin-bottom: 15px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .items-table td {
            padding: 3px 0;
            border-bottom: 1px dashed #ccc;
        }
        
        .item-name {
            width: 60%;
        }
        
        .item-qty {
            width: 15%;
            text-align: center;
        }
        
        .item-price {
            width: 25%;
            text-align: right;
        }
        
        .total-section {
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 12px;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="struk-container">
        <div class="header">
            <div class="restaurant-name">RasaRasa Restaurant</div>
            <div class="tagline">Restoran Terbaik Anda</div>
            <div>🍽️</div>
        </div>
        
        <div class="order-info">
            <div><strong>No. Pesanan:</strong> <?= $order['order_id'] ?></div>
            <div><strong>Pelanggan:</strong> <?= $order['customer_nama'] ?></div>
            <div><strong>Tanggal:</strong> <?= $order['tanggal'] ?></div>
            <div><strong>Waktu:</strong> <?= date('H:i', strtotime($order['created_at'])) ?></div>
        </div>
        
        <table class="items-table">
            <tr>
                <td colspan="3" style="border-bottom: 1px solid #000; padding-bottom: 5px;">
                    <strong>ITEM</strong>
                </td>
            </tr>
            <?php while ($item = mysqli_fetch_assoc($items_query)): ?>
            <tr>
                <td class="item-name"><?= $item['menu_nama'] ?: $item['nama_menu'] ?></td>
                <td class="item-qty"><?= $item['jumlah'] ?>x</td>
                <td class="item-price">Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <div class="total-section">
            <div>TOTAL: Rp <?= number_format($order['total'], 0, ',', '.') ?></div>
        </div>
        
        <div class="footer">
            <div>Terima kasih atas kunjungan Anda!</div>
            <div>Silakan datang kembali 🍽️</div>
            <div>www.rasarasa-restaurant.com</div>
        </div>
        
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #ff6347; color: white; border: none; border-radius: 5px; cursor: pointer;">
                🖨️ Cetak Struk
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                ❌ Tutup
            </button>
        </div>
    </div>
    
    <script>
        // Auto print ketika halaman loaded
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>