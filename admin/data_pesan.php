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

$query = mysqli_query($koneksi, "SELECT * FROM pesan_kontak ORDER BY created_at DESC");
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

        hr {
            border-top: 2px solid #cfd6e6;
            margin-bottom: 20px;
            opacity: .6;
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

        /* KOLOM PESAN */
        .pesan-col {
            max-width: 300px;
            color: #6b7280;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* KOLOM STATUS */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-baru {
            background: rgba(220, 53, 69, 0.2);
            color: #721c24;
        }

        .badge-dibaca {
            background: rgba(25, 135, 84, 0.2);
            color: #155724;
        }

        /* KOLOM AKSI */
        .aksi-col {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            flex-wrap: wrap;
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

        .btn-balas {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .btn-balas:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-dibaca {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .btn-dibaca:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
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

            table {
                font-size: 0.8rem;
            }

            th,
            td {
                padding: 0.75rem;
            }

            .pesan-col {
                max-width: 100px;
            }

            .btn-action {
                padding: 6px 10px;
                font-size: 0.7rem;
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
                <h1 class="page-title">💬 Data Pesan</h1>
            </div>

            <hr>

            <!-- TABLE SECTION -->
            <div class="table-wrapper">
                <?php
                $count = mysqli_num_rows($query);
                mysqli_data_seek($query, 0);
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
                                $nomor = 1;
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
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">💬</div>
                        <p class="empty-state-text">Belum ada pesan masuk</p>
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