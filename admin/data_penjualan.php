<?php
require 'auth_check.php';
include '../admin/koneksi.php';

if (isset($_GET['download'])) {
    $format = $_GET['download'];

    $res = mysqli_query($koneksi, "SELECT * FROM history_penjualan ORDER BY tanggal_selesai DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    // Hitung total pendapatan
    $total_pendapatan = 0;
    foreach ($data as $row) {
        $total_pendapatan += (int)$row['harga_total'];
    }

    if ($format === 'pdf') {
        require_once '../library/fpdf.php';

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 15, 'LAPORAN DATA PENJUALAN', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, 'Tanggal: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(30, 93, 172);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(8, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(12, 8, 'User', 1, 0, 'C', true);
        $pdf->Cell(32, 8, 'Nama Customer', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Produk', 1, 0, 'C', true);
        $pdf->Cell(8, 8, 'Qty', 1, 0, 'C', true);
        $pdf->Cell(24, 8, 'Total', 1, 0, 'C', true);
        $pdf->Cell(15, 8, 'Bayar', 1, 0, 'C', true);
        $pdf->Cell(15, 8, 'Kurir', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Tanggal', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 7.5);
        $pdf->SetTextColor(0, 0, 0);
        $nomor = 1;

        foreach ($data as $row) {
            $produk_array = array_map('trim', explode(',', $row['nama_produk']));
            $qty_array = array_map('trim', explode(',', $row['quantity']));
            $harga_total = $row['harga_total'] ?? 0;
            $max_items = max(count($produk_array), count($qty_array));

            for ($i = 0; $i < $max_items; $i++) {
                $produk = $produk_array[$i] ?? '-';
                $qty = $qty_array[$i] ?? '-';

                $pdf->Cell(8, 6.5, $i === 0 ? $nomor++ : '', 1, 0, 'C');
                $pdf->Cell(12, 6.5, $i === 0 ? $row['user_id'] : '', 1, 0, 'C');
                $pdf->Cell(32, 6.5, $i === 0 ? substr($row['nama_lengkap'], 0, 18) : '', 1);
                $pdf->Cell(35, 6.5, substr($produk, 0, 18), 1);
                $pdf->Cell(8, 6.5, $qty, 1, 0, 'C');
                $pdf->Cell(24, 6.5, $i === 0 ? 'Rp ' . number_format($harga_total, 0, ',', '.') : '', 1, 0, 'R');
                $pdf->Cell(15, 6.5, $i === 0 ? $row['metode_pembayaran'] : '', 1, 0, 'C');
                $pdf->Cell(15, 6.5, $i === 0 ? $row['kurir'] : '', 1, 0, 'C');
                $pdf->Cell(20, 6.5, $i === 0 ? date('d/m/Y', strtotime($row['tanggal_selesai'])) : '', 1, 1, 'C');
            }
        }

        // Tambah baris total dengan alignment yang rapi
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(200, 220, 240);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(97, 8, 'TOTAL PENDAPATAN', 1, 0, 'R', true);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(24, 8, 'Rp ' . number_format($total_pendapatan, 0, ',', '.'), 1, 0, 'R', true);
        $pdf->Cell(60, 8, '', 1, 1, '', true);

        $pdf->Output('D', 'Laporan_Penjualan.pdf');
        exit;
    } elseif ($format === 'excel') {
        // Export Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Penjualan.xls"');
        
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">
        <head>
            <meta charset="UTF-8" />
            <style>
                table { border-collapse: collapse; width: 100%; }
                th { background-color: #1E5DAC; color: white; border: 1px solid #000; padding: 8px; font-weight: bold; }
                td { border: 1px solid #000; padding: 8px; }
                .total-row { background-color: #C8DCF0; font-weight: bold; }
            </style>
        </head>
        <body>
        <h2 style="text-align: center;">LAPORAN DATA PENJUALAN</h2>
        <p style="text-align: center;">Tanggal: ' . date('d/m/Y H:i:s') . '</p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>User ID</th>
                    <th>Nama</th>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Kurir</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>';

        $nomor = 1;
        foreach ($data as $row) {
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
                } else {
                    echo '<td></td><td></td><td></td>';
                }
                echo '<td>' . htmlspecialchars($produk) . '</td>';
                echo '<td>' . $qty . ' unit</td>';
                if ($i === 0) {
                    echo '<td>Rp ' . number_format($row['harga_total'], 0, ',', '.') . '</td>';
                    echo '<td>' . $row['metode_pembayaran'] . '</td>';
                    echo '<td>' . $row['kurir'] . '</td>';
                    echo '<td>' . date('d/m/Y H:i', strtotime($row['tanggal_selesai'])) . '</td>';
                } else {
                    echo '<td></td><td></td><td></td><td></td>';
                }
                echo '</tr>';
            }
        }

        // Tambah baris total
        echo '<tr class="total-row">
                <td colspan="5" style="text-align: right;">TOTAL PENDAPATAN</td>
                <td colspan="4">Rp ' . number_format($total_pendapatan, 0, ',', '.') . '</td>
              </tr>';

        echo '</tbody>
        </table>
        </body>
        </html>';
        exit;
    }
}

$query = mysqli_query($koneksi, "SELECT * FROM history_penjualan ORDER BY tanggal_selesai DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Penjualan - Admin</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" href="../images/icon/logo.png">
    <link rel="stylesheet" href="css_admin/data_penjualan_style.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="container">

        <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-graph-up-arrow"></i> Data Penjualan
            </h1>

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

        <?php
        $total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM history_penjualan"));
        $pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(harga_total) as total FROM history_penjualan"));
        ?>

        <div class="summary-section">
            <div class="summary-card">
                <h4>
                    <i class="bi bi-check-circle"></i> Total Pesanan Selesai
                </h4>
                <div class="value"><?= $total['total'] ?? 0 ?></div>
            </div>

            <div class="summary-card success">
                <h4>
                    <i class="bi bi-cash-coin"></i> Total Pendapatan
                </h4>
                <div class="value">
                    Rp <?= number_format($pendapatan['total'] ?? 0, 0, ',', '.') ?>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <?php if (mysqli_num_rows($query) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User ID</th>
                            <th>Nama</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Kurir</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)):
                            $produk = explode(',', $row['nama_produk']);
                            $qty = explode(',', $row['quantity']);
                            $max = max(count($produk), count($qty));
                        ?>
                        <?php for ($i = 0; $i < $max; $i++): ?>
                        <tr>
                            <?php if ($i === 0): ?>
                                <td rowspan="<?= $max ?>"><?= $no++ ?></td>
                                <td rowspan="<?= $max ?>"><?= $row['user_id'] ?></td>
                                <td rowspan="<?= $max ?>"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($produk[$i] ?? '-') ?></td>
                            <td><?= $qty[$i] ?? '-' ?> unit</td>
                            <?php if ($i === 0): ?>
                                <td rowspan="<?= $max ?>">Rp <?= number_format($row['harga_total'], 0, ',', '.') ?></td>
                                <td rowspan="<?= $max ?>"><?= $row['metode_pembayaran'] ?></td>
                                <td rowspan="<?= $max ?>"><?= $row['kurir'] ?></td>
                                <td rowspan="<?= $max ?>"><?= date('d/m/Y H:i', strtotime($row['tanggal_selesai'])) ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endfor; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-bar-chart-line empty-state-icon"></i>
                    <p>Belum ada data penjualan</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>
