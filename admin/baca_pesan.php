<?php
include 'auth_check.php';
include 'koneksi.php';

$id = (int) $_GET['id'];

mysqli_query($koneksi,
    "UPDATE pesan_kontak SET status='dibaca' WHERE id=$id"
);

header("Location: data_pesan.php");
exit;

?>