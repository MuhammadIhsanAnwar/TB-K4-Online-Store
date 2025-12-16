<?php require 'auth_check.php'; ?>
<?php include '../admin/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<title>Data User</title>
<link rel="stylesheet" href="../css/bootstrap.css">

<style>
:root{
    --primary:#1e5dac;
    --bg:#f3eded;
    --white:#ffffff;
    --text:#1f2937;
}

body{
    margin:0;
    background:var(--bg);
    font-family:Poppins,system-ui,sans-serif;
}

/* ================= SIDEBAR (SAMA DENGAN DASHBOARD) ================= */
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

/* LOGOUT PALING BAWAH + MERAH */
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
    animation: fade .5s ease; /* ANIMASI HALUS */
}

/* ================= ANIMASI HALUS ================= */
@keyframes fade{
    from{opacity:0; transform:translateY(10px);}
    to{opacity:1; transform:translateY(0);}
}

h2{
    color:var(--primary);
    font-weight:700;
}

hr{
    border-top:2px solid #cfd6e6;
    margin-bo
