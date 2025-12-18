<?php
session_start();
include "../../admin/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../user/login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ===== PROSES PEMBATALAN PESANAN =====
if (isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);

    // Cek apakah pesanan milik user dan belum dikirim
    $check_query = "SELECT status FROM pemesanan WHERE id='$order_id' AND user_id='$user_id'";
    $check_result = mysqli_query($koneksi, $check_query);
    $order = mysqli_fetch_assoc($check_result);

    // User bisa membatalkan pesanan jika masih "Menunggu Konfirmasi" atau "Sedang Dikemas"
    if ($order && ($order['status'] === 'Menunggu Konfirmasi' || $order['status'] === 'Sedang Dikemas')) {
        // Update status ke batal
        $update_query = "UPDATE pemesanan SET status='Pesanan Batal' WHERE id='$order_id'";

        if (mysqli_query($koneksi, $update_query)) {
            echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil dibatalkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membatalkan pesanan: ' . mysqli_error($koneksi)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Pesanan tidak bisa dibatalkan (status tidak memungkinkan)']);
    }
    exit;
}

// ===== PROSES MENYELESAIKAN PESANAN =====
if (isset($_POST['selesaikan_pesanan'])) {
    $order_id = intval($_POST['order_id']);

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // 1) Ambil data pesanan
        $order_query = "SELECT * FROM pemesanan WHERE id='$order_id' AND user_id='$user_id'";
        $order_result = mysqli_query($koneksi, $order_query);

        if (!$order_result) {
            throw new Exception('Database error: ' . mysqli_error($koneksi));
        }

        $order = mysqli_fetch_assoc($order_result);

        if (!$order) {
            throw new Exception('Pesanan tidak ditemukan');
        }

        // Cek status harus "Sedang Dikirim"
        if ($order['status'] !== 'Sedang Dikirim') {
            throw new Exception('Pesanan hanya bisa diselesaikan jika sedang dikirim');
        }

        // 2) Insert ke tabel history_penjualan
        $insert_history = "INSERT INTO history_penjualan 
                          (user_id, nama_lengkap, nomor_hp, alamat_lengkap, nama_produk, quantity, 
                           harga_total, metode_pembayaran, kurir, resi, tanggal_dipesan, tanggal_selesai)
                          VALUES ('$order[user_id]', 
                                  '" . mysqli_real_escape_string($koneksi, $order['nama_lengkap']) . "',
                                  '" . mysqli_real_escape_string($koneksi, $order['nomor_hp']) . "',
                                  '" . mysqli_real_escape_string($koneksi, $order['alamat_lengkap']) . "',
                                  '" . mysqli_real_escape_string($koneksi, $order['nama_produk']) . "',
                                  '$order[quantity]',
                                  '$order[harga_total]',
                                  '" . mysqli_real_escape_string($koneksi, $order['metode_pembayaran']) . "',
                                  '" . mysqli_real_escape_string($koneksi, $order['kurir']) . "',
                                  '" . mysqli_real_escape_string($koneksi, $order['resi']) . "',
                                  '$order[waktu_pemesanan]',
                                  NOW())";

        if (!mysqli_query($koneksi, $insert_history)) {
            throw new Exception('Gagal menambah ke history penjualan: ' . mysqli_error($koneksi));
        }

        // 3) Hapus dari pemesanan
        $delete_query = "DELETE FROM pemesanan WHERE id='$order_id'";
        if (!mysqli_query($koneksi, $delete_query)) {
            throw new Exception('Gagal menghapus pesanan: ' . mysqli_error($koneksi));
        }

        mysqli_commit($koneksi);
        echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil diselesaikan']);
        exit;
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// ===== AMBIL DATA PESANAN AKTIF (DARI TABEL pemesanan) =====
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'semua';

// Query dasar untuk pesanan aktif
$query = "SELECT * FROM pemesanan WHERE user_id='$user_id'";

// Filter berdasarkan status
if ($status_filter !== 'semua' && $status_filter !== 'Selesai') {
    $query .= " AND status='" . mysqli_real_escape_string($koneksi, $status_filter) . "'";
}

$query .= " ORDER BY waktu_pemesanan DESC";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Database Error: " . mysqli_error($koneksi));
}

