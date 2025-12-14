<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Pengguna</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet" />

    <style>
        /* ================= URBANHYPE LOGIN ================= */

        html, body {
            height: 100%;
        }

        body.bg-light {
            background-image: url("background 2.jpg");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            font-family: "Poppins", sans-serif;
            position: relative;
        }

        body.bg-light::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(30, 93, 172, 0.45),
                rgba(183, 197, 218, 0.35)
            );
            z-index: -1;
        }

        /* CARD SAJA YANG GLASS */
        .card {
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 20px 45px rgba(30, 93, 172, 0.35);
        }

        /* LOGO */
        .login-logo {
            width: 160px;
            max-width: 100%;
            filter: drop-shadow(0 6px 10px rgba(30, 93, 172, 0.45));
        }

        /* Judul */
        .card h4 {
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

        /* Button */
        .btn-primary {
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
        }

        .btn-primary:hover {
            background: linear-gradient(
                135deg,
                #B7C5DA,
                #1E5DAC
            );
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 93, 172, 0.45);
        }

        /* Link */
        .text-center.small a {
            color: #eef3ff !important;
            text-decoration: none;
            transition: 0.2s;
        }

        .text-center.small a:hover {
            color: #ffffff !important;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <div class="container-fluid d-flex justify-content-center align-items-start align-items-md-center flex-grow-1 pt-5 pt-md-0">
        <div class="card p-4" style="max-width: 380px; width: 100%">

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
