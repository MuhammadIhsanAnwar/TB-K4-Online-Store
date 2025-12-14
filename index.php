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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            background: #f8f9fa;
        }

        /* ========== NAVBAR STYLING ========== */
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
            transition: all 0.4s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .navbar-brand {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .nav-link {
            position: relative;
            font-weight: 500;
            color: #2d3748 !important;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .nav-link:hover {
            color: #667eea !important;
        }

        .bi-search, .bi-bag-fill {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .bi-search:hover {
            color: #667eea;
            transform: scale(1.2);
        }

        .bi-bag-fill {
            transition: all 0.3s ease;
        }

        .bi-bag-fill:hover {
            transform: scale(1.15);
        }

        .user-dropdown img {
            border: 3px solid #667eea;
            transition: all 0.3s ease;
        }

        .user-dropdown img:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-dark {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-dark:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        /* ========== HERO SECTION ========== */
        .hero-section {
            height: 90vh;
            background-size: cover;
            background-position: center;
            position: relative;
            margin-top: 70px;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.7) 0%, rgba(118, 75, 162, 0.7) 100%);
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 0.5; }
        }

        .hero-text {
            position: absolute;
            top: 50%;
            left: 10%;
            transform: translateY(-50%);
            color: white;
            z-index: 2;
            animation: slideInLeft 1s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translate(-100px, -50%);
            }
            to {
                opacity: 1;
                transform: translate(0, -50%);
            }
        }

        .hero-text p {
            letter-spacing: 4px;
            font-weight: 300;
            animation: fadeIn 1.5s ease;
        }

        .hero-text h1 {
            font-weight: 700;
            text-shadow: 2px 4px 10px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1.2s ease;
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

        .hero-text button {
            background: white;
            color: #667eea;
            font-weight: 600;
            border: none;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .hero-text button:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        /* ========== NEW ARRIVALS SECTION ========== */
        .container.py-5 h2 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeInDown 1s ease;
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

        .container.py-5 a {
            position: relative;
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 20px;
        }

        .container.py-5 a:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        /* ========== PRODUCT CARDS ========== */
        .product-card {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            transition: all 0.4s ease;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            animation: fadeInScale 0.8s ease forwards;
            opacity: 0;
        }

        @keyframes fadeInScale {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .product-card:nth-child(1) { animation-delay: 0.1s; }
        .product-card:nth-child(2) { animation-delay: 0.2s; }
        .product-card:nth-child(3) { animation-delay: 0.3s; }
        .product-card:nth-child(4) { animation-delay: 0.4s; }
        .product-card:nth-child(5) { animation-delay: 0.5s; }
        .product-card:nth-child(6) { animation-delay: 0.6s; }
        .product-card:nth-child(7) { animation-delay: 0.7s; }
        .product-card:nth-child(8) { animation-delay: 0.8s; }

        .product-card img {
            width: 100%;
            border-radius: 15px;
            transition: all 0.5s ease;
            transform: scale(1);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }

        .product-card:hover img {
            transform: scale(1.1);
        }

        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
            opacity: 0;
            transition: all 0.4s ease;
            border-radius: 15px;
            z-index: 1;
        }

        .product-card:hover::before {
            opacity: 0.7;
        }

        .product-card h6 {
            font-weight: 600;
            color: #2d3748;
            transition: all 0.3s ease;
        }

        .product-card:hover h6 {
            color: #667eea;
        }

        .product-card p:last-child {
            font-weight: 600;
            color: #667eea;
            font-size: 1.1rem;
        }

        /* ========== BADGE CART ========== */
        .badge-cart {
            animation: bounceIn 0.6s ease;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .hero-text {
                left: 5%;
                width: 90%;
            }

            .hero-text h1 {
                font-size: 2rem;
            }
        }

        /* ========== SCROLL ANIMATIONS ========== */
        .scroll-fade {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s ease;
        }

        .scroll-fade.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* ========== LOADING SPINNER ========== */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeOut 1s ease 2s forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top: 5px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>

    <!-- LOADING SPINNER -->
    <div class="spinner-overlay">
        <div class="spinner"></div>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="#">UrbanHype</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto gap-3">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Pages</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact Us</a></li>
                </ul>

                <ul class="navbar-nav ms-3 d-flex align-items-center gap-3">
                    <li><i class="bi bi-search fs-5"></i></li>

                    <?php if ($user): ?>
                        <!-- Keranjang user -->
                        <li class="nav-item position-relative">
                            <a href="user/produk_pembayaran/cart.php" class="nav-link position-relative">
                                <!-- Ikon keranjang berwarna biru -->
                                <i class="bi bi-bag-fill fs-5 text-primary"></i>
                                <?php
                                $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                                if ($cart_count > 0):
                                ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-cart">
                                        <?php echo $cart_count; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <!-- User dropdown -->
                        <li class="nav-item dropdown user-dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="foto_profil/<?php echo $user['foto_profil']; ?>" alt="Foto Profil" class="rounded-circle" style="width:40px; height:40px; object-fit:cover;">
                                <?php echo $user['nama_lengkap']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="user/settings/settings.php">Setting Akun</a></li>
                                <li><a class="dropdown-item" href="user/logout_user.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="/user/login_user.php" class="btn btn-dark px-3 py-1 rounded-3">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero-section" style="background-image: url('hero.jpg');">
        <div class="hero-text">
            <p class="text-uppercase">Urban Edge</p>
            <h1 class="fw-bold display-5">Jackets for the Modern Man</h1>
            <button class="btn btn-light px-4 py-2 mt-3">Discover Now</button>
        </div>
    </section>

    <!-- NEW ARRIVALS -->
    <div class="container py-5">
        <h2 class="text-center fw-bold">New Arrivals</h2>
        <div class="d-flex justify-content-center gap-4 my-3 flex-wrap">
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
                        <div class="product-card p-3">
                            <img src="foto_produk/<?php echo $row['gambar']; ?>" class="img-fluid rounded">
                            <p class="mt-3 text-secondary small mb-1"><?php echo strtoupper($row['kategori']); ?></p>
                            <h6 class="mb-2"><?php echo $row['nama']; ?></h6>
                            <p class="mb-0">$<?php echo number_format($row['harga'], 2); ?></p>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="js/bootstrap.bundle.js"></script>
    <script>
        // Scroll Animation
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.scroll-fade').forEach(el => observer.observe(el));

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 8px 30px rgba(0, 0, 0, 0.12)';
            } else {
                navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.08)';
            }
        });
    </script>
</body>

</html>