<?php
require 'auth_check.php';
include "../admin/koneksi.php";

/* ================= DELETE PESAN ================= */
if (isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);

    mysqli_query($koneksi, "DELETE FROM pesan_kontak WHERE id='$id'");
    echo json_encode(['status' => 'success', 'message' => 'Pesan berhasil dihapus']);
    exit;
}

/* Tandai pesan dibaca */
if (isset($_POST['read_id'])) {
    $id = intval($_POST['read_id']);
    mysqli_query($koneksi, "UPDATE pesan_kontak SET status='dibaca' WHERE id='$id'");
    echo json_encode(['status' => 'success', 'message' => 'Pesan ditandai dibaca']);
    exit;
}

// PARAMETER PAGINATION & SEARCH
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// BUILD QUERY
$where = "WHERE 1=1";
if (!empty($search)) {
    $where .= " AND (nama LIKE '%$search%' OR email LIKE '%$search%' OR pesan LIKE '%$search%')";
}

// TOTAL DATA
$total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesan_kontak $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $items_per_page);

// GET DATA
$query = mysqli_query($koneksi, "SELECT * FROM pesan_kontak $where ORDER BY created_at DESC LIMIT $offset, $items_per_page");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesan - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link rel="stylesheet" href="css_admin/data_pesan_style.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="container">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h1 class="page-title">💬 Data Pesan</h1>
            </div>

            <hr>

            <!-- SEARCH SECTION -->
            <div class="search-section">
                <form method="GET" class="search-group" style="flex: 1;">
                    <div style="width: 100%;">
                        <label for="search">🔍 Cari Pesan</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" id="search" name="search" placeholder="Cari berdasarkan nama, email, atau pesan..."
                                value="<?php echo htmlspecialchars($search); ?>" style="flex: 1;">
                            <button type="submit" class="btn-search">Cari</button>
                        </div>
                    </div>
                </form>
                <a href="data_pesan.php" class="btn-reset">↺ Reset</a>
            </div>

            <!-- TABLE SECTION -->
            <div class="table-wrapper">
                <?php
                $count = mysqli_num_rows($query);
                ?>

                <?php if ($count > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Pesan</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $nomor = $offset + 1;
                                while ($row = mysqli_fetch_assoc($query)):
                                ?>
                                    <tr id="row-<?= $row['id'] ?>">
                                        <td class="nomor-col"><?= $nomor++ ?></td>
                                        <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td class="pesan-col" title="<?= htmlspecialchars($row['pesan']) ?>"><?= htmlspecialchars($row['pesan']) ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'baru'): ?>
                                                <span class="status-badge badge-baru">🔔 Baru</span>
                                            <?php else: ?>
                                                <span class="status-badge badge-dibaca">✓ Dibaca</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <div class="aksi-col">
                                                <!-- Tombol Balas Pesan -->
                                                <a href="balas_pesan.php?id=<?= $row['id'] ?>&email=<?= urlencode($row['email']) ?>&nama=<?= urlencode($row['nama']) ?>"
                                                    class="btn-action btn-balas"
                                                    title="Balas Pesan">
                                                    <i class="bi bi-reply"></i> Balas
                                                </a>

                                                <!-- Tombol Tandai Dibaca -->
                                                <?php if ($row['status'] == 'baru'): ?>
                                                    <button class="btn-action btn-dibaca"
                                                        onclick="tandaiDibaca(<?= $row['id'] ?>)"
                                                        title="Tandai Dibaca">
                                                        <i class="bi bi-check2-circle"></i> Dibaca
                                                    </button>
                                                <?php endif; ?>

                                                <!-- Tombol Hapus -->
                                                <button class="btn-action btn-delete"
                                                    onclick="hapusPesan(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['nama'])) ?>')"
                                                    title="Hapus Pesan">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
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
                                Menampilkan halaman <?php echo $page; ?> dari <?php echo $total_pages; ?> (Total: <?php echo $total_data; ?> pesan)
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
                        <div class="empty-state-icon">💬</div>
                        <p class="empty-state-text">Tidak ada pesan yang ditemukan</p>
                        <?php if (!empty($search)): ?>
                            <p style="margin-top: 1rem; color: var(--alley);">
                                <a href="data_pesan.php" style="color: var(--blue); text-decoration: none; font-weight: 600;">Lihat semua pesan</a>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function tandaiDibaca(id) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === "success") {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                } catch (e) {
                    location.reload();
                }
            };
            xhr.send("read_id=" + id);
        }

        function hapusPesan(id, nama) {
            Swal.fire({
                title: 'Hapus Pesan?',
                html: `<p>Yakin ingin menghapus pesan dari <strong>${nama}</strong>?</p>`,
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
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    };
                    xhr.send("delete_id=" + id);
                }
            });
        }

        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }
    </script>

</body>

</html>