<div class="collapse navbar-collapse" id="navMenu">
    <ul class="navbar-nav ms-auto gap-3">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Shop</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Pages</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Contact Us</a></li>
    </ul>

    <ul class="navbar-nav ms-3 d-flex align-items-center gap-3">
        <?php if ($user): ?>
            <!-- Ikon Keranjang -->
            <li class="nav-item position-relative">
                <a href="user/produk_pembayaran/cart.php" class="nav-link position-relative">
                    <i class="bi bi-bag-fill fs-5 text-primary"></i>
                    <?php
                    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                    if ($cart_count > 0):
                    ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $cart_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>

            <!-- Dropdown User -->
            <li class="nav-item dropdown">
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