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

// include sidebar
include "sidebar.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <!-- Bootstrap CSS -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">
        <div class="row">
            <!-- sidebar bisa di sini jika mau satu layout -->
            <div class="col-md-3">
                <?php include "sidebar.php"; ?>
            </div>

            <div class="col-md-9 content">
                <?php
                // routing menu
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
        </div>
    </div>

    <!-- Bootstrap JS + Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>