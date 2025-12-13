<?php
include "../admin/koneksi.php";

$token = $_GET['token'];

// Ambil token
$cek = mysqli_query($koneksi, "SELECT * FROM reset_password WHERE token='$token'");
$data = mysqli_fetch_assoc($cek);

if (!$data) {
    die("Token tidak valid!");
}

// Cek expired
if (strtotime($data['expired']) < time()) {
    die("Token sudah kedaluwarsa!");
}

$email = $data['email'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Password Baru</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="max-width:380px; width:100%;">
            <h4 class="text-center mb-3">Password Baru</h4>

            <form action="proses_reset_password.php" method="POST">
                <input type="hidden" name="email" value="<?= $email ?>">

                <div class="mb-3">
                    <label>Password Baru</label>
                    <input type="password" name="password" class="form-control"
                        required
                        minlength="8"
                        pattern="(?=.[a-z])(?=.[A-Z])(?=.*\d).{8,}"
                        title="Password harus ada huruf kecil, huruf besar, dan angka (min 8 karakter)">
                </div>

                <button class="btn btn-success w-100">Update Password</button>
            </form>

        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>