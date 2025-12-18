<?php
session_start();
require 'auth_check.php';
include "koneksi.php";

// PROSES HAPUS PRODUK
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    // Ambil data foto
    $res = mysqli_query($koneksi, "SELECT foto_produk FROM products WHERE id='$delete_id'");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        // Hapus file foto
        if (!empty($row['foto_produk']) && file_exists("../foto_produk/" . $row['foto_produk'])) {
            unlink("../foto_produk/" . $row['foto_produk']);
        }
    }

    // Hapus data dari database
    $delete_query = mysqli_query($koneksi, "DELETE FROM products WHERE id='$delete_id'");

    if ($delete_query) {
        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus produk']);
    }
    exit;
}

// PARAMETER UNTUK FILTER & PAGINATION
$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// BUILD QUERY
$where = "WHERE 1=1";

if (!empty($kategori)) {
    $where .= " AND kategori='$kategori'";
}

if (!empty($search)) {
    $where .= " AND (nama LIKE '%$search%' OR merk LIKE '%$search%' OR deskripsi LIKE '%$search%')";
}

// TOTAL DATA
$total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM products $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $items_per_page);

// GET DATA
$query = mysqli_query($koneksi, "
    SELECT id, kategori, nama, merk, deskripsi, harga, stok, created_at, foto_produk 
    FROM products 
    $where 
    ORDER BY kategori ASC, created_at DESC 
    LIMIT $offset, $items_per_page
");

$products = [];
while ($row = mysqli_fetch_assoc($query)) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --blue: #1E5DAC;
            --beige: #E8D3C1;
            --alley: #B7C5DA;
            --misty: #EAE2E4;
            --white: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--misty) 0%, #f5f5f5 100%);
            min-height: 100vh;
            margin: 0;
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
            margin-bottom: 12px;
        }

        .sidebar .logout:hover {
            background: #ff4d4d;
            color: #fff !important;
            box-shadow: 0 10px 25px rgba(255, 77, 77, .6);
            transform: translateY(-2px);
        }

        /* ================= CONTENT ================= */
        .content {
            margin-left: 220px;
            padding: 30px 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--blue);
        }

        .btn-add {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            cursor: pointer;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        /* FILTER & SEARCH SECTION */
        .filter-search-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.1);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            color: var(--blue);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .filter-group input,
        .filter-group select {
            padding: 10px 14px;
            border: 2px solid var(--alley);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.15);
        }

        .filter-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231E5DAC' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.5rem;
        }

        .filter-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: flex-end;
        }

        .btn-filter {
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--blue), var(--alley));
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.3);
        }

        .btn-reset {
            padding: 10px 20px;
            background: white;
            color: var(--blue);
            border: 2px solid var(--blue);
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            background: rgba(30, 93, 172, 0.1);
        }

        /* TABLE SECTION */
        .table-wrapper {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(30, 93, 172, 0.1);
            animation: slideUp 0.6s ease-out;
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

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        thead {
            background: linear-gradient(135deg, var(--blue), var(--alley));
            color: white;
        }

        th {
            padding: 1.2rem;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }

        td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--misty);
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: var(--misty);
        }

        /* KOLOM NOMOR */
        .nomor-col {
            font-weight: 600;
            color: var(--blue);
            text-align: center;
        }

        /* KOLOM FOTO */
        .foto-col {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid var(--alley);
        }

        /* KOLOM HARGA */
        .harga-col {
            font-weight: 600;
            color: var(--blue);
        }

        /* KOLOM STOK */
        .stok-col {
            font-weight: 600;
        }

        .stok-ready {
            color: #10b981;
        }

        .stok-low {
            color: #f59e0b;
        }

        .stok-out {
            color: #ef4444;
        }

        /* KOLOM KATEGORI */
        .kategori-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kategori-men {
            background: rgba(59, 130, 246, 0.2);
            color: #1e40af;
        }

        .kategori-women {
            background: rgba(236, 72, 153, 0.2);
            color: #831843;
        }

        .kategori-shoes {
            background: rgba(139, 92, 246, 0.2);
            color: #5b21b6;
        }

        .kategori-accessories {
            background: rgba(34, 197, 94, 0.2);
            color: #14532d;
        }

        /* KOLOM DESKRIPSI */
        .deskripsi-col {
            max-width: 200px;
            color: #6b7280;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* KOLOM AKSI */
        .aksi-col {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-delete {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--alley);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .empty-state-text {
            font-size: 1.1rem;
            color: #6b7280;
        }

        /* PAGINATION */
        .pagination-section {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .pagination-info {
            text-align: center;
            color: var(--alley);
            font-weight: 600;
            width: 100%;
            margin-bottom: 1rem;
        }

        .pagination-btn {
            padding: 10px 14px;
            border: 2px solid var(--alley);
            background: white;
            color: var(--blue);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .pagination-btn:hover {
            background: var(--blue);
            color: white;
            border-color: var(--blue);
        }

        .pagination-btn.active {
            background: var(--blue);
            color: white;
            border-color: var(--blue);
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 200px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 180px;
            }

            .content {
                margin-left: 180px;
                padding: 20px 15px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .filter-search-section {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.8rem;
            }

            th,
            td {
                padding: 0.75rem;
            }

            .foto-col {
                width: 60px;
                height: 60px;
            }

            .btn-action {
                padding: 6px 10px;
                font-size: 0.7rem;
            }

            .deskripsi-col {
                max-width: 100px;
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                width: 160px;
            }

            .content {
                margin-left: 160px;
                padding: 15px 10px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .logo-box img {
                width: 60px;
            }

            .menu-title {
                font-size: 11px;
            }

            .sidebar a {
                padding: 10px 15px;
                margin: 3px 8px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="container">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h1 class="page-title">📦 Data Produk</h1>
                <a href="tambah_produk.php" class="btn-add">➕ Tambah Produk Baru</a>
            </div>

            <!-- FILTER & SEARCH SECTION -->
            <form method="GET" class="filter-search-section">
                <div class="filter-group">
                    <label for="search">🔍 Cari Produk</label>
                    <input type="text" id="search" name="search" placeholder="Cari berdasarkan nama, merk, atau deskripsi..."
                        value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <div class="filter-group">
                    <label for="kategori">📂 Filter Kategori</label>
                    <select id="kategori" name="kategori">
                        <option value="">-- Semua Kategori --</option>
                        <option value="Men" <?php echo $kategori === 'Men' ? 'selected' : ''; ?>>Men</option>
                        <option value="Women" <?php echo $kategori === 'Women' ? 'selected' : ''; ?>>Women</option>
                        <option value="Shoes" <?php echo $kategori === 'Shoes' ? 'selected' : ''; ?>>Shoes</option>
                        <option value="Accessories" <?php echo $kategori === 'Accessories' ? 'selected' : ''; ?>>Accessories</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>&nbsp;</label>
                    <div class="filter-buttons">
                        <button type="submit" class="btn-filter">🔎 Cari</button>
                        <a href="data_produk.php" class="btn-reset">↺ Reset</a>
                    </div>
                </div>
            </form>

            <!-- TABLE SECTION -->
            <div class="table-wrapper">
                <?php if (count($products) > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nomor</th>
                                    <th>Foto</th>
                                    <th>Kategori</th>
                                    <th>Nama Produk</th>
                                    <th>Merk</th>
                                    <th>Deskripsi</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Tanggal Publish</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $nomor = $offset + 1;
                                foreach ($products as $product):
                                ?>
                                    <tr id="row-<?= $product['id'] ?>">
                                        <td class="nomor-col"><?php echo $nomor++; ?></td>
                                        <td>
                                            <?php if (!empty($product['foto_produk']) && file_exists("../foto_produk/" . $product['foto_produk'])): ?>
                                                <img src="../foto_produk/<?php echo htmlspecialchars($product['foto_produk']); ?>" alt="<?php echo htmlspecialchars($product['nama']); ?>" class="foto-col">
                                            <?php else: ?>
                                                <div style="width: 80px; height: 80px; background: var(--misty); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--alley);">N/A</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="kategori-badge kategori-<?php echo strtolower($product['kategori']); ?>">
                                                <?php echo htmlspecialchars($product['kategori']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product['nama']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['merk']); ?></td>
                                        <td>
                                            <div class="deskripsi-col" title="<?php echo htmlspecialchars($product['deskripsi']); ?>">
                                                <?php echo htmlspecialchars($product['deskripsi']); ?>
                                            </div>
                                        </td>
                                        <td class="harga-col">
                                            Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?>
                                        </td>
                                        <td class="stok-col">
                                            <?php if ($product['stok'] > 10): ?>
                                                <span class="stok-ready">✓ <?php echo $product['stok']; ?></span>
                                            <?php elseif ($product['stok'] > 0): ?>
                                                <span class="stok-low">⚠ <?php echo $product['stok']; ?></span>
                                            <?php else: ?>
                                                <span class="stok-out">✗ Habis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($product['created_at'])); ?>
                                        </td>
                                        <td>
                                            <div class="aksi-col">
                                                <a href="edit_produk.php?id=<?php echo $product['id']; ?>" class="btn-action btn-edit">✏️ Edit</a>
                                                <button type="button" class="btn-action btn-delete" onclick="hapusProduk(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['nama']); ?>')">🗑️ Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination-section">
                            <div class="pagination-info">
                                Menampilkan halaman <?php echo $page; ?> dari <?php echo $total_pages; ?> (Total: <?php echo $total_data; ?> produk)
                            </div>

                            <?php
                            $query_string = !empty($search) ? "&search=" . urlencode($search) : "";
                            $query_string .= !empty($kategori) ? "&kategori=" . urlencode($kategori) : "";
                            ?>

                            <!-- Tombol Previous -->
                            <?php if ($page > 1): ?>
                                <a href="?page=1<?php echo $query_string; ?>" class="pagination-btn">« Pertama</a>
                                <a href="?page=<?php echo $page - 1;
                                                echo $query_string; ?>" class="pagination-btn">‹ Sebelumnya</a>
                            <?php endif; ?>

                            <!-- Tombol Nomor Halaman -->
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            if ($start_page > 1) {
                                echo '<span style="color: var(--alley); padding: 10px;">...</span>';
                            }

                            for ($i = $start_page; $i <= $end_page; $i++) {
                                $active = $i === $page ? 'active' : '';
                                echo '<a href="?page=' . $i . $query_string . '" class="pagination-btn ' . $active . '">' . $i . '</a>';
                            }

                            if ($end_page < $total_pages) {
                                echo '<span style="color: var(--alley); padding: 10px;">...</span>';
                            }
                            ?>

                            <!-- Tombol Next -->
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1;
                                                echo $query_string; ?>" class="pagination-btn">Selanjutnya ›</a>
                                <a href="?page=<?php echo $total_pages;
                                                echo $query_string; ?>" class="pagination-btn">Terakhir »</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📦</div>
                        <p class="empty-state-text">Tidak ada produk yang ditemukan</p>
                        <?php if (!empty($search) || !empty($kategori)): ?>
                            <p style="margin-top: 1rem; color: var(--alley);">
                                <a href="data_produk.php" style="color: var(--blue); text-decoration: none; font-weight: 600;">Lihat semua produk</a>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SWEET ALERT SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function hapusProduk(id, nama) {
            Swal.fire({
                title: 'Hapus Produk?',
                html: `<p>Yakin ingin menghapus produk <strong>${nama}</strong>?</p><p style="color: #ef4444; font-size: 12px;">⚠️ Aksi ini tidak dapat dibatalkan</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                backdrop: true,
                didOpen: (modal) => {
                    modal.style.zIndex = '9999';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.onload = function() {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.status === "success") {
                                const row = document.getElementById("row-" + id);
                                if (row) {
                                    row.style.animation = "fadeOut 0.3s ease";
                                    setTimeout(() => row.remove(), 300);
                                }

                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        } catch (e) {
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus data', 'error');
                        }
                    };
                    xhr.send("delete_id=" + id);
                }
            });
        }
    </script>

</body>

</html>