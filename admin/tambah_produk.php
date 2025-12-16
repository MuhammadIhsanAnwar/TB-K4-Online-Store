<?php
require 'auth_check.php';
include "../admin/koneksi.php";
$msg = '';

if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
        $file = $_FILES['gambar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($ext, $allowed)) {
            $filename = time() . '_' . $file['name'];
            move_uploaded_file($file['tmp_name'], "../foto_produk/$filename");
            $sql = "INSERT INTO products (nama,kategori,harga,gambar) VALUES ('$nama','$kategori','$harga','$filename')";
            mysqli_query($koneksi, $sql);
            $msg = "Produk berhasil ditambahkan!";
        } else {
            $msg = "Format file tidak diperbolehkan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Produk</title>
<link rel="stylesheet" href="../css/bootstrap.css">
<style>
:root{
    --primary:#1e5dac;
    --bg:#f3eded;
    --white:#ffffff;
    --hover-blue: rgba(30,93,172,.1);
    --text:#1f2937;
}

body{
    margin:0;
    font-family:Poppins,system-ui,sans-serif;
    background:var(--bg);
    animation:fadeIn 0.5s ease;
}

/* ================= SIDEBAR (Dashboard Style) ================= */
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
    padding:40px;
    animation:fadeIn 0.5s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(10px);}
    to{opacity:1; transform:translateY(0);}
}

h2{
    color:var(--primary);
    font-weight:700;
    margin-bottom:8px;
}

hr{
    border-top:2px solid #cfd6e6;
    margin-bottom:20px;
    opacity:.6;
}

/* ================= FORM ================= */
.form-container{
    background: var(--white);
    padding: 25px;
    border-radius: 20px;
    box-shadow:0 18px 45px rgba(0,0,0,.15);
    max-width:600px;
    animation:fadeIn 0.5s ease;
}

.btn-primary{
    border-radius:8px;
    transition:.3s;
}
.btn-primary:hover{
    background:#144a8a;
}
</style>
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Tambah Produk</h2>
    <hr>
    <?php if($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3"><label>Nama Produk</label>
                <input type="text" name="nama" class="form-control" required></div>
            <div class="mb-3"><label>Kategori</label>
                <input type="text" name="kategori" class="form-control" required></div>
            <div class="mb-3"><label>Harga</label>
                <input type="number" name="harga" class="form-control" required></div>
            <div class="mb-3"><label>Gambar</label>
                <input type="file" name="gambar" class="form-control" required></div>
            <button type="submit" name="submit" class="btn btn-primary">Tambah Produk</button>
        </form>
    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
