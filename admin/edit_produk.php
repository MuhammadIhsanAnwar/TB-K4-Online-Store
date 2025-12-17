<?php
session_start();
require 'auth_check.php';
include "koneksi.php";

// Ambil ID produk dari URL
if (!isset($_GET['id'])) {
    header("Location: data_produk.php");
    exit;
}

$id = intval($_GET['id']);

// Ambil data produk dari database
$result = mysqli_query($koneksi, "SELECT * FROM products WHERE id='$id'");
if (!$result || mysqli_num_rows($result) == 0) {
    die("Produk tidak ditemukan.");
}
$product = mysqli_fetch_assoc($result);

$success = false;
$error_msg = "";

// PROSES UPDATE PRODUK
if (isset($_POST['update'])) {
    
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $merk = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);

    // VALIDASI: Nama produk tidak boleh double (kecuali nama yang sekarang)
    $check_nama = mysqli_query($koneksi, "SELECT * FROM products WHERE nama='$nama' AND id!='$id'");
    if (mysqli_num_rows($check_nama) > 0) {
        $error_msg = "❌ Nama produk sudah terdaftar di produk lain!";
    } else if (empty($kategori) || empty($nama) || empty($merk) || empty($deskripsi) || empty($harga)) {
        $error_msg = "❌ Semua field harus diisi!";
    } else if ($harga <= 0 || $stok < 0) {
        $error_msg = "❌ Harga harus lebih dari 0 dan stok tidak boleh negatif!";
    } else {

        $update_foto_sql = "";

        // PROSES UPDATE FOTO
        if (isset($_POST['foto_data']) && !empty($_POST['foto_data'])) {
            
            $foto_data = $_POST['foto_data'];
            
            // Decode base64
            if (preg_match('/^data:image\/(\w+);base64,/', $foto_data, $type)) {
                $foto_data = substr($foto_data, strpos($foto_data, ',') + 1);
                $tipo = strtolower($type[1]);
                
                if (!in_array($tipo, ['jpeg', 'jpg', 'png', 'gif'])) {
                    $error_msg = "❌ Format file tidak diperbolehkan!";
                } else {
                    $data = base64_decode($foto_data);
                    
                    if ($data === false) {
                        $error_msg = "❌ Gagal mengubah format file!";
                    } else {
                        // Buat nama file
                        $foto_produk = time() . "_produk_" . uniqid() . '.' . ($tipo === 'jpeg' ? 'jpg' : $tipo);
                        
                        // Buat folder jika belum ada
                        if (!is_dir("../foto_produk")) {
                            mkdir("../foto_produk", 0777, true);
                        }
                        
                        // Simpan file baru
                        if (file_put_contents("../foto_produk/$foto_produk", $data)) {
                            // Hapus file lama jika ada
                            if (!empty($product['foto_produk']) && file_exists("../foto_produk/" . $product['foto_produk'])) {
                                unlink("../foto_produk/" . $product['foto_produk']);
                            }
                            
                            $update_foto_sql = ", foto_produk='$foto_produk'";
                        } else {
                            $error_msg = "❌ Gagal menyimpan file foto!";
                        }
                    }
                }
            } else {
                $error_msg = "❌ Format foto tidak valid!";
            }
        }

        // Jika tidak ada error, lakukan update
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
                $error_msg = "❌ Gagal update database: " . mysqli_error($koneksi);
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
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --blue: #1E5DAC;
            --beige: #E8D3C1;
            --alley: #B7C5DA;
            --misty: #EAE2E4;
            --white: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--misty) 0%, #f5f5f5 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--blue);
            margin-bottom: 1rem;
            text-align: center;
        }

        .breadcrumb {
            background: transparent;
            padding: 0 0 20px 0;
            border: none;
        }

        .breadcrumb-item a {
            color: var(--blue);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: var(--alley);
        }

        /* ALERT MESSAGES */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-weight: 500;
            animation: slideDown 0.3s ease-out;
        }

        .alert-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .alert-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* FORM CARD */
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(30, 93, 172, 0.1);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--blue);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--alley);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            color: var(--blue);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--alley);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.15);
        }

        input::placeholder,
        textarea::placeholder {
            color: var(--alley);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231E5DAC' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        input:disabled {
            background: #f0f0f0;
            cursor: not-allowed;
            color: #999;
        }

        /* FOTO SECTION */
        .foto-section {
            border: 2px dashed var(--alley);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            background: rgba(30, 93, 172, 0.02);
            transition: all 0.3s ease;
        }

        .foto-section.active {
            border-color: var(--blue);
            background: rgba(30, 93, 172, 0.08);
        }

        .foto-preview {
            width: 200px;
            height: 200px;
            margin: 0 auto 1.5rem;
            border-radius: 15px;
            overflow: hidden;
            display: none;
            border: 3px solid var(--blue);
        }

        .foto-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .foto-preview.show {
            display: block;
        }

        .foto-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        input[type="file"] {
            display: none;
        }

        .file-input-label {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--blue), var(--alley));
            color: white;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.3);
        }

        .file-input-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30, 93, 172, 0.4);
        }

        .foto-info {
            color: var(--alley);
            font-size: 0.85rem;
            margin-top: 1rem;
        }

        /* CROPPER MODAL */
        .cropper-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            animation: fadeIn 0.3s ease-out;
        }

        .cropper-modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .cropper-container-wrapper {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .cropper-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--blue);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .cropper-wrapper {
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .cropper-wrapper img {
            max-width: 100%;
        }

        .cropper-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-crop {
            background: linear-gradient(135deg, var(--blue), var(--alley));
            color: white;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.3);
        }

        .btn-crop:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30, 93, 172, 0.4);
        }

        .btn-cancel {
            background: var(--misty);
            color: var(--blue);
            border: 2px solid var(--alley);
        }

        .btn-cancel:hover {
            background: var(--alley);
            color: white;
        }

        /* FORM BUTTONS */
        .form-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 2px solid var(--alley);
        }

        .btn-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 14px 30px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-cancel-form {
            background: white;
            color: var(--blue);
            border: 2px solid var(--blue);
            padding: 12px 30px;
        }

        .btn-cancel-form:hover {
            background: rgba(30, 93, 172, 0.1);
        }

        .info-text {
            background: rgba(30, 93, 172, 0.08);
            padding: 12px;
            border-left: 3px solid var(--blue);
            border-radius: 8px;
            font-size: 0.85rem;
            color: var(--blue);
            margin-top: 0.5rem;
        }

        .price-input-wrapper {
            position: relative;
        }

        .currency-symbol {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 600;
            color: var(--blue);
            pointer-events: none;
        }

        .price-input-wrapper input {
            padding-left: 40px;
        }

        .readonly-field {
            background: #f5f5f5;
            color: #999;
        }

        .created-at-info {
            background: rgba(100, 116, 139, 0.1);
            padding: 12px 16px;
            border-radius: 8px;
            color: #64748b;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 2rem;
            }

            .cropper-container-wrapper {
                width: 95%;
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <nav class="breadcrumb">
            <a class="breadcrumb-item" href="dashboard.php">Dashboard</a>
            <a class="breadcrumb-item" href="data_produk.php">Data Produk</a>
            <span class="breadcrumb-item active">Edit Produk</span>
        </nav>

        <h1 class="page-title">✏️ Edit Produk</h1>

        <?php if ($success): ?>
            <div class="alert alert-success">
                ✅ Produk berhasil diperbarui!
            </div>
        <?php elseif (!empty($error_msg)): ?>
            <div class="alert alert-danger">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" id="formEditProduk">
                <!-- ID (READONLY) -->
                <div class="form-section">
                    <h3 class="section-title">📋 Informasi Dasar</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="id">ID Produk</label>
                            <input type="text" id="id" value="<?php echo htmlspecialchars($product['id']); ?>" disabled class="readonly-field">
                            <div class="info-text">ID produk tidak dapat diubah</div>
                        </div>

                        <div class="form-group">
                            <label for="kategori">Kategori Produk *</label>
                            <select id="kategori" name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Men" <?php echo $product['kategori'] === 'Men' ? 'selected' : ''; ?>>Men</option>
                                <option value="Women" <?php echo $product['kategori'] === 'Women' ? 'selected' : ''; ?>>Women</option>
                                <option value="Shoes" <?php echo $product['kategori'] === 'Shoes' ? 'selected' : ''; ?>>Shoes</option>
                                <option value="Accessories" <?php echo $product['kategori'] === 'Accessories' ? 'selected' : ''; ?>>Accessories</option>
                            </select>
                            <div class="info-text">Pilih kategori yang sesuai untuk produk</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama">Nama Produk *</label>
                            <input type="text" id="nama" name="nama" placeholder="Nama produk" 
                                   value="<?php echo htmlspecialchars($product['nama']); ?>" required>
                            <div class="info-text">Nama produk harus unik</div>
                        </div>

                        <div class="form-group">
                            <label for="merk">Merk Produk *</label>
                            <input type="text" id="merk" name="merk" placeholder="Merk atau brand" 
                                   value="<?php echo htmlspecialchars($product['merk']); ?>" required>
                            <div class="info-text">Masukkan merk atau brand resmi</div>
                        </div>
                    </div>
                </div>

                <!-- DESKRIPSI & HARGA -->
                <div class="form-section">
                    <h3 class="section-title">💬 Detail Produk</h3>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Produk *</label>
                        <textarea id="deskripsi" name="deskripsi" placeholder="Jelaskan spesifikasi produk..." required><?php echo htmlspecialchars($product['deskripsi']); ?></textarea>
                        <div class="info-text">Deskripsi yang detail membantu pelanggan memahami produk</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="harga">Harga Produk (Rp) *</label>
                            <div class="price-input-wrapper">
                                <span class="currency-symbol">Rp</span>
                                <input type="number" id="harga" name="harga" placeholder="Harga produk" 
                                       value="<?php echo htmlspecialchars($product['harga']); ?>" min="1" required>
                            </div>
                            <div class="info-text">Harga produk dalam rupiah</div>
                        </div>

                        <div class="form-group">
                            <label for="stok">Stok Produk *</label>
                            <input type="number" id="stok" name="stok" placeholder="Jumlah stok" 
                                   value="<?php echo htmlspecialchars($product['stok']); ?>" min="0" required>
                            <div class="info-text">Jumlah stok barang yang tersedia</div>
                        </div>
                    </div>
                </div>

                <!-- TANGGAL PUBLISH (READONLY) -->
                <div class="form-section">
                    <h3 class="section-title">📅 Tanggal</h3>

                    <div class="form-group">
                        <label for="created_at">Tanggal Publish</label>
                        <input type="text" id="created_at" 
                               value="<?php echo date('d/m/Y H:i:s', strtotime($product['created_at'])); ?>" 
                               disabled class="readonly-field">
                        <div class="created-at-info">📌 Tanggal publish tidak dapat diubah. Data ini bersifat otomatis dan permanen.</div>
                    </div>
                </div>

                <!-- FOTO PRODUK -->
                <div class="form-section">
                    <h3 class="section-title">📸 Foto Produk</h3>

                    <!-- Preview Foto Saat Ini -->
                    <?php if (!empty($product['foto_produk']) && file_exists("../foto_produk/" . $product['foto_produk'])): ?>
                        <div style="margin-bottom: 1.5rem;">
                            <p style="color: var(--blue); font-weight: 600; margin-bottom: 0.5rem;">Foto Produk Saat Ini:</p>
                            <img src="../foto_produk/<?php echo htmlspecialchars($product['foto_produk']); ?>" 
                                 alt="Foto Produk" style="width: 200px; height: 200px; border-radius: 10px; object-fit: cover; border: 3px solid var(--blue);">
                        </div>
                    <?php endif; ?>

                    <div class="foto-section" id="fotoSection">
                        <div class="foto-preview" id="fotoPreview">
                            <img id="previewImage" src="" alt="Foto Produk">
                        </div>

                        <div class="foto-input-wrapper">
                            <input type="file" id="fotoInput" accept="image/jpeg,image/png,image/jpg">
                            <label for="fotoInput" class="file-input-label">
                                🖼️ Ganti Foto Produk
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
                    <button type="submit" name="update" class="btn btn-submit">
                        ✅ Simpan Perubahan
                    </button>
                    <a href="data_produk.php" class="btn btn-cancel-form" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        ❌ Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- CROPPER MODAL -->
    <div class="cropper-modal" id="cropperModal">
        <div class="cropper-container-wrapper">
            <h3 class="cropper-title">🖼️ Crop Foto Produk</h3>
            <div class="cropper-wrapper">
                <img id="cropperImage" src="" alt="Crop Image">
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
        document.getElementById('formEditProduk').addEventListener('submit', function(e) {
            // Jika ada file input yang dipilih, harus ada crop data
            if (fotoInput.files.length > 0 && !fotoData.value) {
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