<?php
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
        <div class="logo-container">
            <img src="../../images/icon/logo.png" alt="Urban Hype Logo">
        </div>

        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 6px;">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
            </svg>
            Ubah Password
        </h2>
        <p class="card-subtitle">Kirimkan link perubahan password ke email Anda untuk proses perubahan yang aman</p>

        <div class="info-box">
            <strong>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: text-bottom; margin-right: 4px;">
                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.582l-1.271-.762-6.465 3.878L8 16l7.729-3.738z"/>
                </svg>
                Cara kerja:
            </strong><br>
            Kami akan mengirimkan link aman ke email Anda. Klik link tersebut untuk mengatur password baru Anda. Link berlaku selama 1 jam.
        </div>

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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 6px;">
                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.582l-1.271-.762-6.465 3.878L8 16l7.729-3.738z"/>
                </svg>
                Kirim Link Perubahan Password
            </button>

            <a href="settings.php" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 4px;">
                    <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                </svg>
                Kembali ke Settings
            </a>
        </form>
    </div>

</body>

</html>