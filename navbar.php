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
                    <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                </ul>

                <div class="navbar-actions">
                    <div class="nav-icon">
                        <i class="bi bi-search fs-5"></i>
                    </div>

                    <?php if ($user): ?>
                        <a href="user/produk_pembayaran/cart.php" class="cart-link">
                            <img src="images/cart-icon.png" alt="Cart" style="width:24px; height:24px; filter: var(--cart-icon-filter);">
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
                                <li><a class="dropdown-item" href="user/pesanan_saya.php"><i class="bi bi-bag me-2"></i>Pesanan Saya</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
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