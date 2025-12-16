<?php
session_start();
include "../admin/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
        $user_id = $_SESSION['user_id'];

        // Delete user account
        $sql = "DELETE FROM akun_user WHERE id = '$user_id'";

        if (mysqli_query($koneksi, $sql)) {
            session_destroy();
            showAlert('success', 'Akun Dihapus!', 'Akun Anda telah berhasil dihapus dari sistem. Kami harap Anda akan kembali lagi.', '../index.php');
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
    <title>Hapus Akun - Urban Hype</title>
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">
    <style>
        /* ================= PALETTE ================= */
        :root {
            --blue: #1E5DAC;
            --beige: #E8D3C1;
            --alley: #B7C5DA;
            --misty: #EAE2E4;
            --white: #ffffff;
        }

        /* ================= BASE ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            background-image: url("../images/Background dan Logo/bg regis.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: "Poppins", sans-serif;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg,
                    rgba(30, 93, 172, 0.40),
                    rgba(183, 197, 218, 0.30));
            z-index: -1;
        }

        /* ================= CARD ================= */
        .card {
            border-radius: 22px;
            background: rgba(234, 226, 228, 0.45);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 25px 50px rgba(30, 93, 172, 0.35);
            padding: 50px 40px;
            max-width: 500px;
            width: 100%;
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card h2 {
            color: var(--blue);
            font-weight: 700;
            letter-spacing: 0.6px;
            font-size: 2rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .warning-icon {
            font-size: 4rem;
            text-align: center;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .warning-text {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #dc2626;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 2rem;
            color: #2d3a4a;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .danger-list {
            background: rgba(239, 68, 68, 0.05);
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 2rem;
            border: 1px dashed #fecaca;
        }

        .danger-list ul {
            margin: 0;
            padding-left: 20px;
            list-style: none;
        }

        .danger-list li {
            padding: 8px 0;
            color: #5a6b80;
            font-size: 0.9rem;
            position: relative;
            padding-left: 25px;
        }

        .danger-list li::before {
            content: "✕";
            position: absolute;
            left: 0;
            color: #dc2626;
            font-weight: bold;
        }

        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 2rem;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.5px;
        }

        .btn-cancel {
            background: rgba(255, 255, 255, 0.7);
            color: var(--blue);
            border: 2px solid var(--blue);
            backdrop-filter: blur(10px);
        }

        .btn-cancel:hover {
            background: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 93, 172, 0.2);
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.4);
        }

        .btn-delete:active {
            transform: translateY(0);
        }

        .text-center {
            text-align: center;
            margin-top: 1.5rem;
        }

        .text-center a {
            color: var(--blue);
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .text-center a:hover {
            color: #0d3a7a;
            text-decoration: underline;
        }

        small {
            color: #5a6b80;
            display: block;
            margin-top: 1rem;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 576px) {
            .card {
                padding: 30px 20px;
            }

            .card h2 {
                font-size: 1.5rem;
            }

            .warning-icon {
                font-size: 3rem;
            }

            .button-group {
                grid-template-columns: 1fr;
            }

            .btn {
                padding: 14px;
                font-size: 0.9rem;
            }
        }
    </style>
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