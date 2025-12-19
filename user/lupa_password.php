<?php
// Form minta email reset
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password</title>
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css_user/lupa_password.css">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-start center-box">
        <div class="card p-4" style="max-width: 420px; width: 100%;">

            <div class="text-center mb-3">
                <img src="../images/icon/logo.png" alt="Urban Hype Logo" class="login-logo" style="max-width: 160px; height: auto;" />
            </div>

            <h4 class="text-center mb-3">Reset Password</h4>

            <form action="proses_kirim_reset.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Masukkan Email Anda</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>

                <button type="submit" class="btn btn-reset w-100">
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