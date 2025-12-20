<?php
session_start();
include '../admin/koneksi.php';


if (!isset($_GET['id'])) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Parameter tidak valid'
    ];
    
 
    $from = isset($_GET['from']) ? $_GET['from'] : 'user';
    header("Location: " . ($from === 'admin' ? '../admin/pesanan_masuk.php' : 'pesanan_saya.php'));
    exit;
}

$order_id = intval($_GET['id']);
$from = isset($_GET['from']) ? $_GET['from'] : 'user'; // 'admin' atau 'user'


$check_query = "SELECT * FROM pemesanan WHERE id='$order_id'";
$check_result = mysqli_query($koneksi, $check_query);

if (!$check_result || mysqli_num_rows($check_result) == 0) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Pesanan tidak ditemukan'
    ];
    header("Location: " . ($from === 'admin' ? '../admin/pesanan_masuk.php' : 'pesanan_saya.php'));
    exit;
}

$order = mysqli_fetch_assoc($check_result);

// Jika dari user, cek apakah pesanan milik user tersebut
if ($from === 'user' && !isset($_SESSION['user_id'])) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Anda harus login terlebih dahulu'
    ];
    header('Location: login_user.php');
    exit;
}

if ($from === 'user' && $order['user_id'] != $_SESSION['user_id']) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Anda tidak memiliki akses untuk membatalkan pesanan ini'
    ];
    header('Location: pesanan_saya.php');
    exit;
}

// Cek status pesanan - hanya pesanan yang belum dikirim yang bisa dibatalkan
$valid_statuses = ['Menunggu Konfirmasi', 'Sedang Dikemas'];
if (!in_array($order['status'], $valid_statuses)) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Pesanan dengan status "' . $order['status'] . '" tidak dapat dibatalkan. Hanya pesanan yang "Menunggu Konfirmasi" atau "Sedang Dikemas" yang bisa dibatalkan.'
    ];
    header("Location: " . ($from === 'admin' ? '../admin/pesanan_masuk.php' : 'pesanan_saya.php'));
    exit;
}

try {
    // Delete pesanan dari tabel pemesanan
    $delete_query = "DELETE FROM pemesanan WHERE id='$order_id'";
    
    if (!mysqli_query($koneksi, $delete_query)) {
        throw new Exception('Gagal membatalkan pesanan: ' . mysqli_error($koneksi));
    }

    // Log pembatalan (opsional - jika ada tabel untuk audit)
    $cancel_reason = $from === 'admin' ? 'Dibatalkan oleh Admin' : 'Dibatalkan oleh User';
    $log_query = "INSERT INTO pembatalan_pesanan (order_id, user_id, alasan, tanggal_pembatalan) 
                  VALUES ('$order_id', '{$order['user_id']}', '$cancel_reason', NOW())";
    mysqli_query($koneksi, $log_query); // Ignore if table doesn't exist

    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Pesanan berhasil dibatalkan'
    ];

} catch (Exception $e) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => $e->getMessage()
    ];
}

header("Location: " . ($from === 'admin' ? '../admin/pesanan_masuk.php' : 'pesanan_saya.php'));
exit;
?>
