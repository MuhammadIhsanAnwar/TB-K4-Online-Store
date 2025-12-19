<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Lainnya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../images/icon/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css_user/css_settings/settings_lain.css">
</head>

<body>
    <div class="container">
        <h1 class="page-header">Pengaturan Lainnya</h1>
        <p class="page-subtitle">Kelola akun Anda</p>

        <div class="danger-zone">
            <h2 class="danger-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Zona Berbahaya
            </h2>
            <p class="danger-description">
                Tindakan ini bersifat permanen dan tidak dapat dibatalkan. Semua data Anda termasuk pesanan, alamat, dan informasi pribadi akan dihapus secara permanen dari sistem kami.
            </p>

            <form action="hapus_akun.php" method="POST">
                <button type="submit" class="btn btn-danger">
                    Hapus Akun Permanen
                </button>
            </form>
        </div>
    </div>
</body>

</html>