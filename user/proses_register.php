<?php
include "../admin/koneksi.php";

// Ambil data
$username        = $_POST['username'];
$nama_lengkap    = ucwords(strtolower($_POST['nama_lengkap']));
$jenis_kelamin   = $_POST['jenis_kelamin'];
$tanggal_lahir   = $_POST['tanggal_lahir'];

$provinsi        = ucwords(strtolower($_POST['provinsi']));
$kabupaten_kota  = ucwords(strtolower($_POST['kabupaten_kota']));
$kecamatan       = ucwords(strtolower($_POST['kecamatan']));
$kelurahan_desa  = ucwords(strtolower($_POST['kelurahan_desa']));
$kode_pos        = $_POST['kode_pos']; // kode pos jangan diubah, tetap angka

$alamat          = ucwords(strtolower($_POST['alamat']));
$email           = strtolower($_POST['email']); // email selalu lowercase

$password        = $_POST['password'];
$confirm         = $_POST['confirm_password'];


// Validasi username minimal 8 karakter
if (strlen($username) < 8) {
    die("<script>alert('Username minimal 8 karakter!');history.back();</script>");
}

// Cek konfirmasi password
if ($password !== $confirm) {
    die("<script>alert('Konfirmasi password tidak sama!');history.back();</script>");
}

// Validasi password kuat
if (
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/\d/', $password) ||
    !preg_match('/[\W_]/', $password) ||
    strlen($password) < 8
) {
    die("<script>alert('Password tidak memenuhi syarat!');history.back();</script>");
}

// Cek email
$cek_email = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE email='$email'");
if (mysqli_num_rows($cek_email) > 0) {
    die("<script>alert('Email sudah terdaftar!');history.back();</script>");
}

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Generate token verifikasi
$token = bin2hex(random_bytes(32));

// ======================
// PROSES UPLOAD + CROP FOTO
// ======================
$foto_name = "";

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $tmp = $_FILES['foto']['tmp_name'];

    $check = getimagesize($tmp);
    if (!$check) {
        die("<script>alert('File bukan gambar!');history.back();</script>");
    }

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $ext = strtolower($ext);

    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
        die("<script>alert('Format foto harus JPG atau PNG!');history.back();</script>");
    }

    $image = null;

    if ($ext == 'jpg' || $ext == 'jpeg') {
        $image = imagecreatefromjpeg($tmp);
    } else {
        $image = imagecreatefrompng($tmp);
    }

    $w = imagesx($image);
    $h = imagesy($image);

    // crop ke bentuk square
    $size = min($w, $h);
    $x = ($w - $size) / 2;
    $y = ($h - $size) / 2;

    $crop = imagecreatetruecolor(500, 500); // output 500x500
    imagecopyresampled($crop, $image, 0, 0, $x, $y, 500, 500, $size, $size);

    $foto_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES['foto']['name']);
    $save_path = "../foto_profil/" . $foto_name;

    if (!is_dir("../foto_profil")) {
        mkdir("../foto_profil", 0777, true);
    }

    imagejpeg($crop, $save_path, 90);

    imagedestroy($image);
    imagedestroy($crop);
}

// ======================
// SIMPAN DATABASE
// ======================
$sql = "INSERT INTO akun_user 
(username, nama_lengkap, jenis_kelamin, tanggal_lahir, provinsi, kabupaten_kota, kecamatan, kelurahan_desa, kode_pos, alamat, email, password, foto_profil, status, token)
VALUES
('$username', '$nama_lengkap', '$jenis_kelamin', '$tanggal_lahir', '$provinsi', '$kabupaten_kota', '$kecamatan', '$kelurahan_desa', '$kode_pos', '$alamat', '$email', '$hashed', '$foto_name', '0', '$token')";

if (mysqli_query($koneksi, $sql)) {

    // Kirim email verifikasi
    $link = "https://urbanhype.neoverse.my.id/verifikasi.php?email=$email&token=$token";

    $subject = "Verifikasi Akun Anda";
    $message = "Klik link berikut untuk mengaktifkan akun:\n\n$link";
    $headers = "From: admin@urbanhype.neoverse.my.id";

    mail($email, $subject, $message, $headers);

    echo "<script>
        alert('Registrasi berhasil! Silakan cek email untuk verifikasi.');
        window.location.href='login_user.php';
    </script>";
} else {
    echo "Error: " . mysqli_error($koneksi);
}
