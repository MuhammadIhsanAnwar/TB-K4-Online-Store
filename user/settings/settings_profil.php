<?php
// $user sudah diambil dari settings.php
$success = false;
$error_msg = "";

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
            $error_msg = "Format file tidak diperbolehkan.";
        } elseif ($foto_size > 2 * 1024 * 1024) {
            $error_msg = "Ukuran maksimal 2MB.";
        } else {

            $new_name = time() . "_profil." . $foto_ext;

            if (!is_dir("../foto_profil")) {
                mkdir("../foto_profil", 0777, true);
            }

            if (move_uploaded_file($foto_tmp, "../foto_profil/$new_name")) {

                if (!empty($user['foto_profil']) && file_exists("../foto_profil/" . $user['foto_profil'])) {
                    unlink("../foto_profil/" . $user['foto_profil']);
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

<h2 class="mb-4">Edit Profil</h2>

<?php if ($success): ?>
    <div class="alert alert-success">Data berhasil diperbarui!</div>
<?php elseif (!empty($error_msg)): ?>
    <div class="alert alert-danger"><?= $error_msg ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <form method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control"
                value="<?= $user['username']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control"
                value="<?= $user['nama_lengkap']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control"
                value="<?= $user['tanggal_lahir']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select">
                <option value="Laki-laki" <?= $user['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $user['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>

        <hr>

        <h5>Alamat Lengkap</h5>

        <div class="mb-3"><label>Provinsi</label>
            <input type="text" name="provinsi" class="form-control" value="<?= $user['provinsi']; ?>">
        </div>

        <div class="mb-3"><label>Kabupaten/Kota</label>
            <input type="text" name="kabupaten_kota" class="form-control" value="<?= $user['kabupaten_kota']; ?>">
        </div>

        <div class="mb-3"><label>Kecamatan</label>
            <input type="text" name="kecamatan" class="form-control" value="<?= $user['kecamatan']; ?>">
        </div>

        <div class="mb-3"><label>Kelurahan/Desa</label>
            <input type="text" name="kelurahan_desa" class="form-control" value="<?= $user['kelurahan_desa']; ?>">
        </div>

        <div class="mb-3"><label>Kode Pos</label>
            <input type="number" name="kode_pos" class="form-control" value="<?= $user['kode_pos']; ?>">
        </div>

        <div class="mb-3"><label>Alamat Detail</label>
            <textarea name="alamat" rows="3" class="form-control"><?= $user['alamat']; ?></textarea>
        </div>

        <hr>

        <div class="mb-3">
            <label>Foto Profil</label><br>

            <img id="preview"
                src="../foto_profil/<?= $user['foto_profil'] ?: 'default.png' ?>"
                style="width:100px;height:100px;border-radius:50%;object-fit:cover;">

            <input type="file" id="foto" name="foto"
                class="form-control mt-3" accept="image/*">
        </div>

        <button type="submit" name="update" class="btn btn-primary">
            Simpan Perubahan
        </button>
    </form>


    <!-- ===================== MODAL CROP ===================== -->
    <div class="modal fade" id="cropModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Crop Foto Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="cropImage" style="max-width:100%;">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button id="btnCrop" class="btn btn-primary">Simpan Crop</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
        let cropper;
        let fotoInput = document.getElementById("foto");
        let preview = document.getElementById("preview");
        let cropImage = document.getElementById("cropImage");
        let originalExt = "";

        fotoInput.addEventListener("change", function(e) {
            const file = e.target.files[0];
            if (!file) return;

            originalExt = file.name.split('.').pop().toLowerCase();

            const reader = new FileReader();
            reader.onload = function(event) {

                cropImage.src = event.target.result;

                if (cropper) cropper.destroy();

                new bootstrap.Modal(document.getElementById("cropModal")).show();

                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    dragMode: "move",
                    viewMode: 1
                });
            }
            reader.readAsDataURL(file);
        });

        document.getElementById("btnCrop").addEventListener("click", function() {
            cropper.getCroppedCanvas({
                width: 500,
                height: 500
            }).toBlob((blob) => {

                let newFile = new File([blob], "foto_crop." + originalExt, {
                    type: "image/" + originalExt
                });

                const dt = new DataTransfer();
                dt.items.add(newFile);
                fotoInput.files = dt.files;

                preview.src = URL.createObjectURL(newFile);

                bootstrap.Modal.getInstance(document.getElementById("cropModal")).hide();
            });
        });
    </script>
</body>

</html>