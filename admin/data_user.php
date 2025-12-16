<?php require 'auth_check.php'; ?>
<?php include '../admin/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data User</title>
<link rel="stylesheet" href="../css/bootstrap.css">

<style>
:root{
    --primary:#1e5dac;
    --bg:#f3eded;
    --white:#ffffff;
}

body{
    margin:0;
    background:var(--bg);
    font-family:Poppins,system-ui,sans-serif;
}

/* ===== SIDEBAR (PASTI SAMA DASHBOARD) ===== */
.sidebar{
    position:fixed;
    top:0; left:0;
    width:220px;
    height:100vh;
    background:linear-gradient(180deg,#1e63b6,#0f3f82);
    padding:18px 0;
    display:flex;
    flex-direction:column;
}

.sidebar a{
    color:#fff;
    text-decoration:none;
    padding:12px 20px;
    margin:4px 12px;
    border-radius:10px;
}

.sidebar a:hover{
    background:rgba(255,255,255,.18);
}

.sidebar a.active{
    background:rgba(255,255,255,.32);
    font-weight:600;
}

.logout{
    margin-top:auto;
    color:#ffb3b3!important;
}

.logout:hover{
    background:rgba(255,80,80,.18);
}

/* ===== CONTENT ===== */
.content{
    margin-left:220px;
    padding:30px;
}

h2{
    color:var(--primary);
    font-weight:700;
}

hr{
    border-top:2px solid #cfd6e6;
    margin-bottom:25px;
}

/* ===== TABLE CARD ===== */
.table-card{
    background:#fff;
    border-radius:20px;
    padding:20px;
    box-shadow:0 18px 40px rgba(0,0,0,.15);
}

/* ===== TABLE STYLE ===== */
.table{
    margin:0;
}

.table thead{
    background:#1e5dac;
    color:#fff;
}

.table th,
.table td{
    vertical-align:middle;
    font-size:14px;
}

.table tbody tr:hover{
    background:#f3f6fb;
}

table img{
    width:46px;
    height:46px;
    object-fit:cover;
    border-radius:50%;
    border:2px solid #e5e7eb;
}
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Data User</h2>
    <hr>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="text-center">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>Tgl Lahir</th>
                        <th>Alamat</th>
                        <th>Email</th>
                        <th>Foto</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $res = mysqli_query($koneksi,"SELECT * FROM akun_user ORDER BY id DESC");
                while($row = mysqli_fetch_assoc($res)){
                ?>
                    <tr>
                        <td class="text-center"><?= $row['id'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td class="text-center"><?= $row['jenis_kelamin'] ?></td>
                        <td class="text-center"><?= $row['tanggal_lahir'] ?></td>
                        <td><?= $row['alamat'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td class="text-center">
                            <img src="../foto_profil/<?= $row['foto_profil'] ?>">
                        </td>
                        <td class="text-center"><?= $row['status'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
