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

/* ================= SIDEBAR ================= */
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

/* LOGOUT */
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
    animation:fade .5s ease;
}

@keyframes fade{
    from{opacity:0; transform:translateY(10px);}
    to{opacity:1; transform:translateY(0);}
}

h2{
    color:var(--primary);
    font-weight:700;
}

hr{
    border-top:2px solid #cfd6e6;
    margin-bottom:20px;
}

/* ================= TABLE ================= */
.table-wrapper{
    background:var(--white);
    border-radius:18px;
    padding:20px;
    box-shadow:0 18px 45px rgba(0,0,0,.15);
}

table{
    font-size:14px;
}

thead{
    background:#1e5dac;
    color:white;
}

thead th{
    text-align:center;
    vertical-align:middle;
}

tbody tr{
    transition:.25s;
}

tbody tr:hover{
    background:#eef3ff;
}

table img{
    width:46px;
    height:46px;
    object-fit:cover;
    border-radius:50%;
    box-shadow:0 4px 10px rgba(0,0,0,.25);
}
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Data User</h2>
    <hr>

    <div class="table-wrapper table-responsive">
        <table class="table table-bordered table-striped table-sm align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir</th>
                    <th>Provinsi</th>
                    <th>Kab/Kota</th>
                    <th>Kecamatan</th>
                    <th>Kel/Desa</th>
                    <th>Kode Pos</th>
                    <th>Alamat</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Foto</th>
                    <th>Status</th>
                    <th>Token</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $res = mysqli_query($koneksi,"SELECT * FROM akun_user ORDER BY id DESC");
            while($row = mysqli_fetch_assoc($res)){
                echo "<tr>
                    <td class='text-center'>{$row['id']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['nama_lengkap']}</td>
                    <td class='text-center'>{$row['jenis_kelamin']}</td>
                    <td class='text-center'>{$row['tanggal_lahir']}</td>
                    <td>{$row['provinsi']}</td>
                    <td>{$row['kabupaten_kota']}</td>
                    <td>{$row['kecamatan']}</td>
                    <td>{$row['kelurahan_desa']}</td>
                    <td class='text-center'>{$row['kode_pos']}</td>
                    <td>{$row['alamat']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['password']}</td>
                    <td class='text-center'>
                        <img src='../foto_profil/{$row['foto_profil']}' alt='Foto'>
                    </td>
                    <td class='text-center'>{$row['status']}</td>
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
