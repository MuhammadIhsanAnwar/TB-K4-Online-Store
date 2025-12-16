<?php
require 'auth_check.php';
include '../admin/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Penjualan - Admin</title>
<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
:root{
    --primary:#1e5dac;
    --bg:#f3eded;
    --white:#ffffff;
    --hover-blue: rgba(30,93,172,.1);
    --text:#1f2937;
}

/* ====== BODY ====== */
body{
    margin:0;
    font-family:Poppins,system-ui,sans-serif;
    background:var(--bg);
    animation:fadePage 0.5s ease;
}

/* ====== SIDEBAR (Dashboard Style) ====== */
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

/* ====== CONTENT ====== */
.content{
    margin-left:220px;
    padding:40px;
    animation:fadePage 0.5s ease;
}

@keyframes fadePage{
    from{opacity:0; transform:translateY(10px);}
    to{opacity:1; transform:translateY(0);}
}

h2{
    color:var(--primary);
    font-weight:700;
    margin-bottom:8px;
}

hr{
    border-top:2px solid #cfd6e6;
    margin-bottom:20px;
    opacity:.6;
}

/* ====== TABLE CONTAINER ====== */
.table-container{
    background: var(--white);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 18px 45px rgba(0,0,0,.15);
    overflow-x:auto;
    animation:fadeTable 0.5s ease;
}

@keyframes fadeTable{
    from{opacity:0; transform:translateY(5px);}
    to{opacity:1; transform:translateY(0);}
}

/* ====== TABLE ====== */
.table{
    border-collapse: separate !important;
    border-spacing: 0 10px;
    width:100%;
}

.table thead tr{
    background: var(--primary);
    color: #fff;
    border-radius: 12px;
}

.table tbody tr{
    background: #fff;
    transition: .3s;
}

.table tbody tr:hover{
    background: var(--hover-blue);
}

.table td, .table th{
    vertical-align: middle;
    padding: 12px;
    text-align:center;
}

.table td:nth-child(2),
.table td:nth-child(3){
    text-align:left;
}
</style>
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Data Penjualan</h2>
    <hr>

    <div class="table-container">
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID User</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($koneksi, "SELECT * FROM penjualan ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['user_id']}</td>
                        <td>{$row['produk']}</td>
                        <td>{$row['jumlah']}</td>
                        <td>{$row['total_harga']}</td>
                        <td>{$row['tanggal']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
