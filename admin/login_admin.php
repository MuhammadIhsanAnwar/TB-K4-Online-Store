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

    <link rel="stylesheet" href="../css/bootstrap.css">

    <style>
        /* ================= URBANHYPE ADMIN LOGIN ================= */

        html, body {
            height: 100%;
        }

        body.bg-light {
            background-image: url("background.jpg");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            font-family: "Poppins", sans-serif;
            position: relative;
        }

        /* overlay TONE SAMA DENGAN LOGIN & REGISTER */
        body.bg-light::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(30, 93, 172, 0.55),
                rgba(183, 197, 218, 0.45)
            );
            z-index: -1;
        }

        /* Card glass */
        .card {
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 20px 45px rgba(30, 93, 172, 0.35);
            padding: 35px 30px;
            transition: 0.25s ease;
        }

        /* Logo */
        .logo-box {
            text-align: center;
            margin-bottom: 12px;
        }

        .logo-box img {
            width: 150px;
            max-width: 100%;
            filter: drop-shadow(0 6px 10px rgba(30, 93, 172, 0.45));
        }

        /* Judul */
        .card h3 {
            color: #ffffff;
            font-weight: 700;
            letter-spacing: 0.6px;
        }

        /* Label */
        .form-label {
            color: #eef3ff;
            font-weight: 500;
        }

        /* Input */
        .form-control {
            border-radius: 12px;
            padding: 12px 14px;
            border: none;
            background: rgba(255, 255, 255, 0.95);
        }

        .form-control:focus {
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.25);
        }

        /* Button login (tone sama) */
        .btn-dark {
            background: linear-gradient(
                135deg,
                #1E5DAC,
                #B7C5DA
            );
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            color: #fff;
        }

        .btn-dark:hover {
            background: linear-gradient(
                135deg,
                #B7C5DA,
                #1E5DAC
            );
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 93, 172, 0.45);
        }

        /* Link bawah */
        .small a {
            color: #eef3ff !important;
            text-decoration: none;
            transition: 0.2s;
        }

        .small a:hover {
            color: #ffffff !important;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <div class="container-fluid d-flex justify-content-center align-items-start align-items-md-center flex-grow-1 pt-5 pt-md-0">
        <div class="card shadow-sm" style="max-width: 380px; width: 100%;">

            <!-- LOGO -->
            <div class="logo-box">
                <img src="logo.png" alt="Urban Hype Logo">
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

                <button class="btn btn-dark w-100" name="login">
                    Login
                </button>
            </form>

            <div class="text-center small mt-3">
                <a href="../user/login_user.php" class="d-block mb-1">
                    Login User
                </a>
                <a href="../index.php" class="d-block mt-2">
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>

    <script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
