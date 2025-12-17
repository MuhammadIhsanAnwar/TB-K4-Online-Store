<?php
include 'auth_check.php';
include 'koneksi.php';

$query = mysqli_query($koneksi,
    "SELECT * FROM pesan_kontak ORDER BY created_at DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pesan - Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">

    <style>
        :root {
            --primary: #1e5dac;
            --bg: #f3eded;
            --white: #ffffff;
            --hover: rgba(30,93,172,.08);
        }

        body {
            margin: 0;
            background: var(--bg);
            font-family: Poppins, system-ui, sans-serif;
        }

        /* ===== CONTENT ===== */
        .content {
            margin-left: 220px;
            padding: 30px;
            min-height: 100vh;
            animation: fade .5s ease;
        }

        @keyframes fade {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: var(--primary);
            font-weight: 700;
        }

        hr {
            border-top: 2px solid #cfd6e6;
            margin-bottom: 25px;
        }

        /* ===== CARD TABLE ===== */
        .card-table {
            background: var(--white);
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 15px 40px rgba(0,0,0,.12);
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        thead tr {
            background: var(--primary);
            color: #fff;
        }

        thead th {
            padding: 14px;
            text-align: center;
            border: none;
        }

        tbody tr {
            background: #fff;
            transition: .25s;
        }

        tbody tr:hover {
            background: var(--hover);
        }

        tbody td {
            padding: 14px;
            vertical-align: middle;
        }

        tbody td:nth-child(4) {
            max-width: 350px;
        }

        /* ===== STATUS ===== */
        .status-baru {
            color: #dc3545;
            font-weight: 600;
        }

        .status-dibaca {
            color: #16a34a;
            font-weight: 600;
        }

        /* ===== BUTTON ===== */
        .btn-read {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            transition: .3s;
            text-decoration: none;
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
    <h2>Pesan Masuk</h2>
    <hr>

    <div class="card-table">
        <table>
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
            <?php $no=1; while($row=mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td align="center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['pesan'])) ?></td>
                    <td align="center">
                        <?php if($row['status']=='baru'): ?>
                            <span class="status-baru">Baru</span>
                        <?php else: ?>
                            <span class="status-dibaca">Dibaca</span>
                        <?php endif; ?>
                    </td>
                    <td align="center">
                        <?php if($row['status']=='baru'): ?>
                            <a href="baca_pesan.php?id=<?= $row['id'] ?>"
                               class="btn-read">
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
