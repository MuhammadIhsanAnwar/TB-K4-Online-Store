<?php
session_start();
include "../admin/koneksi.php";

// CEK POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
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

// Simpan data form ke session untuk jaga-jaga ada error
$_SESSION['form_data'] = [
    'username'       => $username,
    'nama_lengkap'   => $_POST['nama_lengkap'],
    'jenis_kelamin'  => $jenis_kelamin,
    'tanggal_lahir'  => $tanggal_lahir,
    'provinsi'       => $_POST['provinsi'],
    'kabupaten_kota' => $_POST['kabupaten_kota'],
    'kecamatan'      => $_POST['kecamatan'],
    'kelurahan_desa' => $_POST['kelurahan_desa'],
    'kode_pos'       => $kode_pos,
    'alamat'         => $_POST['alamat'],
    'email'          => $email
];

// === VALIDASI USERNAME DAN EMAIL BELUM TERDAFTAR ===
$cekUsername = mysqli_query($koneksi, "SELECT username FROM akun_user WHERE username = '$username'");
if (mysqli_num_rows($cekUsername) > 0) {
    showAlert('error', 'Username sudah terdaftar! Gunakan username lain.', 'register.php');
}

$cekEmail = mysqli_query($koneksi, "SELECT email FROM akun_user WHERE email = '$email'");
if (mysqli_num_rows($cekEmail) > 0) {
    showAlert('error', 'Email sudah terdaftar! Gunakan email lain.', 'register.php');
}

// === PROSES FOTO PROFIL DARI CANVAS ===
$foto_nama = "";

if (!empty($_POST['foto_cropped'])) {
    $fotoCropped = $_POST['foto_cropped'];

    // Validasi format base64 data URL
    if (strpos($fotoCropped, 'data:image/png;base64,') !== 0) {
        showAlert('error', 'Format foto tidak valid!', 'register.php');
    }

    // Extract base64 data
    $fotoCropped = str_replace('data:image/png;base64,', '', $fotoCropped);
    $fotoData = base64_decode($fotoCropped);

    if ($fotoData === false) {
        showAlert('error', 'Gagal memproses foto profil!', 'register.php');
    }

    // Generate nama file
    $foto_nama = "foto-" . time() . "-" . rand(1000, 9999) . ".png";
    $folder = "../foto_profil/" . $foto_nama;

    // Simpan file
    if (file_put_contents($folder, $fotoData) === false) {
        showAlert('error', 'Gagal menyimpan foto profil!', 'register.php');
    }
} else {
    showAlert('error', 'Foto profil harus diupload!', 'register.php');
}

// VALIDASI PASSWORD
if ($password !== $confirm) {
    showAlert('error', 'Konfirmasi password tidak cocok!', 'register.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(20));

// INSERT DATA
$query = "INSERT INTO akun_user 
(username, nama_lengkap, jenis_kelamin, tanggal_lahir, provinsi, kabupaten_kota, kecamatan, kelurahan_desa, kode_pos, alamat, email, password, foto_profil, status, token)
VALUES 
('$username', '$nama_lengkap', '$jenis_kelamin', '$tanggal_lahir', '$provinsi', '$kabupaten_kota', '$kecamatan', '$kelurahan_desa', '$kode_pos', '$alamat', '$email', '$hash', '$foto_nama', '0', '$token')";

if (!mysqli_query($koneksi, $query)) {
    showAlert('error', 'Error: ' . mysqli_error($koneksi), 'register.php');
}

// Jika berhasil, hapus session form_data
unset($_SESSION['form_data']);

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
    showAlert('error', "Gagal mengirim email: " . $mail->ErrorInfo, 'register.php');
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