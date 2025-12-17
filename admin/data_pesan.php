<?php
include 'auth_check.php';
include 'koneksi.php';

$query = mysqli_query($koneksi,
    "SELECT * FROM pesan_kontak ORDER BY created_at DESC"
);
?>

<h3>Pesan Masuk</h3>

<table border="1" cellpadding="8">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Pesan</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

<?php $no=1; while($row=mysqli_fetch_assoc($query)): ?>
<tr style="background: <?= $row['status']=='baru' ? '#eef' : '#fff' ?>">
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['nama']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= nl2br(htmlspecialchars($row['pesan'])) ?></td>
    <td><?= $row['status'] ?></td>
    <td>
        <?php if($row['status']=='baru'): ?>
        <a href="baca_pesan.php?id=<?= $row['id'] ?>">Tandai dibaca</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
