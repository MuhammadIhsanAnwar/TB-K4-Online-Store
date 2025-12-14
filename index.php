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

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="icons/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1E5DAC;
            --secondary: #B7C5DA;
            --accent: #E8D3C1;
            --light: #EAE2E4;
            --dark: #2d3748;
            --white: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            background: var(--white);
            color: var(--dark);
        }

        /* ========== NAVBAR STYLING ========== */
        .navbar {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.98) !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 30px rgba(30, 93, 172, 0.08);
            border-bottom: 1px solid rgba(30, 93, 172, 0.1);
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
            transition: all 0.4s ease;
        }

        .navbar-brand::before {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: width 0.4s ease;
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
            transition: all 0.3s ease;
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
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
            transition: all 0.4s ease;
            padding: 8px;
            border-radius: 50%;
        }

        .bi-search:hover {
            color: var(--primary);
            transform: rotate(90deg) scale(1.2);
            background: rgba(30, 93, 172, 0.1);
        }

        .bi-bag-fill {
            transition: all 0.3s ease;
            color: var(--primary) !important;
        }

        .bi-bag-fill:hover {
            transform: scale(1.15) rotate(5deg);
        }

        .user-dropdown img {
            border: 2px solid var(--primary);
            transition: all 0.4s ease;
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
            transition: all 0.4s ease;
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

        /* ========== HERO SECTION ========== */
        .hero-section {
            height: 90vh;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            position: relative;
            margin-top: 70px;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 150%;
            background: radial-gradient(circle, rgba(232, 211, 193, 0.3) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 60%;
            height: 100%;
            background: radial-gradient(circle, rgba(183, 197, 218, 0.3) 0%, transparent 70%);
            animation: float 15s ease-in-out infinite reverse;
        }

        /* @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -50px) rotate(5deg); }
            66% { transform: translate(-20px, 20px) rotate(-5deg); }
        } */

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 600px;
            animation: slideInLeft 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        } */

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
            transition: all 0.4s ease;
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

        /* ========== NEW ARRIVALS SECTION ========== */
        .section-header {
            margin-bottom: 50px;
            animation: fadeInDown 1s ease;
        }

        .section-header h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .section-header h2::after {
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

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .category-nav {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .category-nav a {
            position: relative;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 30px;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            transition: all 0.4s ease;
            color: var(--dark);
            border: 2px solid transparent;
        }

        .category-nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 30px;
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: -1;
        }

        .category-nav a:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 93, 172, 0.3);
        }

        .category-nav a:hover::before {
            opacity: 1;
        }

        .category-nav a.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 5px 20px rgba(30, 93, 172, 0.3);
        }

        /* ========== PRODUCT CARDS ========== */
        .product-card {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            box-shadow: 0 5px 25px rgba(30, 93, 172, 0.08);
            animation: fadeInScale 0.8s ease forwards;
            opacity: 0;
            transform: scale(0.9);
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

        .product-image-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background: var(--light);
        }

        .product-card img {
            width: 100%;
            border-radius: 20px;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            transform: scale(1);
        }

        .product-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 60px rgba(30, 93, 172, 0.25);
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
            transition: all 0.5s ease;
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
            transition: all 0.5s ease 0.1s;
        }

        .product-card:hover .overlay-text {
            transform: translateY(0);
            opacity: 1;
        }

        .product-info {
            padding: 20px;
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
            transition: all 0.3s ease;
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

        /* ========== BADGE CART ========== */
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

        /* ========== FOOTER PROMO ========== */
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

        /* ========== RESPONSIVE ========== */
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

            .category-nav {
                gap: 15px;
            }

            .category-nav a {
                padding: 10px 20px;
                font-size: 0.75rem;
            }
        }

        /* ========== LOADING SPINNER ========== */
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
    </style>
</head>

<body>

    <!-- LOADING SPINNER -->
    <div class="spinner-overlay">
        <div class="spinner"></div>
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

    <!-- NEW ARRIVALS -->
    <div class="container py-5">
        <div class="section-header text-center">
            <h2>New Arrivals</h2>
            <div class="category-nav">
                <a href="#" class="active">All</a>
                <a href="#">Women</a>
                <a href="#">Men</a>
                <a href="#">Shoes</a>
                <a href="#">Bags</a>
                <a href="#">Accessories</a>
            </div>
        </div>

        <div class="row g-4 mt-4">
            <?php
            $query = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id DESC LIMIT 8");
            while ($row = mysqli_fetch_assoc($query)):
            ?>
                <div class="col-6 col-md-3">
                    <a href="user/produk_pembayaran/product_detail.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                        <div class="product-card">
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
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- PROMO SECTION -->
    <section class="promo-section">
        <div class="container">
            <div class="promo-content">
                <h3>Step Into Your Style</h3>
                <p>Experience fashion that transcends boundaries. Premium quality, unisex designs.</p>
            </div>
        </div>
    </section>

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
        document.querySelectorAll('.category-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.category-nav a').forEach(a => a.classList.remove('active'));
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
    </script>
</body>

</html>
