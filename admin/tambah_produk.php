<?php
session_start();
require 'auth_check.php';
include "koneksi.php";

$msg = '';
$msg_type = '';

if (isset($_POST['submit'])) {

    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $merk = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $foto_produk = '';

    $check_nama = mysqli_query($koneksi, "SELECT * FROM products WHERE nama='$nama'");
    if (mysqli_num_rows($check_nama) > 0) {
        $msg = "Nama produk sudah terdaftar dalam database!";
        $msg_type = 'danger';
    } else if (empty($kategori) || empty($nama) || empty($merk) || empty($deskripsi) || empty($harga) || empty($stok)) {
        $msg = "Semua field harus diisi!";
        $msg_type = 'danger';
    } else if ($harga <= 0 || $stok < 0) {
        $msg = "Harga harus lebih dari 0 dan stok tidak boleh negatif!";
        $msg_type = 'danger';
    } else {

        if (isset($_POST['foto_data']) && !empty($_POST['foto_data'])) {

            $foto_data = $_POST['foto_data'];

            if (preg_match('/^data:image\/(\w+);base64,/', $foto_data, $type)) {
                $foto_data = substr($foto_data, strpos($foto_data, ',') + 1);
                $tipo = strtolower($type[1]);

                if (!in_array($tipo, ['jpeg', 'jpg', 'png', 'gif'])) {
                    $msg = "Format file tidak diperbolehkan!";
                    $msg_type = 'danger';
                } else {
                    $data = base64_decode($foto_data);

                    if ($data === false) {
                        $msg = "Gagal mengubah format file!";
                        $msg_type = 'danger';
                    } else {
                        $foto_produk = time() . "_produk_" . uniqid() . '.' . ($tipo === 'jpeg' ? 'jpg' : $tipo);

                        if (!is_dir("../foto_produk")) {
                            mkdir("../foto_produk", 0777, true);
                        }

                        if (file_put_contents("../foto_produk/$foto_produk", $data)) {

                            $tanggal_publish = date('Y-m-d H:i:s');
                            $insert_sql = "
                                INSERT INTO products (kategori, nama, merk, deskripsi, harga, stok, created_at, foto_produk) 
                                VALUES ('$kategori', '$nama', '$merk', '$deskripsi', '$harga', '$stok', '$tanggal_publish', '$foto_produk')
                            ";

                            if (mysqli_query($koneksi, $insert_sql)) {
                                $msg = "Produk berhasil ditambahkan!";
                                $msg_type = 'success';
                                $_POST = [];
                            } else {
                                $msg = "Gagal menyimpan ke database!";
                                $msg_type = 'danger';
                                if (file_exists("../foto_produk/$foto_produk")) {
                                    unlink("../foto_produk/$foto_produk");
                                }
                            }
                        } else {
                            $msg = "Gagal menyimpan file foto!";
                            $msg_type = 'danger';
                        }
                    }
                }
            } else {
                $msg = "Format foto tidak valid!";
                $msg_type = 'danger';
            }
        } else {
            $msg = "Foto produk harus diupload!";
            $msg_type = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link rel="stylesheet" href="css_admin/tambah_produk_style.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
<div class="container">

    <h1 class="page-title">
        <i class="bi bi-plus-circle"></i> Tambah Produk Baru
    </h1>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-<?php echo $msg_type; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
    <form method="POST" id="formTambahProduk">

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-card-text"></i> Informasi Dasar
            </h3>

            <div class="form-row">
                <div class="form-group">
                    <label>Kategori Produk *</label>
                    <select name="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Men">Men</option>
                        <option value="Women">Women</option>
                        <option value="Shoes">Shoes</option>
                        <option value="Accessories">Accessories</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Produk *</label>
                    <input type="text" name="nama" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Merk Produk *</label>
                    <input type="text" name="merk" required>
                </div>

                <div class="form-group">
                    <label>Stok Produk *</label>
                    <input type="number" name="stok" min="0" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-chat-left-text"></i> Detail Produk
            </h3>

            <div class="form-group">
                <label>Deskripsi Produk *</label>
                <textarea name="deskripsi" required></textarea>
            </div>

            <div class="form-group">
                <label>Harga Produk *</label>
                <div class="price-input-wrapper">
                    <span class="currency-symbol">Rp</span>
                    <input type="number" name="harga" min="1" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-camera"></i> Foto Produk
            </h3>

            <div class="foto-input-wrapper">
                <input type="file" id="fotoInput" accept="image/jpeg,image/png,image/jpg">
                <label for="fotoInput" class="file-input-label">
                    <i class="bi bi-image"></i> Pilih Foto Produk
                </label>
            </div>

            <input type="hidden" id="fotoData" name="foto_data">
        </div>

        <div class="form-actions">
            <button type="submit" name="submit" class="btn btn-submit">
                <i class="bi bi-check-circle"></i> Tambah Produk
            </button>
            <a href="dashboard.php" class="btn btn-cancel-form">
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