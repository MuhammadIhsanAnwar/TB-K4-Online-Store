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
    $nomor_hp       = preg_replace('/[^0-9]/', '', $_POST['nomor_hp']); // Hapus karakter non-digit

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
                nomor_hp='$nomor_hp',
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
    <link rel="icon" type="image/png" href="../../images/icon/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css_user/css_settings/settings_profil.css">
</head>
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
                    <img id="preview" src="<?php echo !empty($user['foto_profil']) ? '../../foto_profil/' . htmlspecialchars($user['foto_profil']) : 'https://via.placeholder.com/150?text=Foto+Profil'; ?>" alt="Foto Profil">
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

                <div class="form-row">
                    <div class="form-group">
                        <label>Nomor HP</label>
                        <input type="tel" name="nomor_hp" value="<?php echo htmlspecialchars($user['nomor_hp'] ?? ''); ?>" required pattern="[0-9]{10,13}" title="10-13 digit angka">
                        <div class="info-text">Contoh: 08123456789 (10-13 digit)</div>
                    </div>
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