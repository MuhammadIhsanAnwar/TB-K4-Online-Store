<?php 
require 'auth_check.php';
require 'koneksi.php';

$produk = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
</head>

<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">Kelola Produk</span>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <h3 class="fw-bold mb-3">Data Produk</h3>

    <a href="#" class="btn btn-primary mb-3">Tambah Produk</a>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th width="60">ID</th>
                <th>Nama</th>
                <th>Harga</th>
                <th width="130">Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = mysqli_fetch_assoc($produk)) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
