<?php require 'auth_check.php'; ?>
<?php include '../admin/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data User</title>
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

        table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <nav class="navbar navbar-dark bg-dark fixed-top" style="margin-left:220px;">
        <div class="container-fluid"><span class="navbar-brand fw-bold">Admin Panel</span></div>
    </nav>

    <div class="content">
        <h2 class="fw-bold">Data User</h2>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="table-dark text-center">
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
                            <td class='text-center'><img src='../foto_profil/{$row['foto_profil']}' alt='Foto Profil'></td>
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