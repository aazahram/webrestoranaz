<?php
include "db/koneksi.php";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);

    // Cek username sudah ada atau belum
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        $query = "INSERT INTO users (username, password, nama, telepon) VALUES ('$username', '$password', '$nama', '$telepon')";
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
    <title>Register - RasaRasa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fafafa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            color: #ff6347;
        }
        input[type="text"], input[type="password"], input[type="tel"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #ff6347;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #ff3d00;
        }
        .error {
            color: red;
            text-align: center;
        }
        .success {
            color: green;
            text-align: center;
        }
        .login-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?= $success ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <input type="tel" name="telepon" placeholder="Telepon" required>
            <button type="submit" name="register">Daftar</button>
        </form>
        <div class="login-link">
            <a href="login.php">Sudah punya akun? Login</a>
        </div>
    </div>
</body>
</html>