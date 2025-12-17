<?php
include 'koneksi.php';

$id = intval($_GET['id']);

mysqli_query($koneksi,
    "UPDATE pesan_kontak SET status='dibaca' WHERE id=$id"
);

$q = mysqli_query($koneksi,
    "SELECT * FROM pesan_kontak WHERE id=$id"
);
$p = mysqli_fetch_assoc($q);
?>

<h3><?= $p['subjek'] ?></h3>
<p><b>Dari:</b> <?= $p['nama'] ?> (<?= $p['email'] ?>)</p>
<p><?= nl2br($p['pesan']) ?></p>
