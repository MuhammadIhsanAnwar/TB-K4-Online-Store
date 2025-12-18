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
        $pdf->Cell(12, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'User ID', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Nama Customer', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Produk', 1, 0, 'C', true);
        $pdf->Cell(12, 8, 'Qty', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Harga', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Tanggal Selesai', 1, 1, 'C', true);

        // Data Tabel
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $nomor = 1;

        foreach ($data as $row) {
            // Parse produk dan quantity yang dipisah dengan koma
            $produk_array = array_map('trim', explode(',', $row['nama_produk']));
            $qty_array = array_map('trim', explode(',', $row['quantity']));
            $harga_total = $row['harga_total'] ?? 0;

            // Pastikan jumlah produk dan qty sama
            $max_items = max(count($produk_array), count($qty_array));

            for ($i = 0; $i < $max_items; $i++) {
                $produk = $produk_array[$i] ?? '-';
                $qty = $qty_array[$i] ?? '-';

                if ($i === 0) {
                    // Baris pertama dengan info user
                    $pdf->Cell(12, 7, $nomor++, 1, 0, 'C');
                    $pdf->Cell(20, 7, $row['user_id'], 1, 0, 'C');
                    $pdf->Cell(40, 7, substr($row['nama_lengkap'], 0, 12), 1, 0, 'L');
                    $pdf->Cell(50, 7, substr($produk, 0, 20), 1, 0, 'L');
                    $pdf->Cell(12, 7, $qty, 1, 0, 'C');
                    $pdf->Cell(25, 7, 'Rp ' . number_format($harga_total, 0, ',', '.'), 1, 0, 'R');
                    $pdf->Cell(30, 7, date('d/m/Y', strtotime($row['tanggal_selesai'])), 1, 1, 'C');
                } else {
                    // Baris produk tambahan (tanpa info user)
                    $pdf->Cell(12, 7, '', 1, 0, 'C');
                    $pdf->Cell(20, 7, '', 1, 0, 'C');
                    $pdf->Cell(40, 7, '', 1, 0, 'L');
                    $pdf->Cell(50, 7, substr($produk, 0, 20), 1, 0, 'L');
                    $pdf->Cell(12, 7, $qty, 1, 0, 'C');
                    $pdf->Cell(25, 7, '', 1, 0, 'R');
                    $pdf->Cell(30, 7, '', 1, 1, 'C');
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
        echo '<td colspan="9" style="text-align: center; font-size: 14px;">LAPORAN DATA PENJUALAN</td>';
        echo '</tr>';
        echo '<tr style="background-color: #1e5dac; color: white; font-weight: bold;">';
        echo '<td colspan="9" style="text-align: center;">Tanggal: ' . date('d/m/Y H:i:s') . '</td>';
        echo '</tr>';
        echo '<tr style="background-color: #1e5dac; color: white; font-weight: bold;">';
        echo '<td>No</td>';
        echo '<td>User ID</td>';
        echo '<td>Nama Customer</td>';
        echo '<td>Produk</td>';
        echo '<td>Kuantitas</td>';
        echo '<td>Harga Total</td>';
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
            $harga_total = $row['harga_total'] ?? 0;

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
                    echo '<td style="text-align: right;">Rp ' . number_format($harga_total, 0, ',', '.') . '</td>';
                    echo '<td>' . $row['metode_pembayaran'] . '</td>';
                    echo '<td>' . $row['kurir'] . '</td>';
                    echo '<td>' . date('d/m/Y H:i', strtotime($row['tanggal_selesai'])) . '</td>';
                } else {
                    echo '<td colspan="2"></td>';
                    echo '<td></td>';
                    echo '<td>' . htmlspecialchars($produk) . '</td>';
                    echo '<td style="text-align: center;">' . $qty . '</td>';
                    echo '<td colspan="4"></td>';
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
    <link rel="stylesheet" href="css_admin/data_penjualan_style.css">
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
                                    <th>Harga Total</th>
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
                                    $harga_total = $row['harga_total'] ?? 0;
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
                                            <td class="produk-col"><?= htmlspecialchars($produk) ?></td>
                                            <td class="qty-col"><?= $qty ?> unit</td>
                                            <?php if ($i === 0): ?>
                                                <td class="harga-col" rowspan="<?= $max_items ?>">Rp <?= number_format($harga_total, 0, ',', '.') ?></td>
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