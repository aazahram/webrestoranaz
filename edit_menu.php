<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin_login.php");
    exit;
}

include "../db/koneksi.php";

$id = mysqli_real_escape_string($conn, $_GET['id']);
$menu_query = mysqli_query($conn, "SELECT * FROM menu WHERE id='$id'");

if (mysqli_num_rows($menu_query) == 0) {
    header("Location: menu.php");
    exit;
}

$menu = mysqli_fetch_assoc($menu_query);

if (isset($_POST['update_menu'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $gambar    = mysqli_real_escape_string($conn, $_POST['gambar']);
    
    $query = "UPDATE menu SET 
          nama='$nama',
          deskripsi='$deskripsi',
          harga='$harga',
          kategori='$kategori',
          gambar='$gambar'    -- <-- PENTING BANGET
          WHERE id='$id'";

    
    if (mysqli_query($conn, $query)) {
        $success = "Menu berhasil diupdate!";
        // Refresh data
        $menu_query = mysqli_query($conn, "SELECT * FROM menu WHERE id='$id'");
        $menu = mysqli_fetch_assoc($menu_query);
    } else {
        $error = "Gagal mengupdate menu: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - Admin RasaRasa</title>
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
                <a href="pesanan.php"><button class="nav-btn">Pesanan</button></a>
                <a href="ulasan.php"><button class="nav-btn">Ulasan</button></a>
                <a href="../logout.php"><button class="nav-btn">Logout (<?= $_SESSION['nama'] ?>)</button></a>
            </nav>
        </div>
    </header>

    <div class="container-admin">
        <div class="page-header">
            <h2>Edit Menu</h2>
            <a href="menu.php" class="btn btn-secondary">← Kembali ke Daftar Menu</a>
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

        <div class="card">
            <div class="card-header">
                <h3>Edit Menu: <?= $menu['nama'] ?></h3>
            </div>
            <div class="card-body">
                <form method="POST" class="form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama">Nama Menu</label>
                            <input type="text" id="nama" name="nama" value="<?= $menu['nama'] ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="kategori">Kategori</label>
                            <select id="kategori" name="kategori" required>
                                <option value="makanan" <?= $menu['kategori'] == 'makanan' ? 'selected' : '' ?>>Makanan</option>
                                <option value="minuman" <?= $menu['kategori'] == 'minuman' ? 'selected' : '' ?>>Minuman</option>
                                <option value="dessert" <?= $menu['kategori'] == 'dessert' ? 'selected' : '' ?>>Dessert</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Menu</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" required><?= $menu['deskripsi'] ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga (Rp)</label>
                        <input type="number" id="harga" name="harga" value="<?= $menu['harga'] ?>" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="gambar">URL Gambar (opsional)</label>
                        <input type="text" id="gambar" name="gambar" 
                            value="<?= $menu['gambar'] ?>" 
                            placeholder="https://contoh.com/menu.jpg">

                        <?php if ($menu['gambar']): ?>
                            <p>Preview gambar saat ini:</p>
                            <img src="<?= $menu['gambar'] ?>" alt="Gambar Menu" width="200" style="border-radius:10px;">
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update_menu" class="btn btn-primary">Update Menu</button>
                        <a href="menu.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>