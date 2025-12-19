<?php
session_start();
require 'koneksi.php';

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM akun_admin WHERE username='$user' AND password='$pass'");

    if (mysqli_num_rows($query) > 0) {
        $_SESSION['login'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin</title>
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="css_admin/login_admin_style.css">
</head>

<body class="d-flex align-items-center justify-content-center">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">

                <div class="card">

                    <div class="text-center mb-3">
                        <img src="../images/icon/logo.png" alt="Urban Hype Logo" class="login-logo" style="max-width: 160px; height: auto;" />
                    </div>

                    <h3 class="text-center mb-3">Admin Login</h3>

                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger py-2"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <button class="btn btn-login w-100" name="login">
                            Login
                        </button>
                    </form>

                    <div class="text-center small mt-3">
                        <a href="../user/login_user.php">Login User</a>
                        <a href="../index.php" class="d-block mt-2">Kembali ke Beranda</a>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>