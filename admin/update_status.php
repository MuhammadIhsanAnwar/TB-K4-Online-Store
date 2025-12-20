<?php
session_start();
require 'auth_check.php';
include 'koneksi.php';

// Validasi parameter
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Parameter tidak valid'
    ];
    header('Location: pesanan_masuk.php');
    exit;
}

$order_id = intval($_GET['id']);
$status = $_GET['status'];

// Validasi status
$valid_statuses = ['dikemas', 'selesai'];
if (!in_array($status, $valid_statuses)) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Status tidak valid'
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

$order = mysqli_fetch_assoc($check_result);

try {
    if ($status === 'dikemas') {
        // Update status ke Sedang Dikemas
        $update_query = "UPDATE pemesanan SET status='Sedang Dikemas' WHERE id='$order_id'";
        
        if (!mysqli_query($koneksi, $update_query)) {
            throw new Exception('Gagal mengubah status: ' . mysqli_error($koneksi));
        }

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Status berhasil diubah menjadi Sedang Dikemas'
        ];

    } elseif ($status === 'selesai') {
        // Transaction untuk pindah ke history dan hapus dari pemesanan
        mysqli_begin_transaction($koneksi);

        try {
            // Insert ke history_penjualan
            $insert_history = "
                INSERT INTO history_penjualan
                (user_id, nama_lengkap, nomor_hp, alamat_lengkap, nama_produk, quantity,
                 harga_total, metode_pembayaran, kurir, resi, tanggal_dipesan, tanggal_selesai)
                VALUES 
                ('{$order['user_id']}', '{$order['nama_lengkap']}', '{$order['nomor_hp']}', 
                 '{$order['alamat_lengkap']}', '{$order['nama_produk']}', '{$order['quantity']}',
                 '{$order['harga_total']}', '{$order['metode_pembayaran']}', '{$order['kurir']}',
                 '{$order['resi']}', '{$order['waktu_pemesanan']}', NOW())
            ";

            if (!mysqli_query($koneksi, $insert_history)) {
                throw new Exception('Gagal menyimpan ke history: ' . mysqli_error($koneksi));
            }

            // Delete dari pemesanan
            $delete_query = "DELETE FROM pemesanan WHERE id='$order_id'";
            if (!mysqli_query($koneksi, $delete_query)) {
                throw new Exception('Gagal menghapus pesanan: ' . mysqli_error($koneksi));
            }

            mysqli_commit($koneksi);
            
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Pesanan berhasil diselesaikan dan dipindahkan ke riwayat'
            ];

        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            throw $e;
        }
    }

} catch (Exception $e) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => $e->getMessage()
    ];
}

header('Location: pesanan_masuk.php');
exit;
?>
