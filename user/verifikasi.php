<?php
include "../admin/koneksi.php";

// Menghindari error karakter URL
$email = urldecode($_GET['email']);
$token = urldecode($_GET['token']);

// Prevent SQL injection
$email = mysqli_real_escape_string($koneksi, $email);
$token = mysqli_real_escape_string($koneksi, $token);

// Cek data
$cek = mysqli_query($koneksi, "
    SELECT * FROM akun_user 
    WHERE email='$email' AND token='$token'
");

if (mysqli_num_rows($cek) === 1) {
    mysqli_query($koneksi, "
        UPDATE akun_user 
        SET status='1', token='' 
        WHERE email='$email'
    ");

    echo "<script>
            alert('Akun berhasil diverifikasi!');
            window.location='../user/login_user.php';
          </script>";
} else {
    echo "Link verifikasi tidak valid atau sudah digunakan.";
}
