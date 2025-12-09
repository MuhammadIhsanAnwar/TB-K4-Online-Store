<?php
session_start();
include "../admin/koneksi.php";

// CEK POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login_user.php"); // redirect jika bukan POST
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo "<script>alert('Username dan Password wajib diisi!');history.back();</script>";
    exit;
}

$q = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE username='$username' OR email='$username'");
$data = mysqli_fetch_assoc($q);

if ($data) {
    if (password_verify($password, $data['password'])) {

        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];

        header("Location: ../index.php");
        exit;

    } else {
        echo "<script>alert('Password salah!');history.back();</script>";
    }
} else {
    echo "<script>alert('Akun tidak ditemukan!');history.back();</script>";
}
