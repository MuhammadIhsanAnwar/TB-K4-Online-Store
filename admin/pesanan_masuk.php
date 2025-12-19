<?php
session_start();
require 'auth_check.php';
include 'koneksi.php';

if (isset($_POST['update_status_dikemas'])) {
    $order_id = intval($_POST['order_id']);

    $update_query = "UPDATE pemesanan SET status='Sedang Dikemas' WHERE id='$order_id'";
    if (mysqli_query($koneksi, $update_query)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Status diperbarui menjadi Sedang Dikemas'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memperbarui status: ' . mysqli_error($koneksi)
        ]);
    }
    exit;
}

if (isset($_POST['input_resi'])) {
    $order_id = intval($_POST['order_id']);
    $resi = mysqli_real_escape_string($koneksi, $_POST['resi']);

    if (empty($resi)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Nomor resi tidak boleh kosong'
        ]);
        exit;
    }

    $update_query = "UPDATE pemesanan 
                     SET resi='$resi', status='Sedang Dikirim' 
                     WHERE id='$order_id'";

    if (mysqli_query($koneksi, $update_query)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Resi berhasil diinput dan status diperbarui menjadi Sedang Dikirim'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menginput resi: ' . mysqli_error($koneksi)
        ]);
    }
    exit;
}

if (isset($_POST['selesaikan_pesanan'])) {
    $order_id = intval($_POST['order_id']);
    mysqli_begin_transaction($koneksi);

    try {
        $order_query = "SELECT * FROM pemesanan WHERE id='$order_id'";
        $order_result = mysqli_query($koneksi, $order_query);

        if (!$order_result) {
            throw new Exception(mysqli_error($koneksi));
        }

        $order = mysqli_fetch_assoc($order_result);
        if (!$order) {
            throw new Exception('Pesanan tidak ditemukan');
        }

        $insert_history = "
            INSERT INTO history_penjualan
            (user_id, nama_lengkap, nomor_hp, alamat_lengkap, nama_produk, quantity,
             harga_total, metode_pembayaran, kurir, resi, tanggal_dipesan, tanggal_selesai)
            SELECT user_id, nama_lengkap, nomor_hp, alamat_lengkap, nama_produk, quantity,
                   harga_total, metode_pembayaran, kurir, resi, waktu_pemesanan, NOW()
            FROM pemesanan WHERE id='$order_id'
        ";

        if (!mysqli_query($koneksi, $insert_history)) {
            throw new Exception(mysqli_error($koneksi));
        }

        $delete_query = "DELETE FROM pemesanan WHERE id='$order_id'";
        if (!mysqli_query($koneksi, $delete_query)) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_commit($koneksi);
        echo json_encode([
            'status' => 'success',
            'message' => 'Pesanan berhasil diselesaikan'
        ]);
        exit;

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

$query = "SELECT * FROM pemesanan ORDER BY waktu_pemesanan DESC";
$result = mysqli_query($koneksi, $query);
$pesanan = [];

while ($row = mysqli_fetch_assoc($result)) {
    $pesanan[] = $row;
}

$history_query = "SELECT * FROM history_penjualan ORDER BY tanggal_selesai DESC";
$history_result = mysqli_query($koneksi, $history_query);
$pesanan_selesai = [];

while ($row = mysqli_fetch_assoc($history_result)) {
    $pesanan_selesai[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk - Admin</title>

    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css_admin/pesanan_masuk_style.css">
</head>

<body>
<div class="wrapper">
<?php include 'sidebar.php'; ?>

<div class="main-content">
<div class="container-fluid">

<div class="page-header">
    <h1 class="page-title">
        <i class="bi bi-box-seam"></i> Pesanan
    </h1>
</div>

<div class="tabs-container">
<div class="tabs-header">
    <button class="tab-btn active" onclick="switchTab('pesanan-masuk')">
        <i class="bi bi-inbox"></i> Pesanan Masuk
        <span class="tab-badge"><?= count($pesanan); ?></span>
    </button>
    <button class="tab-btn" onclick="switchTab('pesanan-selesai')">
        <i class="bi bi-check-circle"></i> Pesanan Selesai
        <span class="tab-badge"><?= count($pesanan_selesai); ?></span>
    </button>
</div>

<div id="pesanan-masuk" class="tabs-content" style="display:block">

<div class="stats-container">
    <div class="stat-card">
        <div class="stat-label"><i class="bi bi-box"></i> Total Pesanan</div>
        <div class="stat-value"><?= $total; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="bi bi-hourglass-split"></i> Menunggu</div>
        <div class="stat-value"><?= $menunggu; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="bi bi-archive"></i> Dikemas</div>
        <div class="stat-value"><?= $dikemas; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="bi bi-truck"></i> Dikirim</div>
        <div class="stat-value"><?= $dikirim; ?></div>
    </div>
</div>

<div class="filter-section">
<div class="filter-title">
    <i class="bi bi-funnel"></i> Filter Status
</div>
<div class="filter-buttons">
    <button class="filter-btn active" onclick="filterByStatus('semua')">Semua</button>
    <button class="filter-btn" onclick="filterByStatus('Menunggu Konfirmasi')">Menunggu</button>
    <button class="filter-btn" onclick="filterByStatus('Sedang Dikemas')">Dikemas</button>
    <button class="filter-btn" onclick="filterByStatus('Sedang Dikirim')">Dikirim</button>
</div>
<div class="search-box">
    <input type="text" id="searchMasuk" placeholder="Cari data..." onkeyup="searchTable('ordersList','searchMasuk')">
</div>
</div>

<div class="table-wrapper">
<table class="orders-table" id="ordersList">
<thead>
<tr>
<th>ID</th>
<th>Nama</th>
<th>HP</th>
<th>Alamat</th>
<th>Produk</th>
<th>Qty</th>
<th>Total</th>
<th>Kurir</th>
<th>Resi</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>

<?php foreach ($pesanan as $o): ?>
<tr class="order-row" data-status="<?= $o['status']; ?>">
<td>#<?= str_pad($o['id'],6,'0',STR_PAD_LEFT); ?></td>
<td><?= htmlspecialchars($o['nama_lengkap']); ?></td>
<td><?= $o['nomor_hp']; ?></td>
<td><?= $o['alamat_lengkap']; ?></td>
<td><?= $o['nama_produk']; ?></td>
<td><?= $o['quantity']; ?></td>
<td>Rp <?= number_format($o['harga_total'],0,',','.'); ?></td>
<td><?= $o['kurir']; ?></td>
<td><?= $o['resi'] ?: '-'; ?></td>
<td>
<span class="status-badge status-<?= strtolower(str_replace(' ','-',$o['status'])); ?>">
<?= $o['status']; ?>
</span>
</td>
<td>
<?php if($o['status']=='Menunggu Konfirmasi'): ?>
<button class="btn-action btn-dikemas" onclick="updateStatusDikemas(<?= $o['id']; ?>)">
<i class="bi bi-box-arrow-in-down"></i>
</button>
<?php endif; ?>

<?php if($o['status']!='Sedang Dikirim'): ?>
<button class="btn-action btn-resi" onclick="showResiForm(<?= $o['id']; ?>)">
<i class="bi bi-upc-scan"></i>
</button>
<?php endif; ?>

<?php if($o['status']=='Sedang Dikirim'): ?>
<button class="btn-action btn-selesai" onclick="selesaikanPesanan(<?= $o['id']; ?>)">
<i class="bi bi-check2-circle"></i>
</button>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

<div class="pagination-container" id="paginationMasuk"></div>
</div>

</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
const ITEMS_PER_PAGE = 10;
</script>

</body>
</html>
<script>
function switchTab(tabId) {
    document.querySelectorAll('.tabs-content').forEach(tab => tab.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tabId).style.display = 'block';
    event.currentTarget.classList.add('active');
}

function filterByStatus(status) {
    document.querySelectorAll('.order-row').forEach(row => {
        const rowStatus = row.dataset.status;
        if (status === 'semua' || rowStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function searchTable(tableId, inputId) {
    const filter = document.getElementById(inputId).value.toLowerCase();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
}

function updateStatusDikemas(id) {
    Swal.fire({
        icon: 'question',
        title: 'Ubah status ke Dikemas?',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        confirmButtonColor: '#1E5DAC'
    }).then(res => {
        if (res.isConfirmed) {
            location.href = 'update_status.php?id=' + id + '&status=dikemas';
        }
    });
}

function showResiForm(id) {
    Swal.fire({
        title: 'Masukkan Resi',
        input: 'text',
        inputPlaceholder: 'Nomor Resi',
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        confirmButtonColor: '#1E5DAC'
    }).then(result => {
        if (result.isConfirmed && result.value) {
            location.href = 'update_resi.php?id=' + id + '&resi=' + result.value;
        }
    });
}

function selesaikanPesanan(id) {
    Swal.fire({
        icon: 'success',
        title: 'Selesaikan Pesanan?',
        showCancelButton: true,
        confirmButtonText: 'Selesai',
        confirmButtonColor: '#28a745'
    }).then(res => {
        if (res.isConfirmed) {
            location.href = 'update_status.php?id=' + id + '&status=selesai';
        }
    });
}
</script>
