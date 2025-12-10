<?php
session_start();
include "../admin/koneksi.php";

// hanya menerima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// cek input kosong
if (empty($username) || empty($password)) {
    echo "<script>alert('Username dan Password wajib diisi!');history.back();</script>";
    exit;
}

// ambil user berdasarkan username atau email
$q = mysqli_query(
    $koneksi,
    "SELECT * FROM akun_user 
     WHERE username='$username' 
     OR email='$username' 
     LIMIT 1"
);

$data = mysqli_fetch_assoc($q);

if (!$data) {
    echo "<script>alert('Akun tidak ditemukan!');history.back();</script>";
    exit;
}

// CEK STATUS AKUN
if ($data['status'] == '0') {
    echo "<script>alert('Akun belum diverifikasi! Silakan cek email Anda.');history.back();</script>";
    exit;
}

// verifikasi password
if (!password_verify($password, $data['password'])) {
    echo "<script>alert('Password salah!');history.back();</script>";
    exit;
}

// sukses login
$_SESSION['user_id'] = $data['id'];
$_SESSION['username'] = $data['username'];
$_SESSION['nama_lengkap'] = $data['nama_lengkap'];
$_SESSION['user_email'] = $user['email']; // session email

header("Location: ../index.php");
exit;
