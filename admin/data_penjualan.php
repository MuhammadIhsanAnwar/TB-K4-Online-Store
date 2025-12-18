<?php
require 'auth_check.php';
include '../admin/koneksi.php';

/* =======================
   PROSES DOWNLOAD REPORT
   ======================= */
if (isset($_GET['download'])) {
    $format = $_GET['download'];

    $res = mysqli_query($koneksi, "SELECT * FROM history_penjualan ORDER BY tanggal_selesai DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
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
        $pdf->Cell(12, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'User ID', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Nama Customer', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Produk', 1, 0, 'C', true);
        $pdf->Cell(12, 8, 'Qty', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Harga', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Tanggal', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 8);
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

                $pdf->Cell(12, 7, $i === 0 ? $nomor++ : '', 1);
                $pdf->Cell(20, 7, $i === 0 ? $row['user_id'] : '', 1);
                $pdf->Cell(40, 7, $i === 0 ? substr($row['nama_lengkap'], 0, 15) : '', 1);
                $pdf->Cell(50, 7, substr($produk, 0, 20), 1);
                $pdf->Cell(12, 7, $qty, 1, 0, 'C');
                $pdf->Cell(25, 7, $i === 0 ? 'Rp ' . number_format($harga_total, 0, ',', '.') : '', 1, 0, 'R');
                $pdf->Cell(30, 7, $i === 0 ? date('d/m/Y', strtotime($row['tanggal_selesai'])) : '', 1, 1);
            }
        }

        $pdf->Output('D', 'Laporan_Penjualan.pdf');
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

        <!-- HEADER -->
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

        <!-- SUMMARY -->
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

        <!-- TABLE -->
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
