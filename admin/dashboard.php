<?php require 'auth_check.php'; ?>
<?php include '../admin/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">

    <style>
        :root{
            --primary:#1E5DAC;
            --secondary:#B7C5DA;
            --accent:#E8D3C1;
            --soft:#EAE2E4;
            --white:#ffffff;
        }

        body{
            background:var(--soft);
            font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
        }

        /* ================= SIDEBAR ================= */
        .sidebar{
            height:100vh;
            width:220px;
            position:fixed;
            top:0;left:0;
            background:linear-gradient(180deg,var(--primary),#143d73);
            padding:20px 0;
            box-shadow:4px 0 20px rgba(30,93,172,.25);
            display:flex;
            flex-direction:column;
        }

        .sidebar h5{
            color:#fff;
            font-size:18px;
            font-weight:700;
            text-align:center;
            padding:14px 0;
            margin:0 16px 22px;
            border-radius:16px;
            background:rgba(255,255,255,.18);
            letter-spacing:.6px;
        }

        .sidebar a{
            display:block;
            padding:12px 18px;
            margin:6px 14px;
            color:#fff;
            text-decoration:none;
            border-radius:14px;
            font-weight:500;
            opacity:.9;
            position:relative;
            overflow:hidden;
            transition:.3s ease;
        }

        .sidebar a::after{
            content:"";
            position:absolute;
            inset:0;
            background:rgba(255,255,255,.22);
            opacity:0;
            transition:.3s ease;
        }

        .sidebar a:hover{
            transform:translateX(6px);
            opacity:1;
        }

        .sidebar a:hover::after{opacity:1;}

        /* MENU AKTIF DASHBOARD */
        .sidebar a[href*="dashboard"]{
            background:rgba(255,255,255,.28);
            box-shadow:inset 4px 0 0 #fff;
            opacity:1;
        }

        /* LOGOUT */
        .sidebar a.text-danger{
            margin-top:auto;
            text-align:center;
            color:#ffb3b3!important;
            font-weight:600;
        }
        .sidebar a.text-danger:hover{
            background:rgba(255,107,107,.28);
            transform:none;
        }

        /* ================= NAVBAR ================= */
        .navbar{
            height:64px;
            margin-left:220px;
            background:linear-gradient(90deg,#fff,#f4f6fb)!important;
            box-shadow:0 8px 25px rgba(30,93,172,.15);
            display:flex;
            align-items:center;
        }

        .navbar-brand{
            font-size:16px;
            font-weight:700;
            color:#fff!important;
            background:linear-gradient(135deg,var(--primary),#143d73);
            padding:8px 22px;
            border-radius:30px;
            box-shadow:0 6px 16px rgba(30,93,172,.4);
            transition:.3s ease;
        }
        .navbar-brand:hover{
            transform:translateY(-1px);
            box-shadow:0 12px 26px rgba(30,93,172,.45);
        }

        /* ================= CONTENT ================= */
        .content{
            margin-left:230px;
            padding:90px 30px 30px;
        }

        .content h2{color:var(--primary);}

        hr{
            border-top:2px solid var(--secondary);
            opacity:.6;
        }

        /* ================= CARD ================= */
        .card{
            border:none;
            border-radius:22px;
            padding:24px;
            background:var(--white);
            box-shadow:0 15px 35px rgba(30,93,172,.18);
            position:relative;
            overflow:hidden;
            transition:.35s ease;
        }

        .card::after{
            content:"";
            position:absolute;
            width:120px;height:120px;
            top:-40px;right:-40px;
            border-radius:50%;
            background:var(--accent);
            opacity:.35;
        }

        .card:hover{
            transform:translateY(-10px) scale(1.01);
            box-shadow:0 24px 48px rgba(30,93,172,.3);
        }

        .card h5{
            font-weight:600;
            color:#5f6f86;
            position:relative;
            padding-bottom:8px;
        }

        .card h5::after{
            content:"";
            position:absolute;
            left:0;bottom:0;
            width:40px;height:3px;
            background:var(--primary);
            border-radius:2px;
        }

        .card p{
            margin:0;
            font-size:2.6rem;
            font-weight:800;
        }

        .text-primary{color:var(--primary)!important;}
        .text-success{color:#6c8db6!important;}
        .text-warning{color:#c79a78!important;}
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
                    $res=mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM products");
                    echo mysqli_fetch_assoc($res)['total'];
                    ?>
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <h5>Total User</h5>
                <p class="text-success">
                    <?php
                    $res=mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM akun_user");
                    echo mysqli_fetch_assoc($res)['total'];
                    ?>
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <h5>Total Penjualan</h5>
                <p class="text-warning">
                    <?php
                    $res=mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM penjualan");
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
