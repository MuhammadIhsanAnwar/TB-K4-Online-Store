<?php
include 'koneksi.php';

$q = mysqli_query($koneksi,
    "SELECT id, nama, subjek, pesan
     FROM pesan_kontak
     WHERE status='baru'
     ORDER BY created_at DESC
     LIMIT 1"
);

if(mysqli_num_rows($q) > 0){
    echo json_encode(mysqli_fetch_assoc($q));
}
