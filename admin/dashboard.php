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
    --primary:#1e5dac;
    --sidebar:#1c56a3;
    --bg:#efe9ea;
    --white:#fff;
}

body{
    margin:0;
    background:var(--bg);
    font-family:Poppins,system-ui,sans-serif;
}

/* ================= SIDEBAR ================= */
.sidebar{
    position:fixed;
    top:0; left:0;
    width:220px;
    height:100vh;
    background:linear-gradient(180deg,#1e63b6,#123e7a);
    padding:20px 0;
    display:flex;
    flex-direction:column;
}

.menu-title{
    color:#e5ecff;
    font-size:14px;
    padding:10px 20px;
    margin-bottom:10px;
}

.sidebar a{
    color:white;
    text-decoration:none;
    padding:12px 20px;
    margin:4px 14px;
    border-radius:10px;
    transition:.25s;
}

.sidebar a:hover{
    background:rgba(255,255,255,.18);
}

.sidebar a.active{
    background:rgba(255,255,255,.32);
    font-weight:600;
}

.logout{
    margin-top:auto;
    color:#ffb3b3!important;
}

/* ================= CONTENT ================= */
.content{
    margin-left:220px;
    padding:30px;
}

h2{
    color:var(--primary);
    font-weight:700;
}

hr{
    border-top:2px solid #cbd3e2;
    margin-bottom:30px;
}

/* ================= CARD MODEL GAMBAR ================= */
.stat-card{
    background:var(--white);
    border-radius:20px;
    padding:28px;
    box-shadow:0 18px 40px rgba(0,0,0,.15);
}

.stat-card h5{
    color:#6b7280;
    font-weight:600;
    margin-bottom:12px;
}

.stat-card .number{
    font-size:2.8rem;
    font-weight:800;
}

.blue{ color:#2563eb; }
.green{ color:#16a34a; }
.orange{ color:#d97706; }
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Dashboard</h2>
    <hr>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-card">
                <h5>Total Produk</h5>
                <div class="number blue">
                    <?php
                    $res=mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM products");
                    echo mysqli_fetch_assoc($res)['total'];
                    ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <h5>Total User</h5>
                <div class="number green">
                    <?php
                    $res=mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM akun_user");
                    echo mysqli_fetch_assoc($res)['total'];
                    ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <h5>Total Penjualan</h5>
                <div class="number orange">
                    <?php
                    $res=mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM penjualan");
                    echo mysqli_fetch_assoc($res)['total'];
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
