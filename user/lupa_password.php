<?php
// Form minta email reset
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ================= URBANHYPE RESET PASSWORD ================= */

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
            background-image: url("bg reset.jpeg");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            font-family: "Poppins", sans-serif;
            position: relative;
        }

        /* OVERLAY WARNA (SAMA SEMUA PAGE) */
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

        /* POSISI KONSISTEN */
        .center-box {
            padding-top: 60px;
            padding-bottom: 80px;
        }

        /* CARD GLASS */
        .card {
            border-radius: 22px;
            background: rgba(234, 226, 228, 0.35);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 20px 45px rgba(30, 93, 172, 0.35);
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
        .text-center a {
            color: var(--accent);
            text-decoration: none;
            transition: 0.2s;
        }

        .text-center a:hover {
            color: var(--white);
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-start center-box">
        <div class="card p-4" style="max-width: 420px; width: 100%;">

            <h4 class="text-center mb-3">Reset Password</h4>

            <form action="proses_kirim_reset.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Masukkan Email Anda</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Kirim Link Reset
                </button>
            </form>

            <div class="text-center small mt-3">
                <a href="login_user.php">Kembali ke Login</a>
            </div>

        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
