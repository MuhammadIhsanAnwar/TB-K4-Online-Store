<?php
require 'auth_check.php';
require 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data User - Admin</title>

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS ADMIN -->
    <link rel="stylesheet" href="css_admin/data_user_style.css">

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="container">

        <h1 class="page-title">
            <i class="bi bi-people-fill"></i> Data User
        </h1>
        <hr>

        <!-- SEARCH -->
        <form method="GET" class="search-section">
            <div class="search-group">
                <label>Cari User</label>
                <input
                    type="text"
                    name="keyword"
                    placeholder="Username, nama, email..."
                    value="<?= $_GET['keyword'] ?? '' ?>"
                >
            </div>

            <button type="submit" class="btn-search">
                <i class="bi bi-search"></i> Cari
            </button>

            <a href="data_user.php" class="btn-reset">
                Reset
            </a>
        </form>

        <!-- TABLE -->
        <div class="table-wrapper">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                    $no = 1;
                    $keyword = $_GET['keyword'] ?? '';

                    if ($keyword != '') {
                        $query = mysqli_query(
                            $koneksi,
                            "SELECT * FROM akun_user
                             WHERE username LIKE '%$keyword%'
                             OR nama LIKE '%$keyword%'
                             OR email LIKE '%$keyword%'"
                        );
                    } else {
                        $query = mysqli_query($koneksi, "SELECT * FROM akun_user");
                    }

                    if (mysqli_num_rows($query) > 0):
                        while ($row = mysqli_fetch_assoc($query)):
                    ?>

                        <tr>
                            <td class="nomor-col"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td class="text-truncate-col"><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <span class="status-badge status-aktif">
                                    Aktif
                                </span>
                            </td>
                        </tr>

                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:20px;">
                                Data user tidak ditemukan
                            </td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>
