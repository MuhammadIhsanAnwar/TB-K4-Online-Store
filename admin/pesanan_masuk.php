<?php
session_start();
require_once 'koneksi.php';

// Cek login admin SETELAH koneksi berhasil
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Proses update status ke sedang dikemas
if (isset($_POST['update_status_dikemas'])) {
    $order_id = intval($_POST['order_id']);

    $update_query = "UPDATE pemesanan SET status='Sedang Dikemas' WHERE id='$order_id'";
    if (mysqli_query($koneksi, $update_query)) {
        echo json_encode(['status' => 'success', 'message' => 'Status diperbarui menjadi Sedang Dikemas']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui status']);
    }
    exit;
}
// Proses update status ke sedang dikemas
if (isset($_POST['update_status_dikemas'])) {
    $order_id = intval($_POST['order_id']);

    $update_query = "UPDATE pemesanan SET status='Sedang Dikemas' WHERE id='$order_id'";
    if (mysqli_query($koneksi, $update_query)) {
        echo json_encode(['status' => 'success', 'message' => 'Status diperbarui menjadi Sedang Dikemas']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui status']);
    }
    exit;
}

// Proses input resi kurir
if (isset($_POST['input_resi'])) {
    $order_id = intval($_POST['order_id']);
    $resi = mysqli_real_escape_string($koneksi, $_POST['resi']);

    if (empty($resi)) {
        echo json_encode(['status' => 'error', 'message' => 'Nomor resi tidak boleh kosong']);
        exit;
    }

    $update_query = "UPDATE pemesanan SET resi='$resi', status='Sedang Dikirim' WHERE id='$order_id'";
    if (mysqli_query($koneksi, $update_query)) {
        echo json_encode(['status' => 'success', 'message' => 'Resi berhasil diinput dan status diperbarui menjadi Sedang Dikirim']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menginput resi']);
    }
    exit;
}

// Ambil semua pesanan
$query = "SELECT * FROM pemesanan ORDER BY waktu_pemesanan DESC";
$result = mysqli_query($koneksi, $query);
$pesanan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pesanan[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk - Admin</title>
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .order-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #1E5DAC;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .order-id {
            font-weight: 600;
            color: #1E5DAC;
            font-size: 1.1rem;
        }

        .order-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-menunggu {
            background: #f3e5f5;
            color: #6a1b9a;
        }

        .status-dikemas {
            background: #fff3cd;
            color: #856404;
        }

        .status-dikirim {
            background: #cfe2ff;
            color: #084298;
        }

        .order-info {
            margin-bottom: 1rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-label {
            font-weight: 600;
            color: #333;
            min-width: 150px;
        }

        .info-value {
            color: #666;
            flex: 1;
            word-break: break-word;
        }

        .order-products {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .products-title {
            font-weight: 600;
            color: #1E5DAC;
            margin-bottom: 0.5rem;
        }

        .btn-action {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
            margin-top: 0.5rem;
        }

        .btn-dikemas {
            background: #ffc107;
            color: white;
        }

        .btn-dikemas:hover {
            background: #e0a800;
            transform: translateY(-2px);
        }

        .btn-resi {
            background: #1E5DAC;
            color: white;
        }

        .btn-resi:hover {
            background: #164a8a;
            transform: translateY(-2px);
        }

        .btn-disabled {
            background: #ccc;
            color: #666;
            cursor: not-allowed;
        }

        .resi-input-group {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .resi-input {
            flex: 1;
            padding: 0.6rem;
            border: 2px solid #1E5DAC;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
        }

        .resi-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.1);
        }

        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .filter-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border: 2px solid #1E5DAC;
            background: white;
            color: #1E5DAC;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: #1E5DAC;
            color: white;
        }

        .filter-btn:hover {
            background: #1E5DAC;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 12px;
            color: #999;
        }

        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-row {
                flex-direction: column;
            }

            .resi-input-group {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="container-fluid">
                <h1 class="page-title">📦 Pesanan Masuk</h1>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-title">Filter Status:</div>
                    <div class="filter-buttons">
                        <button class="filter-btn active" onclick="filterByStatus('semua')">Semua</button>
                        <button class="filter-btn" onclick="filterByStatus('Menunggu Konfirmasi')">Menunggu Konfirmasi</button>
                        <button class="filter-btn" onclick="filterByStatus('Sedang Dikemas')">Sedang Dikemas</button>
                        <button class="filter-btn" onclick="filterByStatus('Sedang Dikirim')">Sedang Dikirim</button>
                    </div>
                </div>

                <!-- Orders List -->
                <div id="ordersList">
                    <?php if (empty($pesanan)): ?>
                        <div class="empty-state">
                            <h3>Tidak ada pesanan</h3>
                            <p>Belum ada pesanan yang masuk.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pesanan as $order): ?>
                            <div class="order-card" data-status="<?php echo htmlspecialchars($order['status']); ?>">
                                <div class="order-header">
                                    <div>
                                        <div class="order-id">Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                        <small style="color: #999;">Tanggal: <?php echo date('d M Y H:i', strtotime($order['waktu_pemesanan'])); ?></small>
                                    </div>
                                    <span class="order-status status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </div>

                                <div class="order-info">
                                    <div class="info-row">
                                        <span class="info-label">Nama Customer:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['nama_lengkap']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Nomor HP:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['nomor_hp']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Alamat:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['alamat_lengkap']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Produk:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['nama_produk']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Kuantitas:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['quantity']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Kurir:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['kurir']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Metode Pembayaran:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['metode_pembayaran']); ?></span>
                                    </div>
                                    <?php if (!empty($order['resi'])): ?>
                                        <div class="info-row">
                                            <span class="info-label">Nomor Resi:</span>
                                            <span class="info-value" style="color: #1E5DAC; font-weight: 600; font-family: monospace;">
                                                <?php echo htmlspecialchars($order['resi']); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Buttons -->
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <?php if ($order['status'] === 'Menunggu Konfirmasi'): ?>
                                        <button class="btn-action btn-dikemas" onclick="updateStatusDikemas(<?php echo $order['id']; ?>)">
                                            ✓ Ubah ke Sedang Dikemas
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($order['status'] === 'Sedang Dikemas' || ($order['status'] === 'Menunggu Konfirmasi')): ?>
                                        <button class="btn-action btn-resi" onclick="showResiForm(<?php echo $order['id']; ?>)">
                                            📮 Input Resi
                                        </button>
                                    <?php endif; ?>

                                    <?php if (empty($order['resi']) && $order['status'] !== 'Menunggu Konfirmasi'): ?>
                                        <button class="btn-action btn-disabled" disabled>
                                            📮 Tunggu Status Dikemas
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Resi Input Form (Hidden by default) -->
                                <div id="resiForm<?php echo $order['id']; ?>" style="display:none; margin-top: 1rem;">
                                    <div class="resi-input-group">
                                        <input type="text"
                                            id="resiInput<?php echo $order['id']; ?>"
                                            class="resi-input"
                                            placeholder="Masukkan nomor resi kurir..."
                                            autocomplete="off">
                                        <button class="btn-resi" onclick="submitResi(<?php echo $order['id']; ?>)">
                                            Kirim Resi
                                        </button>
                                        <button class="btn-action" style="background: #999; color: white;" onclick="cancelResiForm(<?php echo $order['id']; ?>)">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function updateStatusDikemas(orderId) {
            Swal.fire({
                icon: 'warning',
                title: 'Ubah Status?',
                text: 'Ubah status pesanan menjadi "Sedang Dikemas"?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1E5DAC'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('update_status_dikemas', '1');
                    formData.append('order_id', orderId);

                    fetch('pesanan_masuk.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
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
                        });
                }
            });
        }

        function showResiForm(orderId) {
            document.getElementById('resiForm' + orderId).style.display = 'block';
            document.getElementById('resiInput' + orderId).focus();
        }

        function cancelResiForm(orderId) {
            document.getElementById('resiForm' + orderId).style.display = 'none';
            document.getElementById('resiInput' + orderId).value = '';
        }

        function submitResi(orderId) {
            const resi = document.getElementById('resiInput' + orderId).value.trim();

            if (!resi) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nomor Resi Kosong',
                    text: 'Silakan masukkan nomor resi kurir',
                    confirmButtonColor: '#1E5DAC'
                });
                return;
            }

            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Input Resi',
                text: 'Nomor resi: ' + resi + '\nStatus akan berubah menjadi "Sedang Dikirim"',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1E5DAC'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('input_resi', '1');
                    formData.append('order_id', orderId);
                    formData.append('resi', resi);

                    fetch('pesanan_masuk.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
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
                        });
                }
            });
        }

        function filterByStatus(status) {
            const cards = document.querySelectorAll('.order-card');
            const buttons = document.querySelectorAll('.filter-btn');

            // Update button active state
            buttons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.textContent.includes(status) || (status === 'semua' && btn.textContent.includes('Semua'))) {
                    btn.classList.add('active');
                }
            });

            // Filter cards
            cards.forEach(card => {
                if (status === 'semua' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>