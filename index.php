<?php
include "admin/koneksi.php";
session_start();

// Jika user sudah login, ambil data user
$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanHype - Online Store</title>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="icons/bootstrap-icons.css">

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .hero-section {
            height: 90vh;
            background-size: cover;
            background-position: center;
            position: relative;
            margin-top: 70px;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.25);
        }

        .hero-text {
            position: absolute;
            top: 50%;
            left: 10%;
            transform: translateY(-50%);
            color: white;
        }

        .product-card img {
            width: 100%;
            border-radius: 8px;
        }

        .user-dropdown img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }

        .badge-cart {
            position: absolute;
            top: 0;
            left: 100%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="#">UrbanHype</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <?php include 'navbar.php'; ?>

    <!-- HERO -->
    <section class="hero-section" style="background-image: url('hero.jpg');">
        <div class="hero-overlay"></div>
        <div class="hero-text">
            <p class="text-uppercase">Urban Edge</p>
            <h1 class="fw-bold display-5">Jackets for the Modern Man</h1>
            <button class="btn btn-light px-4 py-2 mt-3">Discover Now</button>
        </div>
    </section>

    <!-- NEW ARRIVALS -->
    <div class="container py-5">
        <h2 class="text-center fw-bold">New Arrivals</h2>
        <div class="d-flex justify-content-center gap-4 my-3">
            <a href="#" class="text-dark">Women</a>
            <a href="#" class="text-secondary">Men</a>
            <a href="#" class="text-secondary">Shoes</a>
            <a href="#" class="text-secondary">Bags</a>
            <a href="#" class="text-secondary">Accessories</a>
        </div>

        <div class="row g-4 mt-4">
            <?php
            $query = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id DESC LIMIT 8");
            while ($row = mysqli_fetch_assoc($query)):
            ?>
                <div class="col-6 col-md-3">
                    <a href="user/produk_pembayaran/product_detail.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark">
                        <div class="product-card">
                            <img src="foto_produk/<?php echo $row['gambar']; ?>" class="img-fluid rounded">
                            <p class="mt-2 text-secondary small"><?php echo strtoupper($row['kategori']); ?></p>
                            <h6><?php echo $row['nama']; ?></h6>
                            <p>$<?php echo number_format($row['harga'], 2); ?></p>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="js/bootstrap.bundle.js"></script>
</body>

</html>