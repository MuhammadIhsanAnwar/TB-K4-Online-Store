<?php
require 'auth_check.php';
include "../admin/koneksi.php";

// Ambil ID produk dari URL
if (!isset($_GET['id'])) {
    header("Location: data_produk.php");
    exit;
}

$id = $_GET['id'];

// Ambil data produk
$result = mysqli_query($koneksi, "SELECT * FROM products WHERE id='$id'");
if (mysqli_num_rows($result) == 0) {
    die("Produk tidak ditemukan.");
}
$product = mysqli_fetch_assoc($result);

$success = false;
$error_msg = "";

// Proses update produk
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga = floatval($_POST['harga']);

    $update_gambar_sql = "";

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $gambar_name_raw = $_FILES['gambar']['name'];
        $gambar_ext = strtolower(pathinfo($gambar_name_raw, PATHINFO_EXTENSION));
        $gambar_size = $_FILES['gambar']['size'];
        $gambar_tmp = $_FILES['gambar']['tmp_name'];

        if (!in_array($gambar_ext, $allowed_ext)) {
            $error_msg = "Format file tidak diperbolehkan. Hanya jpg, jpeg, png.";
        } elseif ($gambar_size > 2 * 1024 * 1024) {
            $error_msg = "Ukuran file maksimal 2MB.";
        } else {
            $gambar_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $gambar_name_raw);

            if (!is_dir("../foto_produk")) {
                mkdir("../foto_produk", 0777, true);
            }

            if (move_uploaded_file($gambar_tmp, "../foto_produk/$gambar_name")) {
                if (!empty($product['gambar']) && file_exists("../foto_produk/" . $product['gambar'])) {
                    unlink("../foto_produk/" . $product['gambar']);
                }
                $update_gambar_sql = ", gambar='$gambar_name'";
            } else {
                $error_msg = "Gagal mengupload gambar.";
            }
        }
    }

    if (empty($error_msg)) {
        $update_sql = "UPDATE products SET nama='$nama', kategori='$kategori', harga='$harga' $update_gambar_sql WHERE id='$id'";
        if (mysqli_query($koneksi, $update_sql)) {
            $success = true;
            $result = mysqli_query($koneksi, "SELECT * FROM products WHERE id='$id'");
            $product = mysqli_fetch_assoc($result);
        } else {
            $error_msg = "Gagal update database: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
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
            /* agar navbar tidak menutupi sidebar */
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
        <h2 class="fw-bold mb-4">Edit Produk</h2>

        <?php if ($success): ?>
            <div class="alert alert-success">Produk berhasil diperbarui!</div>
        <?php elseif (!empty($error_msg)): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Nama Produk</label>
                <input type="text" name="nama" class="form-control" value="<?php echo $product['nama']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Kategori</label>
                <input type="text" name="kategori" class="form-control" value="<?php echo $product['kategori']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Harga</label>
                <input type="number" step="0.01" name="harga" class="form-control" value="<?php echo $product['harga']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Gambar</label><br>
                <?php if (!empty($product['gambar'])): ?>
                    <img src="../foto_produk/<?php echo $product['gambar']; ?>" alt="Gambar" width="100" class="mb-2"><br>
                <?php endif; ?>
                <input type="file" name="gambar" class="form-control">
                <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar (jpg/jpeg/png, max 2MB)</small>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
            <a href="data_produk.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>