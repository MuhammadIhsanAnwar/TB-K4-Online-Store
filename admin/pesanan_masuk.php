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
$history_query = "SELECT * FROM history_penjualan ORDER BY tanggal_selesai DESC";
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
    <link rel="stylesheet" href="css_admin/pesanan_masuk_style.css">
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

                        <!-- FILTER & SEARCH SECTION -->
                        <div class="filter-section">
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                <div>
                                    <div class="filter-title">🔍 Filter Status:</div>
                                    <div class="filter-buttons">
                                        <button class="filter-btn active" onclick="filterByStatus('semua')">Semua Pesanan</button>
                                        <button class="filter-btn" onclick="filterByStatus('Menunggu Konfirmasi')">⏳ Menunggu Konfirmasi</button>
                                        <button class="filter-btn" onclick="filterByStatus('Sedang Dikemas')">📋 Sedang Dikemas</button>
                                        <button class="filter-btn" onclick="filterByStatus('Sedang Dikirim')">🚚 Sedang Dikirim</button>
                                    </div>
                                </div>
                                <div class="search-box">
                                    <input type="text" id="searchMasuk" placeholder="🔎 Cari Order ID, Nama, HP..." onkeyup="searchTable('ordersList', 'searchMasuk')">
                                </div>
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
                            <!-- PAGINATION -->
                            <div class="pagination-container" id="paginationMasuk"></div>
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

                        <!-- SEARCH SECTION SELESAI -->
                        <div class="filter-section">
                            <div class="search-box">
                                <input type="text" id="searchSelesai" placeholder="🔎 Cari Order ID, Nama, HP..." onkeyup="searchTable('orderSelesaiList', 'searchSelesai')">
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
                            <!-- PAGINATION -->
                            <div class="pagination-container" id="paginationSelesai"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        const ITEMS_PER_PAGE = 10;

        function switchTab(tabName) {
            document.getElementById('pesanan-masuk').style.display = 'none';
            document.getElementById('pesanan-selesai').style.display = 'none';
            document.getElementById(tabName).style.display = 'block';

            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Reset pagination saat switch tab
            if (tabName === 'pesanan-masuk') {
                setupPagination('ordersList', 'paginationMasuk');
            } else {
                setupPagination('orderSelesaiList', 'paginationSelesai');
            }
        }

        function searchTable(tableId, searchInputId) {
            const input = document.getElementById(searchInputId);
            const filter = input.value.toLowerCase();
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            let visibleRows = 0;
            for (let row of rows) {
                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            }

            // Reset pagination setelah search
            if (tableId === 'ordersList') {
                setupPagination('ordersList', 'paginationMasuk');
            } else {
                setupPagination('orderSelesaiList', 'paginationSelesai');
            }
        }

        function setupPagination(tableId, paginationId) {
            const table = document.getElementById(tableId);
            const tbody = table.getElementsByTagName('tbody')[0];
            const rows = Array.from(tbody.getElementsByTagName('tr')).filter(row => row.style.display !== 'none');

            const pageCount = Math.ceil(rows.length / ITEMS_PER_PAGE);
            const paginationContainer = document.getElementById(paginationId);
            paginationContainer.innerHTML = '';

            if (pageCount <= 1) return;

            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.textContent = '← Sebelumnya';
            prevBtn.className = 'pagination-btn';
            prevBtn.onclick = () => goToPage(tableId, paginationId, currentPage - 1);
            paginationContainer.appendChild(prevBtn);

            // Page numbers
            for (let i = 1; i <= pageCount; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                btn.className = 'pagination-btn' + (i === 1 ? ' active' : '');
                btn.onclick = () => goToPage(tableId, paginationId, i);
                paginationContainer.appendChild(btn);
            }

            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Selanjutnya →';
            nextBtn.className = 'pagination-btn';
            nextBtn.onclick = () => goToPage(tableId, paginationId, currentPage + 1);
            paginationContainer.appendChild(nextBtn);

            let currentPage = 1;
            showPage(tableId, 1);
        }

        function goToPage(tableId, paginationId, pageNum) {
            const table = document.getElementById(tableId);
            const tbody = table.getElementsByTagName('tbody')[0];
            const rows = Array.from(tbody.getElementsByTagName('tr')).filter(row => row.style.display !== 'none');
            const pageCount = Math.ceil(rows.length / ITEMS_PER_PAGE);

            if (pageNum < 1 || pageNum > pageCount) return;

            showPage(tableId, pageNum);

            // Update active button
            const buttons = document.querySelectorAll('#' + paginationId + ' .pagination-btn');
            buttons.forEach((btn, index) => {
                btn.classList.remove('active');
                if (parseInt(btn.textContent) === pageNum) {
                    btn.classList.add('active');
                }
            });
        }

        function showPage(tableId, pageNum) {
            const table = document.getElementById(tableId);
            const tbody = table.getElementsByTagName('tbody')[0];
            const rows = Array.from(tbody.getElementsByTagName('tr')).filter(row => row.style.display !== 'none');

            const start = (pageNum - 1) * ITEMS_PER_PAGE;
            const end = start + ITEMS_PER_PAGE;

            rows.forEach((row, index) => {
                row.style.display = index >= start && index < end ? '' : 'none';
            });
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

            buttons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.textContent.includes(status) || (status === 'semua' && btn.textContent.includes('Semua'))) {
                    btn.classList.add('active');
                }
            });

            rows.forEach(row => {
                if (status === 'semua' || row.dataset.status === status) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });

            setupPagination('ordersList', 'paginationMasuk');
        }

        // Initialize pagination saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            setupPagination('ordersList', 'paginationMasuk');
            setupPagination('orderSelesaiList', 'paginationSelesai');
        });
    </script>
</body>

</html>