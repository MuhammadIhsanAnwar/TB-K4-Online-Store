<div class="sidebar">
    <!-- LOGO -->
    <div class="logo-box">
        <img src="logo.png" alt="Logo">
        <span>URBAN HYPE</span>
    </div>

    <p class="menu-title">Menu Admin</p>

    <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>">Dashboard</a>
    <a href="data_user.php" class="<?= basename($_SERVER['PHP_SELF'])=='data_user.php'?'active':'' ?>">Data User</a>
    <a href="data_produk.php" class="<?= basename($_SERVER['PHP_SELF'])=='data_produk.php'?'active':'' ?>">Data Produk</a>
    <a href="tambah_produk.php" class="<?= basename($_SERVER['PHP_SELF'])=='tambah_produk.php'?'active':'' ?>">Tambah Produk</a>
    <a href="data_penjualan.php" class="<?= basename($_SERVER['PHP_SELF'])=='data_penjualan.php'?'active':'' ?>">Data Penjualan</a>

    <a href="logout.php" class="logout">Logout</a>
</div>
