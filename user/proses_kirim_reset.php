<?php
include "../admin/koneksi.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$email = $_POST['email'];

// Cari email
$q = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE email='$email'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    echo "<script>alert('Email tidak terdaftar!');history.back();</script>";
    exit;
}

// Token reset
$token = bin2hex(random_bytes(32));
$expired = date("Y-m-d H:i:s", time() + 3600);

// Simpan token
mysqli_query($koneksi, "INSERT INTO reset_password (email, token, expired) 
                        VALUES ('$email', '$token', '$expired')");

$link_reset = "https://urbanhype.neoverse.my.id/user/reset_password.php?token=$token";

// SEND EMAIL
$mail = new PHPMailer(true);

try {

    // DEBUG (jika mau aktifkan, ubah 0 → 2)
    $mail->SMTPDebug = 0;

    $mail->isSMTP();
    $mail->Host       = "urbanhype.neoverse.my.id";   // HOST SMTP YANG BENAR
    $mail->SMTPAuth   = true;
    $mail->Username   = "mailreset@urbanhype.neoverse.my.id";
    $mail->Password   = "administrator-online-store";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
    $mail->Port       = 465;

    // FIX SSL error (wajib pada beberapa hosting)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom("mailreset@urbanhype.neoverse.my.id", "Reset Password");
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Reset Password Akun Anda";
    $mail->Body = "
        Halo, klik link berikut untuk reset password:<br><br>
        <a href='$link_reset'>$link_reset</a><br><br>
        Link berlaku selama 1 jam.
    ";

    $mail->send();
    echo "<script>alert('Link reset berhasil dikirim ke email Anda');window.location='login_user.php';</script>";

} catch (Exception $e) {
    echo "<script>alert('Gagal mengirim email: {$mail->ErrorInfo}');history.back();</script>";
}
