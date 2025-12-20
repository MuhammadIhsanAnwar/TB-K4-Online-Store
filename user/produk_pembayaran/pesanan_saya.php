<?php
session_start();
include "../../admin/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../user/login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);

    $check_query = "SELECT status FROM pemesanan WHERE id='$order_id' AND user_id='$user_id'";
    $check_result = mysqli_query($koneksi, $check_query);
    $order = mysqli_fetch_assoc($check_result);

    if ($order && ($order['status'] === 'Menunggu Konfirmasi' || $order['status'] === 'Sedang Dikemas')) {

        // Langsung hapus pesanan
        $delete_query = "DELETE FROM pemesanan WHERE id='$order_id' AND user_id='$user_id'";

        if (mysqli_query($koneksi, $delete_query)) {
            echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil dibatalkan dan dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membatalkan pesanan: ' . mysqli_error($koneksi)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Pesanan tidak bisa dibatalkan (status tidak memungkinkan)']);
    }
    exit;
}

if (isset($_POST['selesaikan_pesanan'])) {
    $order_id = intval($_POST['order_id']);

    mysqli_begin_transaction($koneksi);

    try {

        $order_query = "SELECT * FROM pemesanan WHERE id='$order_id' AND user_id='$user_id'";
        $order_result = mysqli_query($koneksi, $order_query);

        if (!$order_result) {
            throw new Exception('Database error: ' . mysqli_error($koneksi));
        }

        $order = mysqli_fetch_assoc($order_result);

        if (!$order) {
            throw new Exception('Pesanan tidak ditemukan');
        }

        if ($order['status'] !== 'Sedang Dikirim') {
            throw new Exception('Pesanan hanya bisa diselesaikan jika sedang dikirim');
        }

        $insert_history = "INSERT INTO history_penjualan 
                          (user_id, nama_lengkap, nomor_hp, alamat_lengkap, nama_produk, quantity, 
                           harga_total, metode_pembayaran, kurir, resi, tanggal_dipesan, tanggal_selesai)
                          VALUES ('$order[user_id]', 
                                  '" . mysqli_real_escape_string($koneksi, $order['nama_lengkap']) . "',
                                  '" . mysqli_real_escape_string($koneksi, $order['nomor_hp']) . "',
                                  '" . mysqli_real_escape_string($koneksi, $order['alamat_lengkap']) . "',
                                  '" . mysqli_real_escape_string($koneksi, $order['nama_produk']) . "',
                                  '$order[quantity]',
                                  '$order[harga_total]',
                                  '" . mysqli_real_escape_string($koneksi, $order['metode_pembayaran']) . "',
                                  '" . mysqli_real_escape_string($koneksi, $order['kurir']) . "',
                                  '" . mysqli_real_escape_string($koneksi, $order['resi']) . "',
                                  '$order[waktu_pemesanan]',
                                  NOW())";

        if (!mysqli_query($koneksi, $insert_history)) {
            throw new Exception('Gagal menambah ke history penjualan: ' . mysqli_error($koneksi));
        }

        $delete_query = "DELETE FROM pemesanan WHERE id='$order_id'";
        if (!mysqli_query($koneksi, $delete_query)) {
            throw new Exception('Gagal menghapus pesanan: ' . mysqli_error($koneksi));
        }

        mysqli_commit($koneksi);
        echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil diselesaikan']);
        exit;
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'semua';

$query = "SELECT * FROM pemesanan WHERE user_id='$user_id'";

if ($status_filter !== 'semua' && $status_filter !== 'Selesai') {
    $query .= " AND status='" . mysqli_real_escape_string($koneksi, $status_filter) . "'";
}

$query .= " ORDER BY waktu_pemesanan DESC";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Database Error: " . mysqli_error($koneksi));
}

$pesanan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pesanan[] = $row;
}

$pesanan_selesai = [];
if ($status_filter === 'semua' || $status_filter === 'Selesai') {
    $history_query = "SELECT * FROM history_penjualan WHERE user_id='$user_id' ORDER BY tanggal_selesai DESC";
    $history_result = mysqli_query($koneksi, $history_query);

    if ($history_result) {
        while ($row = mysqli_fetch_assoc($history_result)) {
            $pesanan_selesai[] = $row;
        }
    }
}

$status_counts = [
    'Menunggu Konfirmasi' => 0,
    'Sedang Dikemas' => 0,
    'Sedang Dikirim' => 0,
    'Selesai' => 0,
    'Pesanan Batal' => 0
];

$count_query = "SELECT status, COUNT(*) as count FROM pemesanan WHERE user_id='$user_id' GROUP BY status";
$count_result = mysqli_query($koneksi, $count_query);
while ($row = mysqli_fetch_assoc($count_result)) {
    if (isset($status_counts[$row['status']])) {
        $status_counts[$row['status']] = $row['count'];
    }
}

