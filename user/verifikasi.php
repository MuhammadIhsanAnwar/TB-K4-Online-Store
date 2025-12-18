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

    showAlert('success', 'Verifikasi Berhasil!', 'Akun Anda telah berhasil diverifikasi. Silakan login dengan akun Anda.', 'login_user.php');
} else {
    showAlert('error', 'Verifikasi Gagal!', 'Link verifikasi tidak valid atau sudah digunakan. Silakan coba daftar ulang.', 'register.php');
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

        <style>
            body {
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                background: linear-gradient(135deg, #1E5DAC 0%, #B7C5DA 100%);
                font-family: 'Poppins', sans-serif;
            }
        </style>
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