$pesanan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pesanan[] = $row;
}

// ===== AMBIL DATA PESANAN SELESAI (DARI TABEL history_penjualan) =====
$pesanan_selesai = [];
if ($status_filter === 'semua' || $status_filter === 'Selesai') {
    $history_query = "SELECT * FROM history_penjualan WHERE user_id='$user_id' ORDER BY tanggal_selesai DESC";
    $history_result = mysqli_query($koneksi, $history_query);

    if ($history_result) {
        while ($row = mysqli_fetch_assoc($history_result)) {
            $pesanan_selesai[] = $row;
        }
    }
}

// ===== HITUNG PESANAN PER STATUS =====
$status_counts = [
    'Menunggu Konfirmasi' => 0,
    'Sedang Dikemas' => 0,
    'Sedang Dikirim' => 0,
    'Selesai' => 0,
    'Pesanan Batal' => 0
];

// Hitung dari pesanan aktif
$count_query = "SELECT status, COUNT(*) as count FROM pemesanan WHERE user_id='$user_id' GROUP BY status";
$count_result = mysqli_query($koneksi, $count_query);
while ($row = mysqli_fetch_assoc($count_result)) {
    if (isset($status_counts[$row['status']])) {
        $status_counts[$row['status']] = $row['count'];
    }
}

