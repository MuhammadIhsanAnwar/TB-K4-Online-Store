<?php
require 'auth_check.php';
include "../admin/koneksi.php";

// Ambil semua produk
$query = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Produk - Admin</title>
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
            /* beri ruang sidebar */
            padding: 20px;
        }

        nav.navbar {
            margin-left: 220px;
            /* beri ruang navbar agar tidak menutupi sidebar */
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">Admin Panel</span>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="content">
        <h2 class="fw-bold">Data Produk</h2>
        <hr>

        <a href="tambah_produk.php" class="btn btn-primary mb-3">Tambah Produk</a>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['kategori']; ?></td>
                        <td>$<?php echo number_format($row['harga'], 2); ?></td>
                        <td>
                            <?php if (!empty($row['gambar'])): ?>
                                <img src="../foto_produk/<?php echo $row['gambar']; ?>" alt="Gambar" width="50">
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_produk.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="hapus_produk.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>