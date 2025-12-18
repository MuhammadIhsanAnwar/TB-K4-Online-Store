<?php
session_start();
include "../../admin/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../user/login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$user_query = "SELECT * FROM akun_user WHERE id='$user_id'";
$user_result = mysqli_query($koneksi, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Ambil selected products atau semua keranjang
$selected_products = isset($_SESSION['checkout_items']) ? $_SESSION['checkout_items'] : [];

if (empty($selected_products)) {
    header("Location: cart.php");
    exit;
}

// Ambil detail produk yang akan dibeli
$placeholders = implode(',', array_map('intval', $selected_products));
$cart_query = "SELECT k.*, p.nama, p.harga FROM keranjang k 
               JOIN products p ON k.product_id = p.id 
               WHERE k.user_id='$user_id' AND k.product_id IN ($placeholders)";
$cart_result = mysqli_query($koneksi, $cart_query);
$cart = [];
$total = 0;
while ($row = mysqli_fetch_assoc($cart_result)) {
    $cart[] = $row;
    $total += $row['harga'] * $row['quantity'];
}

// Proses complete payment
if (isset($_POST['complete_payment'])) {
    $nama_lengkap = $user['nama_lengkap'];
    $alamat_lengkap = $user['provinsi'] . ', ' . $user['kabupaten_kota'] . ', ' .
        $user['kecamatan'] . ', ' . $user['kelurahan_desa'] . ', ' .
        $user['kode_pos'] . ' - ' . $user['alamat'];
    $nomor_hp = $user['nomor_hp'];
    $metode_pembayaran = 'COD';
    $kurir = mysqli_real_escape_string($koneksi, $_POST['kurir']);
    $waktu_pemesanan = date('Y-m-d H:i:s');

    // Insert setiap produk ke tabel pemesanan
    foreach ($cart as $item) {
        $nama_produk = mysqli_real_escape_string($koneksi, $item['nama']);
        $quantity = $item['quantity'];

        $order_query = "INSERT INTO pemesanan 
                        (user_id, nama_lengkap, alamat_lengkap, nomor_hp, nama_produk, quantity, metode_pembayaran, kurir, waktu_pemesanan)
                        VALUES ('$user_id', '$nama_lengkap', '$alamat_lengkap', '$nomor_hp', '$nama_produk', '$quantity', '$metode_pembayaran', '$kurir', '$waktu_pemesanan')";

        if (!mysqli_query($koneksi, $order_query)) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membuat pesanan: ' . mysqli_error($koneksi)]);
            exit;
        }
    }

    // Hapus dari keranjang setelah berhasil
    $delete_query = "DELETE FROM keranjang WHERE user_id='$user_id' AND product_id IN ($placeholders)";
    mysqli_query($koneksi, $delete_query);

    // Hapus session checkout items
    unset($_SESSION['checkout_items']);

    echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil dibuat']);
    exit;
}

