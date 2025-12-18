<?php
require 'auth_check.php';
include '../admin/koneksi.php';

// PROSES DOWNLOAD REPORT
if (isset($_GET['download'])) {
    $format = $_GET['download'];

    // Ambil data penjualan dari history_penjualan
    $res = mysqli_query($koneksi, "SELECT * FROM history_penjualan ORDER BY tanggal_selesai DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    // DOWNLOAD PDF
    if ($format === 'pdf') {
        require_once '../library/fpdf.php';

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 15, 'LAPORAN DATA PENJUALAN', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, 'Tanggal: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        $pdf->Ln(5);

        // Header Tabel
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(30, 93, 172);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(15, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'User ID', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'Nama Customer', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Produk', 1, 0, 'C', true);
        $pdf->Cell(15, 8, 'Qty', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Tanggal Selesai', 1, 1, 'C', true);

        // Data Tabel
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $nomor = 1;

        foreach ($data as $row) {
            // Parse produk dan quantity yang dipisah dengan koma
            $produk_array = array_map('trim', explode(',', $row['nama_produk']));
            $qty_array = array_map('trim', explode(',', $row['quantity']));

            // Pastikan jumlah produk dan qty sama
            $max_items = max(count($produk_array), count($qty_array));

            for ($i = 0; $i < $max_items; $i++) {
                $produk = $produk_array[$i] ?? '-';
                $qty = $qty_array[$i] ?? '-';

                if ($i === 0) {
                    // Baris pertama dengan info user
                    $pdf->Cell(15, 7, $nomor++, 1, 0, 'C');
                    $pdf->Cell(25, 7, $row['user_id'], 1, 0, 'C');
                    $pdf->Cell(45, 7, substr($row['nama_lengkap'], 0, 15), 1, 0, 'L');
                    $pdf->Cell(40, 7, substr($produk, 0, 15), 1, 0, 'L');
                    $pdf->Cell(15, 7, $qty, 1, 0, 'C');
                    $pdf->Cell(35, 7, date('d/m/Y', strtotime($row['tanggal_selesai'])), 1, 1, 'C');
                } else {
                    // Baris produk tambahan (tanpa info user)
                    $pdf->Cell(15, 7, '', 1, 0, 'C');
                    $pdf->Cell(25, 7, '', 1, 0, 'C');
                    $pdf->Cell(45, 7, '', 1, 0, 'L');
                    $pdf->Cell(40, 7, substr($produk, 0, 15), 1, 0, 'L');
                    $pdf->Cell(15, 7, $qty, 1, 0, 'C');
                    $pdf->Cell(35, 7, '', 1, 1, 'C');
                }
            }
        }

        // Output PDF
        $pdf->Output('D', 'Laporan_Penjualan_' . date('d-m-Y_H-i-s') . '.pdf');
        exit;
    }

    // DOWNLOAD EXCEL
    else if ($format === 'excel') {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Laporan_Penjualan_' . date('d-m-Y_H-i-s') . '.xls"');
        header('Cache-Control: max-age=0');

        echo '<html><head><meta charset="UTF-8"></head><body>';
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        echo '<tr style="background-color: #1e5dac; color: white; font-weight: bold;">';
        echo '<td colspan="8" style="text-align: center; font-size: 14px;">LAPORAN DATA PENJUALAN</td>';
        echo '</tr>';
        echo '<tr style="background-color: #1e5dac; color: white; font-weight: bold;">';
        echo '<td colspan="8" style="text-align: center;">Tanggal: ' . date('d/m/Y H:i:s') . '</td>';
        echo '</tr>';
        echo '<tr style="background-color: #1e5dac; color: white; font-weight: bold;">';
        echo '<td>No</td>';
        echo '<td>User ID</td>';
        echo '<td>Nama Customer</td>';
        echo '<td>Produk</td>';
        echo '<td>Kuantitas</td>';
        echo '<td>Metode Pembayaran</td>';
        echo '<td>Kurir</td>';
        echo '<td>Tanggal Selesai</td>';
        echo '</tr>';

        $nomor = 1;
        foreach ($data as $row) {
            // Parse produk dan quantity
            $produk_array = array_map('trim', explode(',', $row['nama_produk']));
            $qty_array = array_map('trim', explode(',', $row['quantity']));
            $max_items = max(count($produk_array), count($qty_array));

            for ($i = 0; $i < $max_items; $i++) {
                $produk = $produk_array[$i] ?? '-';
                $qty = $qty_array[$i] ?? '-';

                echo '<tr>';
                if ($i === 0) {
                    echo '<td>' . $nomor++ . '</td>';
                    echo '<td>' . $row['user_id'] . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_lengkap']) . '</td>';
                    echo '<td>' . htmlspecialchars($produk) . '</td>';
                    echo '<td style="text-align: center;">' . $qty . '</td>';
                    echo '<td>' . $row['metode_pembayaran'] . '</td>';
                    echo '<td>' . $row['kurir'] . '</td>';
                    echo '<td>' . date('d/m/Y H:i', strtotime($row['tanggal_selesai'])) . '</td>';
                } else {
                    echo '<td colspan="3"></td>';
                    echo '<td>' . htmlspecialchars($produk) . '</td>';
                    echo '<td style="text-align: center;">' . $qty . '</td>';
                    echo '<td colspan="3"></td>';
                }
                echo '</tr>';
            }
        }

        echo '</table>';
        echo '</body></html>';
        exit;
    }
}

$query = mysqli_query($koneksi, "SELECT * FROM history_penjualan ORDER BY tanggal_selesai DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">

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
            --success: #10b981;
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

        .btn-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-download {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .btn-pdf {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-pdf:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
        }

        .btn-excel {
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-excel:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
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

        .nomor-col {
            font-weight: 600;
            color: var(--blue);
            text-align: center;
        }

        .user-id-col {
            text-align: center;
        }

        .nama-col {
            font-weight: 500;
        }

        .qty-col {
            text-align: center;
            font-weight: 500;
        }

        .metode-col {
            text-align: center;
        }

        .kurir-col {
            text-align: center;
        }

        .tanggal-col {
            text-align: center;
            color: #6b7280;
        }

        .produk-col {
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

        /* SUMMARY SECTION */
        .summary-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.1);
            text-align: center;
            border-left: 5px solid var(--blue);
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(30, 93, 172, 0.15);
        }

        .summary-card.success {
            border-left-color: var(--success);
        }

        .summary-card h4 {
            color: var(--alley);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card .value {
            color: var(--blue);
            font-size: 1.8rem;
            font-weight: 700;
        }

        .summary-card.success .value {
            color: var(--success);
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

            .btn-group {
                width: 100%;
            }

            .btn-download {
                flex: 1;
                justify-content: center;
            }

            table {
                font-size: 0.8rem;
            }

            th,
            td {
                padding: 0.75rem;
            }

            .produk-col {
                max-width: 100px;
            }

            .summary-section {
                grid-template-columns: 1fr;
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
                <h1 class="page-title">📊 Data Penjualan</h1>
                <div class="btn-group">
                    <a href="?download=pdf" class="btn-download btn-pdf">
                        <i class="bi bi-file-pdf"></i> Download PDF
                    </a>
                    <a href="?download=excel" class="btn-download btn-excel">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Download Excel
                    </a>
                </div>
            </div>

            <hr>

            <!-- SUMMARY SECTION -->
            <?php
            $total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM history_penjualan");
            $summary = mysqli_fetch_assoc($total_query);

            // Hitung total pendapatan dari semua transaksi
            $pendapatan_query = mysqli_query($koneksi, "SELECT SUM(harga_total) as total_pendapatan FROM history_penjualan");
            $pendapatan = mysqli_fetch_assoc($pendapatan_query);
            $total_pendapatan = $pendapatan['total_pendapatan'] ?? 0;
            ?>
            <div class="summary-section">
                <div class="summary-card">
                    <h4>✓ Total Pesanan Selesai</h4>
                    <div class="value"><?= $summary['total'] ?? 0 ?></div>
                </div>
                <div class="summary-card success">
                    <h4>💰 Total Pendapatan</h4>
                    <div class="value">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                </div>
            </div>

            <!-- TABLE SECTION -->
            <div class="table-wrapper">
                <?php
                $res = mysqli_query($koneksi, "SELECT * FROM history_penjualan ORDER BY tanggal_selesai DESC");
                $count = mysqli_num_rows($res);
                ?>

                <?php if ($count > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>User ID</th>
                                    <th>Nama Customer</th>
                                    <th>Produk</th>
                                    <th>Kuantitas</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Kurir</th>
                                    <th>Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $nomor = 1;
                                while ($row = mysqli_fetch_assoc($res)):
                                    // Parse produk dan quantity yang dipisah dengan koma
                                    $produk_array = array_map('trim', explode(',', $row['nama_produk']));
                                    $qty_array = array_map('trim', explode(',', $row['quantity']));
                                    $max_items = max(count($produk_array), count($qty_array));
                                ?>
                                    <?php for ($i = 0; $i < $max_items; $i++): ?>
                                        <?php
                                        $produk = $produk_array[$i] ?? '-';
                                        $qty = $qty_array[$i] ?? '-';
                                        ?>
                                        <tr>
                                            <?php if ($i === 0): ?>
                                                <td class="nomor-col" rowspan="<?= $max_items ?>"><?= $nomor++ ?></td>
                                                <td class="user-id-col" rowspan="<?= $max_items ?>"><?= $row['user_id'] ?></td>
                                                <td class="nama-col" rowspan="<?= $max_items ?>"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                            <?php endif; ?>
                                            <td class="produk-col" title="<?= htmlspecialchars($produk) ?>"><?= htmlspecialchars($produk) ?></td>
                                            <td class="qty-col"><?= $qty ?> unit</td>
                                            <?php if ($i === 0): ?>
                                                <td class="metode-col" rowspan="<?= $max_items ?>"><?= $row['metode_pembayaran'] ?></td>
                                                <td class="kurir-col" rowspan="<?= $max_items ?>"><?= $row['kurir'] ?></td>
                                                <td class="tanggal-col" rowspan="<?= $max_items ?>"><?= date('d/m/Y H:i', strtotime($row['tanggal_selesai'])) ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endfor; ?>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📊</div>
                        <p class="empty-state-text">Belum ada data penjualan. Tunggu sampai pesanan selesai.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>