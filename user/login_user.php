<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Pengguna</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet" />

    <style>
        /* ================= URBANHYPE LOGIN (MATCH REGISTER) ================= */

        :root {
            --primary: #1E5DAC;   /* Mediterranean Blue */
            --secondary: #B7C5DA; /* Alley */
            --accent: #E8D3C1;    /* Blush Beige */
            --soft: #EAE2E4;      /* Misty */
            --white: #ffffff;
        }

        html, body {
            height: 100%;
        }

        /* BACKGROUND FOTO TAJAM (NAMA FILE TETAP) */
        body.bg-light {
            background-image: url("background 2.jpg");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            font-family: "Poppins", sans-serif;
            position: relative;
        }

        /* OVERLAY WARNA SAMA DENGAN REGISTER */
        body.bg-light::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(30, 93, 172, 0.35),
                rgba(183, 197, 218, 0.25)
            );
            z-index: -1;
        }

        /* POSISI */
        .center-box {
            padding-top: 60px;
            padding-bottom: 80px;
        }

        /* CARD GLASS */
        .card {
            border-radius: 22px;
            background: rgba(234, 226, 228, 0.35); /* Misty */
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 20px 45px rgba(30, 93, 172, 0.35);
        }

        /* LOGO */
        .login-logo {
            width: 160px;
            max-width: 100%;
            filter: drop-shadow(0 6px 10px rgba(30, 93, 172, 0.45));
        }

        /* JUDUL */
        .card h4 {
            color: var(--white);
            font-weight: 700;
            letter-spacing: 0.6px;
        }

        /* LABEL */
        .form-label {
            color: var(--soft);
            font-weight: 500;
        }

        /* INPUT */
        .form-control {
            border-radius: 12px;
            padding: 12px 14px;
            border: none;
            background: rgba(255, 255, 255, 0.95);
        }

        .form-control:focus {
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.25);
        }

        /* BUTTON */
        .btn-primary {
            background: linear-gradient(
                135deg,
                var(--primary),
                var(--secondary)
            );
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(
                135deg,
                var(--secondary),
                var(--primary)
            );
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 93, 172, 0.45);
        }

        /* LINK */
        .text-center.small a {
            color: var(--accent);
            text-decoration: none;
            transition: 0.2s;
        }

        .text-center.small a:hover {
            color: var(--white);
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-start center-box">
        <div class="card p-4" style="max-width: 420px; width: 100%;">

            <div class="text-center mb-3">
                <img src="logo.png" alt="Urban Hype Logo" class="login-logo" />
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

                <button type="submit" class="btn btn-primary w-100 mb-3">
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
