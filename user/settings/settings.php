<?php
session_start();
include "../admin/koneksi.php";

if (!isset($_SESSION['user_email'])) {
    header("Location: ../login_user.php");
    exit;
}

// data user
$email = $_SESSION['user_email'];
$q   = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE email='$email'");
$user = mysqli_fetch_assoc($q);

// include sidebar
include "sidebar.php";
?>
<div class="content">

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
