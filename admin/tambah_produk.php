<?php require 'auth_check.php'; ?>
<?php include '../admin/koneksi.php'; ?>
<?php
$msg = '';
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
        $file = $_FILES['gambar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($ext, $allowed)) {
            $filename = time() . '_' . $file['name'];
            move_uploaded_file($file['tmp_name'], "../upload/$filename");
            $sql = "INSERT INTO products (nama,kategori,harga,gambar) VALUES ('$nama','$kategori','$harga','$filename')";
            mysqli_query($koneksi, $sql);
            $msg = "Produk berhasil ditambahkan!";
        } else {
            $msg = "Format file tidak diperbolehkan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
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
    <?php include 'sidebar.php'; ?>

    <nav class="navbar navbar-dark bg-dark fixed-top" style="margin-left:220px;">
        <div class="container-fluid"><span class="navbar-brand fw-bold">Admin Panel</span></div>
    </nav>
    <div class="content">
        <h2 class="fw-bold">Tambah Produk</h2>
        <hr>
        <?php if ($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3"><label>Nama Produk</label><input type="text" name="nama" class="form-control" required></div>
            <div class="mb-3"><label>Kategori</label><input type="text" name="kategori" class="form-control" required></div>
            <div class="mb-3"><label>Harga</label><input type="number" name="harga" class="form-control" required></div>
            <div class="mb-3"><label>Gambar</label><input type="file" name="gambar" class="form-control" required></div>
            <button type="submit" name="submit" class="btn btn-primary">Tambah Produk</button>
        </form>
    </div>
    <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>