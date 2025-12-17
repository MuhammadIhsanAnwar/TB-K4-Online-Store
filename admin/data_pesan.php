<?php
require 'auth_check.php';
include "../admin/koneksi.php";

/* Tandai pesan dibaca */
if (isset($_POST['read_id'])) {
    $id = intval($_POST['read_id']);
    mysqli_query($koneksi, "UPDATE pesan_kontak SET status='dibaca' WHERE id='$id'");
    echo 'success';
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM pesan_kontak ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Pesan - Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">

    <!-- 🔥 CSS DISAMAKAN DENGAN DATA PRODUK -->
    <style>
        :root {
            --primary: #1e5dac;
            --bg: #f3eded;
            --white: #ffffff;
            --hover-blue: rgba(30, 93, 172, .1);
        }

        body {
            margin: 0;
            background: var(--bg);
            font-family: Poppins, system-ui, sans-serif;
        }

        /* ===== CONTENT ===== */
        .content {
            margin-left: 220px;
            padding: 30px 40px;
            animation: fade .5s ease;
        }

        @keyframes fade {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }

        hr {
            border-top: 2px solid #cfd6e6;
            margin-bottom: 20px;
            opacity: .6;
        }

        /* ===== TABLE CONTAINER ===== */
        .table-container {
            background: var(--white);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, .1);
        }

        .table {
            border-collapse: separate !important;
            border-spacing: 0 10px;
        }

        .table thead tr {
            background: var(--primary);
            color: #fff;
        }

        .table tbody tr {
            background: #fff;
            transition: .3s;
        }

        .table tbody tr:hover {
            background: var(--hover-blue);
        }

        .table td,
        .table th {
            padding: 12px;
            vertical-align: middle;
            text-align: center;
        }

        .table td:nth-child(4) {
            text-align: left;
        }

        .badge-baru {
            background: #dc3545;
            padding: 6px 10px;
            border-radius: 20px;
            color: #fff;
            font-size: 12px;
        }

        .badge-dibaca {
            background: #198754;
            padding: 6px 10px;
            border-radius: 20px;
            color: #fff;
            font-size: 12px;
        }

        .btn-info {
            border-radius: 8px;
        }

:root {
    --primary: #1e5dac;
    --bg: #f3eded;
    --white: #ffffff;
}

body {
    margin: 0;
    background: var(--bg);
    font-family: Poppins, system-ui, sans-serif;
}

/* ===== SIDEBAR ===== */
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
    filter: drop-shadow(0 6px 12px rgba(0,0,0,.25));
}

.menu-title {
    color: #dbe6ff;
    font-size: 13px;
    padding: 8px 20px;
}

.sidebar a {
    color: #fff;
    text-decoration: none;
    padding: 12px 20px;
    margin: 4px 12px;
    border-radius: 10px;
    transition: .25s;
}

.sidebar a:hover {
    background: rgba(255,255,255,.18);
}

.sidebar a.active {
    background: rgba(255,255,255,.32);
    font-weight: 600;
}

.sidebar .logout {
    margin-top: auto;
    background: rgba(255,80,80,.15);
    color: #ffd6d6 !important;
    text-align: center;
    border-radius: 14px;
}

/* ===== CONTENT ===== */
.content {
    padding: 30px;
    transition: .3s;
}

/* Desktop saja */
@media (min-width: 992px) {
    .content {
        margin-left: 220px;
    }
}

/* SIDEBAR MOBILE */
@media (max-width: 991px) {
    .sidebar {
        transform: translateX(-100%);
        transition: .3s;
    }

    .sidebar.show {
        transform: translateX(0);
    }
}


    </style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="d-lg-none mb-3">
    <button class="btn btn-primary" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>
</div>

   <div class="d-flex justify-content-between align-items-center flex-wrap">
    <h2 class="mb-2 mb-lg-0">Data Pesan</h2>
</div>

    <hr>

    <div class="table-container table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Pesan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php $no=1; while($row = mysqli_fetch_assoc($query)): ?>
                <tr id="row-<?= $row['id'] ?>">
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['pesan'])) ?></td>
                    <td>
                        <?php if ($row['status']=='baru'): ?>
                            <span class="badge-baru">Baru</span>
                        <?php else: ?>
                            <span class="badge-dibaca">Dibaca</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status']=='baru'): ?>
                            <button class="btn btn-sm btn-info"
                                onclick="tandaiDibaca(<?= $row['id'] ?>)">
                                <i class="bi bi-check2-circle"></i>
                            </button>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function tandaiDibaca(id) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.responseText.trim() === "success") {
            location.reload();
        }
    };
    xhr.send("read_id=" + id);
}
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('show');
}
</script>

</body>
</html>
