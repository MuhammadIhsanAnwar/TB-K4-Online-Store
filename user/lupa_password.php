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
                <button type="submit" class="btn btn-primary w-100">Kirim Link Reset</button>
            </form>

            <div class="text-center small mt-3">
                <a href="login.php" class="text-decoration-none">Kembali ke Login</a>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
