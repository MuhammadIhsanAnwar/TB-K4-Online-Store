<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login User</title>
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link href="../css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css_user/login_user.css">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-start center-box">
        <div class="card p-4" style="max-width: 420px; width: 100%;">

            <div class="text-center mb-3">
                <img src="../images/icon/logo.png" alt="Urban Hype Logo" class="login-logo" style="max-width: 160px; height: auto;" />
            </div>

            <h4 class="text-center mb-3">Login</h4>

            <form action="proses_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus />
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required />
                </div>

                <button type="submit" class="btn btn-login w-100 mb-3">
                    Masuk
                </button>
            </form>

            <div class="text-center mt-3 small">
                Belum punya akun? <a href="register.php">Register</a>
                <a href="lupa_password.php" class="d-block mt-2">Lupa Password?</a>
                <a href="../admin/login_admin.php" class="d-block mt-2">Login Administrator</a>
                <a href="../index.php" class="d-block mt-2">Kembali ke Beranda</a>
            </div>

        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>