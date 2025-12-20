<?php
session_start(); // ← Tambahkan ini
// Sesuaikan path koneksi sesuai lokasi navbar.php
include "../../admin/koneksi.php";

$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($result);
}

$current_path = $_SERVER['REQUEST_URI'];
$is_subdir = (strpos($current_path, '/user/produk_pembayaran/') !== false);
$base_path = $is_subdir ? '../../' : './';
?>

<link rel="stylesheet" href="<?php echo $base_path; ?>user/css_user/navbar.css">

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand-wrapper" href="<?php echo $base_path; ?>index.php">
            <img src="<?php echo $base_path; ?>images/icon/logo.png" alt="Logo" class="navbar-logo">
            <span class="navbar-brand">URBANHYPE</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto me-lg-4">
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>user/produk_pembayaran/shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>index.php#about">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>index.php#contact">Contact Us</a></li>
            </ul>

            <div class="navbar-actions">
                

                <?php if ($user): ?>
                    <a href="<?php echo $base_path; ?>user/produk_pembayaran/cart.php" class="cart-link">
                        <img src="<?php echo $base_path; ?>images/icon/cart-icon.png" alt="Cart" style="width:24px; height:24px; filter: var(--cart-icon-filter);">
                        <?php
                        $cart_count_query = "SELECT COUNT(*) as total FROM keranjang WHERE user_id='{$user['id']}'";
                        $cart_count_result = mysqli_query($koneksi, $cart_count_query);
                        $cart_count_row = mysqli_fetch_assoc($cart_count_result);
                        $cart_count = $cart_count_row['total'] ?? 0;
                        if ($cart_count > 0):
                        ?>
                            <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown user-dropdown">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo $base_path; ?>foto_profil/<?php echo htmlspecialchars($user['foto_profil']); ?>"
                                alt="<?php echo htmlspecialchars($user['nama_lengkap']); ?>"
                                class="rounded-circle"
                                style="width:40px; height:40px; object-fit:cover;">
                            <span class="user-name d-none d-lg-inline"><?php echo htmlspecialchars($user['nama_lengkap']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>user/settings/settings.php"><i class="bi bi-gear me-2"></i>Setting Akun</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>user/produk_pembayaran/pesanan_saya.php"><i class="bi bi-bag me-2"></i>Pesanan Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>user/logout_user.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $base_path; ?>user/login_user.php" class="btn btn-dark">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Pakai $base_path untuk JS juga -->
<script src="<?php echo $base_path; ?>js/bootstrap.bundle.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                navbar.classList.toggle('scrolled', window.scrollY > 50);
            }
        });
    });
</script>