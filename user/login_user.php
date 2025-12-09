<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pengguna</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" style="max-width: 380px; width: 100%;">
            <h4 class="text-center mb-3">Login</h4>

            <form action="proses_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Masuk</button>
            </form>

            <div class="text-center small">
                <a href="../admin/login_admin.php" class="d-block text-decoration-none text-secondary mb-1">
                    Administrator
                </a>

                <a href="register.php" class="d-block text-decoration-none">
                    Register Akun
                </a>

                <a href="lupa_password.php" class="d-block text-decoration-none mt-2">
                    Lupa Password?
                </a>

                <a href="../index.php" class="d-block text-decoration-none mt-2">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>