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
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">
    <title>UrbanHype - Unisex Fashion Store</title>

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

        /* ===== NAVBAR STYLING (GLASSMORPHISM + IMPROVED) ===== */
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

        .navbar-toggler {
            border: 2px solid var(--primary) !important;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            transition: var(--transition);
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(30, 93, 172, 0.25);
        }

        .navbar-toggler:hover {
            background: rgba(30, 93, 172, 0.1);
            transform: scale(1.05);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(30, 93, 172, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .nav-link {
            position: relative;
            font-weight: 500;
            color: var(--dark) !important;
            transition: var(--transition);
            padding: 10px 18px !important;
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

        .nav-icon {
            cursor: pointer;
            transition: var(--transition);
            padding: 8px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-icon:hover {
            color: var(--primary);
            transform: scale(1.1);
            background: rgba(30, 93, 172, 0.1);
        }

        .nav-icon .bi-search:hover {
            transform: rotate(90deg);
        }
        :root {
    /* ... */
    --cart-icon-filter: brightness(0) saturate(100%) invert(28%) sepia(33%) saturate(2300%) hue-rotate(177deg); /* biru #1E5DAC */
}
        .cart-link {
            position: relative;
            display: flex;
            align-items: center;
            padding: 8px;
            border-radius: 50%;
            transition: var(--transition);
            color: var(--primary) !important;
            text-decoration: none;
        }

        .cart-link:hover {
            transform: scale(1.15);
            background: rgba(30, 93, 172, 0.1);
        }

        .cart-link .bi-bag-fill {
            font-size: 1.25rem;
        }

        .cart-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--primary) !important;
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
            border-radius: 50%;
            font-weight: 600;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(30, 93, 172, 0.4);
            animation: bounceIn 0.6s ease;
        }

        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            transition: var(--transition);
            text-decoration: none;
            background: rgba(30, 93, 172, 0.05);
        }

        .user-dropdown .dropdown-toggle:hover {
            background: rgba(30, 93, 172, 0.15);
            transform: translateY(-2px);
        }

        .user-dropdown .dropdown-toggle::after {
            margin-left: 0.5rem;
        }

        .user-dropdown img {
            border: 2px solid var(--primary);
            transition: var(--transition);
            box-shadow: 0 2px 10px rgba(30, 93, 172, 0.2);
        }

        .user-dropdown img:hover {
            border-color: var(--secondary);
        }

        .user-name {
            font-weight: 500;
            color: var(--dark);
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-dropdown .dropdown-menu {
            border: 1px solid rgba(30, 93, 172, 0.1);
            box-shadow: 0 4px 20px rgba(30, 93, 172, 0.15);
            border-radius: 12px;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .user-dropdown .dropdown-item {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            transition: var(--transition);
            font-weight: 500;
        }

        .user-dropdown .dropdown-item:hover {
            background: rgba(30, 93, 172, 0.1);
            color: var(--primary);
            transform: translateX(4px);
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
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
        }

        .btn-dark:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(30, 93, 172, 0.5);
            background: linear-gradient(135deg, #1a4d8f 0%, #9badc2 100%);
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        @media (max-width: 991px) {
            .navbar-brand { font-size: 1.5rem; }
            .navbar-collapse {
                margin-top: 1rem;
                padding: 1.5rem;
                background: rgba(255, 255, 255, 0.98);
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(30, 93, 172, 0.1);
            }
            .nav-link {
                margin: 4px 0;
                text-align: center;
                padding: 12px 18px !important;
            }
            .navbar-nav.ms-auto { margin-left: 0 !important; }
            .navbar-actions {
                justify-content: center;
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(30, 93, 172, 0.1);
                gap: 1.5rem;
            }
            .user-dropdown .dropdown-toggle { justify-content: center; }
            .user-name { display: inline !important; }
        }

        @media (max-width: 576px) {
            .navbar-brand { font-size: 1.3rem; letter-spacing: 1px; }
            .user-name { max-width: 100px; }
            .nav-link { font-size: 0.8rem; }
            .navbar-actions { gap: 1rem; }
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
            from { opacity: 0; transform: translateX(-100px); }
            to { opacity: 1; transform: translateX(0); }
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

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
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
            0%, 100% { transform: scale(1); box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); }
            50% { transform: scale(1.03); box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3); }
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
            from { opacity: 0; transform: translate(100px, -50%); }
            to { opacity: 1; transform: translate(0, -50%); }
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

        /* ===== SCROLL REVEAL ===== */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: var(--transition);
        }

        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== ABOUT & CONTACT SECTION STYLE ===== */
        .section-bg-light {
            background: var(--light);
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
            to { opacity: 0; visibility: hidden; }
        }

        .spinner {
            width: 70px;
            height: 70px;
            border: 6px solid rgba(255, 255, 255, 0.3);
            border-top: 6px solid white;
            border-radius: 50%;
            animation: spin 1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .carousel-caption {
    background: rgba(30, 93, 172, 0.75);
    border-radius: 12px;
    padding: 1rem 1.5rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.carousel-caption h5 {
    font-family: 'Playfair Display', serif;
    font-weight: 600;
}

.carousel img {
    object-fit: cover;
    height: 100%;
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

    <!-- NAVBAR (dari file baru, diperbaiki & diperkaya) -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">URBANHYPE</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto me-lg-4">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>

                <div class="navbar-actions">
                    <div class="nav-icon">
                        <i class="bi bi-search fs-5"></i>
                    </div>

                    <?php if ($user): ?>
                        <a href="user/produk_pembayaran/cart.php" class="cart-link">
                            <img src="images/cart-icon.svg" alt="Cart" style="width:24px; height:24px; filter: var(--cart-icon-filter);">
                            <?php
                            $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                            if ($cart_count > 0):
                            ?>
                                <span class="cart-badge"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="dropdown user-dropdown">
                            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="foto_profil/<?php echo htmlspecialchars($user['foto_profil']); ?>" 
                                     alt="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" 
                                     class="rounded-circle" 
                                     style="width:40px; height:40px; object-fit:cover;">
                                <span class="user-name d-none d-lg-inline"><?php echo htmlspecialchars($user['nama_lengkap']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="user/settings/settings.php"><i class="bi bi-gear me-2"></i>Setting Akun</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="user/logout_user.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/user/login_user.php" class="btn btn-dark">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO (dari file lama) -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="hero-content">
                <p class="subtitle">New Season Collection</p>
                <h1>Timeless Style<br>for Everyone</h1>
                <p class="description">Discover our curated collection of unisex fashion pieces that blend contemporary design with classic elegance.</p>
                <button class="btn hero-btn">Explore Collection</button>
            </div>
        </div>
        <div class="hero-image">
            <!-- <img src="img/hero-model.png" alt="Fashion Model"> -->
        </div>
    </section>

    

    <!-- ABOUT SECTION (dari file lama) -->
    <section id="about" class="py-5 bg-light scroll-reveal">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="text-primary">About <span class="text-dark">UrbanHype</span></h2>
                    <p class="lead mt-3">UrbanHype was founded in 2023 with a vision to break fashion boundaries. We believe style has no gender — only expression.</p>
                    <p>Our unisex collections are designed for the bold, the thoughtful, and the effortlessly cool. Every piece is crafted with sustainable materials and timeless aesthetics.</p>
                    <a href="#" class="btn btn-dark mt-3">Learn More</a>
                </div>
               <div class="col-lg-6">
    <div id="aboutCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel" data-bs-interval="4000">

        <!-- Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="2"></button>
        </div>

        <!-- Slides -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/Background dan Logo/bg reset.jpg" class="d-block w-100" alt="UrbanHype Collection 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5>New Arrival</h5>
                    <p>Fresh unisex collection for modern lifestyle</p>
                </div>
            </div>

            <div class="carousel-item">
                <img src="images/Background dan Logo/bg login.jpg" class="d-block w-100" alt="UrbanHype Collection 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Limited Edition</h5>
                    <p>Bold style with premium quality</p>
                </div>
            </div>

            <div class="carousel-item">
                <img src="images/Background dan Logo/bg regis.jpg" class="d-block w-100" alt="UrbanHype Collection 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Urban Essentials</h5>
                    <p>Comfort meets timeless design</p>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#aboutCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#aboutCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>

    </div>
</div>

            </div>
        </div>
    </section>

    <!-- CONTACT SECTION (dari file lama) -->
    <section id="contact" class="py-5 scroll-reveal">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-primary">Get In <span class="text-dark">Touch</span></h2>
                <p class="text-muted mt-2">Have questions? We’re here to help.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form id="contactForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="text" name="nama" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="subjek" class="form-control" placeholder="Subject">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="pesan" rows="5" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Send Message</button>
                    </form>
                </div>
            </div>
            <div class="row mt-5 text-center">
                <div class="col-md-4 mb-4">
                    <i class="bi bi-geo-alt fs-2 text-primary"></i>
                    <h6 class="mt-3">Location</h6>
                    <p>Jl. Fashion Avenue No. 45,<br>Jakarta, Indonesia</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="bi bi-envelope fs-2 text-primary"></i>
                    <h6 class="mt-3">Email</h6>
                    <p>hello@urbanhype.id</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="bi bi-telephone fs-2 text-primary"></i>
                    <h6 class="mt-3">Phone</h6>
                    <p>+62 812-3456-7890</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER (dari file lama) -->
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
                        <li><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="shop.php" class="text-white text-decoration-none">Shop</a></li>
                        <li><a href="#about" class="text-white text-decoration-none">About Us</a></li>
                        <li><a href="#contact" class="text-white text-decoration-none">Contact</a></li>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);


    fetch('simpan_pesan_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
            icon: data.status,
            title: data.status === 'success' ? 'Success' : 'Error',
            text: data.message,
            confirmButtonColor: '#1E5DAC'
        });

        if (data.status === 'success') {
            form.reset();
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan server',
            confirmButtonColor: '#1E5DAC'
        });
    })
    .finally(() => {
        document.getElementById('btnText').classList.remove('d-none');
        document.getElementById('btnLoading').classList.add('d-none');
    });
});
</script>
</body>

</html>