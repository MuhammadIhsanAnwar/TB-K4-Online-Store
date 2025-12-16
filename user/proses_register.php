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
$confirm        = $_POST['password_confirm'];

// === UPLOAD FOTO PROFIL ===
$foto_nama = "";

if (!empty($_FILES['foto']['name'])) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $foto_nama = "foto-" . time() . "-" . rand(1000, 9999) . "." . $ext;
    $folder = "../foto_profil/" . $foto_nama;

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed)) {
        showAlert('error', 'Format foto tidak valid!', 'register_user.php');
    }

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $folder)) {
        showAlert('error', 'Gagal mengupload foto profil!', 'register_user.php');
    }
}

// VALIDASI PASSWORD
if ($password !== $confirm) {
    showAlert('error', 'Konfirmasi password tidak cocok!', 'register_user.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(20));

// INSERT DATA - gunakan prepared statement
$query = "INSERT INTO akun_user 
(username, nama_lengkap, jenis_kelamin, tanggal_lahir, provinsi, kabupaten_kota, kecamatan, kelurahan_desa, kode_pos, alamat, email, password, foto_profil, status, token)
VALUES 
('$username', '$nama_lengkap', '$jenis_kelamin', '$tanggal_lahir', '$provinsi', '$kabupaten_kota', '$kecamatan', '$kelurahan_desa', '$kode_pos', '$alamat', '$email', '$hash', '$foto_nama', '0', '$token')";

if (!mysqli_query($koneksi, $query)) {
    showAlert('error', 'Error: ' . mysqli_error($koneksi), 'register_user.php');
}

require "../phpmailer/src/PHPMailer.php";
require "../phpmailer/src/SMTP.php";
require "../phpmailer/src/Exception.php";

$link = "https://urbanhype.neoverse.my.id/user/verifikasi.php?email={$email}&token={$token}";

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'urbanhype.neoverse.my.id';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'admin@urbanhype.neoverse.my.id';
    $mail->Password   = 'administrator-online-store';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->CharSet    = "UTF-8";

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
        Halo <b>{$nama_lengkap}</b>,<br><br>
        Klik link berikut untuk mengaktifkan akun kamu:<br><br>
        <a href='{$link}'>{$link}</a><br><br>
        Jika kamu tidak merasa mendaftar, abaikan email ini.
    ";

    $mail->send();
    showAlert('success', 'Registrasi berhasil! Silakan cek email untuk verifikasi.', 'login_user.php');
} catch (Exception $e) {
    showAlert('error', "Gagal mengirim email: " . $mail->ErrorInfo, 'register_user.php');
}

function showAlert($type, $message, $redirect)
{
?>
    <!DOCTYPE html>
    <html>

    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <body>
        <script>
            Swal.fire({
                icon: '<?php echo $type; ?>',
                title: '<?php echo ($type === 'success') ? 'Berhasil!' : 'Terjadi Kesalahan'; ?>',
                text: '<?php echo $message; ?>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
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