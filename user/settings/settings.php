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
    <link rel="icon" type="image/png" href="../../images/Background dan Logo/logo.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #EAE2E4 0%, #f5f5f5 100%);
            margin: 0;
        }

        .content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }

        .welcome-container {
            max-width: 900px;
        }

        .welcome-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #B7C5DA;
        }

        .welcome-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #1E5DAC;
            box-shadow: 0 8px 20px rgba(30, 93, 172, 0.2);
        }

        .welcome-info h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #1E5DAC;
            margin-bottom: 0.5rem;
        }

        .welcome-info p {
            color: #5a6b80;
            font-size: 1.05rem;
            margin: 0.25rem 0;
        }

        .welcome-subtitle {
            color: #999;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }

        .welcome-content {
            margin: 2rem 0;
            color: #2d3a4a;
            line-height: 1.8;
        }

        .welcome-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: #1E5DAC;
            margin-bottom: 1rem;
        }

        .welcome-content p {
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .feature-card {
            background: linear-gradient(135deg, rgba(30, 93, 172, 0.05) 0%, rgba(183, 197, 218, 0.1) 100%);
            border: 2px solid #B7C5DA;
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(30, 93, 172, 0.2);
            border-color: #1E5DAC;
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            color: #1E5DAC;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .feature-card p {
            color: #5a6b80;
            font-size: 0.9rem;
            margin: 0;
            line-height: 1.5;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .stat-box {
            background: linear-gradient(135deg, #1E5DAC 0%, #2a6bbf 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .cta-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1E5DAC 0%, #164a8a 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 93, 172, 0.4);
        }

        .btn-secondary {
            background: rgba(30, 93, 172, 0.1);
            color: #1E5DAC;
            border: 2px solid #B7C5DA;
        }

        .btn-secondary:hover {
            background: rgba(30, 93, 172, 0.2);
            border-color: #1E5DAC;
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #B7C5DA, transparent);
            margin: 2rem 0;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 20px;
            }

            .welcome-card {
                padding: 1.5rem;
            }

            .welcome-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .welcome-info h1 {
                font-size: 2rem;
            }

            .welcome-avatar {
                width: 100px;
                height: 100px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                            <img src="<?php echo !empty($user['foto_profil']) ? '../foto_profil/' . htmlspecialchars($user['foto_profil']) : 'https://via.placeholder.com/120?text=Profile'; ?>" alt="Foto Profil" class="welcome-avatar">
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