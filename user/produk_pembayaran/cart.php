<?php
session_start();
include "../../admin/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../user/login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$total = 0;

if (isset($_POST['update_qty'])) {
    $product_id = intval($_POST['product_id']);
    $new_quantity = intval($_POST['quantity']);

    if ($new_quantity <= 0) {
        $new_quantity = 1;
    }

    $stok_query = "SELECT stok FROM products WHERE id='$product_id'";
    $stok_result = mysqli_query($koneksi, $stok_query);
    $stok_row = mysqli_fetch_assoc($stok_result);

    if ($new_quantity > $stok_row['stok']) {
        $new_quantity = $stok_row['stok'];
    }

    $update_query = "UPDATE keranjang SET quantity='$new_quantity' WHERE user_id='$user_id' AND product_id='$product_id'";
    mysqli_query($koneksi, $update_query);
    header("Location: cart.php");
    exit;
}

if (isset($_POST['remove'])) {
    $product_id = intval($_POST['id']);
    $delete_query = "DELETE FROM keranjang WHERE user_id='$user_id' AND product_id='$product_id'";
    mysqli_query($koneksi, $delete_query);
    header("Location: cart.php");
    exit;
}

if (isset($_POST['checkout_selected'])) {
    $selected_products = isset($_POST['selected_products']) ? $_POST['selected_products'] : [];

    if (empty($selected_products)) {
        $error_msg = "Pilih minimal satu produk untuk checkout!";
    } else {
        $_SESSION['checkout_items'] = $selected_products;
        header("Location: checkout.php");
        exit;
    }
}

$cart_query = "SELECT k.*, p.nama, p.harga, p.foto_produk, p.stok FROM keranjang k JOIN products p ON k.product_id = p.id WHERE k.user_id='$user_id'";
$cart_result = mysqli_query($koneksi, $cart_query);
$cart = [];
while ($row = mysqli_fetch_assoc($cart_result)) {
    $cart[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Urban Hype</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../images/icon/logo.png">
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <link rel="stylesheet" href="../css_user/css_produk_pembayaran/cart.css">
    <link rel="stylesheet" href="../css_user/navbar.css">
</head>

<body>
    <?php include "../../navbar.php"; ?>
    <div class="cart-container">
        <div class="page-header">
            <h1>Shopping Cart</h1>
            <p>Review your items before checkout</p>
        </div>

        <?php if (!$cart): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h3>Your Cart is Empty</h3>
                <p>Looks like you haven't added anything to your cart yet.</p>
                <a href="shop.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-table">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $item):
                            $subtotal = $item['harga'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_products[]" value="<?php echo $item['product_id']; ?>" class="product-checkbox">
                                </td>
                                <td class="product-name"><?php echo htmlspecialchars($item['nama']); ?></td>
                                <td class="price">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <form method="POST" style="display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                                        <button type="submit" name="decrease_qty" class="qty-btn-cart">−</button>
                                        <input type="number" name="quantity" class="qty-input-cart" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stok']; ?>">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" name="update_qty" class="qty-btn-save">✓</button>
                                    </form>
                                </td>
                                <td class="subtotal">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                <td>
                                    <form method="POST" style="margin: 0;">
                                        <input type="hidden" name="id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" name="remove" class="remove-btn">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;">Total</td>
                            <td colspan="3">Rp <span id="totalPrice"><?php echo number_format($total, 0, ',', '.'); ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form method="POST" id="checkoutForm" style="margin-top: 2rem;">
                <div class="cart-actions">
                    <a href="shop.php" class="btn btn-secondary">Continue Shopping</a>
                    <button type="submit" name="checkout_selected" class="btn btn-primary" onclick="return validateCheckout()">Proceed to Checkout</button>
                </div>
            </form> <?php endif; ?>
    </div>
    <script>
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateTotal();
        }

        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                // Jangan preventDefault - biarkan event bubble ke dropdown
                updateSelectAllStatus();
                updateTotal();
            });
        });

        function updateSelectAllStatus() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            const selectAllCheckbox = document.getElementById('selectAll');
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
        }

        function updateTotal() {
            let newTotal = 0;
            const rows = document.querySelectorAll('table tbody tr');

            rows.forEach((row, index) => {
                const checkbox = row.querySelector('.product-checkbox');
                const priceText = row.querySelector('.price')?.textContent || '';
                const qtyInput = row.querySelector('.qty-input-cart');

                if (checkbox && checkbox.checked && priceText && qtyInput) {
                    const price = parseInt(priceText.replace(/[^\d]/g, ''));
                    const qty = parseInt(qtyInput.value);
                    const subtotal = price * qty;
                    newTotal += subtotal;
                }
            });

            document.getElementById('totalPrice').textContent = newTotal.toLocaleString('id-ID');
        }

        function validateCheckout() {
            const checkedItems = document.querySelectorAll('.product-checkbox:checked');

            if (checkedItems.length === 0) {
                alert('Pilih minimal satu produk untuk checkout!');
                return false;
            }

            const form = document.getElementById('checkoutForm');

            form.querySelectorAll('input[name="selected_products[]"]').forEach(el => el.remove());

            checkedItems.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_products[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });

            return true;
        }

        // Initialize total on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });
    </script>
</body>

</html>