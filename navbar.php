<style>
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
        --cart-icon-filter: brightness(0) saturate(100%) invert(28%) sepia(33%) saturate(2300%) hue-rotate(177deg);
        /* biru #1E5DAC */
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
        .navbar-brand {
            font-size: 1.5rem;
        }

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

        .navbar-nav.ms-auto {
            margin-left: 0 !important;
        }

        .navbar-actions {
            justify-content: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(30, 93, 172, 0.1);
            gap: 1.5rem;
        }

        .user-dropdown .dropdown-toggle {
            justify-content: center;
        }

        .user-name {
            display: inline !important;
        }
    }

    @media (max-width: 576px) {
        .navbar-brand {
            font-size: 1.3rem;
            letter-spacing: 1px;
        }

        .user-name {
            max-width: 100px;
        }

        .nav-link {
            font-size: 0.8rem;
        }

        .navbar-actions {
            gap: 1rem;
        }
    }
</style>

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
                <li class="nav-item"><a class="nav-link" href="user/produk_pembayaran/shop.php">Shop</a></li>
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