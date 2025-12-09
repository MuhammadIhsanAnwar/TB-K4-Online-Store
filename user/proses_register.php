<?php
include "../admin/koneksi.php";

$username        = $_POST['username'];
$nama_lengkap    = $_POST['nama_lengkap'];
$jenis_kelamin   = $_POST['jenis_kelamin'];
$tanggal_lahir   = $_POST['tanggal_lahir'];
$alamat          = $_POST['alamat'];
$email           = $_POST['email'];
$password        = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Cek apakah email atau username sudah dipakai
$cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' OR username='$username'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Email atau Username sudah terdaftar!');history.back();</script>";
    exit;
}

// Input data
$query = mysqli_query($conn, "INSERT INTO users 
(username, nama_lengkap, jenis_kelamin, tanggal_lahir, alamat, email, password)
VALUES
('$username','$nama_lengkap','$jenis_kelamin','$tanggal_lahir','$alamat','$email','$password')");

if ($query) {
    echo "<script>alert('Register berhasil! Silakan login.');window.location='login.php';</script>";
} else {
    echo "Gagal menyimpan data: " . mysqli_error($conn);
}
