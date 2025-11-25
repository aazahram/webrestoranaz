<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../user_login.php");
    exit;
}

include "../db/koneksi.php";

$customer_id = $_SESSION['user_id'];
$nama_customer = $_SESSION['nama'];

// Proses tambah ulasan
if (isset($_POST['tambah_ulasan'])) {
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $komentar = mysqli_real_escape_string($conn, $_POST['komentar']);
    
    // Cek apakah user sudah memberikan ulasan sebelumnya
    $check_query = mysqli_query($conn, "SELECT * FROM reviews WHERE customer_id = '$customer_id'");
    
    if (mysqli_num_rows($check_query) > 0) {
        $error = "Anda sudah memberikan ulasan sebelumnya!";
    } else {
        $query = "INSERT INTO reviews (customer_id, nama_customer, rating, komentar) 
                  VALUES ('$customer_id', '$nama_customer', '$rating', '$komentar')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Ulasan berhasil dikirim! Terima kasih atas feedback Anda. 🌟";
        } else {
            $error = "Gagal mengirim ulasan: " . mysqli_error($conn);
        }
    }
}

// Ambil semua ulasan dari database untuk ditampilkan
$reviews = mysqli_query($conn, "
    SELECT * FROM reviews 
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ulasan & Testimoni - RasaRasa</title>
  <link rel="stylesheet" href="style.css">
  <style>
    h2 {
      margin-left: 90px;
      margin-top: 30px;
      color: #333;
    }

    .content {
      display: flex;
      justify-content: space-between;
      padding: 20px 80px;
      gap: 30px;
    }

    .ulasan-list {
      flex: 1;
    }

    .ulasan-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 20px;
      margin-bottom: 20px;
      border: 1px solid #eaeaea;
    }

    .hapus-btn {
      position: absolute;
      right: 15px;
      top: 15px;
      background-color: #e74c3c;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 5px 10px;
      cursor: pointer;
      font-size: 12px;
    }

    .boxkr {
      width: 350px;
      padding: 25px;
      border-radius: 10px;
      background-color: white;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
      border: 1px solid #eaeaea;
      height: fit-content;
      position: sticky;
      top: 120px;
    }

    .boxkr input, .boxkr select, .boxkr textarea {
      width: 100%;
      margin-bottom: 15px;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-family: 'Poppins', sans-serif;
      box-sizing: border-box;
    }

    .boxkr button {
      background-color: #ff6347;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 12px;
      width: 100%;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.3s;
    }

    .boxkr button:hover {
      background-color: #ff3d00;
    }

    .alert {
      padding: 15px 20px;
      margin: 10px 80px;
      border-radius: 8px;
      border-left: 4px solid;
    }

    .alert-success {
      background: #d4edda;
      color: #155724;
      border-color: #28a745;
    }

    .alert-error {
      background: #f8d7da;
      color: #721c24;
      border-color: #dc3545;
    }

    .review-header {
      display: flex;
      justify-content: between;
      align-items: flex-start;
      margin-bottom: 10px;
    }

    .reviewer-name {
      font-weight: bold;
      color: #333;
    }

    .review-rating {
      color: #ffc107;
      font-size: 16px;
    }

    .review-date {
      color: #666;
      font-size: 12px;
      margin-top: 5px;
    }

    .review-comment {
      color: #333;
      line-height: 1.5;
      margin-top: 10px;
    }

    .empty-reviews {
      text-align: center;
      padding: 40px 20px;
      color: #666;
      background: #f8f9fa;
      border-radius: 10px;
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
        <a href="index.php"><button class="nav-btn">Pesan Menu</button></a>
        <a href="reservasi.php"><button class="nav-btn">Reservasi</button></a>
        <a href="riwayat.php"><button class="nav-btn">Riwayat</button></a>
        <a href="ulasan.php"><button class="nav-btn active">Ulasan</button></a>
        <a href="../logout.php"><button class="nav-btn">Logout (<?= $_SESSION['nama'] ?>)</button></a>
      </nav>
    </div>
  </header>

  <h2>Ulasan & Testimoni</h2>

  <!-- Alert Messages -->
  <?php if (isset($success)): ?>
    <div class="alert alert-success">
      ✅ <?= $success ?>
    </div>
  <?php endif; ?>

  <?php if (isset($error)): ?>
    <div class="alert alert-error">
      ❌ <?= $error ?>
    </div>
  <?php endif; ?>

  <div class="content">

    <!-- Daftar Ulasan -->
    <div class="ulasan-list">
      <?php if (mysqli_num_rows($reviews) > 0): ?>
        <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
          <div class="ulasan-card">
            <div class="review-header">
              <div style="flex: 1;">
                <div class="reviewer-name"><?= $review['nama_customer'] ?></div>
                <div class="review-rating">
                  <?php 
                  // Tampilkan bintang sesuai rating
                  for ($i = 1; $i <= 5; $i++) {
                      if ($i <= $review['rating']) {
                          echo '⭐';
                      } else {
                          echo '☆';
                      }
                  }
                  ?>
                  <span style="color: #666; font-size: 14px; margin-left: 5px;">
                    (<?= $review['rating'] ?>/5)
                  </span>
                </div>
              </div>
              <div class="review-date">
                <?= date('d M Y H:i', strtotime($review['created_at'])) ?>
              </div>
            </div>
            <div class="review-comment">
              <?= nl2br(htmlspecialchars($review['komentar'])) ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="empty-reviews">
          <p>📝 Belum ada ulasan</p>
          <p style="margin-top: 10px; font-size: 14px;">
            Jadilah yang pertama memberikan ulasan!
          </p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Form Tambah Ulasan -->
    <div class="boxkr">
      <h3 style="margin-bottom: 20px; color: #333;">Tambah Ulasan</h3>
      <form method="POST">
        <div style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; font-weight: 500;">Nama Anda:</label>
          <input type="text" value="<?= $nama_customer ?>" readonly style="background: #f8f9fa;">
        </div>
        
        <div style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; font-weight: 500;">Rating:</label>
          <select name="rating" required>
            <option value="">Pilih Rating</option>
            <option value="5">⭐⭐⭐⭐⭐ (5) - Sangat Puas</option>
            <option value="4">⭐⭐⭐⭐ (4) - Puas</option>
            <option value="3">⭐⭐⭐ (3) - Cukup</option>
            <option value="2">⭐⭐ (2) - Kurang</option>
            <option value="1">⭐ (1) - Buruk</option>
          </select>
        </div>
        
        <div style="margin-bottom: 20px;">
          <label style="display: block; margin-bottom: 5px; font-weight: 500;">Komentar:</label>
          <textarea name="komentar" rows="5" placeholder="Bagikan pengalaman Anda..." required></textarea>
        </div>
        
        <button type="submit" name="tambah_ulasan">Kirim Ulasan</button>
      </form>
    </div>
  </div>
</body>
</html>