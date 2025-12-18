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

// Proses menyelesaikan pesanan (Pindah ke history penjualan)
if (isset($_POST['selesaikan_pesanan'])) {
    $order_id = intval($_POST['order_id']);

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // 1) Ambil data pesanan
        $order_query = "SELECT * FROM pemesanan WHERE id='$order_id'";
        $order_result = mysqli_query($koneksi, $order_query);
        $order = mysqli_fetch_assoc($order_result);

        if (!$order) {
            throw new Exception('Pesanan tidak ditemukan');
        }

        // 2) Insert ke tabel history_penjualan (asumsikan tabel sudah ada)
        $insert_history = "INSERT INTO history_penjualan 
                          (user_id, nama_lengkap, nomor_hp, alamat_lengkap, nama_produk, quantity, 
                           harga_total, metode_pembayaran, kurir, resi, tanggal_dipesan, tanggal_selesai)
                          SELECT user_id, nama_lengkap, nomor_hp, alamat_lengkap, nama_produk, quantity, 
                                 0, metode_pembayaran, kurir, resi, waktu_pemesanan, NOW()
                          FROM pemesanan WHERE id='$order_id'";

        if (!mysqli_query($koneksi, $insert_history)) {
            throw new Exception('Gagal menambah ke history penjualan: ' . mysqli_error($koneksi));
        }

        // 3) Hapus dari pemesanan
        $delete_query = "DELETE FROM pemesanan WHERE id='$order_id'";
        if (!mysqli_query($koneksi, $delete_query)) {
            throw new Exception('Gagal menghapus pesanan: ' . mysqli_error($koneksi));
        }

        mysqli_commit($koneksi);
        echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil diselesaikan dan dipindahkan ke history penjualan']);
        exit;
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #1E5DAC;
            --primary-dark: #0f3f82;
            --primary-light: #3b7bc9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --bg: #f8fafc;
            --white: #ffffff;
            --text: #1f2937;
            --border: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            min-height: 100vh;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-light) 0%, var(--primary-dark) 100%);
            padding: 18px 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(30, 93, 172, 0.2);
        }

        .logo-box {
            text-align: center;
            padding: 10px 0 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-box img {
            width: 72px;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.25));
            transition: 0.3s ease;
        }

        .logo-box img:hover {
            transform: scale(1.05);
        }

        .menu-title {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            padding: 12px 20px;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 4px 12px;
            border-radius: 10px;
            transition: 0.25s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.18);
            transform: translateX(5px);
        }

        .sidebar a.active {
            background: rgba(255, 255, 255, 0.32);
            font-weight: 600;
        }

        .sidebar .logout {
            margin-top: auto;
            background: rgba(255, 80, 80, 0.15);
            color: #ffd6d6 !important;
            font-weight: 600;
            text-align: center;
            border-radius: 14px;
            transition: 0.3s ease;
            margin-bottom: 12px;
            margin-left: 12px;
            margin-right: 12px;
        }

        .sidebar .logout:hover {
            background: var(--danger);
            color: white !important;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.6);
            transform: translateY(-2px);
        }

        /* ================= MAIN CONTENT ================= */
        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 220px;
            padding: 30px;
            flex: 1;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            color: var(--primary);
            font-weight: 700;
            font-size: 2.5rem;
            font-family: 'Playfair Display', serif;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.08);
            border-left: 5px solid var(--primary);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(30, 93, 172, 0.15);
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* ================= FILTER SECTION ================= */
        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.08);
        }

        .filter-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .filter-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 18px;
            border: 2px solid var(--border);
            background: white;
            color: var(--text);
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .filter-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.3);
        }

        /* ================= ORDER CARDS ================= */
        .orders-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
            gap: 1.5rem;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.08);
            transition: all 0.3s ease;
            border-top: 5px solid var(--primary);
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .order-card:hover {
            box-shadow: 0 12px 24px rgba(30, 93, 172, 0.15);
            transform: translateY(-5px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(30, 93, 172, 0.05) 0%, rgba(183, 197, 218, 0.05) 100%);
            border-bottom: 1px solid var(--border);
        }

        .order-id {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .order-date {
            color: #9ca3af;
            font-size: 0.85rem;
        }

        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-menunggu-konfirmasi {
            background: rgba(245, 158, 11, 0.2);
            color: #b45309;
        }

        .status-sedang-dikemas {
            background: rgba(59, 130, 246, 0.2);
            color: #1e40af;
        }

        .status-sedang-dikirim {
            background: rgba(59, 130, 246, 0.2);
            color: #1e40af;
        }

        .status-selesai {
            background: rgba(16, 185, 129, 0.2);
            color: #065f46;
        }

        .order-info {
            padding: 1.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
            align-items: flex-start;
            gap: 1rem;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6b7280;
            min-width: 150px;
            font-size: 0.9rem;
        }

        .info-value {
            color: var(--text);
            word-break: break-word;
            flex: 1;
            text-align: right;
        }

        .resi-value {
            color: var(--primary);
            font-weight: 600;
            font-family: 'Courier New', monospace;
            background: rgba(30, 93, 172, 0.05);
            padding: 5px 10px;
            border-radius: 5px;
        }

        .order-actions {
            padding: 1.5rem;
            background: var(--bg);
            border-top: 1px solid var(--border);
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 10px 16px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex: 1;
            justify-content: center;
            min-width: 150px;
        }

        .btn-dikemas {
            background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-dikemas:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-resi {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-resi:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
        }

        .btn-selesai {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-selesai:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        .btn-disabled {
            background: #d1d5db;
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .btn-disabled:hover {
            transform: none;
        }

        /* ================= RESI FORM ================= */
        .resi-input-group {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .resi-input {
            flex: 1;
            min-width: 200px;
            padding: 10px 15px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .resi-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.1);
        }

        /* ================= EMPTY STATE ================= */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.08);
        }

        .empty-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #6b7280;
            font-size: 1rem;
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
                justify-content: center;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .filter-buttons {
                flex-direction: column;
            }

            .filter-btn {
                width: 100%;
            }

            .order-header {
                flex-direction: column;
                gap: 1rem;
            }

            .info-row {
                flex-direction: column;
            }

            .info-label {
                min-width: auto;
            }

            .info-value {
                text-align: left;
            }

            .order-actions {
                flex-direction: column;
            }

            .btn-action {
                min-width: auto;
                width: 100%;
            }

            .resi-input-group {
                flex-direction: column;
            }

            .resi-input {
                min-width: auto;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="container-fluid">
                <!-- PAGE HEADER -->
                <div class="page-header">
                    <h1 class="page-title">📦 Pesanan Masuk</h1>
                </div>

                <!-- STATS SECTION -->
                <div class="stats-container">
                    <?php
                    $menunggu = count(array_filter($pesanan, fn($p) => $p['status'] === 'Menunggu Konfirmasi'));
                    $dikemas = count(array_filter($pesanan, fn($p) => $p['status'] === 'Sedang Dikemas'));
                    $dikirim = count(array_filter($pesanan, fn($p) => $p['status'] === 'Sedang Dikirim'));
                    $total = count($pesanan);
                    ?>
                    <div class="stat-card">
                        <div class="stat-label">📦 Total Pesanan</div>
                        <div class="stat-value"><?php echo $total; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">⏳ Menunggu Konfirmasi</div>
                        <div class="stat-value" style="color: #b45309;"><?php echo $menunggu; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">📋 Sedang Dikemas</div>
                        <div class="stat-value" style="color: #1e40af;"><?php echo $dikemas; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">🚚 Sedang Dikirim</div>
                        <div class="stat-value" style="color: #1e40af;"><?php echo $dikirim; ?></div>
                    </div>
                </div>

                <!-- FILTER SECTION -->
                <div class="filter-section">
                    <div class="filter-title">🔍 Filter Status:</div>
                    <div class="filter-buttons">
                        <button class="filter-btn active" onclick="filterByStatus('semua')">Semua Pesanan</button>
                        <button class="filter-btn" onclick="filterByStatus('Menunggu Konfirmasi')">⏳ Menunggu Konfirmasi</button>
                        <button class="filter-btn" onclick="filterByStatus('Sedang Dikemas')">📋 Sedang Dikemas</button>
                        <button class="filter-btn" onclick="filterByStatus('Sedang Dikirim')">🚚 Sedang Dikirim</button>
                    </div>
                </div>

                <!-- ORDERS LIST -->
                <div class="orders-container" id="ordersList">
                    <?php if (empty($pesanan)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <h3>Tidak ada pesanan</h3>
                            <p>Belum ada pesanan yang masuk. Tunggu pelanggan untuk melakukan pembelian.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pesanan as $order): ?>
                            <div class="order-card" data-status="<?php echo htmlspecialchars($order['status']); ?>">
                                <div class="order-header">
                                    <div>
                                        <div class="order-id">Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                        <small class="order-date">📅 <?php echo date('d M Y H:i', strtotime($order['waktu_pemesanan'])); ?></small>
                                    </div>
                                    <span class="order-status status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </div>

                                <div class="order-info">
                                    <div class="info-row">
                                        <span class="info-label">👤 Nama Customer:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['nama_lengkap']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">📱 Nomor HP:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['nomor_hp']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">📍 Alamat:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['alamat_lengkap']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">🛍️ Produk:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['nama_produk']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">📦 Kuantitas:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['quantity']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">🚚 Kurir:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['kurir']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">💳 Metode Pembayaran:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($order['metode_pembayaran']); ?></span>
                                    </div>
                                    <?php if (!empty($order['resi'])): ?>
                                        <div class="info-row">
                                            <span class="info-label">📮 Nomor Resi:</span>
                                            <span class="resi-value"><?php echo htmlspecialchars($order['resi']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- ACTION BUTTONS -->
                                <div class="order-actions">
                                    <?php if ($order['status'] === 'Menunggu Konfirmasi'): ?>
                                        <button class="btn-action btn-dikemas" onclick="updateStatusDikemas(<?php echo $order['id']; ?>)">
                                            ✓ Ubah ke Sedang Dikemas
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($order['status'] === 'Sedang Dikemas' || $order['status'] === 'Menunggu Konfirmasi'): ?>
                                        <button class="btn-action btn-resi" onclick="showResiForm(<?php echo $order['id']; ?>)">
                                            📮 Input Resi
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($order['status'] === 'Sedang Dikirim' && !empty($order['resi'])): ?>
                                        <button class="btn-action btn-selesai" onclick="selesaikanPesanan(<?php echo $order['id']; ?>)">
                                            ✓ Pesanan Selesai
                                        </button>
                                    <?php elseif ($order['status'] !== 'Sedang Dikirim'): ?>
                                        <button class="btn-action btn-disabled" disabled>
                                            ✓ Tunggu Dikirim
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- RESI INPUT FORM (Hidden by default) -->
                                <div id="resiForm<?php echo $order['id']; ?>" style="display:none; padding: 1.5rem; background: var(--bg); border-top: 1px solid var(--border);">
                                    <div style="margin-bottom: 0.75rem;">
                                        <label style="display: block; font-weight: 600; color: var(--primary); margin-bottom: 0.5rem;">Masukkan Nomor Resi</label>
                                        <div class="resi-input-group">
                                            <input type="text"
                                                id="resiInput<?php echo $order['id']; ?>"
                                                class="resi-input"
                                                placeholder="Contoh: JNE123456789..."
                                                autocomplete="off">
                                            <button class="btn-action btn-resi" onclick="submitResi(<?php echo $order['id']; ?>)">
                                                Kirim Resi
                                            </button>
                                            <button class="btn-action btn-disabled" onclick="cancelResiForm(<?php echo $order['id']; ?>)">
                                                Batal
                                            </button>
                                        </div>
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
                icon: 'info',
                title: 'Ubah Status?',
                text: 'Ubah status pesanan menjadi "Sedang Dikemas"?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1E5DAC',
                cancelButtonColor: '#d1d5db'
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
                html: `<p>Nomor resi: <strong>${resi}</strong></p><p style="color: #6b7280; font-size: 0.9rem;">Status akan berubah menjadi "Sedang Dikirim"</p>`,
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1E5DAC',
                cancelButtonColor: '#d1d5db'
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

        function selesaikanPesanan(orderId) {
            Swal.fire({
                icon: 'question',
                title: 'Selesaikan Pesanan?',
                text: 'Pesanan akan dipindahkan ke history penjualan dan dihapus dari list pesanan masuk.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesaikan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#d1d5db'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('selesaikan_pesanan', '1');
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