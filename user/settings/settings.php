<?php
session_start();
include "../admin/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$q = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
$user = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Urban Hype</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #EAE2E4 0%, #f5f5f5 100%);
            margin: 0;
        }

        .content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }

        .page-header {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #1E5DAC;
            margin-bottom: 1rem;
        }

        .page-subtitle {
            color: #6B7280;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 20px;
            }

            .page-header {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <?php include "sidebar.php"; ?>

    <div class="content">
        <?php
        $menu = $_GET['menu'] ?? 'profil';

        switch ($menu) {
            case 'profil':
                include "settings_profil.php";
                break;

            case 'payment':
                include "settings_payment.php";
                break;

            case 'lain':
                include "settings_lain.php";
                break;

            default:
                echo "<h4>Menu tidak ditemukan.</h4>";
        }
        ?>
    </div>
</body>

</html>