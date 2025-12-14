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

        html, body {
            height: 100%;
        }

        body.bg-light {
            background-image: url("bg reset.jpeg");
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

        .card {
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 20px 45px rgba(30, 93, 172, 0.35);
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
        .text-center a {
            color: #eef3ff;
            text-decoration: none;
            transition: 0.2s;
        }

        .text-center a:hover {
            color: #ffffff;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <div class="container-fluid d-flex justify-content-center align-items-start align-items-md-center flex-grow-1 pt-5 pt-md-0">
        <div class="card p-4" style="max-width: 380px; width:100%;">

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
