<?php
include "../admin/koneksi.php";

$email = $_GET['email'];
$token = $_GET['token'];

$cek = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE email='$email' AND token='$token'");

if (mysqli_num_rows($cek) == 1) {
    mysqli_query($koneksi, "UPDATE akun_user SET status='1', token='' WHERE email='$email'");
    echo "<script>alert('Akun berhasil diverifikasi!');window.location='login_user.php';</script>";
} else {
    echo "Link verifikasi tidak valid.";
}
?>
