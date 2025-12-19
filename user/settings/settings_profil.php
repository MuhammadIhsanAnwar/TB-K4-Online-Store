<?php
include "../../admin/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = false;
$error_msg = "";

$q = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
if (!$q || mysqli_num_rows($q) == 0) {
    die("User tidak ditemukan!");
}
$user = mysqli_fetch_assoc($q);

if (isset($_POST['update'])) {

    $username       = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama_lengkap   = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $alamat         = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $nomor_hp       = preg_replace('/[^0-9]/', '', $_POST['nomor_hp']);

    $provinsi       = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
    $kabupaten_kota = mysqli_real_escape_string($koneksi, $_POST['kabupaten_kota']);
    $kecamatan      = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $kelurahan_desa = mysqli_real_escape_string($koneksi, $_POST['kelurahan_desa']);
    $kode_pos       = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $update_foto_sql = "";

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
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: text-bottom; margin-right: 4px;">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
                Data berhasil diperbarui!
            </div>
        <?php elseif (!empty($error_msg)): ?>
            <div class="alert alert-danger">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: text-bottom; margin-right: 4px;">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <?= $error_msg ?>
            </div>
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
                        <label>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 6px;">
                                <path d="M11 1H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zM5 0a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V3a3 3 0 0 1 3-3h6zm1 1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h6zM8 14a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                            Nomor HP
                        </label>
                        <div class="input-wrapper">
                            <input type="tel" name="nomor_hp" value="<?php echo htmlspecialchars($user['nomor_hp'] ?? ''); ?>" required pattern="[0-9]{10,13}" title="10-13 digit angka" placeholder="08123456789">
                        </div>
                        <div class="info-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: text-bottom; margin-right: 4px;">
                                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.753.23 1.278.563.528.34 1.189.957 1.568 1.41H14V6h-3.753a9.48 9.48 0 0 0-1.518-1.412zm.027 11.412h-6a1 1 0 0 1-1-1v-1h3.294a6.6 6.6 0 0 1 .258-1.844H3.066V5.411h5.555a3.08 3.08 0 0 1 .863-1.299c-.726-.341-1.75-.564-2.772-.564H5.039l-.015-.5h4.887a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-1 0v1.294h-3.668a.5.5 0 0 0-.5.5v.5h1.039c1.022 0 1.629.28 2.25.606.584.336 1.139.922 1.502 1.694.087.166.187.33.277.458h.975a7 7 0 0 0-.313-.957c-.468-1.05-1.47-2.118-2.98-2.514a2.26 2.26 0 0 0-.383-.09h-.014.014z"/>
                            </svg>
                            10-13 digit angka (Contoh: 08123456789)
                        </div>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 6px;">
                    <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.125.312l-2.5 2.5a.5.5 0 0 1-.75.063L9.566 4.9a.5.5 0 0 0-.75-.063l-2.5 2.5A.5.5 0 0 1 6 7.25V1.5a.5.5 0 0 1 .5-.5zM11 2v1h2V2h-2zm-2 2v1h3V4h-3zm-2 2v1h5V6h-5zm-2 2v1h7V8h-7zm0 2v1h7v-1h-7zm2 2v1h5v-1h-5zm2 2v1h3v-1h-3zm2 2v1h1v-1h-1z"/>
                </svg>
                Simpan Perubahan
            </button>
        </form>
    </div>

    <script>
        const fotoInput = document.getElementById('foto');
        const preview = document.getElementById('preview');

        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (!['image/jpeg', 'image/png'].includes(file.type)) {
                    alert('Hanya JPG dan PNG yang diizinkan!');
                    this.value = '';
                    return;
                }

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