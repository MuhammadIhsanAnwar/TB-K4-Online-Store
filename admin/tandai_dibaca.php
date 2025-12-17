<?php
include 'koneksi.php';

$id = intval($_POST['id']);

mysqli_query($koneksi,
    "UPDATE pesan_kontak SET status='dibaca' WHERE id=$id"
);
