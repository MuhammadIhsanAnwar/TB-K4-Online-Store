<?php
require 'auth_check.php';
include '../admin/koneksi.php';
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

        /* KOLOM FOTO PROFIL */
        .foto-profil-col {
            text-align: center;
        }

        .foto-profil-col img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--alley);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* KOLOM STATUS */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
        }

        .status-aktif {
            background: rgba(16, 185, 129, 0.2);
            color: #065f46;
        }

        .status-nonaktif {
            background: rgba(239, 68, 68, 0.2);
            color: #7f1d1d;
        }

        /* KOLOM EMAIL & ALAMAT - Truncate text */
        .text-truncate-col {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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

            .text-truncate-col {
                max-width: 100px;
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
                <h1 class="page-title">👥 Data User</h1>
            </div>

            <hr>

            <!-- TABLE SECTION -->
            <div class="table-wrapper">
                <?php
                $res = mysqli_query($koneksi, "SELECT * FROM akun_user ORDER BY id DESC");
                $count = mysqli_num_rows($res);
                ?>

                <?php if ($count > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nomor</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Provinsi</th>
                                    <th>Kabupaten/Kota</th>
                                    <th>Alamat</th>
                                    <th>Foto Profil</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $nomor = 1;
                                mysqli_data_seek($res, 0);
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
                                        <td class="foto-profil-col">
                                            <?php if (!empty($row['foto_profil']) && file_exists("../foto_profil/" . $row['foto_profil'])): ?>
                                                <img src="../foto_profil/<?= htmlspecialchars($row['foto_profil']) ?>" alt="<?= htmlspecialchars($row['username']) ?>">
                                            <?php else: ?>
                                                <div style="width: 50px; height: 50px; background: var(--misty); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--alley); margin: 0 auto;">👤</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['status'] === 'aktif'): ?>
                                                <span class="status-badge status-aktif">✓ Aktif</span>
                                            <?php else: ?>
                                                <span class="status-badge status-nonaktif">✗ Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">👥</div>
                        <p class="empty-state-text">Belum ada data user</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>