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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css_user/css_produk_pembayaran/shop.css">
    <link rel="stylesheet" href="../css_user/navbar.css">
     <script src="../../js/bootstrap.bundle.js"></script> 
</head>

<body>
    <?php include "../../navbar.php"; ?>
    <section class="hero-shop">
        <div class="container-shop">
            <h1>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.15em; margin-right: 8px;">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                Shop UrbanHype
            </h1>
            <p>Temukan gaya fashion terbaik untuk Anda</p>
        </div>
    </section>

    <main class="container-shop">
        <div class="category-tabs">
            <a href="shop.php?kategori=All" class="category-btn <?php echo $kategori === 'All' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.15em; margin-right: 4px;">
                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                </svg>
                All
            </a>
            <a href="shop.php?kategori=Men" class="category-btn <?php echo $kategori === 'Men' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.15em; margin-right: 4px;">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.336C3.154 12.014 3 12.754 3 13h10c0-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c2.21 0 3.542.588 4.168 1.336z"/>
                </svg>
                Men
            </a>
            <a href="shop.php?kategori=Women" class="category-btn <?php echo $kategori === 'Women' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.15em; margin-right: 4px;">
                    <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-4 3a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm10 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm-8 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
                Women
            </a>
            <a href="shop.php?kategori=Shoes" class="category-btn <?php echo $kategori === 'Shoes' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.15em; margin-right: 4px;">
                    <path d="m11.537 6.502-.003-.052-.117-.274a.5.5 0 0 0-.24-.232L8.5 4.568l-.11-.055a.5.5 0 0 0-.46 0l-.11.055-2.677 1.376a.5.5 0 0 0-.24.232l-.117.274-.003.052V12c0 .338.07.587.15.782.11.267.325.52.657.721.332.2.778.333 1.32.396l.7.07v1.5h2.3l.7-.07c.542-.063.988-.195 1.32-.396.332-.201.547-.454.657-.721.08-.195.15-.444.15-.782V6.502z"/>
                </svg>
                Shoes
            </a>
            <a href="shop.php?kategori=Accessories" class="category-btn <?php echo $kategori === 'Accessories' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.15em; margin-right: 4px;">
                    <path d="M4.5 5a.5.5 0 0 0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-.5-.5z"/>
                </svg>
                Accessories
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
                <div class="empty-state-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                    </svg>
                </div>
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