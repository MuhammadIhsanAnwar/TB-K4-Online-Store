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

    <!-- ===== CSS TEMA SAMA ===== -->
    <style>
        body {
            background: url("bg regis.jpg") no-repeat center center/cover;
            font-family: 'Poppins', sans-serif;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(4px);
            z-index: -1;
        }

        .card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
            padding: 35px 30px;
            transition: 0.25s ease;
        }

        .card:hover {
            transform: scale(1.02);
        }

        h4 {
            color: #fff;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            color: #eee;
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

        .btn-success {
            margin-top: 10px;
            background: linear-gradient(to right, #0a1a3f, #112d63);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: .5px;
            transition: 0.25s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            background: linear-gradient(to right, #122752, #1b3b79);
        }
    </style>
    <!-- ========================== -->

</head>

<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card" style="max-width:380px; width:100%;">
        <h4>Password Baru</h4>

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