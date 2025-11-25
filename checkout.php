<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include "../db/koneksi.php";

$input = json_decode(file_get_contents('php://input'), true);
$customer_id = $_SESSION['user_id'];
$total = 0;

// Log received data
error_log("Checkout received for user $customer_id: " . json_encode($input));

// Validasi input
if (empty($input) || !is_array($input)) {
    error_log("Checkout failed: Empty or invalid input");
    echo json_encode(['success' => false, 'message' => 'Data pesanan tidak valid']);
    exit;
}

// Hitung total dan validasi setiap item
foreach ($input as $item) {
    if (!isset($item['id']) || !isset($item['harga']) || !isset($item['jumlah'])) {
        error_log("Checkout failed: Incomplete item data");
        echo json_encode(['success' => false, 'message' => 'Data item tidak lengkap']);
        exit;
    }
    
    // Validasi harga positif
    if ($item['harga'] <= 0 || $item['jumlah'] <= 0) {
        error_log("Checkout failed: Invalid price or quantity");
        echo json_encode(['success' => false, 'message' => 'Harga atau jumlah tidak valid']);
        exit;
    }
    
    $total += $item['harga'] * $item['jumlah'];
}

// Generate order_id yang unik
$order_id = 'ORD' . date('YmdHis') . $customer_id;
$tanggal = date('Y-m-d');

error_log("Creating order $order_id for user $customer_id with total $total");

// Mulai transaction
mysqli_begin_transaction($conn); //Jika salah satu proses gagal, semua data akan dibatalkan (rollback).

try {
    // Simpan ke tabel orders
    $query = "INSERT INTO orders (order_id, customer_id, total, tanggal) VALUES ('$order_id', $customer_id, $total, '$tanggal')";
    
    if (!mysqli_query($conn, $query)) {
        throw new Exception("Gagal menyimpan pesanan: " . mysqli_error($conn));
    }

    error_log("Order $order_id saved successfully");

    // Simpan item-order ke order_items
    foreach ($input as $item) {
        $menu_id = intval($item['id']);
        $nama_menu = mysqli_real_escape_string($conn, $item['nama']);
        $harga = floatval($item['harga']);
        $jumlah = intval($item['jumlah']);
        
        $query = "INSERT INTO order_items (order_id, menu_id, nama_menu, harga, jumlah) 
                  VALUES ('$order_id', $menu_id, '$nama_menu', $harga, $jumlah)";
        
        if (!mysqli_query($conn, $query)) {
            throw new Exception("Gagal menyimpan item pesanan: " . mysqli_error($conn));
        }
        
        error_log("Item saved: $nama_menu x$jumlah = Rp " . ($harga * $jumlah));
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    error_log("Checkout completed successfully for order $order_id");
    
    echo json_encode([
        'success' => true, 
        'total' => $total,
        'order_id' => $order_id,
        'message' => 'Pesanan berhasil disimpan'
    ]);
    
} catch (Exception $e) {
    // Rollback transaction jika ada error
    mysqli_rollback($conn);
    
    error_log("Checkout failed: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>