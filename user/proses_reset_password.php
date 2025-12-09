<?php
include "../admin/koneksi.php";

$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Update password user
mysqli_query($koneksi, "UPDATE akun_user SET password='$password' WHERE email='$email'");

// Hapus token
mysqli_query($koneksi, "DELETE FROM reset_password WHERE email='$email'");

echo "<script>alert('Password berhasil diperbarui!');window.location='login.php';</script>";