$history_count_query = "SELECT COUNT(*) as count FROM history_penjualan WHERE user_id='$user_id'";
$history_count_result = mysqli_query($koneksi, $history_count_query);
$history_count = mysqli_fetch_assoc($history_count_result);
$status_counts['Selesai'] = $history_count['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Urban Hype</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../images/icon/logo.png">
    <link rel="stylesheet" href="../css_user/css_produk_pembayaran/pesanan_saya.css">
    <link rel="stylesheet" href="../css_user/navbar.css">
</head>

<body>
    <?php include '../../navbar.php'; ?>

    <div class="orders-container">
        <div class="orders-header">
            <h1>Pesanan Saya </h1>
        </div>

        <div class="status-tabs">
            <a href="?status=semua" class="status-tab <?php echo $status_filter === 'semua' ? 'active' : ''; ?>">
                <span><i class="bi bi-box-seam"></i> Semua</span>
                <span class="status-count"><?php echo array_sum($status_counts); ?></span>
            </a>
            <a href="?status=Menunggu%20Konfirmasi" class="status-tab <?php echo $status_filter === 'Menunggu Konfirmasi' ? 'active' : ''; ?>">
                <span><i class="bi bi-clock-history"></i> Menunggu</span>
                <span class="status-count"><?php echo $status_counts['Menunggu Konfirmasi']; ?></span>
            </a>
            <a href="?status=Sedang%20Dikemas" class="status-tab <?php echo $status_filter === 'Sedang Dikemas' ? 'active' : ''; ?>">
                <span><i class="bi bi-box"></i> Dikemas</span>
                <span class="status-count"><?php echo $status_counts['Sedang Dikemas']; ?></span>
            </a>
            <a href="?status=Sedang%20Dikirim" class="status-tab <?php echo $status_filter === 'Sedang Dikirim' ? 'active' : ''; ?>">
                <span><i class="bi bi-truck"></i> Dikirim</span>
                <span class="status-count"><?php echo $status_counts['Sedang Dikirim']; ?></span>
            </a>
            <a href="?status=Selesai" class="status-tab <?php echo $status_filter === 'Selesai' ? 'active' : ''; ?>">
                <span><i class="bi bi-check-circle"></i> Selesai</span>
                <span class="status-count"><?php echo $status_counts['Selesai']; ?></span>
            </a>
            <a href="?status=Pesanan%20Batal" class="status-tab <?php echo $status_filter === 'Pesanan Batal' ? 'active' : ''; ?>">
                <span><i class="bi bi-x-circle"></i> Batal</span>
                <span class="status-count"><?php echo $status_counts['Pesanan Batal']; ?></span>
            </a>
        </div>

        <div class="orders-list">
            <?php
            $show_empty = true;

            if (!empty($pesanan)) {
                $show_empty = false;
                foreach ($pesanan as $order) {
                    $status_class = strtolower(str_replace(' ', '', $order['status']));
            ?>
                    <div class="order-card <?php echo $status_class; ?>">
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-id"><i class="bi bi-hash"></i> #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                <div class="order-date"><i class="bi bi-calendar-event"></i> <?php echo date('d M Y, H:i', strtotime($order['waktu_pemesanan'])); ?></div>
                            </div>
                            <span class="order-status status-<?php echo $status_class; ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </div>

                        <div class="order-items">
                            <div class="order-item">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($order['nama_produk']); ?></h4>
                                    <p><strong>Kuantitas:</strong> <?php echo $order['quantity']; ?></p>
                                    <p><strong>Kurir:</strong> <?php echo htmlspecialchars($order['kurir']); ?></p>
                                    <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order['metode_pembayaran']); ?></p>
                                </div>
                                <div class="item-price">Rp <?php echo number_format($order['harga_total'], 0, ',', '.'); ?></div>
                            </div>
                        </div>

                        <div class="order-footer">
                            <div class="order-info-bottom">
                                <div class="order-total">Total: Rp <?php echo number_format($order['harga_total'], 0, ',', '.'); ?></div>
                                <div class="order-address"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($order['alamat_lengkap']); ?></div>
                                <?php if (!empty($order['resi'])) { ?>
                                    <div class="order-resi"><span class="resi-label"><i class="bi bi-barcode"></i> Resi:</span> <?php echo htmlspecialchars($order['resi']); ?></div>
                                <?php } ?>
                            </div>
                            <div class="order-actions">
                                <?php
                                if ($order['status'] === 'Menunggu Konfirmasi' || $order['status'] === 'Sedang Dikemas') {
                                ?>
                                    <button class="btn btn-cancel" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                        <i class="bi bi-x-lg"></i> Batalkan
                                    </button>
                                <?php } ?>

                                <?php
                                if ($order['status'] === 'Sedang Dikirim') {
                                ?>
                                    <button class="btn btn-complete" onclick="completeOrder(<?php echo $order['id']; ?>)">
                                        <i class="bi bi-check-lg"></i> Terima Pesanan
                                    </button>
                                <?php } ?>

                                <button class="btn btn-details" onclick="viewOrderDetail(<?php echo $order['id']; ?>)">
                                    <i class="bi bi-eye"></i> Rincian
                                </button>
                            </div>
                        </div>
                    </div>
                <?php
                }
            }

            if (!empty($pesanan_selesai)) {
                $show_empty = false;
                foreach ($pesanan_selesai as $order) {
                ?>
                    <div class="order-card selesai">
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-id"><i class="bi bi-check-circle"></i> Pesanan Selesai</div>
                                <div class="order-date"><i class="bi bi-calendar-check"></i> Diterima: <?php echo date('d M Y, H:i', strtotime($order['tanggal_selesai'])); ?></div>
                            </div>
                            <span class="order-status status-selesai">
                                ✓ Selesai
                            </span>
                        </div>

                        <div class="order-items">
                            <div class="order-item">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($order['nama_produk']); ?></h4>
                                    <p><strong>Kuantitas:</strong> <?php echo $order['quantity']; ?></p>
                                    <p><strong>Kurir:</strong> <?php echo htmlspecialchars($order['kurir']); ?></p>
                                    <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order['metode_pembayaran']); ?></p>
                                </div>
                                <div class="item-price">Rp <?php echo number_format($order['harga_total'], 0, ',', '.'); ?></div>
                            </div>
                        </div>

                        <div class="order-footer">
                            <div class="order-info-bottom">
                                <div class="order-total">Total: Rp <?php echo number_format($order['harga_total'], 0, ',', '.'); ?></div>
                                <div class="order-address"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($order['alamat_lengkap']); ?></div>
                                <?php if (!empty($order['resi'])) { ?>
                                    <div class="order-resi"><span class="resi-label"><i class="bi bi-barcode"></i> Resi:</span> <?php echo htmlspecialchars($order['resi']); ?></div>
                                <?php } ?>
                            </div>
                            <div class="order-actions">
                                <button class="btn btn-details" onclick="viewOrderDetail(null)">
                                    <i class="bi bi-eye"></i> Rincian
                                </button>
                            </div>
                        </div>
                    </div>
                <?php
                }
            }
            if ($show_empty) {
                ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3>Belum Ada Pesanan</h3>
                    <p>Anda belum memiliki pesanan <?php echo $status_filter !== 'semua' ? 'dengan status ' . htmlspecialchars($status_filter) : ''; ?>.</p>
                    <a href="../../admin/produk.php">← Lanjut Belanja</a>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>

        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        function cancelOrder(orderId) {
            Swal.fire({
                icon: 'warning',
                title: 'Batalkan Pesanan?',
                text: 'Apakah Anda yakin ingin membatalkan pesanan ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Jangan',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#1E5DAC'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('cancel_order', '1');
                    formData.append('order_id', orderId);

                    fetch('pesanan_saya.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Pesanan berhasil dibatalkan',
                                    confirmButtonColor: '#1E5DAC'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message,
                                    confirmButtonColor: '#1E5DAC'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan pada sistem',
                                confirmButtonColor: '#1E5DAC'
                            });
                        });
                }
            });
        }

        function completeOrder(orderId) {
            Swal.fire({
                icon: 'question',
                title: 'Terima Pesanan?',
                text: 'Apakah pesanan sudah sampai dan dalam kondisi baik?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Terima',
                cancelButtonText: 'Belum',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#1E5DAC'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('selesaikan_pesanan', '1');
                    formData.append('order_id', orderId);

                    fetch('pesanan_saya.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terima Kasih!',
                                    text: 'Pesanan telah diselesaikan. Data penjualan telah tersimpan.',
                                    confirmButtonColor: '#1E5DAC'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message,
                                    confirmButtonColor: '#1E5DAC'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan: ' + error,
                                confirmButtonColor: '#1E5DAC'
                            });
                        });
                }
            });
        }

        function viewOrderDetail(orderId) {
            if (orderId) {
                Swal.fire({
                    icon: 'info',
                    title: 'Pesanan #' + String(orderId).padStart(6, '0'),
                    text: 'Rincian lengkap pesanan Anda',
                    confirmButtonColor: '#1E5DAC'
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Pesanan Selesai',
                    text: 'Terima kasih telah berbelanja!',
                    confirmButtonColor: '#1E5DAC'
                });
            }
        }
    </script>
</body>

</html>