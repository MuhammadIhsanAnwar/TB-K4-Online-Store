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

// Proses update
if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $jenis_kelamin = $_POST['jenis_kelamin'];

    // Update foto jika ada file baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto_name = time() . "_" . $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        move_uploaded_file($foto_tmp, "../foto_profil/$foto_name");

        // Hapus foto lama jika ada
        if (!empty($user['foto_profil']) && file_exists("../foto_profil/".$user['foto_profil'])) {
            unlink("../foto_profil/".$user['foto_profil']);
        }

        $update_foto_sql = ", foto_profil='$foto_name'";
    } else {
        $update_foto_sql = "";
    }

    // Update database
    $update_sql = "UPDATE akun_user SET username='$username', nama='$nama', tanggal_lahir='$tanggal_lahir', alamat='$alamat', jenis_kelamin='$jenis_kelamin' $update_foto_sql WHERE id='$user_id'";
    if (mysqli_query($koneksi, $update_sql)) {
        echo "<script>alert('Data berhasil diperbarui');window.location='settings.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: ".mysqli_error($koneksi)."');</script>";
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
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">Settings Akun</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" value="<?php echo $user['nama']; ?>" required>
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
                <img src="../foto_profil/<?php echo $user['foto_profil']; ?>" alt="Foto Profil" width="100" class="mb-2 rounded-circle"><br>
            <?php endif; ?>
            <input type="file" name="foto" class="form-control">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto</small>
        </div>

        <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
