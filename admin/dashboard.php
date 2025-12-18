<?php require 'auth_check.php'; ?>
<?php include '../admin/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="css_admin/dashboard_style.css?v=3">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>📊 Dashboard</h2>
    <p class="subtitle">Ringkasan Data Bisnis Anda</p>

    <div class="cards-wrapper">

        <?php
        $cards = [
            ['primary','bi-box-seam','📦 Total Produk',"SELECT COUNT(*) total FROM products"],
            ['success','bi-people','👥 Total User',"SELECT COUNT(*) total FROM akun_user"],
            ['info','bi-inbox','📬 Pesanan Masuk',"SELECT COUNT(*) total FROM pemesanan"],
            ['warning','bi-cart-check','💰 Total Penjualan',"SELECT COUNT(*) total FROM history_penjualan"],
            ['danger','bi-chat-dots','💬 Komentar',"SELECT COUNT(*) total FROM komentar"],
            ['primary','bi-envelope','✉️ Data Pesan',"SELECT COUNT(*) total FROM pesan"]
        ];

        foreach($cards as $c){
            $res = mysqli_query($koneksi,$c[3]);
            $val = mysqli_fetch_assoc($res)['total'] ?? 0;
            echo "
            <div class='card {$c[0]}'>
                <div class='card-content'>
                    <div class='card-header'>
                        <div class='card-icon'><i class='bi {$c[1]}'></i></div>
                        <h5>{$c[2]}</h5>
                    </div>
                    <p class='card-value'>{$val}</p>
                    <div class='card-footer'>Data tersimpan</div>
                </div>
            </div>";
        }
        ?>

    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
