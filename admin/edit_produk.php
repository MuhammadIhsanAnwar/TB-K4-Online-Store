<?php
session_start();
require 'auth_check.php';
include "koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: data_produk.php");
    exit;
}

$id = intval($_GET['id']);

$result = mysqli_query($koneksi, "SELECT * FROM products WHERE id='$id'");
if (!$result || mysqli_num_rows($result) == 0) {
    die("Produk tidak ditemukan.");
}
$product = mysqli_fetch_assoc($result);

$success = false;
$error_msg = "";

if (isset($_POST['update'])) {

    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $merk = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);

    $check_nama = mysqli_query($koneksi, "SELECT * FROM products WHERE nama='$nama' AND id!='$id'");
    if (mysqli_num_rows($check_nama) > 0) {
        $error_msg = "Nama produk sudah terdaftar di produk lain!";
    } else if (empty($kategori) || empty($nama) || empty($merk) || empty($deskripsi) || empty($harga)) {
        $error_msg = "Semua field harus diisi!";
    } else if ($harga <= 0 || $stok < 0) {
        $error_msg = "Harga harus lebih dari 0 dan stok tidak boleh negatif!";
    } else {

        $update_foto_sql = "";

        if (isset($_POST['foto_data']) && !empty($_POST['foto_data'])) {

            $foto_data = $_POST['foto_data'];

            if (preg_match('/^data:image\/(\w+);base64,/', $foto_data, $type)) {
                $foto_data = substr($foto_data, strpos($foto_data, ',') + 1);
                $tipo = strtolower($type[1]);

                if (!in_array($tipo, ['jpeg', 'jpg', 'png', 'gif'])) {
                    $error_msg = "Format file tidak diperbolehkan!";
                } else {
                    $data = base64_decode($foto_data);

                    if ($data === false) {
                        $error_msg = "Gagal mengubah format file!";
                    } else {
                        $foto_produk = time() . "_produk_" . uniqid() . '.' . ($tipo === 'jpeg' ? 'jpg' : $tipo);

                        if (!is_dir("../foto_produk")) {
                            mkdir("../foto_produk", 0777, true);
                        }

                        if (file_put_contents("../foto_produk/$foto_produk", $data)) {
                            if (!empty($product['foto_produk']) && file_exists("../foto_produk/" . $product['foto_produk'])) {
                                unlink("../foto_produk/" . $product['foto_produk']);
                            }
                            $update_foto_sql = ", foto_produk='$foto_produk'";
                        } else {
                            $error_msg = "Gagal menyimpan file foto!";
                        }
                    }
                }
            } else {
                $error_msg = "Format foto tidak valid!";
            }
        }

        if (empty($error_msg)) {
            $update_sql = "
                UPDATE products SET 
                    kategori='$kategori',
                    nama='$nama',
                    merk='$merk',
                    deskripsi='$deskripsi',
                    harga='$harga',
                    stok='$stok'
                    $update_foto_sql
                WHERE id='$id'
            ";

            if (mysqli_query($koneksi, $update_sql)) {
                $success = true;
                $result = mysqli_query($koneksi, "SELECT * FROM products WHERE id='$id'");
                $product = mysqli_fetch_assoc($result);
            } else {
                $error_msg = "Gagal update database: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link rel="stylesheet" href="css_admin/edit_produk_style.css">
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="container">
        <nav class="breadcrumb">
            <a class="breadcrumb-item" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a class="breadcrumb-item" href="data_produk.php"><i class="bi bi-box-seam"></i> Data Produk</a>
            <span class="breadcrumb-item active"><i class="bi bi-pencil-square"></i> Edit Produk</span>
        </nav>

        <h1 class="page-title"><i class="bi bi-pencil-fill"></i> Edit Produk</h1>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> Produk berhasil diperbarui!
            </div>
        <?php elseif (!empty($error_msg)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-x-circle-fill"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" id="formEditProduk">

                <div class="form-section">
                    <h3 class="section-title"><i class="bi bi-info-circle"></i> Informasi Dasar</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Kategori Produk *</label>
                            <select name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Men" <?= $product['kategori']=='Men'?'selected':'' ?>>Men</option>
                                <option value="Women" <?= $product['kategori']=='Women'?'selected':'' ?>>Women</option>
                                <option value="Shoes" <?= $product['kategori']=='Shoes'?'selected':'' ?>>Shoes</option>
                                <option value="Accessories" <?= $product['kategori']=='Accessories'?'selected':'' ?>>Accessories</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Nama Produk *</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($product['nama']) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Merk *</label>
                            <input type="text" name="merk" value="<?= htmlspecialchars($product['merk']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Stok *</label>
                            <input type="number" name="stok" value="<?= $product['stok'] ?>" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title"><i class="bi bi-card-text"></i> Detail Produk</h3>

                    <div class="form-group">
                        <label>Deskripsi *</label>
                        <textarea name="deskripsi" required><?= htmlspecialchars($product['deskripsi']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Harga (Rp) *</label>
                        <input type="number" name="harga" value="<?= $product['harga'] ?>" min="1" required>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title"><i class="bi bi-camera"></i> Foto Produk</h3>

                    <div class="foto-preview show">
                        <img src="../foto_produk/<?= htmlspecialchars($product['foto_produk']) ?>">
                    </div>

                    <input type="file" id="fotoInput" accept="image/jpeg,image/png,image/jpg">
                    <input type="hidden" id="fotoData" name="foto_data">
                </div>

                <div class="form-actions">
                    <button type="submit" name="update" class="btn btn-submit">
                        <i class="bi bi-save-fill"></i> Update Produk
                    </button>
                    <a href="data_produk.php" class="btn btn-cancel-form">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</body>
</html>
