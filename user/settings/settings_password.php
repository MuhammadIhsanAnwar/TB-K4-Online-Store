<?php
// Tidak ada proses di sini, hanya form untuk memasukkan email
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../images/logo/logo.png">
    <link rel="stylesheet" href="../css_user/css_settings/settings_password.css">
</head>

<body>

    <div class="card">
        <!-- LOGO -->
        <div class="logo-container">
            <img src="../../images/icon/logo.png" alt="Urban Hype Logo">
        </div>

        <h2>🔐 Ubah Password</h2>
        <p class="card-subtitle">Kirimkan link perubahan password ke email Anda untuk proses perubahan yang aman</p>

        <!-- INFO BOX -->
        <div class="info-box">
            <strong>📧 Cara kerja:</strong><br>
            Kami akan mengirimkan link aman ke email Anda. Klik link tersebut untuk mengatur password baru Anda. Link berlaku selama 1 jam.
        </div>

        <!-- FORM -->
        <form action="../proses_kirim_reset.php" method="POST">
            <div class="form-group">
                <label>Email Akun Anda</label>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Masukkan email akun Anda"
                    value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                    required>
                <small>Kami akan mengirimkan link perubahan password ke email ini</small>
            </div>

            <button type="submit" class="btn-primary">
                📧 Kirim Link Perubahan Password
            </button>

            <a href="settings.php" class="back-link">← Kembali ke Settings</a>
        </form>
    </div>

</body>

</html>