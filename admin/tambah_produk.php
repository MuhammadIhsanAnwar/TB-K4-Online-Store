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
        $allowed = ['jpg','jpeg','png'];
        if (in_array($ext,$allowed)) {
            $filename = time().'_'.$file['name'];
            move_uploaded_file($file['tmp_name'], "../foto_produk/$filename");
            $sql = "INSERT INTO products (nama,kategori,harga,gambar) VALUES ('$nama','$kategori','$harga','$filename')";
            mysqli_query($koneksi,$sql);
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
    --text:#1f2937;
}

/* BODY & HALAMAN */
body{
    margin:0;
    font-family:Poppins,system-ui,sans-serif;
    background:var(--bg);
}
.content{
    margin-left:220px;
    padding:30px;
    animation:fadeContent 0.5s ease;
}
@keyframes fadeContent{
    from{opacity:0; transform:translateY(10px);}
    to{opacity:1; transform:translateY(0);}
}

/* SIDEBAR (sama dashboard) */
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
.logo-box{text-align:center;padding:10px 0 18px;}
.logo-box img{
    width:72px;
    filter:drop-shadow(0 6px 12px rgba(0,0,0,.25));
    transition:.3s ease;
}
.logo-box img:hover{transform:scale(1.05);}
.menu-title{color:#dbe6ff;font-size:13px;padding:8px 20px;}
.sidebar a{
    color:white;
    text-decoration:none;
    padding:12px 20px;
    margin:4px 12px;
    border-radius:10px;
    transition:.25s;
}
.sidebar a:hover{background:rgba(255,255,255,.18);}
.sidebar a.active{background:rgba(255,255,255,.32);font-weight:600;}
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

/* FORM CARD (sama dashboard tapi lebih hidup) */
.card-form{
    position:relative;
    overflow:hidden;
    border:none;
    border-radius:22px;
    padding:26px;
    background:var(--white);
    box-shadow:0 18px 45px rgba(0,0,0,.15);
    transition:.3s;
    max-width:600px;
    animation:fadeCard 0.5s ease;
}
.card-form:hover{
    transform:translateY(-8px) scale(1.02);
    box-shadow:0 30px 65px rgba(30,93,172,.35);
}
/* Bulatan dekoratif bergerak */
.card-form::after{
    content:"";
    position:absolute;
    width:120px;
    height:120px;
    border-radius:50%;
    top:-30px;
    right:-30px;
    background:#f3eadd;
    opacity:0.6;
    animation: floatRotate 8s linear infinite;
}
@keyframes floatRotate {
    0% { transform: translate(0,0) rotate(0deg); }
    25% { transform: translate(-5px,5px) rotate(90deg); }
    50% { transform: translate(0,10px) rotate(180deg); }
    75% { transform: translate(5px,5px) rotate(270deg); }
    100% { transform: translate(0,0) rotate(360deg); }
}
@keyframes fadeCard{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

.card-form h2{
    color:var(--primary);
    font-weight:700;
    margin-bottom:20px;
}

/* FORM CONTROL */
.card-form form .form-control{
    margin-bottom:15px;
    border-radius:12px;
    padding:12px;
    border:1px solid #ccc;
    transition: all .3s ease;
}
.card-form form .form-control:focus{
    border-color: var(--primary);
    box-shadow:0 0 8px rgba(30,93,172,.25);
}

/* BUTTON */
.card-form button{
    border-radius:12px;
    transition:.3s;
    width:100%;
}
.card-form button:hover{
    background:#144a8a;
    transform:scale(1.02);
}

/* ALERT */
.alert{
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,.1);
    margin-bottom:15px;
    background: linear-gradient(90deg,#ffffff,#f3f3f3);
    animation: fadeAlert 0.5s ease;
}
@keyframes fadeAlert{
    from{opacity:0; transform:translateY(-10px);}
    to{opacity:1; transform:translateY(0);}
}
</style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="card-form">
        <h2>Tambah Produk</h2>
        <?php if($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="nama" class="form-control" placeholder="Nama Produk" required>
            <input type="text" name="kategori" class="form-control" placeholder="Kategori" required>
            <input type="number" name="harga" class="form-control" placeholder="Harga" required>
            <input type="file" name="gambar" class="form-control" required>
            <button type="submit" name="submit" class="btn btn-primary">Tambah Produk</button>
        </form>
    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
