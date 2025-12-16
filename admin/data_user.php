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
    --text:#1f2937;
}

body{
    margin:0;
    background:var(--bg);
    font-family:Poppins,system-ui,sans-serif;
}

/* ================= SIDEBAR (SAMA DENGAN DASHBOARD) ================= */
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

.logo-box{
    text-align:center;
    padding:10px 0 18px;
}

.logo-box img{
    width:72px;
    filter:drop-shadow(0 6px 12px rgba(0,0,0,.25));
    transition:.3s ease;
}

.logo-box img:hover{
    transform:scale(1.05);
}

.menu-title{
    color:#dbe6ff;
    font-size:13px;
    padding:8px 20px;
}

.sidebar a{
    color:white;
    text-decoration:none;
    padding:12px 20px;
    margin:4px 12px;
    border-radius:10px;
    transition:.25s;
}

.sidebar a:hover{
    background:rgba(255,255,255,.18);
}

.sidebar a.active{
    background:rgba(255,255,255,.32);
    font-weight:600;
}

/* LOGOUT PALING BAWAH + MERAH */
.sidebar .logout{
    margin-top:auto;
    background:rgba(255,80,80,.15);
    color:#ffd6d6!important;
    font-weight:600;
    text-align:center;
    border-radius:14px;
    transition:.3s ease;
}

.sidebar .logout:hover{
    background:#ff4d4d;
    color:#fff!important;
    box-shadow:0 10px 25px rgba(255,77,77,.6);
    transform:translateY(-2px);
}

/* ================= CONTENT ================= */
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
    margin-bottom:20px;
}

/* ================= TABLE CONTAINER ================= */
.table-responsive{
    background:var(--white);
    padding:18px;
    border-radius:20px;
    box-shadow:0 20px 45px rgba(30,93,172,.2);
    overflow-x:auto;
}

/* ================= TABLE ================= */
.table{
    border-collapse:separate;
    border-spacing:0;
    font-size:14px;
    color:var(--text);
}

/* HEADER */
.table thead{
    background:linear-gradient(180deg,#1e63b6,#0f3f82);
    color:#fff;
}

.table thead th{
    border:none;
    padding:14px 10px;
    text-align:center;
    white-space:nowrap;
    font-weight:600;
}

/* BODY */
.table tbody tr{
    transition:.25s;
}

.table tbody tr:hover{
    background:#eef3ff;
}

.table td{
    padding:12px 10px;
    vertical-align:middle;
    border-top:1px solid #e5e7eb;
    white-space:nowrap;
}

/* FOTO PROFIL */
.table img{
    width:46px;
    height:46px;
    object-fit:cover;
    border-radius:50%;
    box-shadow:0 6px 14px rgba(0,0,0,.25);
}

/* ALIGN */
.table td,
.table th{
    text-align:center;
}

.table td:nth-child(2),
.table td:nth-child(3),
.table td:nth-child(11),
.table td:nth-child(12){
    text-align:left;
}
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Data User</h2>
    <hr>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir</th>
                    <th>Provinsi</th>
                    <th>Kabupaten/Kota</th>
                    <th>Kecamatan</th>
                    <th>Kelurahan/Desa</th>
                    <th>Kode Pos</th>
                    <th>Alamat</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Foto Profil</th>
                    <th>Status</th>
                    <th>Token</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($koneksi, "SELECT * FROM akun_user ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['nama_lengkap']}</td>
                        <td>{$row['jenis_kelamin']}</td>
                        <td>{$row['tanggal_lahir']}</td>
                        <td>{$row['provinsi']}</td>
                        <td>{$row['kabupaten_kota']}</td>
                        <td>{$row['kecamatan']}</td>
                        <td>{$row['kelurahan_desa']}</td>
                        <td>{$row['kode_pos']}</td>
                        <td>{$row['alamat']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['password']}</td>
                        <td><img src='../foto_profil/{$row['foto_profil']}'></td>
                        <td>{$row['status']}</td>
                        <td>{$row['token']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
