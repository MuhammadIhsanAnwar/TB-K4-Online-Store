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

    <!-- ===== CSS TAMBAHAN SAJA (LOGIKA TIDAK DIUBAH) ===== -->
    <style>
        body {
            background: url("bg regis.jpg");
            font-family: 'Poppins', sans-serif;
            position: relative;
            min-height: 100vh;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(4px);
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

        .card h4 {
            color: #fff;
            font-weight: 700;
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
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #122752, #1b3b79);
            transform: translateY(-2px);
        }

        .text-center a {
            color: #e5e5e5;
            text-decoration: none;
        }

        .text-center a:hover {
            color: #fff;
            font-weight: 600;
        }
    </style>
    <!-- ===== AKHIR CSS ===== -->

</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" style="max-width: 380px; width:100%;">
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
