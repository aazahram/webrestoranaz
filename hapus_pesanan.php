<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../user_login.php");
    exit;
}

include "../db/koneksi.php";

if (isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
    $customer_id = $_SESSION['user_id'];
    
    // Pastikan pesanan milik customer yang login
    $check_query = mysqli_query($conn, "SELECT order_id FROM orders WHERE id='$order_id' AND customer_id='$customer_id'");
    
    if (mysqli_num_rows($check_query) > 0) {
        $order_data = mysqli_fetch_assoc($check_query);
        $order_code = $order_data['order_id'];
        
        // Hapus dari order_items terlebih dahulu (karena foreign key constraint)
        mysqli_query($conn, "DELETE FROM order_items WHERE order_id='$order_code'");
        
        // Kemudian hapus dari orders
        if (mysqli_query($conn, "DELETE FROM orders WHERE id='$order_id' AND customer_id='$customer_id'")) {
            $_SESSION['success'] = "Pesanan berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus pesanan: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Pesanan tidak ditemukan atau tidak memiliki akses!";
    }
} else {
    $_SESSION['error'] = "ID pesanan tidak valid!";
}

header("Location: riwayat.php");
exit;
?>