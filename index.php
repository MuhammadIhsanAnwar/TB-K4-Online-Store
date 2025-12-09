<?php include "admin/koneksi.php"; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanHype - Online Store</title>

    <!-- BOOTSTRAP CSS OFFLINE -->
    <link rel="stylesheet" href="css/bootstrap.css">

    <!-- BOOTSTRAP ICONS OFFLINE -->
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
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="#">UrbanHype</a>

            <!-- TOMBOL HAMBURGER -->
            <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navMenu"
                aria-controls="navMenu"
                aria-expanded="false"
                aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">

                <!-- MENU KIRI -->
                <ul class="navbar-nav ms-auto gap-3">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Pages</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact Us</a></li>
                </ul>

                <!-- ICONS + LOGIN -->
                <ul class="navbar-nav ms-3 d-flex align-items-center gap-3">

                    <li><i class="bi bi-search fs-5"></i></li>
                    <li><i class="bi bi-person fs-5"></i></li>
                    <li><i class="bi bi-bag fs-5"></i></li>

                    <li>
                        <a href="user/login_user.php" class="btn btn-dark px-3 py-1 rounded-3">
                            Login
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>


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
            while ($row = mysqli_fetch_assoc($query)) {
            ?>
                <div class="col-6 col-md-3">
                    <div class="product-card">
                        <img src="upload/<?php echo $row['gambar']; ?>" class="img-fluid rounded">

                        <p class="mt-2 text-secondary small">
                            <?php echo strtoupper($row['kategori']); ?>
                        </p>

                        <h6><?php echo $row['nama']; ?></h6>
                        <p>$<?php echo number_format($row['harga'], 2); ?></p>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>


    <!-- BOOTSTRAP JS OFFLINE -->
    <script src="js/bootstrap.bundle.js"></script>

</body>

</html>