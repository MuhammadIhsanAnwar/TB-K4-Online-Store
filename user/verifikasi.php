<?php
include "../admin/koneksi.php";

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

// Jika parameter kosong
if ($email === '' || $token === '') {
    die("Link verifikasi tidak valid.");
}

// Escaping untuk cegah SQL Injection
$email = mysqli_real_escape_string($koneksi, $email);
$token = mysqli_real_escape_string($koneksi, $token);

$cek = mysqli_query(
    $koneksi,
    "SELECT id FROM akun_user WHERE email='$email' AND token='$token' LIMIT 1"
);

if (mysqli_num_rows($cek) === 1) {

    mysqli_query(
        $koneksi,
        "UPDATE akun_user 
         SET status='1', token='' 
         WHERE email='$email' LIMIT 1"
    );

    echo "<script>
            alert('Akun berhasil diverifikasi!');
            window.location='login_user.php';
          </script>";
} else {
    echo "Link verifikasi tidak valid.";
}
