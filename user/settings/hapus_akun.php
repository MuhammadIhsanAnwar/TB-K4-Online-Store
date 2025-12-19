<?php
session_start();
include "../../admin/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
        $user_id = $_SESSION['user_id'];

        // Delete user account
        $sql = "DELETE FROM akun_user WHERE id = '$user_id'";

        if (mysqli_query($koneksi, $sql)) {
            session_destroy();
            showAlert('success', 'Akun Dihapus!', 'Akun Anda telah berhasil dihapus dari sistem. Kami harap Anda akan kembali lagi.', '../../index.php');
        } else {
            $error = "Error: " . mysqli_error($koneksi);
            showAlert('error', 'Gagal Menghapus', 'Terjadi kesalahan saat menghapus akun. Silakan coba lagi.', 'settings.php');
        }

        mysqli_close($koneksi);
    }
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
        <link rel="icon" type="image/png" href="../../images/icon/logo.png">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Akun</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link rel="stylesheet" href="../css_user/css_settings/hapus_akun.css">
</head>

<body>

    <div class="card">
        <!-- ICON WARNING -->
        <div class="warning-icon">⚠️</div>

        <!-- TITLE -->
        <h2>Hapus Akun?</h2>

        <!-- WARNING MESSAGE -->
        <div class="warning-text">
            Anda akan menghapus akun secara permanen. Tindakan ini <strong>TIDAK DAPAT DIBATALKAN</strong> dan semua data Anda akan hilang selamanya.
        </div>

        <!-- DATA YANG AKAN DIHAPUS -->
        <div class="danger-list">
            <strong style="color: #dc2626;">Data yang akan dihapus:</strong>
            <ul>
                <li>Profil akun dan informasi pribadi</li>
                <li>Riwayat pesanan</li>
                <li>Alamat pengiriman yang tersimpan</li>
                <li>Metode pembayaran</li>
                <li>Wishlist dan preferensi</li>
            </ul>
        </div>

        <!-- BUTTONS -->
        <div class="button-group">
            <button class="btn btn-cancel" onclick="window.history.back()">
                Batal
            </button>
            <form method="POST" style="margin: 0; width: 100%;">
                <input type="hidden" name="confirm_delete" value="yes">
                <button type="submit" class="btn btn-delete">
                    Hapus Permanen
                </button>
            </form>
        </div>

        <!-- FOOTER LINK -->
        <div class="text-center">
            <small>Jika ragu, Anda bisa kembali ke <a href="settings.php">Pengaturan</a></small>
        </div>
    </div>

</body>

</html>