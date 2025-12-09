<?php require 'auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
</head>

<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">Admin Panel</span>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="fw-bold">Dashboard</h2>
    <hr>

    <div class="row g-3">

        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Total Produk</h5>
                <p class="fs-3 fw-bold text-primary">– nanti dinamis –</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>User Login</h5>
                <p class="fs-3 fw-bold text-success">Admin</p>
            </div>
        </div>

    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
