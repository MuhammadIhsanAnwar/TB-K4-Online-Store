<?php
require 'auth_check.php';
include '../admin/koneksi.php';

// PARAMETER PAGINATION & SEARCH
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// BUILD QUERY
$where = "WHERE 1=1";
if (!empty($search)) {
    $where .= " AND (username LIKE '%$search%' OR nama_lengkap LIKE '%$search%' OR email LIKE '%$search%')";
}

// TOTAL DATA
$total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM akun_user $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $items_per_page);

// GET DATA
$query = mysqli_query($koneksi, "SELECT * FROM akun_user $where ORDER BY id DESC LIMIT $offset, $items_per_page");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data User - Admin</title>

    <!-- WAJIB -->
    <link rel="stylesheet" href="../css/bootstrap.css">

    <!-- CSS HALAMAN -->
    <link rel="stylesheet" href="css_admin/data_user_style.css">

    <!-- ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="container">

        <div class="page-header">
            <h1 class="page-title">👥 Data User</h1>
        </div>

        <hr>

        <!-- SEARCH -->
        <div class="search-section">
            <form method="GET" class="search-group">
                <input type="text" name="search"
                       placeholder="Cari username, nama, email..."
                       value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="btn-search">Cari</button>
            </form>
            <a href="data_user.php" class="btn-reset">Reset</a>
        </div>

        <!-- TABLE -->
        <div class="table-wrapper">
            <?php if (mysqli_num_rows($query) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offset + 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td class="nomor-col"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['username']); ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <?php if ($row['status'] == 1): ?>
                                        <span class="status-badge status-aktif">Aktif</span>
                                    <?php else: ?>
                                        <span class="status-badge status-nonaktif">Belum Aktif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">Tidak ada data user</div>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>
