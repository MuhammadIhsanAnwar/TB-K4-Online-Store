<?php
session_start();
require 'auth_check.php';
include 'koneksi.php';

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

    <style>
        :root {
            --primary: #1e5dac;
            --bg: #f3eded;
            --white: #ffffff;
        }

        body {
            margin: 0;
            background: var(--bg);
            font-family: Poppins, system-ui, sans-serif;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100vh;
            background: linear-gradient(180deg, #1e63b6, #0f3f82);
            padding: 18px 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .logo-box {
            text-align: center;
            padding: 10px 0 18px;
        }

        .logo-box img {
            width: 72px;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, .25));
            transition: .3s ease;
        }

        .logo-box img:hover {
            transform: scale(1.05);
        }

        .menu-title {
            color: #dbe6ff;
            font-size: 13px;
            padding: 8px 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 4px 12px;
            border-radius: 10px;
            transition: .25s;
            position: relative;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, .18);
        }

        .sidebar a.active {
            background: rgba(255, 255, 255, .32);
            font-weight: 600;
        }

        .sidebar .logout {
            margin-top: auto;
            background: rgba(255, 80, 80, .15);
            color: #ffd6d6 !important;
            font-weight: 600;
            text-align: center;
            border-radius: 14px;
            transition: .3s ease;
        }

        .sidebar .logout:hover {
            background: #ff4d4d;
            color: #fff !important;
            box-shadow: 0 10px 25px rgba(255, 77, 77, .6);
            transform: translateY(-2px);
        }

        /* ================= WRAPPER & CONTENT ================= */
        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 220px;
            padding: 30px;
            flex: 1;
            animation: fade .5s ease;
        }

        @keyframes fade {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 2rem;
            font-size: 2rem;
        }

        .container-fluid {
            max-width: 100%;
        }

        /* ================= BADGES ================= */
        .badge-pesan,
        .badge-notif {
            position: absolute;
            top: 8px;
            right: 15px;
            background: #dc3545;
            color: white;
            font-size: 11px;
            padding: 3px 7px;
            border-radius: 50%;
            font-weight: 600;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .main-content {
                margin-left: 70px;
                padding: 15px;
            }

            .menu-title,
            .sidebar a span {
                display: none;
            }

            .sidebar a {
                text-align: center;
                padding: 15px 10px;
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