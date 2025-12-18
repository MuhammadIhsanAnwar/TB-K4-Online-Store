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
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h1 class="page-title">👥 Data User</h1>
            </div>

            <hr>

            <!-- SEARCH SECTION -->
            <div class="search-section">
                <form method="GET" class="search-group" style="flex: 1;">
                    <div style="width: 100%;">
                        <label for="search">🔍 Cari User</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" id="search" name="search" placeholder="Cari berdasarkan username, nama, atau email..."
                                value="<?php echo htmlspecialchars($search); ?>" style="flex: 1;">
                            <button type="submit" class="btn-search">Cari</button>
                        </div>
                    </div>
                </form>
                <a href="data_user.php" class="btn-reset">↺ Reset</a>
            </div>

            <!-- TABLE SECTION -->
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
                                <?php
                                $nomor = $offset + 1;
                                while ($row = mysqli_fetch_assoc($res)):
                                ?>
                                    <tr>
                                        <td class="nomor-col"><?= $nomor++ ?></td>
                                        <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                                        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                        <td class="text-truncate-col" title="<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                                        <td><?= $row['tanggal_lahir'] ?></td>
                                        <td><?= htmlspecialchars($row['provinsi']) ?></td>
                                        <td><?= htmlspecialchars($row['kabupaten_kota']) ?></td>
                                        <td class="text-truncate-col" title="<?= htmlspecialchars($row['alamat']) ?>"><?= htmlspecialchars($row['alamat']) ?></td>
                                        <td><?= htmlspecialchars($row['nomor_hp']) ?></td>
                                        <td class="foto-profil-col">
                                            <?php if (!empty($row['foto_profil']) && file_exists("../foto_profil/" . $row['foto_profil'])): ?>
                                                <img src="../foto_profil/<?= htmlspecialchars($row['foto_profil']) ?>" alt="<?= htmlspecialchars($row['username']) ?>">
                                            <?php else: ?>
                                                <div style="width: 50px; height: 50px; background: var(--misty); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--alley); margin: 0 auto;">👤</div>
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

                    <!-- PAGINATION -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination-section">
                            <div class="pagination-info">
                                Menampilkan halaman <?php echo $page; ?> dari <?php echo $total_pages; ?> (Total: <?php echo $total_data; ?> user)
                            </div>

                            <?php
                            $query_string = !empty($search) ? "&search=" . urlencode($search) : "";
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
                        <div class="empty-state-icon">👥</div>
                        <p class="empty-state-text">Tidak ada data user yang ditemukan</p>
                        <?php if (!empty($search)): ?>
                            <p style="margin-top: 1rem; color: var(--alley);">
                                <a href="data_user.php" style="color: var(--blue); text-decoration: none; font-weight: 600;">Lihat semua user</a>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>