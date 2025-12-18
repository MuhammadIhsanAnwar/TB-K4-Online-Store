<?php require 'auth_check.php'; ?>
<?php include '../admin/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">

    <style>
        :root {
            --primary: #1e5dac;
            --bg: #f3eded;
            --white: #ffffff;
        }

        body {
            margin: 0;
            background: var(--bg);
            font-family: Poppins, system-ui, sans-serif;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100vh;
            background: linear-gradient(180deg, #1e63b6, #0f3f82);
            padding: 18px 0;
            display: flex;
            flex-direction: column;
        }

        .logo-box {
            text-align: center;
            padding: 10px 0 18px;
        }

        .logo-box img {
            width: 72px;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, .25));
            transition: .3s ease;
        }

        .logo-box img:hover {
            transform: scale(1.05);
        }

        .menu-title {
            color: #dbe6ff;
            font-size: 13px;
            padding: 8px 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 4px 12px;
            border-radius: 10px;
            transition: .25s;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, .18);
        }

        .sidebar a.active {
            background: rgba(255, 255, 255, .32);
            font-weight: 600;
        }

        .sidebar .logout {
            margin-top: auto;
            background: rgba(255, 80, 80, .15);
            color: #ffd6d6 !important;
            font-weight: 600;
            text-align: center;
            border-radius: 14px;
            transition: .3s ease;
        }

        .sidebar .logout:hover {
            background: #ff4d4d;
            color: #fff !important;
            box-shadow: 0 10px 25px rgba(255, 77, 77, .6);
            transform: translateY(-2px);
        }

        /* ================= CONTENT ================= */
        .content {
            margin-left: 220px;
            padding: 30px;
            animation: fade .5s ease;
            min-height: 100vh;
        }

        @keyframes fade {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            color: var(--primary);
            font-weight: 700;
        }

        hr {
            border-top: 2px solid #cfd6e6;
            margin-bottom: 30px;
        }

        /* ================= CARD ================= */
        .row.g-4 {
            display: flex;
            flex-wrap: nowrap;
            /* tetap 1 baris */
            gap: 1rem;
        }

        .card {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: 22px;
            padding: 20px;
            background: var(--white);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .12);
            transition: all .3s ease;
            flex: 1;
            max-width: 300px;
            /* supaya card tidak terlalu lebar */
        }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 55px rgba(30, 93, 172, .25);
        }

        /* Bulatan dekoratif bergerak */
        .card::after {
            content: "";
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            top: -30px;
            right: -30px;
            background: #f3eadd;
            opacity: 0.6;
            animation: floatRotate 8s linear infinite;
        }

        @keyframes floatRotate {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(-5px, 5px) rotate(90deg);
            }

            50% {
                transform: translate(0, 10px) rotate(180deg);
            }

            75% {
                transform: translate(5px, 5px) rotate(270deg);
            }

            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }

        /* Typography */
        .card h5 {
            color: #6b7280;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card p {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 800;
        }

        /* Tetap gunakan warna asli */
        .text-primary {
            color: #2563eb !important;
        }

        .text-success {
            color: #16a34a !important;
        }

        .text-warning {
            color: #d97706 !important;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h2>Dashboard</h2>
        <hr>

        <div class="row g-4">
            <div class="col">
                <div class="card">
                    <h5><i class="bi bi-box-seam"></i> Total Produk</h5>
                    <p class="text-primary">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM products");
                        echo mysqli_fetch_assoc($res)['total'];
                        ?>
                    </p>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <h5><i class="bi bi-people"></i> Total User</h5>
                    <p class="text-success">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM akun_user");
                        echo mysqli_fetch_assoc($res)['total'];
                        ?>
                    </p>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <h5><i class="bi bi-cart-check"></i> Total Penjualan</h5>
                    <p class="text-warning">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM penjualan");
                        echo mysqli_fetch_assoc($res)['total'];
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>