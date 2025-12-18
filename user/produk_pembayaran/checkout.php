<?php
session_start();
include "../../admin/koneksi.php";
$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    header("Location: ../../index.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Urban Hype</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #EAE2E4 0%, #B7C5DA 50%, #1E5DAC 100%);
            min-height: 100vh;
            padding-bottom: 60px;
        }

        /* Navbar styling with Urban Hype branding */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1.2rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(30, 93, 172, 0.1);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #1E5DAC;
            text-decoration: none;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #2c3e50;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            position: relative;
            padding: 0.5rem 0;
            transition: color 0.3s ease;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #1E5DAC;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(-50%);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a:hover {
            color: #1E5DAC;
        }

        /* Checkout container with elegant card design */
        .checkout-container {
            max-width: 900px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .checkout-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            color: #fff;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        }

        .checkout-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
        }

        .checkout-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(30, 93, 172, 0.2);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Order summary section */
        .order-summary {
            margin-bottom: 2.5rem;
        }

        .order-summary h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: #1E5DAC;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #E8D3C1;
        }

        .order-items {
            margin-bottom: 2rem;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(183, 197, 218, 0.3);
            transition: background 0.3s ease;
        }

        .order-item:hover {
            background: rgba(183, 197, 218, 0.1);
            padding-left: 1rem;
            margin-left: -1rem;
            padding-right: 1rem;
            margin-right: -1rem;
            border-radius: 8px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.3rem;
        }

        .item-qty {
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        .item-price {
            font-weight: 600;
            color: #1E5DAC;
            font-size: 1.1rem;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
            margin-top: 1.5rem;
            border-top: 3px solid #1E5DAC;
        }

        .order-total .label {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .order-total .amount {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: #1E5DAC;
        }

        /* Payment form styling */
        .payment-section {
            margin-bottom: 2rem;
        }

        .payment-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #E8D3C1;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1E5DAC;
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        /* Checkout button with premium styling */
        .checkout-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 1.2rem 2.5rem;
            border: none;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1E5DAC 0%, #2868bd 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 93, 172, 0.4);
        }

        .btn-secondary {
            background: #fff;
            color: #1E5DAC;
            border: 2px solid #1E5DAC;
        }

        .btn-secondary:hover {
            background: #1E5DAC;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
        }

        /* Security badge */
        .security-badge {
            text-align: center;
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(30, 93, 172, 0.05);
            border-radius: 10px;
        }

        .security-badge p {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin: 0;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .nav-links {
                gap: 1.5rem;
            }

            .checkout-header h1 {
                font-size: 2rem;
            }

            .checkout-card {
                padding: 2rem 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .checkout-actions {
                flex-direction: column-reverse;
            }

            .order-total .label {
                font-size: 1.2rem;
            }

            .order-total .amount {
                font-size: 1.6rem;
            }
        }

        @media (max-width: 480px) {
            .nav-container {
                padding: 0 1rem;
            }

            .logo {
                font-size: 1.4rem;
            }

            .nav-links {
                gap: 1rem;
                font-size: 0.85rem;
            }

            .checkout-container {
                padding: 0 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar with Urban Hype branding -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">URBAN HYPE</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="checkout.php">Checkout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Checkout content with elegant card design -->
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Secure Checkout</h1>
            <p>Complete your order with confidence</p>
        </div>

        <div class="checkout-card">
            <!-- Order summary section -->
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="order-items">
                    <?php foreach ($cart as $item): ?>
                        <div class="order-item">
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item['nama']); ?></div>

                                <div class="item-qty">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <button type="button" class="qty-btn" onclick="updateQty(this, -1)">−</button>
                                        <input type="number" class="qty-input" value="<?php echo $item['qty']; ?>" min="1" data-product="<?php echo $item['id']; ?>" onchange="updateQuantity(this)">
                                        <button type="button" class="qty-btn" onclick="updateQty(this, 1)">+</button>
                                    </div>
                                </div>

                                <div class="item-price">
                                    $<?php echo number_format($item['harga'] * $item['qty'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                        <div class="order-total">
                            <span class="label">Total Amount:</span>
                            <span class="amount">$<?php echo number_format($total, 2); ?></span>
                        </div>
                </div>

                <!-- Payment form section -->
                <form method="POST">
                    <div class="payment-section">
                        <h3>Shipping Information</h3>
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" id="fullname" name="fullname" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Shipping Address</label>
                            <textarea id="address" name="address" rows="3" required></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            <div class="form-group">
                                <label for="zipcode">ZIP Code</label>
                                <input type="text" id="zipcode" name="zipcode" required>
                            </div>
                        </div>
                    </div>

                    <div class="payment-section">
                        <h3>Payment Method</h3>
                        <div class="form-group">
                            <label for="payment">Select Payment Method</label>
                            <select id="payment" name="payment" required>
                                <option value="">Choose payment method</option>
                                <option value="credit">Credit Card</option>
                                <option value="debit">Debit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="checkout-actions">
                        <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                        <button type="submit" name="checkout" class="btn btn-primary">Complete Payment</button>
                    </div>

                    <!-- Security badge -->
                    <div class="security-badge">
                        <p>🔒 Your payment information is secure and encrypted</p>
                    </div>
                </form>
            </div>
        </div>
</body>

</html>