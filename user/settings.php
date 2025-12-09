<?php
session_start();
include "../admin/koneksi.php";

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$result = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);

$success = false;
$error_msg = "";

// Proses update
if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $jenis_kelamin = $_POST['jenis_kelamin'];

    $update_foto_sql = "";

    // Proses upload foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] != 4) { // 4 = no file uploaded
        $allowed_ext = ['jpg','jpeg','png'];
        $foto_name_raw = $_FILES['foto']['name'];
        $foto_ext = strtolower(pathinfo($foto_name_raw, PATHINFO_EXTENSION));
        $foto_size = $_FILES['foto']['size'];
        $foto_tmp = $_FILES['foto']['tmp_name'];

        // Validasi ekstensi
        if (!in_array($foto_ext, $allowed_ext)) {
            $error_msg = "Format file tidak diperbolehkan. Hanya jpg, jpeg, png.";
        } elseif ($foto_size > 2*1024*1024) { // 2MB
            $error_msg = "Ukuran file maksimal 2MB.";
        } else {
            $foto_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $foto_name_raw);

            // Pastikan folder ada
            if (!is_dir("../foto_profil")) {
                mkdir("../foto_profil", 0777, true);
            }

            if (move_uploaded_file($foto_tmp, "../foto_profil/$foto_name")) {
                // Hapus foto lama
                if (!empty($user['foto_profil']) && file_exists("../foto_profil/".$user['foto_profil'])) {
                    unlink("../foto_profil/".$user['foto_profil']);
                }
                $update_foto_sql = ", foto_profil='$foto_name'";
            } else {
                $error_msg = "Gagal mengupload foto. Cek permission folder foto_profil.";
            }
        }
    }

    // Update database jika tidak ada error
    if (empty($error_msg)) {
        $update_sql = "UPDATE akun_user SET username='$username', nama_lengkap='$nama_lengkap', tanggal_lahir='$tanggal_lahir', alamat='$alamat', jenis_kelamin='$jenis_kelamin' $update_foto_sql WHERE id='$user_id'";
        if (mysqli_query($koneksi, $update_sql)) {
            $success = true;
            // Refresh data user
            $result = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
            $user = mysqli_fetch_assoc($result);
        } else {
            $error_msg = "Gagal update database: ".mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings Akun</title>
<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
<style>
#preview {
    border-radius: 50%;
    object-fit: cover;
    width: 100px;
    height: 100px;
    display: inline-block;
}
</style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">Settings Akun</h2>

    <?php if($success): ?>
        <div class="alert alert-success">Data berhasil diperbarui!</div>
    <?php elseif(!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $user['nama_lengkap']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $user['tanggal_lahir']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="3" required><?php echo $user['alamat']; ?></textarea>
        </div>
        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select" required>
                <option value="Laki-laki" <?php if($user['jenis_kelamin']=='Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                <option value="Perempuan" <?php if($user['jenis_kelamin']=='Perempuan') echo 'selected'; ?>>Perempuan</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Foto Profil</label><br>
            <?php if(!empty($user['foto_profil'])): ?>
                <img id="preview" src="../foto_profil/<?php echo $user['foto_profil']; ?>" alt="Foto Profil"><br>
            <?php else: ?>
                <img id="preview" src="" alt="Preview" style="display:none;"><br>
            <?php endif; ?>
            <input type="file" id="foto" name="foto" class="form-control">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto (maks 2MB, jpg/jpeg/png)</small>
        </div>

        <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

<script src="../js/bootstrap.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
let cropper;
const fotoInput = document.getElementById('foto');
const preview = document.getElementById('preview');

fotoInput.addEventListener('change', function(e){
    const file = e.target.files[0];
    if(!file) return;

    const reader = new FileReader();
    reader.onload = function(event){
        preview.src = event.target.result;
        preview.style.display = "block";

        if(cropper) cropper.destroy();

        cropper = new Cropper(preview, {
            aspectRatio: 1,
            viewMode: 1,
            background: false,
            zoomable: false,
            movable: false,
            scalable: false
        });
    }
    reader.readAsDataURL(file);
});

const form = document.querySelector('form');
form.addEventListener('submit', function(e){
    if(cropper){
        e.preventDefault();
        cropper.getCroppedCanvas({
            width: 300,
            height: 300,
            imageSmoothingQuality: 'high'
        }).toBlob((blob) => {
            const fileInput = new File([blob], fotoInput.files[0].name, {type: blob.type});
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(fileInput);
            fotoInput.files = dataTransfer.files;
            form.submit();
        }, 'image/png');
    }
});
</script>
</body>
</html>
