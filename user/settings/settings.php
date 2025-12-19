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

// Tentukan halaman mana yang akan ditampilkan
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
                // Dashboard / Welcome page
        ?>
                <div class="welcome-container">
                    <!-- WELCOME CARD -->
                    <div class="welcome-card">
                        <div class="welcome-header">
                            <img src="<?php echo !empty($user['foto_profil']) ? '../../foto_profil/' . htmlspecialchars($user['foto_profil']) : 'https://via.placeholder.com/120?text=Profile'; ?>" alt="Foto Profil" class="welcome-avatar">
                            <div class="welcome-info">
                                <h1>Selamat Datang! 👋</h1>
                                <p><strong><?php echo htmlspecialchars($user['nama_lengkap']); ?></strong></p>
                                <p class="welcome-subtitle">@<?php echo htmlspecialchars($user['username']); ?></p>
                                <p class="welcome-subtitle">📧 <?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                        </div>

                        <div class="welcome-content">
                            <h2>Kelola Akun Anda</h2>
                            <p>Di sini Anda dapat mengelola informasi profil, keamanan akun, metode pembayaran, dan pengaturan lainnya untuk pengalaman belanja yang lebih baik di Urban Hype.</p>
                        </div>

                        <!-- QUICK STATS -->
                        <div class="quick-stats">
                            <div class="stat-box">
                                <div class="stat-value">👤</div>
                                <div class="stat-label">Profil Aktif</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-value">✅</div>
                                <div class="stat-label">Terverifikasi</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-value">🔒</div>
                                <div class="stat-label">Aman</div>
                            </div>
                        </div>
                    </div>

                    <!-- FEATURES SECTION -->
                    <div class="welcome-card">
                        <h2 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: #1E5DAC; margin-bottom: 1.5rem;">Fitur Pengaturan</h2>

                        <div class="features-grid">
                            <div class="feature-card" onclick="window.location.href='settings.php?menu=profil'">
                                <div class="feature-icon">👤</div>
                                <h3>Edit Profil</h3>
                                <p>Perbarui informasi pribadi, foto profil, dan alamat Anda.</p>
                            </div>

                            <div class="feature-card" onclick="window.location.href='settings.php?menu=password'">
                                <div class="feature-icon">🔐</div>
                                <h3>Ubah Password</h3>
                                <p>Tingkatkan keamanan akun dengan password yang lebih kuat.</p>
                            </div>

                            <div class="feature-card" onclick="window.location.href='settings.php?menu=payment'">
                                <div class="feature-icon">💳</div>
                                <h3>Metode Pembayaran</h3>
                                <p>Kelola metode pembayaran untuk checkout yang lebih cepat.</p>
                            </div>

                            <div class="feature-card" onclick="window.location.href='settings.php?menu=lain'">
                                <div class="feature-icon">⚙️</div>
                                <h3>Pengaturan Lainnya</h3>
                                <p>Atur preferensi tampilan dan opsi keamanan lainnya.</p>
                            </div>
                        </div>
                    </div>

                    <!-- TIPS SECTION -->
                    <div class="welcome-card">
                        <h2 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: #1E5DAC; margin-bottom: 1.5rem;">💡 Tips Keamanan</h2>

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

                    <!-- CTA BUTTONS -->
                    <div class="cta-buttons">
                        <a href="settings.php?menu=profil" class="btn btn-primary">
                            📝 Edit Profil Saya
                        </a>
                        <a href="../index.php" class="btn btn-secondary">
                            🏠 Kembali ke Beranda
                        </a>
                    </div>
                </div>
        <?php
        }
        ?>
    </div>
</body>

</html>