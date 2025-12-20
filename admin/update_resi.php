<?php
session_start();
require 'auth_check.php';
include 'koneksi.php';

// Validasi parameter
if (!isset($_GET['id']) || !isset($_GET['resi'])) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Parameter tidak valid'
    ];
    header('Location: pesanan_masuk.php');
    exit;
}

$order_id = intval($_GET['id']);
$resi = mysqli_real_escape_string($koneksi, trim($_GET['resi']));

// Validasi resi tidak boleh kosong
if (empty($resi)) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Nomor resi tidak boleh kosong'
    ];
    header('Location: pesanan_masuk.php');
    exit;
}

// Cek apakah pesanan ada
$check_query = "SELECT * FROM pemesanan WHERE id='$order_id'";
$check_result = mysqli_query($koneksi, $check_query);

if (!$check_result || mysqli_num_rows($check_result) == 0) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Pesanan tidak ditemukan'
    ];
    header('Location: pesanan_masuk.php');
    exit;
}

try {
    // Update resi dan status ke Sedang Dikirim
    $update_query = "
        UPDATE pemesanan 
        SET resi='$resi', status='Sedang Dikirim'
        WHERE id='$order_id'
    ";

    if (!mysqli_query($koneksi, $update_query)) {
        throw new Exception('Gagal menyimpan resi: ' . mysqli_error($koneksi));
    }

    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Resi berhasil disimpan dan status diubah menjadi Sedang Dikirim'
    ];

} catch (Exception $e) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => $e->getMessage()
    ];
}

header('Location: pesanan_masuk.php');
exit;
?>
