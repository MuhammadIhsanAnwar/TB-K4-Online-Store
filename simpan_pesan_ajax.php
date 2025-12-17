<?php
include "admin/koneksi.php";
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Metode tidak valid'
    ]);
    exit;
}

$nama   = trim($_POST['nama'] ?? '');
$email  = trim($_POST['email'] ?? '');
$subjek = trim($_POST['subjek'] ?? '');
$pesan  = trim($_POST['pesan'] ?? '');

if ($nama === '' || $email === '' || $pesan === '') {
    echo json_encode([
        'status' => 'warning',
        'message' => 'Harap lengkapi data wajib'
    ]);
    exit;
}

$nama   = mysqli_real_escape_string($koneksi, $nama);
$email  = mysqli_real_escape_string($koneksi, $email);
$subjek = mysqli_real_escape_string($koneksi, $subjek);
$pesan  = mysqli_real_escape_string($koneksi, $pesan);

$query = "INSERT INTO pesan_kontak (nama, email, subjek, pesan)
          VALUES ('$nama', '$email', '$subjek', '$pesan')";

if (mysqli_query($koneksi, $query)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Pesan berhasil dikirim. Terima kasih!'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan pesan'
    ]);
}
