<?php
include "db/koneksi.php";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);

    // Cek username sudah ada atau belum
    $query = "SELECT * FROM customers WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    
    //Di sini user baru dimasukkan ke tabel customers
    if (mysqli_num_rows($result) == 0) {
        $query = "INSERT INTO customers (username, password, nama, telepon) VALUES ('$username', '$password', '$nama', '$telepon')";
        if (mysqli_query($conn, $query)) {
            $success = "Registrasi berhasil! Silakan login.";
        } else {
            $error = "Registrasi gagal!";
        }
    } else {
        $error = "Username sudah digunakan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan - RasaRasa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Daftar Pelanggan</h2>
                <p>Buat akun baru di RasaRasa</p>
            </div>
            
            <!-- //Kalau variabel $error ada isinya, berarti terjadi kesalahan (misal password salah, username sudah dipakai, dll.) -->
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <!-- //Kalau $success ada isinya, berarti proses berhasil (misal registrasi sukses) -->
            <?php if (isset($success)): ?>
                <div class="success-message">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required>
                </div>

                <div class="form-group">
                    <label for="telepon">Nomor Telepon</label>
                    <input type="tel" id="telepon" name="telepon" required>
                </div>

                <button type="submit" name="register" class="btn btn-primary btn-block">Daftar</button>
            </form>

            <div class="auth-footer">
                <p>Sudah punya akun? <a href="user_login.php">Login di sini</a></p>
                <a href="index.php" class="back-link">← Kembali ke Halaman Utama</a>
            </div>
        </div>
    </div>
</body>
</html>