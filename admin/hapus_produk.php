<?php require 'auth_check.php'; ?>
<?php include '../admin/koneksi.php'; ?>
<?php
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $res = mysqli_query($koneksi, "SELECT gambar FROM products WHERE id='$id'");
    $row = mysqli_fetch_assoc($res);
    if (file_exists("../foto_produk/" . $row['gambar'])) unlink("../foto_produk/" . $row['gambar']);
    mysqli_query($koneksi, "DELETE FROM products WHERE id='$id'");
    header("Location: hapus_produk.php");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hapus Produk</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">

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
    <?php include 'sidebar.php'; ?>

    <nav class="navbar navbar-dark bg-dark fixed-top" style="margin-left:220px;">
        <div class="container-fluid"><span class="navbar-brand fw-bold">Admin Panel</span></div>
    </nav>
    <div class="content">
        <h2 class="fw-bold">Hapus Produk</h2>
        <hr>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>
    <td>{$row['id']}</td>
    <td>{$row['nama']}</td>
    <td>{$row['kategori']}</td>
    <td>{$row['harga']}</td>
    <td><img src='../upload/{$row['gambar']}' width='50'></td>
    <td><a href='hapus_produk.php?hapus={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus produk ini?\")'>Hapus</a></td>
    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>