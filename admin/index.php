<?php require 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <style>
        body {
            min-height: 100vh;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            background-color: #343a40;
            padding-top: 70px;
        }
        .sidebar a {
            display: block;
            padding: 10px 15px;
            color: #fff;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
            border-radius: 5px;
        }
        .content {
            margin-left: 230px;
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h5 class="text-white ps-3">Menu Admin</h5>
    <a href="dashboard.php">Dashboard</a>
    <a href="data_user.php">Data User</a>
    <a href="tambah_produk.php">Tambah Produk</a>
    <a href="hapus_produk.php">Hapus Produk</a>
    <a href="data_penjualan.php">Data Penjualan</a>
    <a href="logout.php" class="mt-3 text-danger">Logout</a>
</div>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark fixed-top" style="margin-left:220px;">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">Admin Panel</span>
    </div>
</nav>

<!-- Content -->
<div class="content">
    <h2 class="fw-bold">Dashboard</h2>
    <hr>

    <div class="row g-3">

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h5>Total Produk</h5>
                <p class="fs-3 fw-bold text-primary">
                    <?php 
                    $result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM products");
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'];
                    ?>
                </p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h5>Total User</h5>
                <p class="fs-3 fw-bold text-success">
                    <?php 
                    $result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM akun_user");
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'];
                    ?>
                </p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h5>Total Penjualan</h5>
                <p class="fs-3 fw-bold text-warning">
                    <?php 
                    $result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penjualan");
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'];
                    ?>
                </p>
            </div>
        </div>

    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
