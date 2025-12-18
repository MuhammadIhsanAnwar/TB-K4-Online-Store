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
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --danger: #ef4444;
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
            z-index: 1000;
        }

        .logo-box {
            text-align: center;
            padding: 10px 0 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
            margin-top: 12px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 4px 12px;
            border-radius: 10px;
            transition: .25s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, .18);
            transform: translateX(5px);
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
            margin-bottom: 12px;
            margin-left: 12px;
            margin-right: 12px;
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
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #6b7280;
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        hr {
            border-top: 2px solid #cfd6e6;
            margin-bottom: 30px;
            display: none;
        }

        /* ================= CARD GRID ================= */
        .cards-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .card {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: 18px;
            padding: 1.8rem;
            background: var(--white);
            box-shadow: 0 4px 15px rgba(0, 0, 0, .08);
            transition: all .3s ease;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(30, 93, 172, .15);
        }

        /* Bulatan dekoratif bergerak */
        .card::after {
            content: "";
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            top: -40px;
            right: -40px;
            opacity: 0.08;
            animation: floatRotate 8s linear infinite;
            z-index: 0;
        }

        .card.primary::after {
            background: var(--primary);
        }

        .card.success::after {
            background: var(--success);
        }

        .card.warning::after {
            background: var(--warning);
        }

        .card.info::after {
            background: var(--info);
        }

        .card.danger::after {
            background: var(--danger);
        }

        @keyframes floatRotate {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(-8px, 8px) rotate(90deg);
            }

            50% {
                transform: translate(0, 16px) rotate(180deg);
            }

            75% {
                transform: translate(8px, 8px) rotate(270deg);
            }

            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }

        /* Card Content */
        .card-content {
            position: relative;
            z-index: 1;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1rem;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .card.primary .card-icon {
            background: rgba(30, 93, 172, 0.1);
            color: var(--primary);
        }

        .card.success .card-icon {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .card.warning .card-icon {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .card.info .card-icon {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }

        .card.danger .card-icon {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .card h5 {
            color: #6b7280;
            font-weight: 600;
            font-size: 0.95rem;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-value {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 800;
            margin-top: 0.5rem;
        }

        .card.primary .card-value {
            color: var(--primary);
        }

        .card.success .card-value {
            color: var(--success);
        }

        .card.warning .card-value {
            color: var(--warning);
        }

        .card.info .card-value {
            color: var(--info);
        }

        .card.danger .card-value {
            color: var(--danger);
        }

        .card-footer {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
            font-size: 0.85rem;
            color: #9ca3af;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .content {
                margin-left: 70px;
                padding: 15px;
            }

            .menu-title,
            .sidebar a span {
                display: none;
            }

            .sidebar a {
                justify-content: center;
                padding: 12px 10px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .cards-wrapper {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .card {
                padding: 1.5rem;
            }

            .card-value {
                font-size: 2rem;
            }
        }

        @media (max-width: 600px) {
            .content {
                padding: 10px;
            }

            h2 {
                font-size: 1.3rem;
            }

            .subtitle {
                font-size: 0.9rem;
            }

            .card {
                padding: 1.2rem;
            }

            .card-value {
                font-size: 1.8rem;
            }

            .card-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h2>📊 Dashboard</h2>
        <p class="subtitle">Ringkasan Data Bisnis Anda</p>

        <div class="cards-wrapper">
            <!-- Total Produk -->
            <div class="card primary">
                <div class="card-content">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <h5>📦 Total Produk</h5>
                        </div>
                    </div>
                    <p class="card-value">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM products");
                        $total_produk = mysqli_fetch_assoc($res)['total'];
                        echo $total_produk;
                        ?>
                    </p>
                    <div class="card-footer">Produk aktif di toko</div>
                </div>
            </div>

            <!-- Total User -->
            <div class="card success">
                <div class="card-content">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h5>👥 Total User</h5>
                        </div>
                    </div>
                    <p class="card-value">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM akun_user");
                        $total_user = mysqli_fetch_assoc($res)['total'];
                        echo $total_user;
                        ?>
                    </p>
                    <div class="card-footer">Pengguna terdaftar</div>
                </div>
            </div>

            <!-- Pesanan Masuk -->
            <div class="card info">
                <div class="card-content">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <div>
                            <h5>📬 Pesanan Masuk</h5>
                        </div>
                    </div>
                    <p class="card-value">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pemesanan");
                        $pesanan_masuk = mysqli_fetch_assoc($res)['total'];
                        echo $pesanan_masuk;
                        ?>
                    </p>
                    <div class="card-footer">Pesanan yang belum selesai</div>
                </div>
            </div>

            <!-- Total Penjualan -->
            <div class="card warning">
                <div class="card-content">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div>
                            <h5>💰 Total Penjualan</h5>
                        </div>
                    </div>
                    <p class="card-value">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM history_penjualan");
                        $total_penjualan = mysqli_fetch_assoc($res)['total'];
                        echo $total_penjualan;
                        ?>
                    </p>
                    <div class="card-footer">Transaksi selesai</div>
                </div>
            </div>

            <!-- Komentar -->
            <div class="card danger">
                <div class="card-content">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <div>
                            <h5>💬 Komentar</h5>
                        </div>
                    </div>
                    <p class="card-value">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM komentar");
                        $total_komentar = mysqli_fetch_assoc($res)['total'] ?? 0;
                        echo $total_komentar;
                        ?>
                    </p>
                    <div class="card-footer">Ulasan produk dari pelanggan</div>
                </div>
            </div>

            <!-- Data Pesan -->
            <div class="card primary">
                <div class="card-content">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div>
                            <h5>✉️ Data Pesan</h5>
                        </div>
                    </div>
                    <p class="card-value">
                        <?php
                        $res = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesan");
                        $total_pesan = mysqli_fetch_assoc($res)['total'] ?? 0;
                        echo $total_pesan;
                        ?>
                    </p>
                    <div class="card-footer">Pesan dari pengunjung</div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>