// Hitung dari history_penjualan
$history_count_query = "SELECT COUNT(*) as count FROM history_penjualan WHERE user_id='$user_id'";
$history_count_result = mysqli_query($koneksi, $history_count_query);
$history_count = mysqli_fetch_assoc($history_count_result);
$status_counts['Selesai'] = $history_count['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Urban Hype</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../images/icon/logo.png">

    <style>
        :root {
            --primary: #1E5DAC;
            --secondary: #B7C5DA;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --shadow: 0 8px 32px rgba(30, 93, 172, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #EAE2E4 0%, #B7C5DA 50%, #1E5DAC 100%);
            min-height: 100vh;
            padding-top: 80px;
        }

        /* ===== NAVBAR STYLING ===== */
        .navbar {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95) !important;
            transition: var(--transition);
            box-shadow: var(--shadow);
            border-bottom: 1px solid rgba(30, 93, 172, 0.1);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 40px rgba(30, 93, 172, 0.15);
            background: rgba(255, 255, 255, 1) !important;
        }

        .navbar-logo {
            height: 50px;
            width: 50px;
            margin-right: 1rem;
            object-fit: contain;
            transition: var(--transition);
        }

        .navbar-logo:hover {
            transform: scale(1.1);
        }

        .navbar-brand-wrapper {
            display: flex;
            align-items: center;
            text-decoration: none !important;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 2px;
            color: var(--primary) !important;
            transition: var(--transition);
            text-decoration: none !important;
        }

        .navbar-brand:hover {
            transform: translateY(-2px);
            color: var(--primary) !important;
        }

        .navbar-toggler {
            border: 2px solid var(--primary) !important;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            transition: var(--transition);
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(30, 93, 172, 0.25);
        }

        .navbar-toggler:hover {
            background: rgba(30, 93, 172, 0.1);
            transform: scale(1.05);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(30, 93, 172, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .nav-link {
            position: relative;
            font-weight: 500;
            color: var(--dark) !important;
            transition: var(--transition);
            padding: 10px 18px !important;
            margin: 0 4px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: var(--transition);
            transform: translateX(-50%);
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .cart-link {
            position: relative;
            display: flex;
            align-items: center;
            padding: 8px;
            border-radius: 50%;
            transition: var(--transition);
            color: var(--primary) !important;
            text-decoration: none;
        }

        .cart-link:hover {
            transform: scale(1.15);
            background: rgba(30, 93, 172, 0.1);
        }

        .cart-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger) !important;
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
            border-radius: 50%;
            font-weight: 600;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(30, 93, 172, 0.4);
            animation: bounceIn 0.6s ease;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.3);
            }

            100% {
                transform: scale(1);
            }
        }

        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            transition: var(--transition);
            text-decoration: none;
            background: rgba(30, 93, 172, 0.05);
        }

        .user-dropdown .dropdown-toggle:hover {
            background: rgba(30, 93, 172, 0.15);
            transform: translateY(-2px);
        }

        .user-dropdown img {
            border: 2px solid var(--primary);
            transition: var(--transition);
            box-shadow: 0 2px 10px rgba(30, 93, 172, 0.2);
        }

        .user-name {
            font-weight: 500;
            color: var(--dark);
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-dropdown .dropdown-menu {
            border: 1px solid rgba(30, 93, 172, 0.1);
            box-shadow: 0 4px 20px rgba(30, 93, 172, 0.15);
            border-radius: 12px;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .user-dropdown .dropdown-item {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            transition: var(--transition);
            font-weight: 500;
        }

        .user-dropdown .dropdown-item:hover {
            background: rgba(30, 93, 172, 0.1);
            color: var(--primary);
            transform: translateX(4px);
        }

        /* ===== ORDERS PAGE STYLING ===== */
        .orders-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .orders-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .orders-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #fff;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 0.5rem;
        }

        .status-tabs {
            display: flex;
            gap: 0.8rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            justify-content: center;
            padding: 0 1rem;
        }

        .status-tab {
            padding: 0.7rem 1.2rem;
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
            font-size: 0.9rem;
        }

        .status-tab:hover {
            background: #B7C5DA;
            color: white;
            transform: translateY(-2px);
        }

        .status-tab.active {
            background: #1E5DAC;
            color: white;
            border-color: #1E5DAC;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.3);
        }

        .status-count {
            background: var(--danger);
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
            border-left: 5px solid var(--primary);
        }

        .order-card:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }

        .order-card.menunggu {
            border-left-color: var(--warning);
        }

        .order-card.dikemas {
            border-left-color: var(--info);
        }

        .order-card.dikirim {
            border-left-color: var(--warning);
        }

        .order-card.selesai {
            border-left-color: var(--success);
        }

        .order-card.batal {
            border-left-color: var(--danger);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .order-info {
            flex: 1;
            min-width: 200px;
        }

        .order-id {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 0.3rem;
            font-weight: 500;
        }

        .order-date {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.3rem;
        }

        .order-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            min-width: 140px;
            text-align: center;
        }

        /* STATUS STYLES */
        .status-menunggu {
            background: #fef3c7;
            color: #92400e;
        }

        .status-dikemas {
            background: #cfe2ff;
            color: #084298;
        }

        .status-dikirim {
            background: #fff3cd;
            color: #664d03;
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
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }

        .item-details {
            flex: 1;
        }

        .item-details h4 {
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .item-details p {
            color: #666;
            font-size: 0.9rem;
            margin: 0.3rem 0;
        }

        .item-price {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.1rem;
            white-space: nowrap;
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

        .order-info-bottom {
            flex: 1;
        }

        .order-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .order-address {
            color: #666;
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }

        .order-resi {
            color: #666;
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }

        .resi-label {
            font-weight: 600;
            color: var(--primary);
        }

        .order-actions {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-cancel {
            background: var(--danger);
            color: white;
        }

        .btn-cancel:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-complete {
            background: var(--success);
            color: white;
        }

        .btn-complete:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-details {
            background: var(--info);
            color: white;
        }

        .btn-details:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .empty-state a {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.8rem 1.5rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .empty-state a:hover {
            background: #164390;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .orders-header h1 {
                font-size: 1.8rem;
            }

            .status-tabs {
                gap: 0.5rem;
                padding: 0;
            }

            .status-tab {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
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
                gap: 0.5rem;
            }

            .btn {
                flex: 1;
                text-align: center;
                min-width: 140px;
            }
        }

        @media (max-width: 576px) {
            .orders-container {
                padding: 1rem;
            }

            .orders-header h1 {
                font-size: 1.5rem;
            }

            .order-card {
                padding: 1rem;
            }

            .order-item {
                flex-direction: column;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }

            .order-status {
                min-width: auto;
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
                <span><i class="bi bi-box-seam"></i> Semua</span>
                <span class="status-count"><?php echo array_sum($status_counts); ?></span>
            </a>
            <a href="?status=Menunggu%20Konfirmasi" class="status-tab <?php echo $status_filter === 'Menunggu Konfirmasi' ? 'active' : ''; ?>">
                <span><i class="bi bi-clock-history"></i> Menunggu</span>
                <span class="status-count"><?php echo $status_counts['Menunggu Konfirmasi']; ?></span>
            </a>
            <a href="?status=Sedang%20Dikemas" class="status-tab <?php echo $status_filter === 'Sedang Dikemas' ? 'active' : ''; ?>">
                <span><i class="bi bi-box"></i> Dikemas</span>
                <span class="status-count"><?php echo $status_counts['Sedang Dikemas']; ?></span>
            </a>
            <a href="?status=Sedang%20Dikirim" class="status-tab <?php echo $status_filter === 'Sedang Dikirim' ? 'active' : ''; ?>">
                <span><i class="bi bi-truck"></i> Dikirim</span>
                <span class="status-count"><?php echo $status_counts['Sedang Dikirim']; ?></span>
            </a>
            <a href="?status=Selesai" class="status-tab <?php echo $status_filter === 'Selesai' ? 'active' : ''; ?>">
                <span><i class="bi bi-check-circle"></i> Selesai</span>
                <span class="status-count"><?php echo $status_counts['Selesai']; ?></span>
            </a>
            <a href="?status=Pesanan%20Batal" class="status-tab <?php echo $status_filter === 'Pesanan Batal' ? 'active' : ''; ?>">
                <span><i class="bi bi-x-circle"></i> Batal</span>
                <span class="status-count"><?php echo $status_counts['Pesanan Batal']; ?></span>
            </a>
        </div>

        <!-- Orders List -->
        <div class="orders-list">
            <?php
            $show_empty = true;

            // Tampilkan pesanan aktif (non-selesai)
            if (!empty($pesanan)) {
                $show_empty = false;
                foreach ($pesanan as $order) {
                    $status_class = strtolower(str_replace(' ', '', $order['status']));
            ?>
                    <div class="order-card <?php echo $status_class; ?>">
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-id"><i class="bi bi-hash"></i> #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                <div class="order-date"><i class="bi bi-calendar-event"></i> <?php echo date('d M Y, H:i', strtotime($order['waktu_pemesanan'])); ?></div>
                            </div>
                            <span class="order-status status-<?php echo $status_class; ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </div>

                        <div class="order-items">
                            <div class="order-item">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($order['nama_produk']); ?></h4>
                                    <p><strong>Kuantitas:</strong> <?php echo $order['quantity']; ?></p>
                                    <p><strong>Kurir:</strong> <?php echo htmlspecialchars($order['kurir']); ?></p>
                                    <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order['metode_pembayaran']); ?></p>
                                </div>
                                <div class="item-price">Rp <?php echo number_format($order['harga_total'], 0, ',', '.'); ?></div>
                            </div>
                        </div>

                        <div class="order-footer">
                            <div class="order-info-bottom">
                                <div class="order-total">Total: Rp <?php echo number_format($order['harga_total'], 0, ',', '.'); ?></div>
                                <div class="order-address"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($order['alamat_lengkap']); ?></div>
                                <?php if (!empty($order['resi'])) { ?>
                                    <div class="order-resi"><span class="resi-label"><i class="bi bi-barcode"></i> Resi:</span> <?php echo htmlspecialchars($order['resi']); ?></div>
                                <?php } ?>
                            </div>
                            <div class="order-actions">
                                <?php
                                // Tombol pembatalan untuk status Menunggu Konfirmasi atau Sedang Dikemas
                                if ($order['status'] === 'Menunggu Konfirmasi' || $order['status'] === 'Sedang Dikemas') {
                                ?>
                                    <button class="btn btn-cancel" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                        <i class="bi bi-x-lg"></i> Batalkan
                                    </button>
                                <?php } ?>

                                <?php
                                // Tombol penyelesaian untuk status Sedang Dikirim
                                if ($order['status'] === 'Sedang Dikirim') {
                                ?>
                                    <button class="btn btn-complete" onclick="completeOrder(<?php echo $order['id']; ?>)">
                                        <i class="bi bi-check-lg"></i> Terima Pesanan
                                    </button>
                                <?php } ?>

                                <button class="btn btn-details" onclick="viewOrderDetail(<?php echo $order['id']; ?>)">
                                    <i class="bi bi-eye"></i> Rincian
                                </button>
                            </div>
                        </div>
                    </div>
                <?php
                }
            }

            // Tampilkan pesanan selesai dari history_penjualan
            if (!empty($pesanan_selesai)) {
                $show_empty = false;
                foreach ($pesanan_selesai as $order) {
                ?>
                    <div class="order-card selesai">
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-id"><i class="bi bi-check-circle"></i> Pesanan Selesai</div>
                                <div class="order-date"><i class="bi bi-calendar-check"></i> Diterima: <?php echo date('d M Y, H:i', strtotime($order['tanggal_selesai'])); ?></div>
                            </div>
                            <span class="order-status status-selesai">
                                ✓ Selesai
                            </span>
                        </div>

                        <div class="order-items">
                            <div class="order-item">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($order['nama_produk']); ?></h4>
                                    <p><strong>Kuantitas:</strong> <?php echo $order['quantity']; ?></p>
                                    <p><strong>Kurir:</strong> <?php echo htmlspecialchars($order['kurir']); ?></p>
                                    <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order['metode_pembayaran']); ?></p>
                                </div>
                                <div class="item-price">Rp <?php echo number_format($order['harga_total'], 0, ',', '.'); ?></div>
                            </div>
                        </div>

                        <div class="order-footer">
                            <div class="order-info-bottom">
                                <div class="order-total">Total: Rp <?php echo number_format($order['harga_total'], 0, ',', '.'); ?></div>
                                <div class="order-address"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($order['alamat_lengkap']); ?></div>
                                <?php if (!empty($order['resi'])) { ?>
                                    <div class="order-resi"><span class="resi-label"><i class="bi bi-barcode"></i> Resi:</span> <?php echo htmlspecialchars($order['resi']); ?></div>
                                <?php } ?>
                            </div>
                            <div class="order-actions">
                                <button class="btn btn-details" onclick="viewOrderDetail(null)">
                                    <i class="bi bi-eye"></i> Rincian
                                </button>
                            </div>
                        </div>
                    </div>
                <?php
                }
            }

            // Tampilkan empty state jika tidak ada pesanan
            if ($show_empty) {
                ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3>Belum Ada Pesanan</h3>
                    <p>Anda belum memiliki pesanan <?php echo $status_filter !== 'semua' ? 'dengan status ' . htmlspecialchars($status_filter) : ''; ?>.</p>
                    <a href="../../admin/produk.php">← Lanjut Belanja</a>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        // Scroll effect untuk navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Cancel order function - untuk pembatalan pesanan
        function cancelOrder(orderId) {
            Swal.fire({
                icon: 'warning',
                title: 'Batalkan Pesanan?',
                text: 'Apakah Anda yakin ingin membatalkan pesanan ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Jangan',
                confirmButtonColor: '#ef4444',
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

        // Complete order function - untuk menyelesaikan pesanan yang sedang dikirim
        function completeOrder(orderId) {
            Swal.fire({
                icon: 'question',
                title: 'Terima Pesanan?',
                text: 'Apakah pesanan sudah sampai dan dalam kondisi baik?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Terima',
                cancelButtonText: 'Belum',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#1E5DAC'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('selesaikan_pesanan', '1');
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
                                    title: 'Terima Kasih!',
                                    text: 'Pesanan telah diselesaikan. Data penjualan telah tersimpan.',
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
                                text: 'Terjadi kesalahan: ' + error,
                                confirmButtonColor: '#1E5DAC'
                            });
                        });
                }
            });
        }

        function viewOrderDetail(orderId) {
            if (orderId) {
                Swal.fire({
                    icon: 'info',
                    title: 'Pesanan #' + String(orderId).padStart(6, '0'),
                    text: 'Rincian lengkap pesanan Anda',
                    confirmButtonColor: '#1E5DAC'
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Pesanan Selesai',
                    text: 'Terima kasih telah berbelanja!',
                    confirmButtonColor: '#1E5DAC'
                });
            }
        }
    </script>
</body>

</html>