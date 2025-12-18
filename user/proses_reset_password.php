<?php
include "../admin/koneksi.php";

// CEK POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php"); // redirect jika bukan POST
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// VALIDASI PASSWORD
if (!preg_match('/[A-Z]/', $password)) {
    die("<script>alert('Password harus mengandung huruf BESAR!');history.back();</script>");
}
if (!preg_match('/[a-z]/', $password)) {
    die("<script>alert('Password harus mengandung huruf kecil!');history.back();</script>");
}
if (!preg_match('/[0-9]/', $password)) {
    die("<script>alert('Password harus mengandung angka!');history.back();</script>");
}
if (!preg_match('/[\W_]/', $password)) {
    die("<script>alert('Password harus mengandung karakter spesial!');history.back();</script>");
}
if (strlen($password) < 8) {
    die("<script>alert('Password minimal 8 karakter!');history.back();</script>");
}

// Hash password baru
$pass_hash = password_hash($password, PASSWORD_DEFAULT);

// Update password akun
$update = mysqli_query(
    $koneksi,
    "UPDATE akun_user SET password='$pass_hash' WHERE email='$email'"
);

if ($update) {
    // Hapus token agar tidak bisa digunakan lagi
    mysqli_query($koneksi, "DELETE FROM reset_password WHERE email='$email'");

    showAlert('success', 'Password berhasil diperbarui!', 'login_user.php');
} else {
    showAlert('error', 'Gagal memperbarui password!', 'reset.php');
}

function showAlert($type, $message, $redirect)
{
?>
    <!DOCTYPE html>
    <html>

    <head>
        <link rel="icon" type="image/png" href="../images/icon/logo.png">

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