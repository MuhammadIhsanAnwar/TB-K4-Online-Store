<?php require 'auth_check.php'; ?>
<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css_admin/dashboard_style.css?v=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="icon" type="image/png" href="../images/icon/logo.png">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>📊 Dashboard</h2>
    <p class="subtitle">Ringkasan Data Bisnis Anda</p>

    <div class="cards-wrapper">

        <!-- Total Produk -->
        <div class="card primary">
            <div class="card-content">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h5>📦 Total Produk</h5>
                </div>
                <p class="card-value">
                    <?php
                    $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM products");
                    echo mysqli_fetch_assoc($res)['total'];
                    ?>
                </p>
                <div class="card-footer">Produk aktif di toko</div>
            </div>
        </div>

        <!-- Total User -->
        <div class="card success">
            <div class="card-content">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h5>👥 Total User</h5>
                </div>
                <p class="card-value">
                    <?php
                    $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM akun_user");
                    echo mysqli_fetch_assoc($res)['total'];
                    ?>
                </p>
                <div class="card-footer">Pengguna terdaftar</div>
            </div>
        </div>

        <!-- Pesanan -->
        <div class="card info">
            <div class="card-content">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h5>📬 Pesanan Masuk</h5>
                </div>
                <p class="card-value">
                    <?php
                    $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pemesanan");
                    echo mysqli_fetch_assoc($res)['total'];
                    ?>
                </p>
                <div class="card-footer">Pesanan belum diproses</div>
            </div>
        </div>

        <!-- Penjualan -->
        <div class="card warning">
            <div class="card-content">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <h5>💰 Total Penjualan</h5>
                </div>
                <p class="card-value">
                    <?php
                    $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM history_penjualan");
                    echo mysqli_fetch_assoc($res)['total'];
                    ?>
                </p>
                <div class="card-footer">Transaksi selesai</div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
