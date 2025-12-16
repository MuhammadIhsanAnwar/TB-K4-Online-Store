<?php
require 'auth_check.php';
include "../admin/koneksi.php";

// Ambil ID produk dari URL
if (!isset($_GET['id'])) {
    header("Location: data_produk.php");
    exit;
}

$id = $_GET['id'];

// Ambil data produk
$result = mysqli_query($koneksi, "SELECT * FROM products WHERE id='$id'");
if (mysqli_num_rows($result) == 0) {
    die("Produk tidak ditemukan.");
}
$product = mysqli_fetch_assoc($result);

$success = false;
$error_msg = "";

// Proses update produk
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga = floatval($_POST['harga']);

    $update_gambar_sql = "";

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $gambar_name_raw = $_FILES['gambar']['name'];
        $gambar_ext = strtolower(pathinfo($gambar_name_raw, PATHINFO_EXTENSION));
        $gambar_size = $_FILES['gambar']['size'];
        $gambar_tmp = $_FILES['gambar']['tmp_name'];

        if (!in_array($gambar_ext, $allowed_ext)) {
            $error_msg = "Format file tidak diperbolehkan. Hanya jpg, jpeg, png.";
        } elseif ($gambar_size > 2 * 1024 * 1024) {
            $error_msg = "Ukuran file maksimal 2MB.";
        } else {
            $gambar_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $gambar_name_raw);

            if (!is_dir("../foto_produk")) {
                mkdir("../foto_produk", 0777, true);
            }

            if (move_uploaded_file($gambar_tmp, "../foto_produk/$gambar_name")) {
                if (!empty($product['gambar']) && file_exists("../foto_produk/" . $product['gambar'])) {
                    unlink("../foto_produk/" . $product['gambar']);
                }
                $update_gambar_sql = ", gambar='$gambar_name'";
            } else {
                $error_msg = "Gagal mengupload gambar.";
            }
        }
    }

    if (empty($error_msg)) {
        $update_sql = "UPDATE products SET nama='$nama', kategori='$kategori', harga='$harga' $update_gambar_sql WHERE id='$id'";
        if (mysqli_query($koneksi, $update_sql)) {
            $success = true;
            $result = mysqli_query($koneksi, "SELECT * FROM products WHERE id='$id'");
            $product = mysqli_fetch_assoc($result);
        } else {
            $error_msg = "Gagal update database: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Produk</title>
<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
:root{
    --primary:#1e5dac;
    --bg:#f3eded;
    --white:#ffffff;
    --hover-blue: rgba(30,93,172,.1);
    --text:#1f2937;
}

/* ===== BODY ===== */
body{
    margin:0;
    font-family:Poppins,system-ui,sans-serif;
    background:var(--bg);
}

/* ===== SIDEBAR ===== */
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

/* ===== CONTENT ===== */
.content{
    margin-left:220px;
    padding:40px;
    animation:fadeContent 0.5s ease;
}

@keyframes fadeContent{
    from{opacity:0; transform:translateY(10px);}
    to{opacity:1; transform:translateY(0);}
}

h2{color:var(--primary); font-weight:700; margin-bottom:10px;}
hr{border-top:2px solid #cfd6e6; margin-bottom:20px; opacity:.6;}

/* ===== FORM CARD ===== */
.form-container{
    background: var(--white);
    padding: 25px;
    border-radius: 20px;
    box-shadow:0 18px 45px rgba(0,0,0,.15);
    max-width:600px;
    animation:fadeCard 0.5s ease;
}

@keyframes fadeCard{
    from{opacity:0; transform:translateY(10px);}
    to{opacity:1; transform:translateY(0);}
}

.btn-primary{border-radius:8px; transition:.3s;}
.btn-primary:hover{background:#144a8a;}
.btn-secondary{border-radius:8px; transition:.3s;}
.btn-secondary:hover{background:#6c757d;}
</style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <h2>Edit Produk</h2>
    <hr>

    <?php if ($success): ?>
        <div class="alert alert-success">Produk berhasil diperbarui!</div>
    <?php elseif (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Nama Produk</label>
                <input type="text" name="nama" class="form-control" value="<?php echo $product['nama']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Kategori</label>
                <input type="text" name="kategori" class="form-control" value="<?php echo $product['kategori']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Harga</label>
                <input type="number" step="0.01" name="harga" class="form-control" value="<?php echo $product['harga']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Gambar</label><br>
                <?php if (!empty($product['gambar'])): ?>
                    <img src="../foto_produk/<?php echo $product['gambar']; ?>" alt="Gambar" width="100" class="mb-2"><br>
                <?php endif; ?>
                <input type="file" name="gambar" class="form-control">
                <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar (jpg/jpeg/png, max 2MB)</small>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
            <a href="data_produk.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
