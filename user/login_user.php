<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login User</title>
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">

    <link href="../css/bootstrap.min.css" rel="stylesheet" />

    <style>
        /* ================= PALETTE ================= */
        :root {
            --blue: #1E5DAC;
            /* Mediterranean Blue */
            --alley: #B7C5DA;
            /* Alley */
            --misty: #EAE2E4;
            /* Misty */
            --white: #ffffff;
        }

        html,
        body {
            height: 100%;
        }

        /* BACKGROUND FOTO TAJAM (NAMA FILE TETAP) */
        body.bg-light {
            background-image: url("../images/Background dan Logo/bg login.jpg");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            font-family: "Poppins", sans-serif;
            position: relative;
        }

        /* OVERLAY WARNA (SAMA LOGIN ADMIN) */
        body.bg-light::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg,
                    rgba(30, 93, 172, 0.45),
                    rgba(183, 197, 218, 0.35));
            z-index: -1;
        }

        /* POSISI */
        .center-box {
            padding-top: 60px;
            padding-bottom: 80px;
        }

        /* CARD */
        .card {
            border-radius: 22px;
            background: rgba(234, 226, 228, 0.40);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 25px 50px rgba(30, 93, 172, 0.35);
        }

        /* LOGO */
        .login-logo {
            width: 160px;
            filter: drop-shadow(0 6px 12px rgba(30, 93, 172, 0.5));
        }

        /* JUDUL */
        .card h4 {
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

        /* LINK — SAMA LOGIN ADMIN */
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

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-start center-box">
        <div class="card p-4" style="max-width: 420px; width: 100%;">

            <div class="text-center mb-3">
                <img src="../images/Background dan Logo/logo.png" alt="Urban Hype Logo" class="login-logo" style="max-width: 160px; height: auto;" />
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

            <div class="text-center small">
                <a href="../admin/login_admin.php" class="d-block mb-1">Administrator</a>
                <a href="register.php" class="d-block">Register Akun</a>
                <a href="lupa_password.php" class="d-block mt-2">Lupa Password?</a>
                <a href="../index.php" class="d-block mt-2">Kembali ke Beranda</a>
            </div>

        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>