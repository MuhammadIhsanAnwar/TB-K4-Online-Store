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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">

    <link rel="stylesheet" href="css_admin/data_user_style.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="container">

        <div class="page-header">
            <h1 class="page-title">👥 Data User</h1>
        </div>

        <hr>

        <div class="search-section">
            <form method="GET" class="search-group" style="flex:1;">
                <div style="width:100%;">
                    <label for="search">🔍 Cari User</label>
                    <div style="display:flex; gap:0.5rem;">
                        <input type="text" id="search" name="search"
                               value="<?= htmlspecialchars($search) ?>"
                               placeholder="Cari berdasarkan username, nama, atau email...">
                        <button class="btn-search">Cari</button>
                    </div>
                </div>
            </form>
            <a href="data_user.php" class="btn-reset">↺ Reset</a>
        </div>

        <div class="table-wrapper">
            <?php
            $res = mysqli_query($koneksi, "SELECT * FROM akun_user $where ORDER BY id DESC LIMIT $offset, $items_per_page");
            $count = mysqli_num_rows($res);
            ?>

            <?php if ($count > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Provinsi</th>
                            <th>Kabupaten/Kota</th>
                            <th>Alamat</th>
                            <th>Nomor Hp</th>
                            <th>Foto Profil</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offset + 1; while($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td class="nomor-col"><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td class="text-truncate-col"><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                            <td><?= $row['tanggal_lahir'] ?></td>
                            <td><?= htmlspecialchars($row['provinsi']) ?></td>
                            <td><?= htmlspecialchars($row['kabupaten_kota']) ?></td>
                            <td class="text-truncate-col"><?= htmlspecialchars($row['alamat']) ?></td>
                            <td><?= htmlspecialchars($row['nomor_hp']) ?></td>
                            <td class="foto-profil-col">
                                <?php if (!empty($row['foto_profil']) && file_exists("../foto_profil/".$row['foto_profil'])): ?>
                                    <img src="../foto_profil/<?= $row['foto_profil'] ?>">
                                <?php else: ?>
                                    <div class="foto-kosong">👤</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 1): ?>
                                    <span class="status-badge status-aktif">✓ Akun Aktif</span>
                                <?php else: ?>
                                    <span class="status-badge status-nonaktif">⏳ Belum Aktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
