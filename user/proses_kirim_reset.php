<?php
include "../admin/koneksi.php";

$email = $_POST['email'];

// Cari user berdasarkan email
$q = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE email='$email'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    echo "<script>alert('Email tidak terdaftar!');history.back();</script>";
    exit;
}

// Buat token random
$token = bin2hex(random_bytes(32));
$expired = date("Y-m-d H:i:s", time() + 3600); // Berlaku 1 jam

// Simpan token ke database (buat tabel jika belum ada)
// Tabel: reset_password(email, token, expired)
mysqli_query($koneksi, "INSERT INTO reset_password (email, token, expired) VALUES ('$email', '$token', '$expired')");

// Link reset
$link_reset = "https://urbanhype.neoverse.my.id/user/reset_password.php?token=$token";

// PENGIRIMAN EMAIL VIA SMTP HOSTING
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = "mail.urbanhype.neoverse.my.id"; // server email hosting
$mail->SMTPAuth = true;
$mail->Username = "mailreset@urbanhype.neoverse.my.id"; // email pengirim hosting
$mail->Password = "administrator-online-store"; // password email pengirim
$mail->SMTPSecure = "ssl";
$mail->Port = 465;

$mail->setFrom("mailreset@urbanhype.neoverse.my.id", "Reset Password");
$mail->addAddress($email);

$mail->isHTML(true);
$mail->Subject = "Reset Password Akun Anda";
$mail->Body = "
    Halo, klik link berikut untuk mereset password:<br><br>
    <a href='$link_reset'>Reset Password</a><br><br>
    Link berlaku selama 1 jam.
";

if ($mail->send()) {
    echo "<script>alert('Link reset berhasil dikirim ke email Anda');window.location='login.php';</script>";
} else {
    echo "<script>alert('Gagal mengirim email!');history.back();</script>";
}
