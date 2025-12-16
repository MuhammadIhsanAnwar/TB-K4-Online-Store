<?php
// Koneksi database
include "../../admin/koneksi.php";

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = false;
$error_msg = "";

// Ambil data user dari database
$q = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
if (!$q || mysqli_num_rows($q) == 0) {
    die("User tidak ditemukan!");
}
$user = mysqli_fetch_assoc($q);

// PROSES UPDATE
if (isset($_POST['update'])) {

    $username       = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama_lengkap   = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $alamat         = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $jenis_kelamin  = $_POST['jenis_kelamin'];

    $provinsi       = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
    $kabupaten_kota = mysqli_real_escape_string($koneksi, $_POST['kabupaten_kota']);
    $kecamatan      = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $kelurahan_desa = mysqli_real_escape_string($koneksi, $_POST['kelurahan_desa']);
    $kode_pos       = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);

    $update_foto_sql = "";

    // UPLOAD FOTO
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] != 4) {

        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $foto_name_raw = $_FILES['foto']['name'];
        $foto_ext = strtolower(pathinfo($foto_name_raw, PATHINFO_EXTENSION));
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_size = $_FILES['foto']['size'];

        if (!in_array($foto_ext, $allowed_ext)) {
            $error_msg = "Format file tidak diperbolehkan. (JPG, JPEG, PNG)";
        } elseif ($foto_size > 2 * 1024 * 1024) {
            $error_msg = "Ukuran file maksimal 2MB.";
        } else {

            $new_name = time() . "_profil." . $foto_ext;

            if (!is_dir("../../foto_profil")) {
                mkdir("../../foto_profil", 0777, true);
            }

            if (move_uploaded_file($foto_tmp, "../../foto_profil/$new_name")) {

                if (!empty($user['foto_profil']) && file_exists("../../foto_profil/" . $user['foto_profil'])) {
                    unlink("../../foto_profil/" . $user['foto_profil']);
                }

                $update_foto_sql = ", foto_profil='$new_name'";
            } else {
                $error_msg = "Gagal mengupload foto.";
            }
        }
    }

    if (empty($error_msg)) {

        $update_sql = "
            UPDATE akun_user SET 
                username='$username',
                nama_lengkap='$nama_lengkap',
                tanggal_lahir='$tanggal_lahir',
                jenis_kelamin='$jenis_kelamin',
                alamat='$alamat',
                provinsi='$provinsi',
                kabupaten_kota='$kabupaten_kota',
                kecamatan='$kecamatan',
                kelurahan_desa='$kelurahan_desa',
                kode_pos='$kode_pos'
                $update_foto_sql
            WHERE id='$user_id'
        ";

        if (mysqli_query($koneksi, $update_sql)) {
            $success = true;
            $result = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
            $user = mysqli_fetch_assoc($result);
        } else {
            $error_msg = "Gagal update database: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link rel="icon" type="image/png" href="../../images/Background dan Logo/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #EAE2E4 0%, #B7C5DA 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .profile-container {
        max-width: 800px;
        margin: 0 auto;
    }

    h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        font-weight: 700;
        color: #1E5DAC;
        margin-bottom: 2rem;
        text-align: center;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Styled alert messages */
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
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .alert-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
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

    /* Styled form */
    form {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        font-weight: 600;
        color: #1E5DAC;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #B7C5DA;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    label {
        display: block;
        font-weight: 600;
        color: #1E5DAC;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    input[type="text"],
    input[type="date"],
    input[type="number"],
    input[type="file"],
    select,
    textarea {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #B7C5DA;
        border-radius: 10px;
        font-family: 'Inter', sans-serif;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    input[type="number"]:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #1E5DAC;
        box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.1);
        transform: translateY(-2px);
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231E5DAC' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
    }

    /* Two column layout for address fields */
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    /* Photo preview styling */
    .photo-section {
        text-align: center;
        padding: 2rem;
        background: linear-gradient(135deg, #EAE2E4 0%, #B7C5DA 100%);
        border-radius: 15px;
        margin-bottom: 1.5rem;
    }

    #preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease;
    }

    #preview:hover {
        transform: scale(1.05);
    }

    input[type="file"] {
        cursor: pointer;
    }

    input[type="file"]::file-selector-button {
        padding: 0.5rem 1.5rem;
        background: #1E5DAC;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 1rem;
    }

    input[type="file"]::file-selector-button:hover {
        background: #164a8a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 93, 172, 0.3);
    }

    /* Styled submit button */
    .btn-submit {
        width: 100%;
        padding: 1rem 2rem;
        background: linear-gradient(135deg, #1E5DAC 0%, #164a8a 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .btn-submit:hover::before {
        left: 100%;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(30, 93, 172, 0.4);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    hr {
        border: none;
        height: 2px;
        background: linear-gradient(90deg, transparent, #B7C5DA, transparent);
        margin: 2rem 0;
    }

    .info-text {
        background: linear-gradient(135deg, rgba(30, 93, 172, 0.05) 0%, rgba(183, 197, 218, 0.1) 100%);
        padding: 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        color: #5a6b80;
        margin-top: 4px;
    }

    input:disabled {
        background-color: #f0f0f0;
        cursor: not-allowed;
    }
</style>

<body>
    <div class="profile-container">
        <h2>Edit Profil</h2>

        <?php if ($success): ?>
            <div class="alert alert-success">✓ Data berhasil diperbarui!</div>
        <?php elseif (!empty($error_msg)): ?>
            <div class="alert alert-danger">✗ <?= $error_msg ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-section">
                <h3 class="section-title">Foto Profil</h3>

                <div class="photo-section">
                    <img id="preview" src="<?php echo !empty($user['foto_profil']) ? '../foto_profil/' . htmlspecialchars($user['foto_profil']) : 'https://via.placeholder.com/150?text=Foto+Profil'; ?>" alt="Foto Profil">
                    <div class="form-group">
                        <label>Pilih Foto Baru</label>
                        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png">
                        <div class="info-text">Format: JPG, JPEG, PNG. Ukuran maksimal: 2MB</div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="form-section">
                <h3 class="section-title">Informasi Dasar</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="<?php echo htmlspecialchars($user['tanggal_lahir'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" <?php echo (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="P" <?php echo (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                    <div class="info-text">Email tidak dapat diubah</div>
                </div>
            </div>

            <hr>

            <div class="form-section">
                <h3 class="section-title">Alamat Lengkap</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi" value="<?php echo htmlspecialchars($user['provinsi'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Kabupaten/Kota</label>
                        <input type="text" name="kabupaten_kota" value="<?php echo htmlspecialchars($user['kabupaten_kota'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Kecamatan</label>
                        <input type="text" name="kecamatan" value="<?php echo htmlspecialchars($user['kecamatan'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Kelurahan/Desa</label>
                        <input type="text" name="kelurahan_desa" value="<?php echo htmlspecialchars($user['kelurahan_desa'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Kode Pos</label>
                    <input type="text" name="kode_pos" value="<?php echo htmlspecialchars($user['kode_pos'] ?? ''); ?>" required pattern="[0-9]{5}">
                </div>

                <div class="form-group">
                    <label>Alamat Detail</label>
                    <textarea name="alamat" rows="3" required><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
                </div>
            </div>

            <button type="submit" name="update" class="btn-submit">
                💾 Simpan Perubahan
            </button>
        </form>
    </div>

    <script>
        const fotoInput = document.getElementById('foto');
        const preview = document.getElementById('preview');

        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validasi tipe file
                if (!['image/jpeg', 'image/png'].includes(file.type)) {
                    alert('Hanya JPG dan PNG yang diizinkan!');
                    this.value = '';
                    return;
                }

                // Validasi ukuran file
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB!');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>