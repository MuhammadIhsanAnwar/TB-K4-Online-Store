<?php require 'auth_check.php'; ?>
<?php include '../admin/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <style>
        :root {
            --primary: #1E5DAC;
            --secondary: #B7C5DA;
            --accent: #E8D3C1;
            --soft: #EAE2E4;
            --dark: #0f1e33;
            --white: #ffffff;
        }

        body {
            background: var(--soft);
            font-family: Poppins, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            background: linear-gradient(180deg, var(--primary), #143d73);
            padding: 20px 0; /* DIUBAH: menu naik ke atas */
            box-shadow: 4px 0 20px rgba(30, 93, 172, 0.25);
            display: flex;
            flex-direction: column;
        }

        .sidebar a {
            display: block;
            padding: 12px 18px;
            margin: 6px 12px;
            color: #fff;
            text-decoration: none;
            border-radius: 14px;
            font-weight: 500;
            transition: .3s ease;
            opacity: .9;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, .2);
            transform: translateX(4px);
            opacity: 1;
        }

        /* ================= NAVBAR ================= */
        .navbar {
            background: var(--white) !important;
            margin-left: 220px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            height: 60px;
        }

        .navbar-brand {
            color: var(--primary) !important;
            font-weight: 700;
            letter-spacing: .4px;
        }

        /* ================= CONTENT ================= */
        .content {
            margin-left: 230px;
            padding: 90px 30px 30px;
        }

        .content h2 {
            color: var(--primary);
        }

        hr {
            border-top: 2px solid var(--secondary);
            opacity: .6;
        }

        /* ================= STAT CARD ================= */
        .card {
            border: none;
            border-radius: 22px;
            padding: 24px;
            background: var(--white);
            box-shadow: 0 15px 35px rgba(30, 93, 172, 0.18);
            transition: .3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .card::after {
            content: "";
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            top: -40px;
            right: -40px;
            background: var(--accent);
            opacity: .35;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 22px 45px rgba(30, 93, 172, 0.28);
        }

        .card h5 {
            font-weight: 600;
            color: #5f6f86;
        }

        .card p {
            margin: 0;
            font-size: 2.6rem;
            font-weight: 800;
        }

        .text-primary { color: var(--primary) !important; }
        .text-success { color: #6c8db6 !important; }
        .text-warning { color: #c79a78 !important; }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <nav class="navbar navbar-expand fixed-top">
        <div class="container-fluid">
            <span class="navbar-brand">Admin Panel</span>
        </div>
    </nav>

    <div class="content">
        <h2 class="fw-bold">Dashboard</h2>
        <hr>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card">
                    <h5>Total Produk</h5>
                    <p class="text-primary">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM products");
                        $row = mysqli_fetch_assoc($res);
                        echo $row['total'];
                        ?>
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <h5>Total User</h5>
                    <p class="text-success">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM akun_user");
                        $row = mysqli_fetch_assoc($res);
                        echo $row['total'];
                        ?>
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <h5>Total Penjualan</h5>
                    <p class="text-warning">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penjualan");
                        $row = mysqli_fetch_assoc($res);
                        echo $row['total'];
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
