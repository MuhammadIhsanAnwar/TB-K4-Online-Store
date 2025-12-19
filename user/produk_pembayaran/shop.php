<?php
session_start(); 
include "../../admin/koneksi.php";

$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($result);
}

$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : 'All';
$valid_categories = ['Men', 'Women', 'Shoes', 'Accessories', 'All'];

if (!in_array($kategori, $valid_categories)) {
    $kategori = 'All';
}

if ($kategori === 'All') {
    $query = "SELECT * FROM products ORDER BY created_at DESC";
} else {
    $query = "SELECT * FROM products WHERE kategori='$kategori' ORDER BY created_at DESC";
}
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
    <link rel="icon" type="image/png" href="../../images/icon/logo.png">
    <title>Shop - UrbanHype</title>

    <link rel="stylesheet" href="../../css/bootstrap.css">
    <link rel="stylesheet" href="../../icons/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css_user/css_produk_pembayaran/shop.css">
    <link rel="stylesheet" href="../css_user/navbar.css">
</head>

<body>
    <?php include "../../navbar.php"; ?>
    <section class="hero-shop">
        <div class="container-shop">
            <h1>🛍️ Shop UrbanHype</h1>
            <p>Temukan gaya fashion terbaik untuk Anda</p>
        </div>
    </section>

    <main class="container-shop">
        <div class="category-tabs">
            <a href="shop.php?kategori=All" class="category-btn <?php echo $kategori === 'All' ? 'active' : ''; ?>">
                ✨ All
            </a>
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
        <?php if (count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" onclick="openProductDetail(<?php echo $product['id']; ?>)">
                        <img src="../../foto_produk/<?php echo htmlspecialchars($product['foto_produk']); ?>"
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

    <footer class="footer">
        <div class="container-shop">
            <p>&copy; 2025 UrbanHype. Kelompok 4 Tugas Besar Pemrograman Web. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="../../js/bootstrap.bundle.js"></script>

    <script>
        function openProductDetail(productId) {
            window.location.href = 'product_detail.php?id=' + productId;
        }
    </script>
</body>

</html>