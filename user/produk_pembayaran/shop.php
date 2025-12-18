<?php
include "admin/koneksi.php";
include "navbar.php";
session_start();

// Ambil data user jika sudah login
$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($result);
}

// Ambil kategori dari URL (default: Men)
$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : 'Men';
$valid_categories = ['Men', 'Women', 'Shoes', 'Accessories'];

if (!in_array($kategori, $valid_categories)) {
    $kategori = 'Men';
}

// Ambil data produk berdasarkan kategori
$query = "SELECT * FROM products WHERE kategori='$kategori' ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/Background dan Logo/logo.png">
    <title>Shop - UrbanHype</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="icons/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

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
            background: linear-gradient(135deg, var(--light) 0%, #f5f5f5 100%);
            color: var(--dark);
        }

        /* HERO SECTION */
        .hero-shop {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 4rem 0;
            text-align: center;
            margin-bottom: 3rem;
            margin-top: 80px;
        }

        .hero-shop h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            animation: slideDown 0.6s ease-out;
        }

        .hero-shop p {
            font-size: 1.1rem;
            opacity: 0.9;
            animation: slideDown 0.6s ease-out 0.2s backwards;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* CATEGORY TABS */
        .category-tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
            animation: slideUp 0.6s ease-out 0.3s backwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .category-btn {
            padding: 12px 30px;
            border: 2px solid var(--primary);
            background: white;
            color: var(--primary);
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .category-btn:hover,
        .category-btn.active {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(30, 93, 172, 0.3);
        }

        /* CONTAINER */
        .container-shop {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* PRODUCTS GRID */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
            animation: fadeIn 0.6s ease-out 0.4s backwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* PRODUCT CARD */
        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.08);
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(30, 93, 172, 0.15);
        }

        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: linear-gradient(135deg, var(--light), var(--secondary));
            transition: var(--transition);
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-info {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-kategori {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            width: fit-content;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.5rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .product-merk {
            color: var(--secondary);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .product-description {
            color: #6b7280;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            flex-grow: 1;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--light);
        }

        .product-harga {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
        }

        .product-stok {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: 600;
        }

        .stok-ready {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .stok-low {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .stok-out {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--secondary);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .empty-state-text {
            font-size: 1.2rem;
            color: var(--dark);
        }

        /* FOOTER */
        .footer {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2rem 0;
            text-align: center;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .hero-shop {
                margin-top: 70px;
            }

            .hero-shop h1 {
                font-size: 2rem;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1rem;
            }

            .category-tabs {
                gap: 0.5rem;
            }

            .category-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <!-- HERO SECTION -->
    <section class="hero-shop">
        <div class="container-shop">
            <h1>🛍️ Shop UrbanHype</h1>
            <p>Temukan gaya fashion terbaik untuk Anda</p>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <main class="container-shop">
        <!-- CATEGORY TABS -->
        <div class="category-tabs">
            <a href="shop.php?kategori=Men" class="category-btn <?php echo $kategori === 'Men' ? 'active' : ''; ?>">
                👕 Men
            </a>
            <a href="shop.php?kategori=Women" class="category-btn <?php echo $kategori === 'Women' ? 'active' : ''; ?>">
                👗 Women
            </a>
            <a href="shop.php?kategori=Shoes" class="category-btn <?php echo $kategori === 'Shoes' ? 'active' : ''; ?>">
                👟 Shoes
            </a>
            <a href="shop.php?kategori=Accessories" class="category-btn <?php echo $kategori === 'Accessories' ? 'active' : ''; ?>">
                ✨ Accessories
            </a>
        </div>

        <!-- PRODUCTS GRID -->
        <?php if (count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" onclick="openProductDetail(<?php echo $product['id']; ?>)">
                        <img src="foto_produk/<?php echo htmlspecialchars($product['foto_produk']); ?>"
                            alt="<?php echo htmlspecialchars($product['nama']); ?>" class="product-image">
                        <div class="product-info">
                            <span class="product-kategori"><?php echo htmlspecialchars($product['kategori']); ?></span>
                            <h3 class="product-name"><?php echo htmlspecialchars($product['nama']); ?></h3>
                            <p class="product-merk"><?php echo htmlspecialchars($product['merk']); ?></p>
                            <p class="product-description"><?php echo htmlspecialchars($product['deskripsi']); ?></p>
                            <div class="product-footer">
                                <div class="product-harga">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></div>
                                <div class="product-stok <?php echo $product['stok'] > 10 ? 'stok-ready' : ($product['stok'] > 0 ? 'stok-low' : 'stok-out'); ?>">
                                    <?php echo $product['stok'] > 0 ? $product['stok'] . ' Stok' : 'Habis'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <p class="empty-state-text">Tidak ada produk di kategori ini</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container-shop">
            <p>&copy; 2025 UrbanHype. All rights reserved. | Designed with ❤️</p>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="js/bootstrap.bundle.js"></script>

    <script>
        function openProductDetail(productId) {
            window.location.href = 'product_detail.php?id=' + productId;
        }
    </script>
</body>

</html>