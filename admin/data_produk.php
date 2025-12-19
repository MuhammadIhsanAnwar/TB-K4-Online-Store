<?php
session_start();
require 'auth_check.php';
include "koneksi.php";

if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    $res = mysqli_query($koneksi, "SELECT foto_produk FROM products WHERE id='$delete_id'");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if (!empty($row['foto_produk']) && file_exists("../foto_produk/" . $row['foto_produk'])) {
            unlink("../foto_produk/" . $row['foto_produk']);
        }
    }

    $delete_query = mysqli_query($koneksi, "DELETE FROM products WHERE id='$delete_id'");

    if ($delete_query) {
        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus produk']);
    }
    exit;
}

$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : '';
$search   = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$page     = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;


$where = "WHERE 1=1";
if (!empty($kategori)) {
    $where .= " AND kategori='$kategori'";
}
if (!empty($search)) {
    $where .= " AND (nama LIKE '%$search%' OR merk LIKE '%$search%' OR deskripsi LIKE '%$search%')";
}

$total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM products $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $items_per_page);

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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link rel="stylesheet" href="css_admin/data_produk_style.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
<div class="container">

    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-box-seam"></i> Data Produk
        </h1>
        <a href="tambah_produk.php" class="btn-add">
            <i class="bi bi-plus-circle"></i> Tambah Produk Baru
        </a>
    </div>

    <form method="GET" class="filter-search-section">

        <div class="filter-group">
            <label for="search">
                <i class="bi bi-search"></i> Cari Produk
            </label>
            <input type="text" id="search" name="search"
                   placeholder="Cari berdasarkan nama, merk, atau deskripsi..."
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <div class="filter-group">
            <label for="kategori">
                <i class="bi bi-folder2-open"></i> Filter Kategori
            </label>
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
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Cari
                </button>
                <a href="data_produk.php" class="btn-reset">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>

    </form>

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

            <?php $nomor = $offset + 1; foreach ($products as $product): ?>
            <tr id="row-<?= $product['id'] ?>">
                <td class="nomor-col"><?= $nomor++ ?></td>

                <td>
                    <?php if (!empty($product['foto_produk']) && file_exists("../foto_produk/" . $product['foto_produk'])): ?>
                        <img src="../foto_produk/<?= htmlspecialchars($product['foto_produk']); ?>" class="foto-col">
                    <?php else: ?>
                        <div style="width:80px;height:80px;background:var(--misty);border-radius:8px;
                        display:flex;align-items:center;justify-content:center;color:var(--alley);">
                            <i class="bi bi-image"></i>
                        </div>
                    <?php endif; ?>
                </td>

                <td>
                    <span class="kategori-badge kategori-<?= strtolower($product['kategori']); ?>">
                        <?= htmlspecialchars($product['kategori']); ?>
                    </span>
                </td>

                <td><strong><?= htmlspecialchars($product['nama']); ?></strong></td>
                <td><?= htmlspecialchars($product['merk']); ?></td>

                <td>
                    <div class="deskripsi-col" title="<?= htmlspecialchars($product['deskripsi']); ?>">
                        <?= htmlspecialchars($product['deskripsi']); ?>
                    </div>
                </td>

                <td class="harga-col">
                    Rp <?= number_format($product['harga'], 0, ',', '.'); ?>
                </td>

                <td class="stok-col">
                    <?php if ($product['stok'] > 10): ?>
                        <span class="stok-ready">
                            <i class="bi bi-check-circle-fill"></i> <?= $product['stok']; ?>
                        </span>
                    <?php elseif ($product['stok'] > 0): ?>
                        <span class="stok-low">
                            <i class="bi bi-exclamation-triangle-fill"></i> <?= $product['stok']; ?>
                        </span>
                    <?php else: ?>
                        <span class="stok-out">
                            <i class="bi bi-x-circle-fill"></i> Habis
                        </span>
                    <?php endif; ?>
                </td>

                <td><?= date('d/m/Y H:i', strtotime($product['created_at'])); ?></td>

                <td>
                    <div class="aksi-col">
                        <a href="edit_produk.php?id=<?= $product['id']; ?>" class="btn-action btn-edit">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <button type="button" class="btn-action btn-delete"
                                onclick="hapusProduk(<?= $product['id']; ?>, '<?= htmlspecialchars($product['nama']); ?>')">
                            <i class="bi bi-trash-fill"></i> Hapus
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
        </div>

    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-box"></i>
            </div>
            <p class="empty-state-text">Tidak ada produk yang ditemukan</p>
        </div>
    <?php endif; ?>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

</body>
</html>
