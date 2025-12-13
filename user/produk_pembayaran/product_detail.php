<?php
session_start();
include "admin/koneksi.php";

$id = $_GET['id'] ?? 0;
$query = mysqli_query($koneksi, "SELECT * FROM products WHERE id='$id'");
$product = mysqli_fetch_assoc($query);

if (!$product) {
    die("Produk tidak ditemukan!");
}

// Tambah ke keranjang
if (isset($_POST['add_to_cart'])) {
    $cart_item = [
        'id' => $product['id'],
        'nama' => $product['nama'],
        'harga' => $product['harga'],
        'gambar' => $product['gambar'],
        'qty' => $_POST['qty']
    ];

    // Jika session cart kosong, buat baru
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    // Cek apakah produk sudah ada di keranjang
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $cart_item['id']) {
            $item['qty'] += $cart_item['qty'];
            $found = true;
            break;
        }
    }
    if (!$found) $_SESSION['cart'][] = $cart_item;

    header("Location: cart.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product['nama']; ?></title>
    <link href="css/bootstrap.css" rel="stylesheet">
</head>
<body class="p-5">

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <img src="foto_produk/<?php echo $product['gambar']; ?>" class="img-fluid rounded">
        </div>
        <div class="col-md-6">
            <h3><?php echo $product['nama']; ?></h3>
            <p><?php echo strtoupper($product['kategori']); ?></p>
            <p>$<?php echo number_format($product['harga'], 2); ?></p>
            <p><?php echo $product['deskripsi']; ?></p>

            <form method="POST">
                <input type="number" name="qty" value="1" min="1" class="form-control w-25 mb-3">
                <button type="submit" name="add_to_cart" class="btn btn-success">Tambah ke Keranjang</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
