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
    <title>UrbanHype - Unisex Fashion Store</title>
    <link rel="icon" type="image/png" href="Background dan Logo/logo.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="icons/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #1E5DAC;
            --secondary: #B7C5DA;
            --accent: #E8D3C1;
            --light: #EAE2E4;
            --dark: #2d3748;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(30, 93, 172, 0.1);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            background: var(--white);
            color: var(--dark);
            position: relative;
        }

        /* ===== ANIMATED BACKGROUND ===== */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .bg-animation .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            filter: blur(80px);
            animation: float 15s ease-in-out infinite;
        }

        .bg-animation .shape:nth-child(1) {
            width: 600px;
            height: 600px;
            top: -200px;
            right: -150px;
            opacity: 0.7;
        }

        .bg-animation .shape:nth-child(2) {
            width: 500px;
            height: 500px;
            bottom: -100px;
            left: -100px;
            opacity: 0.5;
            animation-delay: -5s;
        }

        .bg-animation .shape:nth-child(3) {
            width: 400px;
            height: 400px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.3;
            animation-duration: 20s;
            animation-delay: -10s;
        }

        @keyframes float {
            0% { transform: translate(0px, 0px) rotate(0deg); }
            50% { transform: translate(15px, -15px) rotate(180deg); }
            100% { transform: translate(0px, 0px) rotate(360deg); }
        }

        /* ===== NAVBAR STYLING (GLASSMORPHISM) ===== */
        .navbar {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95) !important;
            transition: var(--transition);
            box-shadow: var(--shadow);
            border-bottom: 1px solid rgba(30, 93, 172, 0.1);
            padding: 1rem 0;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 40px rgba(30, 93, 172, 0.15);
            background: rgba(255, 255, 255, 1) !important;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 2px;
            position: relative;
            color: var(--primary) !important;
            transition: var(--transition);
        }

        .navbar-brand::before {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: var(--transition);
        }

        .navbar-brand:hover::before {
            width: 100%;
        }

        .navbar-brand:hover {
            transform: translateY(-2px);
            color: var(--primary) !important;
        }

        .nav-link {
            position: relative;
            font-weight: 500;
            color: var(--dark) !important;
            transition: var(--transition);
            padding: 8px 16px !important;
            margin: 0 4px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            background: var(--primary);
            border-radius: 50%;
            opacity: 0;
            transition: var(--transition);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: var(--transition);
            transform: translateX(-50%);
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .nav-link:hover::before {
            opacity: 1;
            top: -8px;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .bi-search {
            cursor: pointer;
            transition: var(--transition);
            padding: 8px;
            border-radius: 50%;
        }

        .bi-search:hover {
            color: var(--primary);
            transform: rotate(90deg) scale(1.2);
            background: rgba(30, 93, 172, 0.1);
        }

        .bi-bag-fill {
            transition: var(--transition);
            color: var(--primary) !important;
        }

        .bi-bag-fill:hover {
            transform: scale(1.15) rotate(5deg);
        }

        .user-dropdown img {
            border: 2px solid var(--primary);
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.2);
        }

        .user-dropdown img:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(30, 93, 172, 0.4);
            border-color: var(--secondary);
        }

        .btn-dark {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .btn-dark:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(30, 93, 172, 0.5);
            background: linear-gradient(135deg, #1a4d8f 0%, #9badc2 100%);
        }

        /* ===== HERO SECTION (MODERN & DYNAMIC) ===== */
        .hero-section {
            height: 90vh;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            position: relative;
            margin-top: 70px;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 600px;
            padding: 2rem;
            animation: fadeInLeft 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-content .subtitle {
            letter-spacing: 6px;
            font-weight: 300;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 20px;
            animation: fadeIn 1.5s ease;
            opacity: 0.9;
        }

        .hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 4rem;
            line-height: 1.2;
            margin-bottom: 30px;
            text-shadow: 2px 4px 20px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1.4s ease;
        }

        .hero-content .description {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 40px;
            opacity: 0.95;
            animation: fadeIn 1.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-btn {
            background: white;
            color: var(--primary);
            padding: 16px 50px;
            font-weight: 600;
            border: 2px solid white;
            transition: var(--transition);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
            animation: pulse 2.5s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            }
            50% {
                transform: scale(1.03);
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
            }
        }

        .hero-btn:hover {
            background: transparent;
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
            border-color: white;
        }

        .hero-image {
            position: absolute;
            right: 5%;
            top: 50%;
            transform: translateY(-50%);
            width: 45%;
            z-index: 2;
            animation: fadeInRight 1.4s ease;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translate(100px, -50%);
            }
            to {
                opacity: 1;
                transform: translate(0, -50%);
            }
        }

        .hero-image img {
            width: 100%;
            height: auto;
            filter: drop-shadow(0 20px 50px rgba(0, 0, 0, 0.3));
        }

        /* ===== CATEGORY FILTERS (MODERN TABS) ===== */
        .category-filters {
            padding: 3rem 5%;
            background: var(--white);
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .category-filters h2 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: var(--primary);
            position: relative;
        }

        .category-filters h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 2px;
        }

        .filter-tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }

        .filter-tab {
            padding: 0.8rem 1.5rem;
            background: white;
            border: 2px solid var(--primary);
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .filter-tab::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            z-index: -1;
            transition: var(--transition);
        }

        .filter-tab:hover::before {
            left: 0;
        }

        .filter-tab:hover {
            color: white;
            transform: translateY(-2px);
        }

        .filter-tab.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .filter-tab.active::before {
            left: 0;
        }

        /* ===== PRODUCT GRID (MODERN CARD WITH HOVER EFFECT) ===== */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            padding: 2rem 5%;
            background: var(--white);
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            transform: scale(0.95);
            opacity: 0;
            animation: fadeInScale 0.8s ease forwards;
        }

        @keyframes fadeInScale {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .product-card:nth-child(1) { animation-delay: 0.1s; }
        .product-card:nth-child(2) { animation-delay: 0.15s; }
        .product-card:nth-child(3) { animation-delay: 0.2s; }
        .product-card:nth-child(4) { animation-delay: 0.25s; }
        .product-card:nth-child(5) { animation-delay: 0.3s; }
        .product-card:nth-child(6) { animation-delay: 0.35s; }
        .product-card:nth-child(7) { animation-delay: 0.4s; }
        .product-card:nth-child(8) { animation-delay: 0.45s; }

        .product-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 20px 60px rgba(30, 93, 172, 0.25);
        }

        .product-image-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background: var(--light);
        }

        .product-card img {
            width: 100%;
            border-radius: 20px;
            transition: var(--transition);
            transform: scale(1);
        }

        .product-card:hover img {
            transform: scale(1.15);
        }

        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(30, 93, 172, 0.85) 0%, rgba(183, 197, 218, 0.85) 100%);
            opacity: 0;
            transition: var(--transition);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-card:hover .product-overlay {
            opacity: 1;
        }

        .overlay-text {
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            transform: translateY(20px);
            opacity: 0;
            transition: var(--transition) 0.1s;
        }

        .product-card:hover .overlay-text {
            transform: translateY(0);
            opacity: 1;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-card .category {
            color: var(--secondary);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        .product-card h6 {
            font-weight: 600;
            color: var(--dark);
            transition: var(--transition);
            font-size: 1rem;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .product-card:hover h6 {
            color: var(--primary);
        }

        .product-card .price {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.25rem;
            font-family: 'Playfair Display', serif;
        }

        /* ===== FOOTER PROMO ===== */
        .promo-section {
            background: linear-gradient(135deg, var(--accent) 0%, var(--light) 100%);
            padding: 80px 0;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }

        .promo-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -10%;
            width: 40%;
            height: 200%;
            background: radial-gradient(circle, rgba(30, 93, 172, 0.1) 0%, transparent 70%);
            animation: float 15s ease-in-out infinite;
        }

        .promo-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .promo-content h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .promo-content p {
            font-size: 1.1rem;
            color: var(--dark);
            opacity: 0.8;
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 992px) {
            .hero-image {
                display: none;
            }

            .hero-content {
                max-width: 100%;
            }

            .hero-content h1 {
                font-size: 3rem;
            }
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .section-header h2 {
                font-size: 2.2rem;
            }

            .filter-tabs {
                gap: 0.5rem;
            }

            .filter-tab {
                padding: 0.6rem 1rem;
                font-size: 0.75rem;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        /* ===== LOADING SPINNER ===== */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeOut 0.8s ease 1.5s forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        .spinner {
            width: 70px;
            height: 70px;
            border: 6px solid rgba(255, 255, 255, 0.3);
            border-top: 6px solid white;
            border-radius: 50%;
            animation: spin 1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .logo-loader {
            position: absolute;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: white;
            font-weight: 700;
            letter-spacing: 3px;
            animation: pulse 2s ease infinite;
        }

        /* ===== SCROLL REVEAL ANIMATION ===== */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: var(--transition);
        }

        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== BADGE CART ===== */
        .badge-cart {
            animation: bounceIn 0.6s ease;
            background: var(--primary) !important;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.3);
            }
            100% {
                transform: scale(1);
            }
        }

    </style>
</head>

<body>

    <!-- LOADING SPINNER -->
    <div class="spinner-overlay">
        <div class="spinner"></div>
    </div>

    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">URBANHYPE</a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto gap-2">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Pages</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>

                <ul class="navbar-nav ms-4 d-flex align-items-center gap-3">
                    <li><i class="bi bi-search fs-5"></i></li>

                    <?php if ($user): ?>
                        <!-- Keranjang user -->
                        <li class="nav-item position-relative">
                            <a href="user/produk_pembayaran/cart.php" class="nav-link position-relative">
                                <i class="bi bi-bag-fill fs-5"></i>
                                <?php
                                $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                                if ($cart_count > 0):
                                ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill badge-cart">
                                        <?php echo $cart_count; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <!-- User dropdown -->
                        <li class="nav-item dropdown user-dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="foto_profil/<?php echo $user['foto_profil']; ?>" alt="Foto Profil" class="rounded-circle" style="width:40px; height:40px; object-fit:cover;">
                                <span class="d-none d-lg-inline"><?php echo $user['nama_lengkap']; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="user/settings/settings.php">Setting Akun</a></li>
                                <li><a class="dropdown-item" href="user/logout_user.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="/user/login_user.php" class="btn btn-dark px-4 py-2 rounded-pill">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <p class="subtitle">New Season Collection</p>
                <h1>Timeless Style<br>for Everyone</h1>
                <p class="description">Discover our curated collection of unisex fashion pieces that blend contemporary design with classic elegance.</p>
                <button class="btn hero-btn">Explore Collection</button>
            </div>
        </div>
        <div class="hero-image">
            <!-- <img src="/placeholder.svg?height=600&width=400" alt="Fashion Model"> -->
        </div>
    </section>

    <!-- CATEGORY FILTERS -->
    <section class="category-filters scroll-reveal">
        <h2>Explore Collections</h2>
        <div class="filter-tabs">
            <button class="filter-tab active">All</button>
            <button class="filter-tab">Women</button>
            <button class="filter-tab">Men</button>
            <button class="filter-tab">Accessories</button>
        </div>
    </section>

    <!-- PRODUCT GRID -->
    <section class="products-grid">
        <?php
        $query = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id DESC LIMIT 8");
        while ($row = mysqli_fetch_assoc($query)):
        ?>
            <div class="product-card scroll-reveal">
                <a href="user/produk_pembayaran/product_detail.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                    <div class="product-image-wrapper">
                        <img src="foto_produk/<?php echo $row['gambar']; ?>" class="img-fluid">
                        <div class="product-overlay">
                            <span class="overlay-text">View Details</span>
                        </div>
                    </div>
                    <div class="product-info">
                        <p class="category mb-1"><?php echo strtoupper($row['kategori']); ?></p>
                        <h6><?php echo $row['nama']; ?></h6>
                        <p class="price mb-0">$<?php echo number_format($row['harga'], 2); ?></p>
                    </div>
                </a>
            </div>
        <?php endwhile; ?>
    </section>

    <!-- PROMO SECTION -->
    <section class="promo-section">
        <div class="container">
            <div class="promo-content">
                <h3>Step Into Your Style</h3>
                <p>Experience fashion that transcends boundaries. Premium quality, unisex designs.</p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-4">URBANHYPE</h5>
                    <p>Where streetwear meets sophistication. Discover the latest unisex trends that defy gender norms and redefine urban style.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Shop</a></li>
                        <li><a href="#" class="text-white text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Women</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Men</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Accessories</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Shoes</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6>Subscribe to Our Newsletter</h6>
                    <p>Get the latest updates on new arrivals and exclusive offers.</p>
                    <form>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Your Email" aria-label="Your Email">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </form>
                    <div class="social-icons mt-3">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2025 UrbanHype. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="js/bootstrap.bundle.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Category filter active state
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll Reveal Animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.scroll-reveal').forEach(el => {
            observer.observe(el);
        });

        // Loading Spinner
        window.addEventListener('load', function() {
            const spinner = document.querySelector('.spinner-overlay');
            setTimeout(() => {
                spinner.style.opacity = '0';
                setTimeout(() => {
                    spinner.style.display = 'none';
                }, 500);
            }, 1500);
        });
    </script>
</body>

</html>