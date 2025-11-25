<?php
session_start();
include "db/koneksi.php";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Cek di tabel admin
    $query = "SELECT * FROM admin WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        if (password_verify($password, $admin['password'])) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['user_role'] = 'admin';
            $_SESSION['nama'] = $admin['nama'];
            
            header("Location: admin/index.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username admin tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - RasaRasa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Login Administrator</h2>
                <p>Masuk ke panel admin RasaRasa</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= $error ?>
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

                <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="auth-footer">
                <a href="index.php" class="back-link">← Kembali ke Halaman Utama</a>
            </div>
        </div>
    </div>
</body>
</html>