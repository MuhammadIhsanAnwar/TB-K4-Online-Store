<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    header("Location: index.php");
    exit;
}

// Contoh checkout sederhana
$total = 0;
foreach ($cart as $item) {
    $total += $item['harga'] * $item['qty'];
}

// Setelah checkout, bisa kosongkan keranjang
if (isset($_POST['checkout'])) {
    $_SESSION['cart'] = [];
    echo "<script>alert('Pembayaran berhasil!'); window.location='index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <link href="css/bootstrap.css" rel="stylesheet">
</head>
<body class="p-5">
<div class="container">
    <h2>Pembayaran</h2>
    <p>Total yang harus dibayar: <strong>$<?php echo number_format($total,2); ?></strong></p>
    <form method="POST">
        <button type="submit" name="checkout" class="btn btn-success">Bayar Sekarang</button>
    </form>
</div>
</body>
</html>