// Gabungkan alamat lengkap
$alamat_lengkap = $user['provinsi'] . ', ' . $user['kabupaten_kota'] . ', ' .
    $user['kecamatan'] . ', ' . $user['kelurahan_desa'] . ', ' .
    $user['kode_pos'] . ' - ' . $user['alamat'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Urban Hype</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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
            padding: 2rem;
        }

        .checkout-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .checkout-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            color: #fff;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        }

        .checkout-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .checkout-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #1E5DAC;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #B7C5DA;
        }

        .product-list {
            margin-bottom: 2rem;
        }

        .product-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-info h4 {
            color: #1E5DAC;
            margin-bottom: 0.5rem;
        }

        .product-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .product-price {
            font-weight: 600;
            color: #1E5DAC;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            color: #1E5DAC;
            margin-bottom: 0.5rem;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #B7C5DA;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #1E5DAC;
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.1);
        }

        input[type="text"]:disabled {
            background: #f0f0f0;
            cursor: not-allowed;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .kurir-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .kurir-option {
            padding: 1rem;
            border: 2px solid #B7C5DA;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .kurir-option:hover {
            border-color: #1E5DAC;
            background: rgba(30, 93, 172, 0.05);
        }

        .kurir-option input[type="radio"] {
            width: auto;
            margin-right: 0.5rem;
        }

        .order-summary {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.3rem;
            font-weight: 700;
            color: #1E5DAC;
            padding-top: 1rem;
            border-top: 2px solid #1E5DAC;
        }

        .btn-complete {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #1E5DAC 0%, #164a8a 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 2rem;
        }

        .btn-complete:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 93, 172, 0.3);
        }

        .btn-complete:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }

            .kurir-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>🛒 Checkout</h1>
            <p>Selesaikan pemesanan Anda</p>
        </div>

        <div class="checkout-content">
            <!-- LEFT SIDE: Order Details -->
            <form id="checkoutForm">
                <!-- ORDER SUMMARY -->
                <div class="checkout-card">
                    <h3 class="card-title">Produk yang Dipesan</h3>
                    <div class="product-list">
                        <?php foreach ($cart as $item): ?>
                            <div class="product-item">
                                <div class="product-info">
                                    <h4><?php echo htmlspecialchars($item['nama']); ?></h4>
                                    <p>Qty: <?php echo $item['quantity']; ?> × Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                                </div>
                                <div class="product-price">
                                    Rp <?php echo number_format($item['harga'] * $item['quantity'], 0, ',', '.'); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SHIPPING INFORMATION -->
                <div class="checkout-card">
                    <h3 class="card-title">Informasi Pengiriman</h3>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea disabled><?php echo htmlspecialchars($alamat_lengkap); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Nomor HP</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['nomor_hp']); ?>" disabled>
                    </div>
                </div>

                <!-- PAYMENT METHOD -->
                <div class="checkout-card">
                    <h3 class="card-title">Metode Pembayaran</h3>
                    <div class="form-group">
                        <label>Pilih Metode Pembayaran</label>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px; text-align: center;">
                            <input type="radio" name="payment_method" value="COD" checked disabled>
                            <label style="display: inline; margin-left: 0.5rem; font-weight: 600;">💵 Cash on Delivery (COD)</label>
                        </div>
                    </div>
                </div>

                <!-- COURIER SELECTION -->
                <div class="checkout-card">
                    <h3 class="card-title">Pilih Kurir Pengiriman</h3>
                    <div class="kurir-options">
                        <label class="kurir-option">
                            <input type="radio" name="kurir" value="JNE" required>
                            <div>📦 JNE</div>
                        </label>
                        <label class="kurir-option">
                            <input type="radio" name="kurir" value="JNT" required>
                            <div>📦 JNT</div>
                        </label>
                        <label class="kurir-option">
                            <input type="radio" name="kurir" value="ID Express" required>
                            <div>📦 ID Express</div>
                        </label>
                        <label class="kurir-option">
                            <input type="radio" name="kurir" value="Si Cepat" required>
                            <div>📦 Si Cepat</div>
                        </label>
                        <label class="kurir-option">
                            <input type="radio" name="kurir" value="Pos Indonesia" required>
                            <div>📦 Pos Indonesia</div>
                        </label>
                    </div>
                </div>
            </form>

            <!-- RIGHT SIDE: Order Summary -->
            <div class="order-summary">
                <h3 class="card-title">Ringkasan Pesanan</h3>

                <?php foreach ($cart as $item): ?>
                    <div class="summary-item">
                        <span><?php echo htmlspecialchars($item['nama']); ?> (×<?php echo $item['quantity']; ?>)</span>
                        <span>Rp <?php echo number_format($item['harga'] * $item['quantity'], 0, ',', '.'); ?></span>
                    </div>
                <?php endforeach; ?>

                <div class="summary-total">
                    <span>Total:</span>
                    <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                </div>

                <button type="button" class="btn-complete" onclick="completePayment()">
                    ✓ Selesaikan Pesanan
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function completePayment() {
            const kurir = document.querySelector('input[name="kurir"]:checked');

            if (!kurir) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Kurir',
                    text: 'Silakan pilih kurir pengiriman terlebih dahulu',
                    confirmButtonColor: '#1E5DAC'
                });
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi Pesanan',
                text: 'Pesanan tidak bisa dibatalkan setelah dikonfirmasi. Lanjutkan?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1E5DAC',
                cancelButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitOrder();
                }
            });
        }

        function submitOrder() {
            const formData = new FormData(document.getElementById('checkoutForm'));
            formData.append('complete_payment', '1');

            fetch('checkout.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pesanan Berhasil!',
                            text: 'Pesanan Anda telah dibuat. Terima kasih telah berbelanja!',
                            confirmButtonColor: '#1E5DAC'
                        }).then(() => {
                            window.location.href = '../../index.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message,
                            confirmButtonColor: '#1E5DAC'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada sistem',
                        confirmButtonColor: '#1E5DAC'
                    });
                });
        }
    </script>
</body>

</html>