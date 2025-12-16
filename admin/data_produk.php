<?php
require 'auth_check.php';
include "../admin/koneksi.php";

if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $res = mysqli_query($koneksi, "SELECT gambar FROM products WHERE id='$delete_id'");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if (!empty($row['gambar']) && file_exists("../foto_produk/" . $row['gambar'])) {
            unlink("../foto_produk/" . $row['gambar']);
        }
    }
    mysqli_query($koneksi, "DELETE FROM products WHERE id='$delete_id'");
    echo 'success';
    exit;
}

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
    --hover-blue: rgba(30,93,172,.1);
}

body{
    margin:0;
    background:var(--bg);
    font-family:Poppins,system-ui,sans-serif;
}

/* ================= SIDEBAR (pakai dashboard CSS) ================= */
.sidebar{
    position:fixed;
    top:0; left:0;
    width:220px;
    height:100vh;
    background:linear-gradient(180deg,#1e63b6,#0f3f82);
    display:flex;
    flex-direction:column;
    padding:18px 0;
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
    padding:30px 40px;
}

/* ================= TABEL PRODUK ================= */
.table-container{
    background: var(--white);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 15px 30px rgba(0,0,0,.1);
}

.table{
    border-collapse: separate !important;
    border-spacing: 0 10px;
}

.table thead tr{
    background: var(--primary);
    color: #fff;
    border-radius: 12px;
}

.table tbody tr{
    background: #fff;
    transition: .3s;
}

.table tbody tr:hover{
    background: var(--hover-blue);
}

.table td, .table th{
    vertical-align: middle;
    padding: 12px;
}

.table img{
    border-radius:6px;
    object-fit:cover;
}

.btn-primary, .btn-warning, .btn-danger{
    border-radius:8px;
    transition:.3s;
}
.btn-primary:hover{background:#144a8a;}
.btn-warning:hover{background:#b38f00;}
.btn-danger:hover{background:#cc0000;}
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Data Produk</h2>
    <hr>

    <a href="tambah_produk.php" class="btn btn-primary mb-3">Tambah Produk</a>

    <div class="table-container">
        <table class="table table-bordered table-striped align-middle">
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
                            <img src="../foto_produk/<?php echo $row['gambar']; ?>" width="50" alt="Gambar">
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

</body>
</html>
