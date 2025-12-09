<?php
include "../admin/koneksi.php";

// CEK POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register_user.php"); // redirect jika bukan POST
    exit;
}

// AMBIL DATA
$username        = $_POST['username'] ?? '';
$nama_lengkap    = $_POST['nama_lengkap'] ?? '';
$jenis_kelamin   = $_POST['jenis_kelamin'] ?? '';
$tanggal_lahir   = $_POST['tanggal_lahir'] ?? '';
$alamat          = $_POST['alamat'] ?? '';
$email           = $_POST['email'] ?? '';
$password        = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

// VALIDASI SEDERHANA
if (!$username || !$nama_lengkap || !$jenis_kelamin || !$tanggal_lahir || !$alamat || !$email || !$password) {
    echo "<script>alert('Semua field wajib diisi!');history.back();</script>";
    exit;
}

// CEK USERNAME / EMAIL SUDAH ADA BELUM
$cek = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE email='$email' OR username='$username'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Email atau Username sudah terdaftar!');history.back();</script>";
    exit;
}

// INSERT DATA
$query = mysqli_query($koneksi, "INSERT INTO akun_user 
(username, nama_lengkap, jenis_kelamin, tanggal_lahir, alamat, email, password)
VALUES
('$username','$nama_lengkap','$jenis_kelamin','$tanggal_lahir','$alamat','$email','$password')");

if ($query) {
    echo "<script>alert('Register berhasil! Silakan login.');window.location='login_user.php';</script>";
} else {
    echo "Gagal menyimpan data: " . mysqli_error($koneksi);
}
