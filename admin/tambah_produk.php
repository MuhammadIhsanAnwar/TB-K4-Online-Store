<?php
session_start();
require 'auth_check.php';
include "koneksi.php";

$msg = '';
$msg_type = '';

// PROSES TAMBAH PRODUK
if (isset($_POST['submit'])) {

    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $merk = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $foto_produk = '';

    // VALIDASI: Nama produk tidak boleh double
    $check_nama = mysqli_query($koneksi, "SELECT * FROM products WHERE nama='$nama'");
    if (mysqli_num_rows($check_nama) > 0) {
        $msg = "❌ Nama produk sudah terdaftar dalam database!";
        $msg_type = 'danger';
    } else if (empty($kategori) || empty($nama) || empty($merk) || empty($deskripsi) || empty($harga) || empty($stok)) {
        $msg = "❌ Semua field harus diisi!";
        $msg_type = 'danger';
    } else if ($harga <= 0 || $stok < 0) {
        $msg = "❌ Harga harus lebih dari 0 dan stok tidak boleh negatif!";
        $msg_type = 'danger';
    } else {

        // PROSES UPLOAD FOTO
        if (isset($_POST['foto_data']) && !empty($_POST['foto_data'])) {

            $foto_data = $_POST['foto_data'];

            // Decode base64
            if (preg_match('/^data:image\/(\w+);base64,/', $foto_data, $type)) {
                $foto_data = substr($foto_data, strpos($foto_data, ',') + 1);
                $tipo = strtolower($type[1]); // jpg, png, gif, etc.

                if (!in_array($tipo, ['jpeg', 'jpg', 'png', 'gif'])) {
                    $msg = "❌ Format file tidak diperbolehkan!";
                    $msg_type = 'danger';
                } else {
                    $data = base64_decode($foto_data);

                    if ($data === false) {
                        $msg = "❌ Gagal mengubah format file!";
                        $msg_type = 'danger';
                    } else {
                        // Buat nama file
                        $foto_produk = time() . "_produk_" . uniqid() . '.' . ($tipo === 'jpeg' ? 'jpg' : $tipo);

                        // Buat folder jika belum ada
                        if (!is_dir("../foto_produk")) {
                            mkdir("../foto_produk", 0777, true);
                        }

                        // Simpan file
                        if (file_put_contents("../foto_produk/$foto_produk", $data)) {

                            // INSERT ke database
                            $tanggal_publish = date('Y-m-d H:i:s');
                            $insert_sql = "
                                INSERT INTO products (kategori, nama, merk, deskripsi, harga, stok, created_at, foto_produk) 
                                VALUES ('$kategori', '$nama', '$merk', '$deskripsi', '$harga', '$stok', '$tanggal_publish', '$foto_produk')
                            ";

                            if (mysqli_query($koneksi, $insert_sql)) {
                                $msg = "✅ Produk berhasil ditambahkan!";
                                $msg_type = 'success';

                                // Reset form
                                $_POST = [];
                            } else {
                                $msg = "❌ Gagal menyimpan ke database: " . mysqli_error($koneksi);
                                $msg_type = 'danger';
                                // Hapus file jika insert gagal
                                if (file_exists("../foto_produk/$foto_produk")) {
                                    unlink("../foto_produk/$foto_produk");
                                }
                            }
                        } else {
                            $msg = "❌ Gagal menyimpan file foto!";
                            $msg_type = 'danger';
                        }
                    }
                }
            } else {
                $msg = "❌ Format foto tidak valid!";
                $msg_type = 'danger';
            }
        } else {
            $msg = "❌ Foto produk harus diupload!";
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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
      <link rel="stylesheet" href="css_admin/tambah_produk_style.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="container">

            <h1 class="page-title">➕ Tambah Produk Baru</h1>

            <?php if (!empty($msg)): ?>
                <div class="alert alert-<?php echo $msg_type; ?>">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="form-card">
                <form method="POST" id="formTambahProduk">
                    <!-- KATEGORI & NAMA -->
                    <div class="form-section">
                        <h3 class="section-title">📋 Informasi Dasar</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="kategori">Kategori Produk *</label>
                                <select id="kategori" name="kategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Men" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'Men') ? 'selected' : ''; ?>>Men</option>
                                    <option value="Women" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'Women') ? 'selected' : ''; ?>>Women</option>
                                    <option value="Shoes" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'Shoes') ? 'selected' : ''; ?>>Shoes</option>
                                    <option value="Accessories" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'Accessories') ? 'selected' : ''; ?>>Accessories</option>
                                </select>
                                <div class="info-text">Pilih kategori yang sesuai untuk produk</div>
                            </div>

                            <div class="form-group">
                                <label for="nama">Nama Produk *</label>
                                <input type="text" id="nama" name="nama" placeholder="Contoh: T-Shirt Premium Cotton"
                                    value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" required>
                                <div class="info-text">Nama produk harus unik dan tidak boleh sama dengan yang lain</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="merk">Merk Produk *</label>
                                <input type="text" id="merk" name="merk" placeholder="Contoh: Urban Hype, Nike, Adidas"
                                    value="<?php echo isset($_POST['merk']) ? htmlspecialchars($_POST['merk']) : ''; ?>" required>
                                <div class="info-text">Masukkan merk atau brand resmi produk</div>
                            </div>

                            <div class="form-group">
                                <label for="stok">Stok Produk *</label>
                                <input type="number" id="stok" name="stok" placeholder="Contoh: 50"
                                    value="<?php echo isset($_POST['stok']) ? intval($_POST['stok']) : ''; ?>" min="0" required>
                                <div class="info-text">Jumlah stok barang yang tersedia</div>
                            </div>
                        </div>
                    </div>

                    <!-- DESKRIPSI & HARGA -->
                    <div class="form-section">
                        <h3 class="section-title">💬 Detail Produk</h3>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi Produk *</label>
                            <textarea id="deskripsi" name="deskripsi" placeholder="Jelaskan spesifikasi, fitur, dan keunggulan produk..." required><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                            <div class="info-text">Deskripsi yang detail membantu pelanggan memahami produk dengan lebih baik</div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="harga">Harga Produk (Rp) *</label>
                                <div class="price-input-wrapper">
                                    <span class="currency-symbol">Rp</span>
                                    <input type="number" id="harga" name="harga" placeholder="Contoh: 150000"
                                        value="<?php echo isset($_POST['harga']) ? intval($_POST['harga']) : ''; ?>" min="1" required>
                                </div>
                                <div class="info-text">Harga produk dalam rupiah (tanpa titik/koma)</div>
                            </div>
                        </div>
                    </div>

                    <!-- FOTO PRODUK -->
                    <div class="form-section">
                        <h3 class="section-title">📸 Foto Produk</h3>

                        <div class="foto-section" id="fotoSection">
                            <div class="foto-preview" id="fotoPreview">
                                <img id="previewImage" src="" alt="Foto Produk">
                            </div>

                            <div class="foto-input-wrapper">
                                <input type="file" id="fotoInput" accept="image/jpeg,image/png,image/jpg" required>
                                <label for="fotoInput" class="file-input-label">
                                    🖼️ Pilih Foto Produk
                                </label>
                            </div>

                            <div class="foto-info">
                                Format: JPG, JPEG, PNG | Ukuran max: 5MB | Akan dicrop menjadi square (1:1)
                            </div>

                            <!-- Hidden input untuk foto crop data -->
                            <input type="hidden" id="fotoData" name="foto_data">
                        </div>
                    </div>

                    <!-- FORM BUTTONS -->
                    <div class="form-actions">
                        <button type="submit" name="submit" class="btn btn-submit">
                            ✅ Tambah Produk
                        </button>
                        <a href="dashboard.php" class="btn btn-cancel-form" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">
                            ❌ Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CROPPER MODAL -->
    <div class="cropper-modal" id="cropperModal">
        <div class="cropper-container-wrapper">
            <h3 class="cropper-title">🖼️ Crop Foto Produk</h3>
            <div class="cropper-wrapper">
                <img id="cropperImage" src="" alt="Crop Image" style="width: auto; height: auto; max-width: 100%; max-height: 100%;">
            </div>
            <div class="cropper-buttons">
                <button type="button" class="btn btn-crop" id="cropButton">✂️ Simpan Crop</button>
                <button type="button" class="btn btn-cancel" id="cancelCropButton">Batal</button>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        let cropper = null;

        const fotoInput = document.getElementById('fotoInput');
        const cropperModal = document.getElementById('cropperModal');
        const cropperImage = document.getElementById('cropperImage');
        const cropButton = document.getElementById('cropButton');
        const cancelCropButton = document.getElementById('cancelCropButton');
        const fotoSection = document.getElementById('fotoSection');
        const previewImage = document.getElementById('previewImage');
        const fotoPreview = document.getElementById('fotoPreview');
        const fotoData = document.getElementById('fotoData');

        // Handle file input change
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (!file) return;

            // Validasi tipe file
            if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tidak Diizinkan',
                    text: 'Hanya JPG, JPEG, dan PNG yang diperbolehkan!'
                });
                fotoInput.value = '';
                return;
            }

            // Validasi ukuran file (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran File Terlalu Besar',
                    text: 'Ukuran maksimal file adalah 5MB!'
                });
                fotoInput.value = '';
                return;
            }

            // Baca file dan tampilkan di cropper
            const reader = new FileReader();
            reader.onload = function(event) {
                cropperImage.src = event.target.result;
                cropperModal.classList.add('show');

                // Destroy cropper lama jika ada
                if (cropper) {
                    cropper.destroy();
                }

                // Initialize cropper
                cropper = new Cropper(cropperImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    guides: true,
                    grid: true,
                    highlight: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: true,
                });
            };

            reader.readAsDataURL(file);
        });

        // Handle crop button
        cropButton.addEventListener('click', function() {
            const canvas = cropper.getCroppedCanvas({
                maxWidth: 1000,
                maxHeight: 1000,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            // Convert canvas to base64
            const croppedImageData = canvas.toDataURL('image/jpeg', 0.9);

            // Store in hidden input
            fotoData.value = croppedImageData;

            // Display preview
            previewImage.src = croppedImageData;
            fotoPreview.classList.add('show');
            fotoSection.classList.add('active');

            // Close modal
            cropperModal.classList.remove('show');

            Swal.fire({
                icon: 'success',
                title: 'Foto Berhasil Dicrop',
                text: 'Foto produk siap disimpan!',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // Handle cancel crop button
        cancelCropButton.addEventListener('click', function() {
            cropperModal.classList.remove('show');
            fotoInput.value = '';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        // Handle form submit
        document.getElementById('formTambahProduk').addEventListener('submit', function(e) {
            if (!fotoData.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Foto Belum Dicrop',
                    text: 'Silakan pilih dan crop foto produk terlebih dahulu!'
                });
            }
        });
    </script>
</body>

</html>