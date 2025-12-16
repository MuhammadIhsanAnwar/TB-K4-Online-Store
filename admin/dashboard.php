<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
:root{
    --primary:#1e5dac;
    --success:#16a34a;
    --warning:#d97706;
    --bg:#f3eded;
    --card-bg: rgba(255,255,255,0.15);
}

body{
    margin:0;
    background: linear-gradient(135deg, #e0f7ff, #ffffff);
    font-family:Poppins,system-ui,sans-serif;
}

/* ================= SIDEBAR ================= */
.sidebar{
    position:fixed;
    top:0; left:0;
    width:220px;
    height:100vh;
    background:linear-gradient(180deg,#1e63b6,#0f3f82);
    padding:18px 0;
    display:flex;
    flex-direction:column;
}

.logo-box{
    text-align:center;
    padding:10px 0 18px;
}

.logo-box img{
    width:72px;
    filter:drop-shadow(0 6px 12px rgba(0,0,0,.25));
    transition:.3s ease;
}

.logo-box img:hover{
    transform:scale(1.05);
}

.menu-title{
    color:#dbe6ff;
    font-size:13px;
    padding:8px 20px;
}

.sidebar a{
    color:white;
    text-decoration:none;
    padding:12px 20px;
    margin:4px 12px;
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

.sidebar .logout{
    margin-top:auto;
    background:rgba(255,80,80,.15);
    color:#ffd6d6!important;
    font-weight:600;
    text-align:center;
    border-radius:14px;
    transition:.3s ease;
}

.sidebar .logout:hover{
    background:#ff4d4d;
    color:#fff!important;
    box-shadow:0 10px 25px rgba(255,77,77,.6);
    transform:translateY(-2px);
}

/* ================= CONTENT ================= */
.content{
    margin-left:220px;
    padding:30px;
    min-height:100vh;
}

h2{
    color:var(--primary);
    font-weight:700;
}

hr{
    border-top:2px solid #cfd6e6;
    margin-bottom:30px;
}

/* ================= CARD ================= */
.row.g-4{
    gap: 1.5rem;
}

.card{
    position:relative;
    overflow:hidden;
    border:none;
    border-radius:22px;
    padding:26px;
    background: var(--card-bg);
    backdrop-filter: blur(10px);
    box-shadow:0 18px 45px rgba(0,0,0,.1);
    transition:.3s;
}

.card:hover{
    transform:translateY(-10px) scale(1.02);
    box-shadow:0 30px 65px rgba(30,93,172,.35);
}

/* Bulatan dekoratif */
.card::after{
    content:"";
    position:absolute;
    width:120px; height:120px;
    border-radius:50%;
    top:-40px; right:-40px;
    background: linear-gradient(135deg,#f3eadd,#a3c4f3);
    opacity:.7;
    animation: rotate 6s linear infinite;
}

@keyframes rotate{
    from{transform:rotate(0deg);}
    to{transform:rotate(360deg);}
}

/* Typography */
.card h5{
    color:#fff;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:8px;
}

.card p{
    margin:0;
    font-size:2.8rem;
    font-weight:800;
    background: linear-gradient(90deg,#1e5dac,#2563eb);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Color variations */
.text-primary{color:#1e5dac!important;}
.text-success{color:#16a34a!important;}
.text-warning{color:#d97706!important;}
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Dashboard</h2>
    <hr>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card">
                <h5><i class="bi bi-box-seam"></i> Total Produk</h5>
                <p class="text-primary">123</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <h5><i class="bi bi-people"></i> Total User</h5>
                <p class="text-success">56</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <h5><i class="bi bi-cart-check"></i> Total Penjualan</h5>
                <p class="text-warning">78</p>
            </div>
        </div>
    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
