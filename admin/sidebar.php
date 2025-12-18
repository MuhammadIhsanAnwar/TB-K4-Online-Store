<?php
require_once 'koneksi.php';

$qPesan = mysqli_query($koneksi,
    "SELECT COUNT(*) AS total
     FROM pesan_kontak
     WHERE status='baru'"
);
$notifPesan = mysqli_fetch_assoc($qPesan)['total'];
?>

<style>
.badge-pesan {
    position: absolute;
    top: 8px;
    right: 15px;
    background: #dc3545;
    color: white;
    font-size: 11px;
    padding: 3px 7px;
    border-radius: 50%;
    font-weight: 600;
}
</style>


<div class="sidebar">

    <!-- LOGO -->
    <div class="logo-box">
        <img src="../images/icon/logo.png" alt="Logo">
    </div>

    <p class="menu-title">Menu Admin</p>

    <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>">Dashboard</a>
    <a href="data_user.php" class="<?= basename($_SERVER['PHP_SELF'])=='data_user.php'?'active':'' ?>">Data User</a>
    <a href="data_produk.php" class="<?= basename($_SERVER['PHP_SELF'])=='data_produk.php'?'active':'' ?>">Data Produk</a>
    <a href="tambah_produk.php" class="<?= basename($_SERVER['PHP_SELF'])=='tambah_produk.php'?'active':'' ?>">Tambah Produk</a>
    <a href="data_penjualan.php" class="<?= basename($_SERVER['PHP_SELF'])=='data_penjualan.php'?'active':'' ?>">Data Penjualan</a>
    <a href="data_pesan.php"
   class="<?= basename($_SERVER['PHP_SELF'])=='data_pesan.php'?'active':'' ?>"
   style="position:relative;">

    Data Pesan

    <?php if($notifPesan > 0): ?>
        <span class="badge-pesan"><?= $notifPesan ?></span>
    <?php endif; ?>
</a>


    <a href="logout.php" class="logout">Logout</a>

</div>
