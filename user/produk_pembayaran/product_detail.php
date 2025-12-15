<?php
session_start();
include "admin/koneksi.php";

$id = $_GET['id'] ?? 0;
$query = mysqli_query($koneksi, "SELECT * FROM products WHERE id='$id'");
$product = mysqli_fetch_assoc($query);

// if (!$product) {
//     die("Produk tidak ditemukan!");
// }

// Tambah ke keranjang
if (isset($_POST['add_to_cart'])) {
    $cart_item = [
        'id' => $product['id'],
        'nama' => $product['nama'],
        'harga' => $product['harga'],
        'gambar' => $product['gambar'],
        'qty' => $_POST['qty']
    ];

    // Jika session cart kosong, buat baru
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    // Cek apakah produk sudah ada di keranjang
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $cart_item['id']) {
            $item['qty'] += $cart_item['qty'];
            $found = true;
            break;
        }
    }
    if (!$found) $_SESSION['cart'][] = $cart_item;

    header("Location: cart.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['nama']; ?> - Urban Hype</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1E5DAC;
            --beige: #E8D3C1;
            --alley: #B7C5DA;
            --misty: #EAE2E4;
            --dark: #1a1a1a;
            --gray: #666;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--misty) 0%, #ffffff 100%);
            color: var(--dark);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1.5rem 0;
            box-shadow: 0 2px 20px rgba(30, 93, 172, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
            color: var(--dark);
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            position: relative;
            transition: color 0.3s ease;
            padding: 0.5rem 0;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 100%;
            height: 2px;
            background: var(--primary);
            transition: transform 0.3s ease;
        }

        .nav-links a:hover::after {
            transform: translateX(-50%) scaleX(1);
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        /* Product Section */
        .product-container {
            max-width: 1200px;
            margin: 4rem auto;
            padding: 0 2rem;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .breadcrumb {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .breadcrumb a:hover {
            opacity: 0.7;
        }

        .product-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        .product-image-container {
            position: relative;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(30, 93, 172, 0.15);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-image-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(30, 93, 172, 0.25);
        }

        .product-image {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.5s ease;
        }

        .product-image-container:hover .product-image {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: linear-gradient(135deg, var(--primary), var(--alley));
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
        }

        .product-info {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .product-category {
            color: var(--primary);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1rem;
        }

        .product-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .product-price {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .product-description {
            color: var(--gray);
            line-height: 1.8;
            margin-bottom: 2rem;
            font-size: 1.05rem;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--alley), transparent);
            margin: 2rem 0;
        }

        /* Quantity Selector */
        .quantity-section {
            margin-bottom: 2rem;
        }

        .quantity-label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        .quantity-input {
            width: 120px;
            padding: 1rem;
            border: 2px solid var(--alley);
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            text-align: center;
            transition: all 0.3s ease;
            background: var(--misty);
        }

        .quantity-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(30, 93, 172, 0.1);
        }

        /* Add to Cart Button */
        .btn-add-cart {
            width: 100%;
            padding: 1.25rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, #2a7fd9 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(30, 93, 172, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-add-cart::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-add-cart:hover::before {
            left: 100%;
        }

        .btn-add-cart:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(30, 93, 172, 0.4);
        }

        .btn-add-cart:active {
            transform: translateY(-1px);
        }

        /* Product Features */
        .product-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature-item {
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--misty), white);
            border-radius: 12px;
            border: 1px solid var(--alley);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateX(5px);
            border-color: var(--primary);
            box-shadow: 0 5px 20px rgba(30, 93, 172, 0.15);
        }

        .feature-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .feature-text {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                gap: 1.5rem;
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .product-info {
                padding: 2rem;
            }

            .product-title {
                font-size: 2rem;
            }

            .product-features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">Urban Hype</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="cart.php">Cart</a></li>
        </ul>
    </div>
</nav>

<!-- Product Section -->
<div class="product-container">
    <div class="breadcrumb">
        <a href="index.php">Home</a>
        <span>/</span>
        <a href="products.php">Products</a>
        <span>/</span>
        <span><?php echo $product['nama']; ?></span>
    </div>

    <div class="product-grid">
        <!-- Product Image -->
        <div class="product-image-container">
            <div class="product-badge">New Arrival</div>
            <img src="foto_produk/<?php echo $product['gambar']; ?>" alt="<?php echo $product['nama']; ?>" class="product-image">
        </div>

        <!-- Product Info -->
        <div class="product-info">
            <div class="product-category"><?php echo strtoupper($product['kategori']); ?></div>
            <h1 class="product-title"><?php echo $product['nama']; ?></h1>
            <div class="product-price">$<?php echo number_format($product['harga'], 2); ?></div>
            
            <div class="divider"></div>
            
            <p class="product-description"><?php echo $product['deskripsi']; ?></p>

            <form method="POST">
                <div class="quantity-section">
                    <label class="quantity-label">Quantity</label>
                    <input type="number" name="qty" value="1" min="1" class="quantity-input">
                </div>
                
                <button type="submit" name="add_to_cart" class="btn-add-cart">
                    Add to Cart
                </button>
            </form>

            <div class="product-features">
                <div class="feature-item">
                    <div class="feature-title">Free Shipping</div>
                    <div class="feature-text">On orders over $100</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Easy Returns</div>
                    <div class="feature-text">30-day return policy</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Authentic</div>
                    <div class="feature-text">100% genuine products</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Secure Payment</div>
                    <div class="feature-text">Safe & encrypted</div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
