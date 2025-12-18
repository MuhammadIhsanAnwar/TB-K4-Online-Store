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
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
      <link rel="stylesheet" href="css_admin/data_produk_style.css">
 
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="container">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h1 class="page-title"><i class="bi bi-box-seam"></i> Data Produk</h1>
                <a href="tambah_produk.php" class="btn-add"><i class="bi bi-plus-circle"></i> Tambah Produk Baru</a>
            </div>

            <!-- FILTER & SEARCH SECTION -->
            <form method="GET" class="filter-search-section">
                <div class="filter-group">
                    <label for="search"><i class="bi bi-search"></i> Cari Produk</label>

                    <input type="text" id="search" name="search" placeholder="Cari berdasarkan nama, merk, atau deskripsi..."
                        value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <div class="filter-group">
                    <label for="kategori"><i class="bi bi-folder2-open"></i> Filter Kategori</label>
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
                        <button type="submit" class="btn-filter"><i class="bi bi-search"></i> Cari</button>
                        <a href="data_produk.php" class="btn-reset"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
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
                                    <th>No.</th>
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
                                              <span class="stok-ready"><i class="bi bi-check-circle-fill"></i> <?php echo $product['stok']; ?></span>
                                            <?php elseif ($product['stok'] > 0): ?>
                                                <span class="stok-low"><i class="bi bi-exclamation-triangle-fill"></i> <?php echo $product['stok']; ?></span>
                                            <?php else: ?>
                                                <span class="stok-out"><i class="bi bi-x-circle-fill"></i> Habis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($product['created_at'])); ?>
                                        </td>
                                        <td>
                                            <div class="aksi-col">
                                                <a href="edit_produk.php?id=<?php echo $product['id']; ?>"class="btn-action btn-edit"><i class="bi bi-pencil-square"></i> Edit</a>
                                                <button type="button" class="btn-action btn-delete"><i class="bi bi-trash-fill" onclick="hapusProduk(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['nama']); ?>')">Hapus</button>
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
                        <div class="empty-state-icon"><i class="bi bi-box" style="font-size: 3rem;"></i></div>
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
                html: `<p>Yakin ingin menghapus produk <strong>${nama}</strong>?</p><p style="color:#ef4444;font-size:12px;"><i class="bi bi-exclamation-triangle-fill"></i>Aksi ini tidak dapat dibatalkan</p>`,
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