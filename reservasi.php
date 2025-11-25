<?php
include "../db/koneksi.php";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../user_login.php");
    exit;
}

include "../db/koneksi.php";

if (isset($_POST['submit'])) {
    $nama     = $_POST['nama'];
    $telepon  = $_POST['telepon'];
    $tanggal  = $_POST['tanggal'];
    $jam      = $_POST['jam'];
    $jumlah   = $_POST['jumlah'];
    $catatan  = $_POST['catatan'];

    $query = "INSERT INTO reservasi (nama, telepon, tanggal, jam, jumlah, catatan)
              VALUES ('$nama', '$telepon', '$tanggal', '$jam', '$jumlah', '$catatan')";

    mysqli_query($conn, $query);

    echo "<script>alert('Reservasi berhasil dibuat!'); window.location='reservasi.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                <a href="index.php"><button class="nav-btn">Pesan Menu</button></a>
                <a href="reservasi.php"><button class="nav-btn active">Reservasi</button></a>
                <a href="riwayat.php"><button class="nav-btn">Riwayat</button></a>
                <a href="ulasan.php"><button class="nav-btn">Ulasan</button></a>
                <a href="../logout.php"><button class="nav-btn">Logout (<?= $_SESSION['nama'] ?>)</button></a>
            </nav>
        </div>
    </header>
    <div class="box">
        <div class="headerrsv">
            <h2>Reservasi Meja</h2>
        </div>
        <div class="content">
            <form action="" method="POST">
              Nama Anda
              <input type="text" name="nama" required><br>

              Nomor Telepon
              <input type="text" name="telepon" required><br>

              Tanggal
              <input type="date" name="tanggal" required><br>

              Waktu
              <input type="time" name="jam" required><br>

              Jumlah Orang
              <input type="number" name="jumlah" required><br>

              Catatan Khusus
              <textarea name="catatan" rows="4"></textarea>

              <button type="submit" name="submit">Buat reservasi</button>
          </form>

        </div>
    </div>
</body>
</html>