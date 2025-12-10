<?php
session_start();
include "../admin/koneksi.php";

// CEK POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register_user.php");
    exit;
}

// Tangkap input
$username       = $_POST['username'];
$nama_lengkap   = ucwords(strtolower($_POST['nama_lengkap']));
$jenis_kelamin  = $_POST['jenis_kelamin'];
$tanggal_lahir  = $_POST['tanggal_lahir'];

$provinsi       = ucwords(strtolower($_POST['provinsi']));
$kabupaten_kota = ucwords(strtolower($_POST['kabupaten_kota']));
$kecamatan      = ucwords(strtolower($_POST['kecamatan']));
$kelurahan_desa = ucwords(strtolower($_POST['kelurahan_desa']));
$kode_pos       = $_POST['kode_pos'];

$alamat         = ucwords(strtolower($_POST['alamat']));
$email          = $_POST['email'];

$password       = $_POST['password'];
$confirm        = $_POST['confirm_password'];

// VALIDASI PASSWORD
if ($password !== $confirm) {
    echo "<script>alert('Konfirmasi password tidak cocok!');history.back();</script>";
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

// Generate token verifikasi
$token = bin2hex(random_bytes(20));

// INSERT DATA
$query = "
INSERT INTO akun_user 
(username, nama_lengkap, jenis_kelamin, tanggal_lahir, provinsi, kabupaten_kota, kecamatan, kelurahan_desa, kode_pos, alamat, email, password, status, token)
VALUES 
('$username', '$nama_lengkap', '$jenis_kelamin', '$tanggal_lahir', '$provinsi', '$kabupaten_kota', '$kecamatan', '$kelurahan_desa', '$kode_pos', '$alamat', '$email', '$hash', '0', '$token')
";

if (!mysqli_query($koneksi, $query)) {
    echo "Error: " . mysqli_error($koneksi);
    exit;
}

// === PHPMailer === //
require "../phpmailer/src/PHPMailer.php";
require "../phpmailer/src/SMTP.php";
require "../phpmailer/src/Exception.php";

$link = "https://urbanhype.neoverse.my.id/verifikasi.php?email=$email&token=$token";

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'urbanhype.neoverse.my.id';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'admin@urbanhype.neoverse.my.id';
    $mail->Password   = 'PASSWORD_EMAIL_KAMU'; // ← Ganti password email
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->CharSet    = "UTF-8";

    // Wajib untuk hosting shared
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('admin@urbanhype.neoverse.my.id', 'UrbanHype');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Verifikasi Akun UrbanHype";
    $mail->Body = "
        <h3>Verifikasi Akun Anda</h3>
        Halo <b>$nama_lengkap</b>,<br><br>
        Klik link berikut untuk mengaktifkan akun kamu:<br><br>
        <a href='$link'>$link</a><br><br>
        Jika kamu tidak merasa mendaftar, abaikan email ini.
    ";

    $mail->send();

    echo "<script>
        alert('Registrasi berhasil! Silakan cek email untuk verifikasi.');
        window.location='login_user.php';
    </script>";
} catch (Exception $e) {
    echo "Gagal mengirim email: " . $mail->ErrorInfo;
}
