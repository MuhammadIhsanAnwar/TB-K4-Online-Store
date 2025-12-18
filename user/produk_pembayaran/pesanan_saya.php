<?php
session_start();
include "../../admin/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../user/login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Proses pembatalan pesanan
if (isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);

    // Cek apakah pesanan milik user dan belum dikirim
    $check_query = "SELECT status FROM pemesanan WHERE id='$order_id' AND user_id='$user_id'";
    $check_result = mysqli_query($koneksi, $check_query);
    $order = mysqli_fetch_assoc($check_result);

    if ($order && $order['status'] === 'Sedang Dikemas') {
        // Update status ke batal
        $update_query = "UPDATE pemesanan SET status='Pesanan Batal' WHERE id='$order_id'";

        if (mysqli_query($koneksi, $update_query)) {
            echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil dibatalkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membatalkan pesanan']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Pesanan tidak bisa dibatalkan (sudah dikirim atau lebih)']);
    }
    exit;
}

// Ambil parameter filter status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'semua';

// Query dasar
$query = "SELECT * FROM pemesanan WHERE user_id='$user_id'";

// Filter berdasarkan status
if ($status_filter !== 'semua') {
    $query .= " AND status='" . mysqli_real_escape_string($koneksi, $status_filter) . "'";
}

$query .= " ORDER BY waktu_pemesanan DESC";

$result = mysqli_query($koneksi, $query);
$pesanan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pesanan[] = $row;
}

// Hitung pesanan per status
$status_counts = [
    'Sedang Dikemas' => 0,
    'Sedang Dikirim' => 0,
    'Selesai' => 0,
    'Pesanan Batal' => 0
];

