<?php
include "../admin/koneksi.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

// CEK POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Bisa redirect ke halaman lain atau tampilkan error
    header("Location: ../index.php");
    exit;
}

$email = $_POST['email'] ?? '';

// Validasi sederhana
if (empty($email)) {
    showAlert('error', 'Input Tidak Valid', 'Email tidak boleh kosong!', 'forgot.php');
}

// Cari email
$q = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE email='$email'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    showAlert('error', 'Email Tidak Terdaftar', 'Email yang Anda masukkan tidak terdaftar dalam sistem kami.', 'forgot.php');
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
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host       = "urbanhype.neoverse.my.id";
    $mail->SMTPAuth   = true;
    $mail->Username   = "mailreset@urbanhype.neoverse.my.id";
    $mail->Password   = "administrator-online-store";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
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
        <h3>Reset Password</h3>
        Halo, klik link berikut untuk reset password:<br><br>
        <a href='$link_reset' style='background-color: #1E5DAC; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a><br><br>
        <p style='color: #666;'>Atau copy link berikut: $link_reset</p>
        <p style='color: #999; font-size: 12px;'>Link berlaku selama 1 jam.</p>
    ";

    $mail->send();
    showAlert('success', 'Berhasil!', 'Link reset password telah dikirim ke email Anda. Silakan cek email dan ikuti instruksi untuk mereset password.', 'login_user.php');
} catch (Exception $e) {
    showAlert('error', 'Gagal Mengirim Email', 'Terjadi kesalahan saat mengirim email: ' . $mail->ErrorInfo, 'forgot.php');
}

function showAlert($type, $title, $message, $redirect)
{
?>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title; ?></title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="icon" type="image/png" href="../images/icon/logo.png">
        <link rel="stylesheet" href="css_user/proses_kirim_reset.css">
    </head>

    <body>
        <script>
            Swal.fire({
                icon: '<?php echo $type; ?>',
                title: '<?php echo $title; ?>',
                text: '<?php echo $message; ?>',
                confirmButtonColor: '#1E5DAC',
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '<?php echo $redirect; ?>';
                }
            });
        </script>
    </body>

    </html>
<?php
    exit;
}
?>