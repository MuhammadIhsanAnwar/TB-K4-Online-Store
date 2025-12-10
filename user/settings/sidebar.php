<?php
?>
<link href="../../css/bootstrap.min.css" rel="stylesheet">
<style>
    .sidebar {
        width: 230px;
        height: 100vh;
        background: #212529;
        color: #fff;
        position: fixed;
        left: 0;
        top: 0;
        padding-top: 20px;
    }

    .sidebar a {
        display: block;
        padding: 12px 18px;
        color: #ddd;
        text-decoration: none;
        font-size: 15px;
    }

    .sidebar a:hover {
        background: #343a40;
        color: #fff;
    }

    .sidebar .title {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 15px;
    }

    .content {
        margin-left: 230px;
        padding: 25px;
    }
</style>

<div class="sidebar">
    <div class="title">Settings</div>

    <a href="settings.php?menu=profil">🧑 Edit Profil</a>
    <a href="settings.php?menu=payment">💳 Metode Pembayaran</a>
    <a href="settings.php?menu=lain">⚙ Pengaturan Lainnya</a>
</div>