$count_query = "SELECT status, COUNT(*) as count FROM pemesanan WHERE user_id='$user_id' GROUP BY status";
$count_result = mysqli_query($koneksi, $count_query);
while ($row = mysqli_fetch_assoc($count_result)) {
    if (isset($status_counts[$row['status']])) {
        $status_counts[$row['status']] = $row['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Urban Hype</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../images/icon/logo.png">

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

        .orders-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .orders-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .orders-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            color: #fff;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 1rem;
        }

        .status-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .status-tab {
            padding: 0.8rem 1.5rem;
            border: 2px solid #B7C5DA;
            border-radius: 25px;
            background: white;
            cursor: pointer;
            font-weight: 600;
            color: #1E5DAC;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-tab:hover {
            background: #B7C5DA;
            color: white;
        }

        .status-tab.active {
            background: #1E5DAC;
            color: white;
            border-color: #1E5DAC;
        }

        .status-count {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .order-card:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .order-info {
            flex: 1;
        }

        .order-id {
            font-size: 0.9rem;
            color: #999;
            margin-bottom: 0.5rem;
        }

        .order-date {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .order-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-dikemas {
            background: #fff3cd;
            color: #856404;
        }

        .status-dikirim {
            background: #cfe2ff;
            color: #084298;
        }

        .status-selesai {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-batal {
            background: #f8d7da;
            color: #842029;
        }

        .order-items {
            margin-bottom: 1rem;
        }

        .order-item {
            padding: 0.8rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-details h4 {
            color: #1E5DAC;
            margin-bottom: 0.3rem;
        }

        .item-details p {
            color: #666;
            font-size: 0.9rem;
            margin: 0.2rem 0;
        }

        .item-price {
            font-weight: 700;
            color: #1E5DAC;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 2px solid #f0f0f0;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .order-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1E5DAC;
        }

        .order-actions {
            display: flex;
            gap: 0.8rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-cancel {
            background: #dc3545;
            color: white;
        }

        .btn-cancel:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .btn-track {
            background: #1E5DAC;
            color: white;
        }

        .btn-track:hover {
            background: #164390;
            transform: scale(1.05);
        }

        .btn-disabled {
            background: #ccc;
            color: #999;
            cursor: not-allowed;
        }

        .btn-disabled:hover {
            transform: none;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 15px;
            color: #999;
        }

        .empty-state h3 {
            color: #1E5DAC;
            margin-bottom: 1rem;
        }

        .empty-state a {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.8rem 1.5rem;
            background: #1E5DAC;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .empty-state a:hover {
            background: #164390;
        }

        @media (max-width: 768px) {
            .orders-header h1 {
                font-size: 2rem;
            }

            .status-tabs {
                flex-direction: column;
                align-items: stretch;
            }

            .status-tab {
                justify-content: center;
            }

            .order-header {
                flex-direction: column;
            }

            .order-footer {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-actions {
                width: 100%;
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
    <?php include '../../navbar.php'; ?>

    <div class="orders-container">
        <div class="orders-header">
            <h1>📦 Pesanan Saya</h1>
        </div>

        <!-- Status Tabs -->
        <div class="status-tabs">
            <a href="?status=semua" class="status-tab <?php echo $status_filter === 'semua' ? 'active' : ''; ?>">
                <span>Semua</span>
                <span class="status-count"><?php echo array_sum($status_counts); ?></span>
            </a>
            <a href="?status=Sedang%20Dikemas" class="status-tab <?php echo $status_filter === 'Sedang Dikemas' ? 'active' : ''; ?>">
                <span>Sedang Dikemas</span>
                <span class="status-count"><?php echo $status_counts['Sedang Dikemas']; ?></span>
            </a>
            <a href="?status=Sedang%20Dikirim" class="status-tab <?php echo $status_filter === 'Sedang Dikirim' ? 'active' : ''; ?>">
                <span>Sedang Dikirim</span>
                <span class="status-count"><?php echo $status_counts['Sedang Dikirim']; ?></span>
            </a>
            <a href="?status=Selesai" class="status-tab <?php echo $status_filter === 'Selesai' ? 'active' : ''; ?>">
                <span>Selesai</span>
                <span class="status-count"><?php echo $status_counts['Selesai']; ?></span>
            </a>
            <a href="?status=Pesanan%20Batal" class="status-tab <?php echo $status_filter === 'Pesanan Batal' ? 'active' : ''; ?>">
                <span>Pesanan Batal</span>
                <span class="status-count"><?php echo $status_counts['Pesanan Batal']; ?></span>
            </a>
        </div>

        <!-- Orders List -->
        <div class="orders-list">
            <?php if (empty($pesanan)) : ?>
                <div class="empty-state">
                    <h3>Belum Ada Pesanan</h3>
                    <p>Anda belum memiliki pesanan <?php echo $status_filter !== 'semua' ? 'dengan status ' . $status_filter : ''; ?>.</p>
                    <a href="shop.php">Lanjut Belanja</a>
                </div>
            <?php else : ?>
                <?php foreach ($pesanan as $order) : ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-id">Order ID: #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                <div class="order-date">Tanggal Pesanan: <?php echo date('d M Y, H:i', strtotime($order['waktu_pemesanan'])); ?></div>
                            </div>
                            <span class="order-status status-<?php echo strtolower(str_replace(' ', '', $order['status'])); ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </div>

                        <div class="order-items">
                            <div class="order-item">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($order['nama_produk']); ?></h4>
                                    <p>Kuantitas: <strong><?php echo $order['quantity']; ?></strong></p>
                                    <p>Kurir: <strong><?php echo htmlspecialchars($order['kurir']); ?></strong></p>
                                    <p>Metode Pembayaran: <strong><?php echo htmlspecialchars($order['metode_pembayaran']); ?></strong></p>
                                </div>
                            </div>
                        </div>

                        <div class="order-footer">
                            <div>
                                <div class="order-total">Total: Rp <?php echo number_format($order['quantity'] * 100000, 0, ',', '.'); ?></div>
                                <p style="color: #666; font-size: 0.9rem; margin-top: 0.5rem;">Alamat: <?php echo htmlspecialchars($order['alamat_lengkap']); ?></p>
                            </div>
                            <div class="order-actions">
                                <?php if ($order['status'] === 'Sedang Dikemas') : ?>
                                    <button class="btn btn-cancel" onclick="cancelOrder(<?php echo $order['id']; ?>)">Batalkan Pesanan</button>
                                <?php else : ?>
                                    <button class="btn btn-disabled" disabled>Tidak Bisa Dibatalkan</button>
                                <?php endif; ?>
                                <button class="btn btn-track" onclick="viewOrderDetail(<?php echo $order['id']; ?>)">Rincian</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function cancelOrder(orderId) {
            Swal.fire({
                icon: 'warning',
                title: 'Batalkan Pesanan?',
                text: 'Apakah Anda yakin ingin membatalkan pesanan ini? Tindakan ini tidak bisa dibatalkan.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Jangan',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#1E5DAC'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('cancel_order', '1');
                    formData.append('order_id', orderId);

                    fetch('pesanan_saya.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Pesanan berhasil dibatalkan',
                                    confirmButtonColor: '#1E5DAC'
                                }).then(() => {
                                    location.reload();
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
            });
        }

        function viewOrderDetail(orderId) {
            Swal.fire({
                icon: 'info',
                title: 'Rincian Pesanan',
                text: 'Fitur detail pesanan akan segera tersedia',
                confirmButtonColor: '#1E5DAC'
            });
        }
    </script>
</body>

</html>