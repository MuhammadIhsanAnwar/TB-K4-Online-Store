<?php require 'auth_check.php'; ?>
<?php include 'koneksi.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pesan</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">

    <style>
        :root {
            --primary: #1e5dac;
            --bg: #f3eded;
            --white: #ffffff;
            --text: #1f2937;
        }

        body {
            margin: 0;
            background: var(--bg);
            font-family: Poppins, system-ui, sans-serif;
        }

        /* ================= CONTENT ================= */
        .content {
            margin-left: 220px;
            padding: 30px;
            animation: fade .5s ease;
        }

        @keyframes fade {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            color: var(--primary);
            font-weight: 700;
        }

        hr {
            border-top: 2px solid #cfd6e6;
            margin-bottom: 20px;
        }

        /* ================= TABLE CONTAINER ================= */
        .table-responsive {
            background: var(--white);
            padding: 18px;
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(30, 93, 172, .2);
            overflow-x: auto;
        }

        /* ================= TABLE ================= */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            font-size: 14px;
            color: var(--text);
        }

        .table thead {
            background: linear-gradient(180deg, #1e63b6, #0f3f82);
            color: #fff;
        }

        .table thead th {
            border: none;
            padding: 14px 10px;
            text-align: center;
            white-space: nowrap;
            font-weight: 600;
        }

        .table tbody tr {
            transition: .25s;
        }

        .table tbody tr:hover {
            background: #eef3ff;
        }

        .table td {
            padding: 12px 10px;
            vertical-align: middle;
            border-top: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        /* ALIGN KHUSUS */
        .table td:nth-child(3),
        .table td:nth-child(4) {
            text-align: left;
            white-space: normal;
        }

        /* STATUS */
        .status-baru {
            color: #dc2626;
            font-weight: 600;
        }

        .status-dibaca {
            color: #16a34a;
            font-weight: 600;
        }

        /* BUTTON */
        .btn-read {
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 20px;
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
            text-decoration: none;
            transition: .25s;
        }

        .btn-read:hover {
            background: var(--primary);
            color: #fff;
        }
    </style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Data Pesan</h2>
    <hr>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm">
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
                <?php
                $query = mysqli_query($koneksi, "SELECT * FROM pesan_kontak ORDER BY created_at DESC");
                $no = 1;
                while ($row = mysqli_fetch_assoc($query)) :
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['pesan'])) ?></td>
                    <td>
                        <?php if ($row['status'] == 'baru'): ?>
                            <span class="status-baru">Baru</span>
                        <?php else: ?>
                            <span class="status-dibaca">Dibaca</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'baru'): ?>
                            <a href="baca_pesan.php?id=<?= $row['id'] ?>" class="btn-read">
                                Tandai Dibaca
                            </a>
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

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
