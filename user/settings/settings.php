<?php
session_start();
include "../../admin/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$q = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
$user = mysqli_fetch_assoc($q);

$menu = isset($_GET['menu']) ? $_GET['menu'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Settings</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../images/icon/logo.png">
    <link rel="stylesheet" href="../css_user/css_settings/settings.css">
    <link rel="stylesheet" href="../css_user/css_settings/sidebar.css">
</head>

<body>
    <?php include "sidebar.php"; ?>

    <div class="content">
        <?php
        switch ($menu) {
            case 'profil':
                include "settings_profil.php";
                break;
            case 'password':
                include "settings_password.php";
                break;
            case 'payment':
                include "settings_payment.php";
                break;
            case 'lain':
                include "settings_lain.php";
                break;
            default:
        ?>
                <div class="welcome-container">
                    <div class="welcome-card">
                        <div class="welcome-header">
                            <img src="<?php echo !empty($user['foto_profil']) ? '../../foto_profil/' . htmlspecialchars($user['foto_profil']) : 'https://via.placeholder.com/120?text=Profile'; ?>" alt="Foto Profil" class="welcome-avatar">
                            <div class="welcome-info">
                                <h1>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 6px;">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.336C3.154 12.014 3 12.754 3 13h10c0-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c2.21 0 3.542.588 4.168 1.336z"/>
                                    </svg>
                                    Selamat Datang!
                                </h1>
                                <p><strong><?php echo htmlspecialchars($user['nama_lengkap']); ?></strong></p>
                                <p class="welcome-subtitle">@<?php echo htmlspecialchars($user['username']); ?></p>
                                <p class="welcome-subtitle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: text-bottom; margin-right: 4px;">
                                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="welcome-content">
                            <h2>Kelola Akun Anda</h2>
                            <p>Di sini Anda dapat mengelola informasi profil, keamanan akun, metode pembayaran, dan pengaturan lainnya untuk pengalaman belanja yang lebih baik di Urban Hype.</p>
                        </div>

                        <div class="quick-stats">
                            <div class="stat-box">
                                <div class="stat-value">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.336C3.154 12.014 3 12.754 3 13h10c0-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c2.21 0 3.542.588 4.168 1.336z"/>
                                    </svg>
                                </div>
                                <div class="stat-label">Profil Aktif</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-value">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                </div>
                                <div class="stat-label">Terverifikasi</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-value">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                    </svg>
                                </div>
                                <div class="stat-label">Aman</div>
                            </div>
                        </div>
                    </div>

                    <div class="welcome-card">
                        <h2 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: #1E5DAC; margin-bottom: 1.5rem;">Fitur Pengaturan</h2>

                        <div class="features-grid">
                            <div class="feature-card" onclick="window.location.href='settings.php?menu=profil'">
                                <div class="feature-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.336C3.154 12.014 3 12.754 3 13h10c0-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c2.21 0 3.542.588 4.168 1.336z"/>
                                    </svg>
                                </div>
                                <h3>Edit Profil</h3>
                                <p>Perbarui informasi pribadi, foto profil, dan alamat Anda.</p>
                            </div>

                            <div class="feature-card" onclick="window.location.href='settings.php?menu=password'">
                                <div class="feature-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                    </svg>
                                </div>
                                <h3>Ubah Password</h3>
                                <p>Tingkatkan keamanan akun dengan password yang lebih kuat.</p>
                            </div>

                            <div class="feature-card" onclick="window.location.href='settings.php?menu=lain'">
                                <div class="feature-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                                    </svg>
                                </div>
                                <h3>Pengaturan Lainnya</h3>
                                <p>Atur preferensi tampilan dan opsi keamanan lainnya.</p>
                            </div>
                        </div>
                    </div>

                    <div class="welcome-card">
                        <h2 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: #1E5DAC; margin-bottom: 1.5rem;">Tips Keamanan</h2>

                        <div style="color: #2d3a4a; line-height: 1.8;">
                            <p><strong>1. Jaga Kerahasiaan Password</strong></p>
                            <p style="margin-bottom: 1rem; color: #5a6b80;">Jangan bagikan password Anda kepada siapapun. Urban Hype tidak akan pernah meminta password Anda melalui email atau pesan.</p>

                            <p><strong>2. Perbarui Informasi Secara Berkala</strong></p>
                            <p style="margin-bottom: 1rem; color: #5a6b80;">Pastikan data profil dan alamat Anda selalu up-to-date untuk pengiriman yang lebih akurat.</p>

                            <p><strong>3. Gunakan Password yang Kuat</strong></p>
                            <p style="margin-bottom: 1rem; color: #5a6b80;">Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol untuk password yang lebih aman.</p>

                            <p><strong>4. Verifikasi Email Anda</strong></p>
                            <p style="color: #5a6b80;">Email yang terverifikasi membantu kami mengirimkan update penting dan notifikasi pesanan Anda.</p>
                        </div>
                    </div>

                    <div class="cta-buttons">
                        <a href="settings.php?menu=profil" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 6px;">
                                <path d="M15.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h.5a.5.5 0 0 1 0 1h-.5v1a.5.5 0 0 0 .5.5h.5a.5.5 0 0 1 0 1h-.5v1a.5.5 0 0 0 .5.5h.5a.5.5 0 0 1 0 1h-.5v1a.5.5 0 0 0 .5.5h.5a.5.5 0 0 1 0 1h-.5v1a.5.5 0 0 0 .5.5h.5a.5.5 0 0 1 0 1h-.5v.5a.5.5 0 0 1-.5.5H14v.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-.5H2.5a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 0-.5-.5H1a.5.5 0 0 1 0-1h.5v-1a.5.5 0 0 0-.5-.5H1a.5.5 0 0 1 0-1h.5v-1a.5.5 0 0 0-.5-.5H1a.5.5 0 0 1 0-1h.5v-1a.5.5 0 0 0-.5-.5H1a.5.5 0 0 1 0-1h.5v-1a.5.5 0 0 0-.5-.5H1a.5.5 0 0 1 0-1h.5v-.5A.5.5 0 0 1 2 2h13.5zM3 3a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3zm0 4a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1H3zm0 4a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1h-1zm5-8a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H8zm0 4a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1H8zm0 4a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1h-1zm5-8a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-1zm0 4a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-1zm0 4a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1h-1z"/>
                            </svg>
                            Edit Profil Saya
                        </a>
                        <a href="../index.php" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 6px;">
                                <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0L6 2.793V1.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v3.25L1.146 7.146a.5.5 0 0 0 0 .708l3 3A.5.5 0 0 0 4.5 11h6.25a.25.25 0 0 1 .25.25v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/>
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
        <?php
        }
        ?>
    </div>
</body>

</html>