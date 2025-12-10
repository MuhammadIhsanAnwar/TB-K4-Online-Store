<?php
include "../admin/koneksi.php";

// Ambil data (pakai mysqli_real_escape_string untuk mencegah error SQL jika ada tanda kutip)
$username        = mysqli_real_escape_string($koneksi, $_POST['username']);
$nama_lengkap    = ucwords(strtolower(mysqli_real_escape_string($koneksi, $_POST['nama_lengkap'])));
$jenis_kelamin   = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
$tanggal_lahir   = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);

$provinsi        = ucwords(strtolower(mysqli_real_escape_string($koneksi, $_POST['provinsi'])));
$kabupaten_kota  = ucwords(strtolower(mysqli_real_escape_string($koneksi, $_POST['kabupaten_kota'])));
$kecamatan       = ucwords(strtolower(mysqli_real_escape_string($koneksi, $_POST['kecamatan'])));
$kelurahan_desa  = ucwords(strtolower(mysqli_real_escape_string($koneksi, $_POST['kelurahan_desa'])));
$kode_pos        = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);

$alamat          = ucwords(strtolower(mysqli_real_escape_string($koneksi, $_POST['alamat'])));
$email           = strtolower(mysqli_real_escape_string($koneksi, $_POST['email']));

$password        = $_POST['password'];
$confirm         = $_POST['confirm_password'];


// Validasi username minimal 8 karakter
if (strlen($username) < 8) {
    die("<script>alert('Username minimal 8 karakter!');history.back();</script>");
}

// Cek konfirmasi password
if ($password !== $confirm) {
    die("<script>alert('Konfirmasi password tidak sama!');history.back();</script>");
}

// Validasi password kuat
if (
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/\d/', $password) ||
    !preg_match('/[\W_]/', $password) ||
    strlen($password) < 8
) {
    die("<script>alert('Password tidak memenuhi syarat!');history.back();</script>");
}

// Cek email
$cek_email = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE email='$email'");
if (mysqli_num_rows($cek_email) > 0) {
    die("<script>alert('Email sudah terdaftar!');history.back();</script>");
}

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Generate token verifikasi
$token = bin2hex(random_bytes(32));

// ======================
// PROSES UPLOAD + CROP FOTO
// ======================
$foto_name = "";

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $tmp = $_FILES['foto']['tmp_name'];

    $check = getimagesize($tmp);
    if (!$check) {
        die("<script>alert('File bukan gambar!');history.back();</script>");
    }

    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
        die("<script>alert('Format foto harus JPG atau PNG!');history.back();</script>");
    }

    if ($ext == 'jpg' || $ext == 'jpeg') {
        $image = imagecreatefromjpeg($tmp);
    } else {
        $image = imagecreatefrompng($tmp);
    }

    $w = imagesx($image);
    $h = imagesy($image);

    $size = min($w, $h);
    $x = ($w - $size) / 2;
    $y = ($h - $size) / 2;

    $crop = imagecreatetruecolor(500, 500);

    // if PNG preserve transparency (optional)
    if ($ext === 'png') {
        imagealphablending($crop, false);
        imagesavealpha($crop, true);
        $transparent = imagecolorallocatealpha($crop, 255, 255, 255, 127);
        imagefilledrectangle($crop, 0, 0, 500, 500, $transparent);
    }

    imagecopyresampled($crop, $image, 0, 0, $x, $y, 500, 500, $size, $size);

    $foto_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES['foto']['name']);
    $save_path = "../foto_profil/" . $foto_name;

    if (!is_dir("../foto_profil")) {
        mkdir("../foto_profil", 0777, true);
    }

    // Simpan sesuai ekstensi asli
    if ($ext == 'png') {
        imagepng($crop, $save_path);
    } else {
        imagejpeg($crop, $save_path, 90);
    }

    imagedestroy($image);
    imagedestroy($crop);
}

// ======================
// SIMPAN DATABASE
// ======================
$sql = "INSERT INTO akun_user 
(username, nama_lengkap, jenis_kelamin, tanggal_lahir, provinsi, kabupaten_kota, kecamatan, kelurahan_desa, kode_pos, alamat, email, password, foto_profil, status, token)
VALUES
('$username', '$nama_lengkap', '$jenis_kelamin', '$tanggal_lahir', '$provinsi', '$kabupaten_kota', '$kecamatan', '$kelurahan_desa', '$kode_pos', '$alamat', '$email', '$hashed', '$foto_name', '0', '$token')";

if (mysqli_query($koneksi, $sql)) {

    // ============================================
    //      PHPMailer untuk kirim verifikasi (tanpa "use")
    // ============================================
    $link = "https://urbanhype.neoverse.my.id/verifikasi.php?email=" . urlencode($email) . "&token=" . urlencode($token);

    // require PHPMailer (pastikan path benar)
    require '../phpmailer/src/PHPMailer.php';
    require '../phpmailer/src/SMTP.php';
    require '../phpmailer/src/Exception.php';

    // buat instance dengan namespace lengkap
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        // gunakan host mail.* biasanya "mail.domain.com"
        $mail->Host       = 'urbanhype.neoverse.my.id';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'admin@urbanhype.neoverse.my.id';
        $mail->Password   = 'administrator-online-store'; // ganti jika perlu
        $mail->SMTPSecure = 'tls'; // atau 'ssl'
        $mail->Port       = 465;   // 587 untuk TLS, 465 untuk SSL

        $mail->setFrom('admin@urbanhype.neoverse.my.id', 'UrbanHype');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Verifikasi Akun Anda";
        $mail->Body = "
            <h3>Verifikasi Akun UrbanHype</h3>
            Klik link berikut untuk aktivasi akun:<br><br>
            <a href='$link' target='_blank'>$link</a>
        ";

        $mail->send();

        echo "<script>
            alert('Registrasi berhasil! Silakan cek email untuk verifikasi.');
            window.location.href='login_user.php';
        </script>";
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        // tampilkan error PHPMailer (berguna saat debug)
        die('<script>alert(\"Gagal mengirim email verifikasi: ' . mysqli_real_escape_string($koneksi, $mail->ErrorInfo) . '\");history.back();</script>');
    }
} else {
    echo "Error: " . mysqli_error($koneksi);
}
