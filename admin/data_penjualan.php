<?php require 'auth_check.php'; ?>
<?php include '../admin/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Penjualan</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
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
    <div class="sidebar">
        <h5 class="text-white ps-3">Menu Admin</h5>
        <a href="dashboard.php">Dashboard</a>
        <a href="data_user.php">Data User</a>
        <a href="tambah_produk.php">Tambah Produk</a>
        <a href="hapus_produk.php">Hapus Produk</a>
        <a href="data_penjualan.php">Data Penjualan</a>
        <a href="logout.php" class="mt-3 text-danger">Logout</a>
    </div>
    <nav class="navbar navbar-dark bg-dark fixed-top" style="margin-left:220px;">
        <div class="container-fluid"><span class="navbar-brand fw-bold">Admin Panel</span></div>
    </nav>
    <div class="content">
        <h2 class="fw-bold">Data Penjualan</h2>
        <hr>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID User</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($koneksi, "SELECT * FROM penjualan ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>
    <td>{$row['id']}</td>
    <td>{$row['user_id']}</td>
    <td>{$row['produk']}</td>
    <td>{$row['jumlah']}</td>
    <td>{$row['total_harga']}</td>
    <td>{$row['tanggal']}</td>
    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>