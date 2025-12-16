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
            --soft:#f4f6f9;
            --white:#ffffff;
            --border:#e5e7eb;
            --text:#1f2937;
        }

        body{
            background:var(--soft);
            font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
            color:var(--text);
        }

        /* ================= SIDEBAR ================= */
        .sidebar{
            position:fixed;
            top:0; left:0;
            width:220px;
            height:100vh;
            background:linear-gradient(180deg,var(--primary),#143d73);
            padding:18px 0;
            display:flex;
            flex-direction:column;
        }

        .sidebar-logo{
            display:flex;
            justify-content:center;
            margin-bottom:12px;
        }

        .sidebar-logo img{
            width:120px;
            height:auto;
        }

        .sidebar h5{
            color:#fff;
            font-size:13px;
            font-weight:600;
            text-transform:uppercase;
            opacity:.8;
            padding:0 20px 10px;
        }

        .menu-item{
            display:block;
            padding:11px 20px;
            margin:3px 0;
            color:#fff;
            text-decoration:none;
            font-size:14px;
            opacity:.85;
            border-left:4px solid transparent;
            transition:.25s ease;
        }

        .menu-item:hover{
            background:rgba(255,255,255,.12);
            opacity:1;
        }

        /* MENU AKTIF (dashboard) */
        .menu-item[href*="dashboard"]{
            background:rgba(255,255,255,.18);
            border-left:4px solid #fff;
            font-weight:700;
            opacity:1;
        }

        .logout{
            margin-top:auto;
            color:#ffd2d2!important;
        }

        /* ================= NAVBAR ================= */
        .navbar{
            position:fixed;
            top:0;
            left:220px;
            right:0;
            height:56px;
            background:var(--white)!important;
            border-bottom:1px solid var(--border);
        }

        /* ================= CONTENT ================= */
        .content{
            margin-left:220px;
            padding:80px 32px 32px;
        }

        .content h2{
            font-size:22px;
            font-weight:600;
            color:var(--primary);
        }

        hr{
            border-top:2px solid var(--border);
        }

        /* ================= CARD ================= */
        .card{
            border:none;
            border-radius:18px;
            padding:24px;
            background:var(--white);
            box-shadow:0 10px 28px rgba(0,0,0,.12);
            transition:.25s ease;
        }

        .card:hover{
            transform:translateY(-6px);
            box-shadow:0 18px 38px rgba(0,0,0,.18);
        }

        .card h5{
            font-size:14px;
            font-weight:600;
            color:#6b7280;
        }

        .card p{
            font-size:2.4rem;
            font-weight:800;
            margin:0;
        }
    </style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<!-- NAVBAR TANPA ADMIN PANEL -->
<nav class="navbar navbar-expand fixed-top">
    <div class="container-fluid"></div>
</nav>

<div class="content">
    <h2>Dashboard</h2>
    <hr>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card">
                <h5>Total Produk</h5>
                <p class="text-primary">
                    <?php
                    $res = mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM products");
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
                    $res = mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM akun_user");
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
                    $res = mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM penjualan");
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
