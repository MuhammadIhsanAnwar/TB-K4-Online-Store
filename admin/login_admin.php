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

    <style>
        /* ================= PALETTE ================= */
        :root {
            --blue: #1E5DAC;
            /* Mediterranean Blue */
            --beige: #E8D3C1;
            /* Blush Beige */
            --alley: #B7C5DA;
            /* Alley */
            --misty: #EAE2E4;
            /* Misty */
            --white: #ffffff;
        }

        /* ================= BASE ================= */
        html,
        body {
            height: 100%;
        }

        /* BACKGROUND FOTO TAJAM (NO BLUR) */
        body {
            background-image: url("../images/background/bg_admin.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: "Poppins", sans-serif;
            position: relative;
        }

        /* OVERLAY WARNA PALETTE (TIDAK BLUR) */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg,
                    rgba(30, 93, 172, 0.45),
                    /* Mediterranean Blue */
                    rgba(183, 197, 218, 0.35)
                    /* Alley */
                );
            z-index: -1;
        }

        /* ================= CARD ================= */
        .card {
            border-radius: 22px;
            background: rgba(234, 226, 228, 0.40);
            /* Misty */
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 25px 50px rgba(30, 93, 172, 0.35);
            padding: 35px 30px;
        }

        /* LOGO */
        .logo-box {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo-box img {
            width: 150px;
            filter: drop-shadow(0 6px 12px rgba(30, 93, 172, 0.5));
        }

        /* JUDUL */
        .card h3 {
            color: var(--blue);
            font-weight: 700;
            letter-spacing: 0.6px;
        }

        /* LABEL */
        .form-label {
            color: #2d3a4a;
            font-weight: 500;
        }

        /* INPUT */
        .form-control {
            border-radius: 12px;
            padding: 12px 14px;
            border: none;
            background: var(--white);
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.25);
        }

        /* BUTTON */
        .btn-login {
            background: linear-gradient(135deg,
                    var(--blue),
                    var(--alley));
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            color: #fff;
            transition: 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg,
                    var(--alley),
                    var(--blue));
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 93, 172, 0.45);
        }

        /* LINK */
        .small a {
            color: var(--blue);
            text-decoration: none;
            transition: 0.2s;
        }

        .small a:hover {
            color: #000;
            font-weight: 600;
        }
    </style>
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