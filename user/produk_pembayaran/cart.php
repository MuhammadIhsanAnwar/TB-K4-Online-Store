<?php
session_start();
include "../../admin/koneksi.php";

// Ambil user ID
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../user/login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$total = 0;

// Proses update quantity
if (isset($_POST['update_qty'])) {
    $product_id = intval($_POST['product_id']);
    $new_quantity = intval($_POST['quantity']);

    if ($new_quantity <= 0) {
        $new_quantity = 1;
    }

    // Cek stok produk
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

// Proses hapus dari keranjang
if (isset($_POST['remove'])) {
    $product_id = intval($_POST['id']);
    $delete_query = "DELETE FROM keranjang WHERE user_id='$user_id' AND product_id='$product_id'";
    mysqli_query($koneksi, $delete_query);
    header("Location: cart.php");
    exit;
}

// Proses checkout (hanya produk yang dipilih)
if (isset($_POST['checkout_selected'])) {
    $selected_products = isset($_POST['selected_products']) ? $_POST['selected_products'] : [];

    if (empty($selected_products)) {
        $error_msg = "Pilih minimal satu produk untuk checkout!";
    } else {
        // Simpan selected products ke session untuk dibawa ke halaman checkout
        $_SESSION['checkout_items'] = $selected_products;
        header("Location: checkout.php");
        exit;
    }
}

// Ambil data keranjang dari database
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

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1E5DAC;
            --beige: #E8D3C1;
            --alley: #B7C5DA;
            --misty: #EAE2E4;
            --dark: #1a1a1a;
            --gray: #666;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--misty) 0%, #fff 100%);
            min-height: 100vh;
            color: var(--dark);
            line-height: 1.6;
        }

        .cart-container {
            max-width: 1200px;
            margin: 4rem auto;
            padding: 0 2rem;
            margin-top: 100px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeInDown 0.6s ease;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .page-header p {
            color: var(--gray);
            font-size: 1.1rem;
        }

        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(30, 93, 172, 0.1);
            animation: fadeIn 0.6s ease;
        }

        .empty-cart-icon {
            font-size: 4rem;
            color: var(--alley);
            margin-bottom: 1rem;
        }

        .empty-cart h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .empty-cart p {
            color: var(--gray);
            margin-bottom: 2rem;
        }

        .cart-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(30, 93, 172, 0.1);
            animation: fadeInUp 0.6s ease;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, var(--primary) 0%, #164a8a 100%);
            color: white;
        }

        thead th {
            padding: 1.5rem 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid var(--misty);
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: var(--misty);
            transform: scale(1.01);
        }

        tbody td {
            padding: 1.5rem 1.5rem;
            vertical-align: middle;
        }

        .product-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.05rem;
        }

        .price {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .qty-badge {
            background: var(--alley);
            color: var(--primary);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }

        .subtotal {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .remove-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .remove-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }

        .remove-btn:active {
            transform: translateY(0);
        }

        .total-row {
            background: linear-gradient(135deg, var(--beige) 0%, #f5e8dc 100%);
            font-size: 1.3rem;
        }

        .total-row td {
            padding: 2rem 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .cart-actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            animation: fadeInUp 0.6s ease 0.2s both;
        }

        .btn {
            padding: 1rem 2.5rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-block;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #164a8a 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 93, 172, 0.4);
        }

        .btn-secondary {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-secondary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .qty-btn-cart {
            background: var(--primary);
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: 700;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn-cart:hover {
            background: #164a8a;
            transform: scale(1.1);
        }

        .qty-input-cart {
            width: 50px;
            padding: 0.4rem 0.5rem;
            border: 1px solid var(--primary);
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .qty-input-cart:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.1);
        }

        .qty-btn-save {
            background: #28a745;
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn-save:hover {
            background: #218838;
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .container {
                margin-top: 80px;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            table {
                font-size: 0.9rem;
            }

            thead th,
            tbody td {
                padding: 1rem;
            }

            .cart-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
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
        }

        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAllStatus);
        });

        function updateSelectAllStatus() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            const selectAllCheckbox = document.getElementById('selectAll');
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
        }

        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateTotal();
        }

        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
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
                    // Ambil harga dari text (format: "Rp XXX.XXX.XXX")
                    const price = parseInt(priceText.replace(/[^\d]/g, ''));
                    const qty = parseInt(qtyInput.value);
                    const subtotal = price * qty;
                    newTotal += subtotal;
                }
            });

            // Update display total
            document.getElementById('totalPrice').textContent = newTotal.toLocaleString('id-ID');
        }

        function validateCheckout() {
            const checkedItems = document.querySelectorAll('.product-checkbox:checked');

            if (checkedItems.length === 0) {
                alert('Pilih minimal satu produk untuk checkout!');
                return false;
            }

            // Buat hidden inputs untuk produk yang dicentang
            const form = document.getElementById('checkoutForm');

            // Hapus hidden inputs lama jika ada
            form.querySelectorAll('input[name="selected_products[]"]').forEach(el => el.remove());

            // Tambahkan hidden inputs untuk produk yang dicentang
            checkedItems.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_products[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });

            return true;
        }

        // Update total saat page load
        updateTotal();
    </script>
</body>

</html>