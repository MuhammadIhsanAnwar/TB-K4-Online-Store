<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pengguna</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ================= LOGIN USER STYLE ================= */

        body.bg-light {
            background: url("background 2.jpg") no-repeat center center / cover;
            font-family: 'Poppins', sans-serif;
            position: relative;
        }

        body.bg-light::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: -1;
        }

        .card {
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
        }

        /* LOGO */
        .login-logo {
            width: 160px;
            max-width: 100%;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.4));
        }

        .card h4 {
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .form-label {
            color: #fff;
            font-weight: 500;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 14px;
            border: none;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            background: #fff;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        }

        .btn-primary {
            background: linear-gradient(to right, #0a1a3f, #112d63);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: 0.25s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(to right, #122752, #1b3b79);
        }

        .text-center.small a {
            color: #e5e5e5 !important;
        }

        .text-center.small a:hover {
            color: #ffffff !important;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" style="max-width: 380px; width: 100%;">

            <!-- LOGO -->
            <div class="text-center mb-3">
                <img src="logo.png" alt="Urban Hype Logo" class="login-logo">
            </div>

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
                <a href="../admin/login_admin.php" class="d-block text-decoration-none mb-1">
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
