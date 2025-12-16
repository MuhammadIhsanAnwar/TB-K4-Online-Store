<?php
// PHP, koneksi, auth, dan AJAX delete tetap sama
require 'auth_check.php';
include "../admin/koneksi.php";

$query = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Produk - Admin</title>
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

/* Logo & menu tetap dari dashboard */
.logo-box{text-align:center;padding:10px 0 18px;}
.logo-box img{width:72px;filter:drop-shadow(0 6px 12px rgba(0,0,0,.25));transition:.3s ease;}
.logo-box img:hover{transform:scale(1.05);}
.menu-title{color:#dbe6ff;font-size:13px;padding:8px 20px;}
.sidebar a{color:white;text-decoration:none;padding:12px 20px;margin:4px 12px;border-radius:10px;transition:.25s;}
.sidebar a:hover{background:rgba(255,255,255,.18);}
.sidebar a.active{background:rgba(255,255,255,.32);font-weight:600;}
.sidebar .logout{margin-top:auto;background:rgba(255,80,80,.15);color:#ffd6d6!important;font-weight:600;text-align:center;border-radius:14px;transition:.3s ease;}
.sidebar .logout:hover{background:#ff4d4d;color:#fff!important;box-shadow:0 10px 25px rgba(255,77,77,.6);transform:translateY(-2px);}

/* ================= CONTENT ================= */
.content{
    margin-left:220px;
    padding:30px;
    animation:fade .5s ease;
}
@keyframes fade{from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);}}
h2{color:var(--primary); font-weight:700;}
hr{border-top:2px solid #cfd6e6; margin-bottom:30px;}

/* ================= CARD SUMMARY ================= */
.row-summary{margin-bottom:30px;}
.card-summary{
    position:relative;
    overflow:hidden;
    border:none;
    border-radius:22px;
    padding:20px;
    background:var(--white);
    box-shadow:0 18px 45px rgba(0,0,0,.15);
    transition:.3s;
}
.card-summary:hover{
    transform:translateY(-5px);
    box-shadow:0 25px 60px rgba(30,93,172,.25);
}
.card-summary::after{
    content:"";
    position:absolute;
    width:120px; height:120px; border-radius:50%;
    top:-40px; right:-40px;
    background:#f3eadd;
    opacity:.7;
}
.card-summary h5{color:#6b7280; font-weight:600; position:relative; z-index:1;}
.card-summary p{margin:0; font-size:2rem; font-weight:700; position:relative; z-index:1;}
.text-primary{color:#2563eb!important;}
.text-success{color:#16a34a!important;}
.text-warning{color:#d97706!important;}

/* ================= TABEL PRODUK ================= */
.table thead{background:var(--primary); color:#fff;}
.table tbody tr:hover{background:rgba(30,93,172,.1);}
.btn-primary, .btn-warning, .btn-danger{border-radius:8px;}
.btn-primary:hover{background:#144a8a;}
.btn-warning:hover{background:#b38f00;}
.btn-danger:hover{background:#cc0000;}
img.rounded{border-radius:8px;}
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Data Produk</h2>
    <hr>

    <!-- CARD SUMMARY -->
    <div class="row row-summary g-4">
        <div class="col-md-4">
            <div class="card-summary">
                <h5>Total Produk</h5>
                <p class="text-primary">
                    <?php $res=mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM products"); echo mysqli_fetch_assoc($res)['total']; ?>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-summary">
                <h5>Total User</h5>
                <p class="text-success">
                    <?php $res=mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM akun_user"); echo mysqli_fetch_assoc($res)['total']; ?>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-summary">
                <h5>Total Penjualan</h5>
                <p class="text-warning">
                    <?php $res=mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM penjualan"); echo mysqli_fetch_assoc($res)['total']; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- TABEL PRODUK -->
    <a href="tambah_produk.php" class="btn btn-primary mb-3">Tambah Produk</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <tr id="row-<?php echo $row['id']; ?>">
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['kategori']; ?></td>
                    <td>$<?php echo number_format($row['harga'], 2); ?></td>
                    <td>
                        <?php if (!empty($row['gambar'])): ?>
                            <img src="../foto_produk/<?php echo $row['gambar']; ?>" alt="Gambar" width="50" class="rounded">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_produk.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduk(<?php echo $row['id']; ?>)">Hapus</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function deleteProduk(id) {
    if (confirm("Yakin ingin menghapus produk ini?")) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.responseText.trim() === "success") {
                const row = document.getElementById('row-' + id);
                if (row) row.remove();
                alert("Produk berhasil dihapus.");
            } else {
                alert("Gagal menghapus produk.");
            }
        };
        xhr.send("delete_id=" + id);
    }
}
</script>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
