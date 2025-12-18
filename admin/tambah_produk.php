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
            margin: 0;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100vh;
            background: linear-gradient(180deg, #1e63b6, #0f3f82);
            padding: 18px 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .logo-box {
            text-align: center;
            padding: 10px 0 18px;
        }

        .logo-box img {
            width: 72px;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, .25));
            transition: .3s ease;
        }

        .logo-box img:hover {
            transform: scale(1.05);
        }

        .menu-title {
            color: #dbe6ff;
            font-size: 13px;
            padding: 8px 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 4px 12px;
            border-radius: 10px;
            transition: .25s;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, .18);
        }

        .sidebar a.active {
            background: rgba(255, 255, 255, .32);
            font-weight: 600;
        }

        .sidebar .logout {
            margin-top: auto;
            background: rgba(255, 80, 80, .15);
            color: #ffd6d6 !important;
            font-weight: 600;
            text-align: center;
            border-radius: 14px;
            transition: .3s ease;
            margin-bottom: 12px;
        }

        .sidebar .logout:hover {
            background: #ff4d4d;
            color: #fff !important;
            box-shadow: 0 10px 25px rgba(255, 77, 77, .6);
            transform: translateY(-2px);
        }

        /* ================= CONTENT ================= */
        .content {
            margin-left: 220px;
            padding: 30px 20px;
            min-height: 100vh;
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
            background-color: rgba(0, 0, 0, 0.92);
            animation: fadeIn 0.3s ease-out;
            padding: 20px;
            overflow-y: auto;
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
            background: #ffffff;
            border-radius: 20px;
            padding: 2.5rem;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 20px 80px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.4s ease-out;
        }

        .cropper-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--blue);
            margin-bottom: 2rem;
            text-align: center;
        }

        .cropper-wrapper {
            width: 100%;
            margin-bottom: 2rem;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
            border: 3px solid #e0e0e0;
            position: relative;
            min-height: 350px;
            max-height: 500px;
        }

        .cropper-wrapper img {
            width: 100% !important;
            height: 100% !important;
            display: block !important;
            object-fit: contain !important;
            background: white !important;
        }

        /* Pastikan Cropper.js tidak transparan */
        .cropper-canvas {
            background: white !important;
            opacity: 1 !important;
        }

        .cropper-container {
            background: white !important;
        }

        .cropper-crop-box {
            opacity: 1 !important;
        }

        .cropper-crop-box .cropper-face {
            background: rgba(30, 93, 172, 0.15) !important;
            opacity: 1 !important;
        }

        .cropper-crop-box .cropper-line {
            border-color: var(--blue) !important;
            opacity: 1 !important;
        }

        .cropper-crop-box .cropper-point {
            background: var(--blue) !important;
            box-shadow: 0 0 0 2px white, 0 0 0 4px var(--blue) !important;
            width: 12px !important;
            height: 12px !important;
            opacity: 1 !important;
        }

        .cropper-center {
            background: var(--blue) !important;
            opacity: 1 !important;
        }

        .cropper-grid {
            border-color: rgba(30, 93, 172, 0.6) !important;
            opacity: 1 !important;
        }

        .cropper-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            width: 100%;
        }

        .btn {
            padding: 14px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            width: 100%;
        }

        .btn-crop {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-crop:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-crop:active {
            transform: translateY(0);
        }

        .btn-cancel {
            background: white;
            color: var(--blue);
            border: 2px solid var(--blue);
        }

        .btn-cancel:hover {
            background: var(--blue);
            color: white;
            transform: translateY(-2px);
        }

        .btn-cancel:active {
            transform: translateY(0);
        }

        /* RESPONSIVE CROPPER */
        @media (max-width: 1024px) {
            .cropper-container-wrapper {
                padding: 2rem;
                max-width: 90%;
            }

            .cropper-wrapper {
                min-height: 320px;
                max-height: 450px;
            }

            .cropper-title {
                font-size: 1.7rem;
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .cropper-modal {
                padding: 15px;
            }

            .cropper-container-wrapper {
                padding: 1.5rem;
                max-width: 100%;
                border-radius: 15px;
            }

            .cropper-title {
                font-size: 1.4rem;
                margin-bottom: 1.2rem;
            }

            .cropper-wrapper {
                min-height: 280px;
                max-height: 400px;
                border-radius: 10px;
                margin-bottom: 1.5rem;
            }

            .cropper-buttons {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .btn {
                padding: 12px 20px;
                font-size: 0.9rem;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .cropper-modal {
                padding: 10px;
            }

            .cropper-container-wrapper {
                padding: 1rem;
                border-radius: 12px;
            }

            .cropper-title {
                font-size: 1.2rem;
                margin-bottom: 1rem;
            }

            .cropper-wrapper {
                min-height: 250px;
                max-height: 350px;
                margin-bottom: 1rem;
                border: 2px solid #e0e0e0;
            }

            .cropper-buttons {
                gap: 0.75rem;
            }

            .btn {
                padding: 10px 16px;
                font-size: 0.85rem;
            }
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

        @media (max-width: 1024px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 200px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 180px;
            }

            .content {
                margin-left: 180px;
                padding: 20px 15px;
            }

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

        @media (max-width: 600px) {
            .sidebar {
                width: 160px;
            }

            .content {
                margin-left: 160px;
                padding: 15px 10px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .logo-box img {
                width: 60px;
            }

            .menu-title {
                font-size: 11px;
            }

            .sidebar a {
                padding: 10px 15px;
                margin: 3px 8px;
                font-size: 0.9rem;
            }

            .form-card {
                padding: 1rem;
            }
        }
    </style>
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