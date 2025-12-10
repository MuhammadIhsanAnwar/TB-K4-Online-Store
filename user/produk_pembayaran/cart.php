<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
$total = 0;

if (isset($_POST['remove'])) {
    $id = $_POST['id'];
    foreach ($cart as $key => $item) {
        if ($item['id'] == $id) unset($cart[$key]);
    }
    $_SESSION['cart'] = $cart;
    header("Location: cart.php");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Keranjang</title>
    <link href="css/bootstrap.css" rel="stylesheet">
</head>

<body class="p-5">

    <div class="container">
        <h2>Keranjang Belanja</h2>
        <?php if (!$cart): ?>
            <p>Keranjang kosong.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
                <?php foreach ($cart as $item):
                    $subtotal = $item['harga'] * $item['qty'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td><?php echo $item['nama']; ?></td>
                        <td>$<?php echo number_format($item['harga'], 2); ?></td>
                        <td><?php echo $item['qty']; ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="remove" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end fw-bold">Total</td>
                    <td colspan="2" class="fw-bold">$<?php echo number_format($total, 2); ?></td>
                </tr>
            </table>
            <a href="checkout.php" class="btn btn-success">Lanjut ke Pembayaran</a>
        <?php endif; ?>
    </div>

</body>

</html>