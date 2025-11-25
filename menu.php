<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin_login.php");
    exit;
}

include "../db/koneksi.php";

// Tambah menu baru
if (isset($_POST['tambah_menu'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $gambar = mysqli_real_escape_string($conn, $_POST['gambar']);

    
    $query = "INSERT INTO menu (nama, deskripsi, harga, kategori, gambar) 
          VALUES ('$nama', '$deskripsi', '$harga', '$kategori', '$gambar')";

    
    if (mysqli_query($conn, $query)) {
        $success = "Menu berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan menu: " . mysqli_error($conn);
    }
}

// Hapus menu
if (isset($_GET['hapus_menu'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus_menu']);
    $query = "DELETE FROM menu WHERE id='$id'";
    
    if (mysqli_query($conn, $query)) {
        $success = "Menu berhasil dihapus!";
    } else {
        $error = "Gagal menghapus menu: " . mysqli_error($conn);
    }
}

$menu = mysqli_query($conn, "SELECT * FROM menu ORDER BY kategori, nama");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Admin RasaRasa</title>
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
                <a href="menu.php"><button class="nav-btn active">Kelola Menu</button></a>
                <a href="reservasi.php"><button class="nav-btn">Reservasi</button></a>
                <a href="pesanan.php"><button class="nav-btn">Pesanan</button></a>
                <a href="ulasan.php"><button class="nav-btn">Ulasan</button></a>
                <a href="../logout.php"><button class="nav-btn">Logout (<?= $_SESSION['nama'] ?>)</button></a>
            </nav>
        </div>
    </header>

    <div class="container-admin">
        <div class="page-header">
            <h2>Kelola Menu Restoran</h2>
            <p>Tambah, edit, atau hapus menu makanan dan minuman</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Form Tambah Menu -->
        <div class="card">
            <div class="card-header">
                <h3>Tambah Menu Baru</h3>
            </div>
            <div class="card-body">
                <form method="POST" class="form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama">Nama Menu</label>
                            <input type="text" id="nama" name="nama" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="kategori">Kategori</label>
                            <select id="kategori" name="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="dessert">Dessert</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Menu</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga (Rp)</label>
                        <input type="number" id="harga" name="harga" min="0" required>
                    </div>
                    <div class="form-group">
    <label for="gambar">Link Gambar (URL)</label>
    <input type="text" id="gambar" name="gambar" 
           placeholder="https://contoh.com/gambar.jpg" required>
</div>

                    <button type="submit" name="tambah_menu" class="btn btn-primary">Tambah Menu</button>
                </form>
            </div>
        </div>

        <!-- Daftar Menu -->
        <div class="card">
            <div class="card-header">
                <h3>Daftar Menu</h3>
                <span class="badge"><?= mysqli_num_rows($menu) ?> menu</span>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($menu) > 0): ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Menu</th>
                                    <th>Deskripsi</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($menu)): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= $row['nama'] ?></strong>
                                    </td>
                                    <td><?= $row['deskripsi'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= $row['kategori'] ?>">
                                            <?= ucfirst($row['kategori']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>Rp <?= number_format($row['harga'], 0, ',', '.') ?></strong>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_menu.php?id=<?= $row['id'] ?>" class="btn btn-edit btn-sm">
                                                Edit
                                            </a>
                                            <a href="menu.php?hapus_menu=<?= $row['id'] ?>" 
                                               onclick="return confirm('Yakin ingin menghapus menu <?= $row['nama'] ?>?')" 
                                               class="btn btn-hapus btn-sm">
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Belum ada menu yang ditambahkan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>