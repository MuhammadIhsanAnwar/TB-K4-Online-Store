<?php
include "../admin/koneksi.php";

// CEK POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php"); // redirect jika bukan POST
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// VALIDASI PASSWORD
if (!preg_match('/[A-Z]/', $password)) {
    die("<script>alert('Password harus mengandung huruf BESAR!');history.back();</script>");
}
if (!preg_match('/[a-z]/', $password)) {
    die("<script>alert('Password harus mengandung huruf kecil!');history.back();</script>");
}
if (!preg_match('/[0-9]/', $password)) {
    die("<script>alert('Password harus mengandung angka!');history.back();</script>");
}
if (!preg_match('/[\W_]/', $password)) {
    die("<script>alert('Password harus mengandung karakter spesial!');history.back();</script>");
}
if (strlen($password) < 8) {
    die("<script>alert('Password minimal 8 karakter!');history.back();</script>");
}

// Hash password baru
$pass_hash = password_hash($password, PASSWORD_DEFAULT);

// Update password akun
$update = mysqli_query($koneksi, 
    "UPDATE akun_user SET password='$pass_hash' WHERE email='$email'"
);

if ($update) {
    // Hapus token agar tidak bisa digunakan lagi
    mysqli_query($koneksi, "DELETE FROM reset_password WHERE email='$email'");

    echo "<script>
            alert('Password berhasil diperbarui! Silakan login.');
            window.location='login_user.php';
          </script>";
} else {
    echo "<script>alert('Gagal memperbarui password!');history.back();</script>";
}
