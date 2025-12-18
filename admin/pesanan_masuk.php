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
                                 harga_total, metode_pembayaran, kurir, resi, waktu_pemesanan, NOW()
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

// Ambil semua pesanan dengan join ke products untuk mendapatkan harga
$query = "SELECT p.*, GROUP_CONCAT(pr.harga SEPARATOR ',') as harga_list 
          FROM pemesanan p 
          LEFT JOIN products pr ON FIND_IN_SET(pr.id, p.product_id)
          GROUP BY p.id
          ORDER BY p.waktu_pemesanan DESC";
$result = mysqli_query($koneksi, $query);
$pesanan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pesanan[] = $row;
}

// Ambil pesanan yang sudah selesai dari history_penjualan
$history_query = "SELECT * FROM history_penjualan ORDER BY tanggal_selesai DESC LIMIT 100";
$history_result = mysqli_query($koneksi, $history_query);
$pesanan_selesai = [];
if ($history_result) {
    while ($row = mysqli_fetch_assoc($history_result)) {
        $pesanan_selesai[] = $row;
    }
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

        /* ================= TABS SECTION ================= */
        .tabs-container {
            background: white;
            border-radius: 15px;
            padding: 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.08);
            overflow: hidden;
        }

        .tabs-header {
            display: flex;
            border-bottom: 2px solid var(--border);
        }

        .tab-btn {
            flex: 1;
            padding: 1.2rem 1rem;
            background: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .tab-btn:hover {
            color: var(--primary);
            background: rgba(30, 93, 172, 0.02);
        }

        .tab-btn.active {
            color: white;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-bottom: none;
        }

        .tab-badge {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 700;
            min-width: 28px;
            text-align: center;
        }

        .tab-btn.active .tab-badge {
            background: rgba(255, 255, 255, 0.4);
        }

        .tabs-content {
            padding: 2rem;
        }

        /* ================= STATS SECTION ================= */
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

        /* ================= TABLE STYLES ================= */
        .table-wrapper {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.08);
            overflow-x: auto;
            margin-top: 1.5rem;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        .orders-table thead {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .orders-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }

        .orders-table tbody tr {
            border-bottom: 1px solid var(--border);
            animation: slideUp 0.5s ease;
        }

        .orders-table tbody tr:hover {
            background: rgba(30, 93, 172, 0.02);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .orders-table td {
            padding: 1rem;
            vertical-align: top;
        }

        .order-id-cell {
            font-weight: 700;
            color: var(--primary);
        }

        .customer-name {
            font-weight: 600;
            color: var(--text);
        }

        .produk-col {
            word-wrap: break-word;
            white-space: normal;
            min-width: 250px;
        }

        .qty-col {
            text-align: center;
            font-weight: 600;
            color: var(--info);
        }

        .harga-col {
            text-align: right;
            font-weight: 600;
            color: var(--success);
            min-width: 140px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
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

        .resi-number {
            font-family: 'Courier New', monospace;
            background: rgba(30, 93, 172, 0.05);
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: var(--primary);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .btn-dikemas {
            background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);
            color: white;
        }

        .btn-dikemas:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-resi {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
        }

        .btn-resi:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-selesai {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
        }

        .btn-selesai:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
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
        @media (max-width: 1024px) {
            .orders-table {
                font-size: 0.9rem;
            }

            .orders-table th,
            .orders-table td {
                padding: 0.75rem;
            }

            .produk-col {
                min-width: 200px;
            }
        }

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

            .tab-btn {
                font-size: 0.8rem;
                padding: 1rem 0.5rem;
            }

            .tab-badge {
                font-size: 0.7rem;
            }

            .table-wrapper {
                border-radius: 10px;
            }

            .orders-table {
                font-size: 0.85rem;
            }

            .orders-table th,
            .orders-table td {
                padding: 0.5rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.25rem;
            }

            .btn-action {
                width: 100%;
                padding: 6px 8px;
                font-size: 0.75rem;
            }

            .produk-col {
                min-width: 150px;
            }
        }

        @media (max-width: 600px) {
            .main-content {
                padding: 10px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .tabs-content {
                padding: 1rem;
            }

            .orders-table {
                font-size: 0.8rem;
            }

            .orders-table th,
            .orders-table td {
                padding: 0.4rem;
            }

            .harga-col {
                min-width: 100px;
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
                    <h1 class="page-title">📦 Pesanan</h1>
                </div>

                <!-- TABS SECTION -->
                <div class="tabs-container">
                    <div class="tabs-header">
                        <button class="tab-btn active" onclick="switchTab('pesanan-masuk')">
                            📬 Pesanan Masuk
                            <span class="tab-badge"><?php echo count($pesanan); ?></span>
                        </button>
                        <button class="tab-btn" onclick="switchTab('pesanan-selesai')">
                            ✓ Pesanan Selesai
                            <span class="tab-badge"><?php echo count($pesanan_selesai); ?></span>
                        </button>
                    </div>

                    <!-- TAB PESANAN MASUK -->
                    <div id="pesanan-masuk" class="tabs-content" style="display: block;">
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

                        <!-- ORDERS TABLE -->
                        <?php if (empty($pesanan)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">📭</div>
                                <h3>Tidak ada pesanan</h3>
                                <p>Belum ada pesanan yang masuk. Tunggu pelanggan untuk melakukan pembelian.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-wrapper">
                                <table class="orders-table" id="ordersList">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Nama Customer</th>
                                            <th>No. HP</th>
                                            <th>Alamat</th>
                                            <th>Produk</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Total Harga</th>
                                            <th>Kurir</th>
                                            <th>Resi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pesanan as $order):
                                            // Parse produk dan quantity
                                            $produk_array = array_map('trim', explode(',', $order['nama_produk']));
                                            $qty_array = array_map('trim', explode(',', $order['quantity']));
                                            $harga_array = !empty($order['harga_list']) ? array_map('trim', explode(',', $order['harga_list'])) : [];

                                            $max_items = max(count($produk_array), count($qty_array), count($harga_array));
                                            $total_harga = $order['harga_total'] ?? 0;
                                            $first_row = true;
                                        ?>
                                            <?php for ($i = 0; $i < $max_items; $i++):
                                                $produk = $produk_array[$i] ?? '';
                                                $qty = $qty_array[$i] ?? '';
                                                $harga = $harga_array[$i] ?? 0;
                                            ?>
                                                <tr class="order-row" data-status="<?php echo htmlspecialchars($order['status']); ?>" data-order-id="<?php echo $order['id']; ?>">
                                                    <?php if ($first_row): ?>
                                                        <td class="order-id-cell" rowspan="<?php echo $max_items; ?>">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                                        <td class="customer-name" rowspan="<?php echo $max_items; ?>"><?php echo htmlspecialchars($order['nama_lengkap']); ?></td>
                                                        <td rowspan="<?php echo $max_items; ?>"><?php echo htmlspecialchars($order['nomor_hp']); ?></td>
                                                        <td rowspan="<?php echo $max_items; ?>"><?php echo htmlspecialchars($order['alamat_lengkap']); ?></td>
                                                    <?php endif; ?>

                                                    <td class="produk-col"><?php echo htmlspecialchars($produk); ?></td>
                                                    <td class="qty-col"><?php echo htmlspecialchars($qty); ?></td>
                                                    <td class="harga-col">Rp <?php echo number_format($harga, 0, ',', '.'); ?></td>

                                                    <?php if ($first_row): ?>
                                                        <td class="harga-col" rowspan="<?php echo $max_items; ?>">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></td>
                                                        <td rowspan="<?php echo $max_items; ?>"><?php echo htmlspecialchars($order['kurir']); ?></td>
                                                        <td rowspan="<?php echo $max_items; ?>">
                                                            <?php if (!empty($order['resi'])): ?>
                                                                <span class="resi-number"><?php echo htmlspecialchars($order['resi']); ?></span>
                                                            <?php else: ?>
                                                                <span style="color: #9ca3af;">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td rowspan="<?php echo $max_items; ?>">
                                                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                                                                <?php echo $order['status']; ?>
                                                            </span>
                                                        </td>
                                                        <td rowspan="<?php echo $max_items; ?>">
                                                            <div class="action-buttons">
                                                                <?php if ($order['status'] === 'Menunggu Konfirmasi'): ?>
                                                                    <button class="btn-action btn-dikemas" onclick="updateStatusDikemas(<?php echo $order['id']; ?>)">
                                                                        Dikemas
                                                                    </button>
                                                                <?php endif; ?>

                                                                <?php if ($order['status'] === 'Sedang Dikemas' || $order['status'] === 'Menunggu Konfirmasi'): ?>
                                                                    <button class="btn-action btn-resi" onclick="showResiForm(<?php echo $order['id']; ?>)">
                                                                        Resi
                                                                    </button>
                                                                <?php endif; ?>

                                                                <?php if ($order['status'] === 'Sedang Dikirim' && !empty($order['resi'])): ?>
                                                                    <button class="btn-action btn-selesai" onclick="selesaikanPesanan(<?php echo $order['id']; ?>)">
                                                                        Selesai
                                                                    </button>
                                                                <?php elseif ($order['status'] !== 'Sedang Dikirim'): ?>
                                                                    <button class="btn-action btn-disabled" disabled>
                                                                        Selesai
                                                                    </button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>

                                                <!-- RESI INPUT FORM (Hidden by default) -->
                                                <?php if ($first_row): ?>
                                                    <tr id="resiForm<?php echo $order['id']; ?>" style="display:none;">
                                                        <td colspan="12" style="padding: 1.5rem; background: var(--bg);">
                                                            <label style="display: block; font-weight: 600; color: var(--primary); margin-bottom: 0.75rem;">Masukkan Nomor Resi</label>
                                                            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                                                <input type="text"
                                                                    id="resiInput<?php echo $order['id']; ?>"
                                                                    style="flex: 1; min-width: 250px; padding: 10px 15px; border: 2px solid var(--border); border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 0.9rem;"
                                                                    placeholder="Contoh: JNE123456789..."
                                                                    autocomplete="off">
                                                                <button class="btn-action btn-resi" onclick="submitResi(<?php echo $order['id']; ?>)">
                                                                    Kirim Resi
                                                                </button>
                                                                <button class="btn-action btn-disabled" onclick="cancelResiForm(<?php echo $order['id']; ?>)">
                                                                    Batal
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>

                                                <?php $first_row = false; ?>
                                            <?php endfor; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB PESANAN SELESAI -->
                    <div id="pesanan-selesai" class="tabs-content" style="display: none;">
                        <!-- STATS SECTION SELESAI -->
                        <div class="stats-container">
                            <div class="stat-card" style="border-left-color: var(--success);">
                                <div class="stat-label">✓ Total Pesanan Selesai</div>
                                <div class="stat-value" style="color: var(--success);"><?php echo count($pesanan_selesai); ?></div>
                            </div>
                        </div>

                        <!-- ORDERS SELESAI TABLE -->
                        <?php if (empty($pesanan_selesai)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">📪</div>
                                <h3>Belum ada pesanan selesai</h3>
                                <p>Pesanan yang sudah diselesaikan akan tampil di sini.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-wrapper">
                                <table class="orders-table" id="orderSelesaiList">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Nama Customer</th>
                                            <th>No. HP</th>
                                            <th>Alamat</th>
                                            <th>Produk</th>
                                            <th>Qty</th>
                                            <th>Total Harga</th>
                                            <th>Kurir</th>
                                            <th>Resi</th>
                                            <th>Tgl Dipesan</th>
                                            <th>Tgl Selesai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pesanan_selesai as $order):
                                            // Parse produk dan quantity
                                            $produk_array = array_map('trim', explode(',', $order['nama_produk']));
                                            $qty_array = array_map('trim', explode(',', $order['quantity']));
                                            $max_items = max(count($produk_array), count($qty_array));
                                            $first_row = true;
                                        ?>
                                            <?php for ($i = 0; $i < $max_items; $i++):
                                                $produk = $produk_array[$i] ?? '';
                                                $qty = $qty_array[$i] ?? '';
                                            ?>
                                                <tr>
                                                    <?php if ($first_row): ?>
                                                        <td class="order-id-cell" rowspan="<?php echo $max_items; ?>">#<?php echo str_pad($order['id'] ?? 0, 6, '0', STR_PAD_LEFT); ?></td>
                                                        <td class="customer-name" rowspan="<?php echo $max_items; ?>"><?php echo htmlspecialchars($order['nama_lengkap']); ?></td>
                                                        <td rowspan="<?php echo $max_items; ?>"><?php echo htmlspecialchars($order['nomor_hp']); ?></td>
                                                        <td rowspan="<?php echo $max_items; ?>"><?php echo htmlspecialchars($order['alamat_lengkap']); ?></td>
                                                    <?php endif; ?>

                                                    <td class="produk-col"><?php echo htmlspecialchars($produk); ?></td>
                                                    <td class="qty-col"><?php echo htmlspecialchars($qty); ?></td>

                                                    <?php if ($first_row): ?>
                                                        <td class="harga-col" rowspan="<?php echo $max_items; ?>">Rp <?php echo number_format($order['harga_total'] ?? 0, 0, ',', '.'); ?></td>
                                                        <td rowspan="<?php echo $max_items; ?>"><?php echo htmlspecialchars($order['kurir']); ?></td>
                                                        <td rowspan="<?php echo $max_items; ?>">
                                                            <?php if (!empty($order['resi'])): ?>
                                                                <span class="resi-number"><?php echo htmlspecialchars($order['resi']); ?></span>
                                                            <?php else: ?>
                                                                <span style="color: #9ca3af;">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td rowspan="<?php echo $max_items; ?>" style="font-size: 0.85rem;"><?php echo date('d M Y H:i', strtotime($order['tanggal_dipesan'] ?? 'now')); ?></td>
                                                        <td rowspan="<?php echo $max_items; ?>" style="font-size: 0.85rem; color: var(--success); font-weight: 600;"><?php echo date('d M Y H:i', strtotime($order['tanggal_selesai'] ?? 'now')); ?></td>
                                                    <?php endif; ?>
                                                </tr>
                                                <?php $first_row = false; ?>
                                            <?php endfor; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function switchTab(tabName) {
            // Sembunyikan semua tab
            document.getElementById('pesanan-masuk').style.display = 'none';
            document.getElementById('pesanan-selesai').style.display = 'none';

            // Tampilkan tab yang dipilih
            document.getElementById(tabName).style.display = 'block';

            // Update tombol aktif
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

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
            document.getElementById('resiForm' + orderId).style.display = 'table-row';
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
            const rows = document.querySelectorAll('#ordersList tbody tr.order-row');
            const buttons = document.querySelectorAll('.filter-btn');

            // Update button active state
            buttons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.textContent.includes(status) || (status === 'semua' && btn.textContent.includes('Semua'))) {
                    btn.classList.add('active');
                }
            });

            // Filter rows
            rows.forEach(row => {
                if (status === 'semua' || row.dataset.status === status) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>