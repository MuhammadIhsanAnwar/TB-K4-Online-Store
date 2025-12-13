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
    <title>Login Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">

    <!-- ====================== CSS LOGIN ====================== -->
    <style>
        /* Background preview fashion */
        body {
             background-image: url("/TB-K4-Online-Store/assets/background.jpg");
             background-size: cover;
             background-position: center;
             background-repeat: no-repeat;
             font-family: 'Poppins', sans-serif;
            }


        /* Overlay gelap */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.35);
            z-index: -1;
        }

        /* Card login */
        .card {
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 40px 30px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
            transition: 0.25s ease-in-out;
        }

        .card:hover {
            transform: scale(1.02);
        }

        /* Logo */
        .logo-box {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo-box img {
            width: 180px;
            filter: drop-shadow(0px 4px 6px rgba(0, 0, 0, 0.4));
        }

        /* Judul */
        .card h3 {
            color: #fff;
            font-size: 26px;
            font-weight: 700;
            text-align: center;
        }

        /* Input */
        .form-control {
            border-radius: 12px;
            padding: 12px 14px;
            border: none;
            background: rgba(255, 255, 255, 0.85);
            transition: 0.2s ease;
        }

        .form-control:focus {
            background: #fff;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        }

        /* Tombol login */
        .btn-dark {
            margin-top: 5px;
            background: linear-gradient(to right, #0a1a3f, #112d63);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: .5px;
            transition: 0.25s ease;
        }

        .btn-dark:hover {
            transform: translateY(-2px);
            background: linear-gradient(to right, #122752, #1b3b79);
        }

        /* Link bawah */
        .small a {
            color: #eee !important;
        }

        .small a:hover {
            color: #fff !important;
            font-weight: 600;
        }
    </style>
    <!-- ====================== END CSS ====================== -->

</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 380px;">

            <!-- LOGO (penambahan tanpa ubah PHP) -->
            <div class="logo-box">
                <img src="logo.png" alt="Urban Hype Logo">
            </div>

            <h3 class="text-center fw-bold mb-3">Admin Login</h3>

            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger py-2"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label text-white">Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <button class="btn btn-dark w-100" name="login">Login</button>
            </form>

            <div class="text-center small mt-3">
                <a href="../user/login_user.php" class="d-block text-decoration-none mb-1">Login User</a>
                <a href="../index.php" class="d-block text-decoration-none mt-2">Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>