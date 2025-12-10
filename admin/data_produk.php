<?php
require 'auth_check.php';
include "../admin/koneksi.php";

// Hapus produk jika request AJAX
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    // Ambil nama gambar agar bisa dihapus dari folder
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

// Ambil semua produk
$query = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Produk - Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
            width: 220px;
            background-color: #343a40;
            padding-top: 70px;
        }

        .sidebar a {
            display: block;
            padding: 10px 15px;
            color: #fff;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
            border-radius: 5px;
        }

        .content {
            margin-left: 230px;
            padding: 20px;
        }

        nav.navbar {
            margin-left: 220px;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">Admin Panel</span>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="content">
        <h2 class="fw-bold">Data Produk</h2>
        <hr>

        <a href="tambah_produk.php" class="btn btn-primary mb-3">Tambah Produk</a>

        <table class="table table-bordered table-striped" id="produkTable">
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
                                <img src="../foto_produk/<?php echo $row['gambar']; ?>" alt="Gambar" width="50">
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
                // AJAX request
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "", true); // kirim ke halaman yang sama
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.responseText.trim() === "success") {
                        // Hapus baris tabel
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