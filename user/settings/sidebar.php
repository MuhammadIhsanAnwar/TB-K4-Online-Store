<?php
?>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css_user/css_settings/sidebar.css">

<div class="sidebar">
    <!-- HEADER DENGAN LOGO -->
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="../../images/icon/logo.png" alt="Urban Hype Logo">
        </div>
        <div class="title">Settings</div>
    </div>

    <!-- MENU UTAMA -->
    <div class="sidebar-content">
        <a href="settings.php" class="<?php echo (!isset($_GET['menu'])) ? 'active' : ''; ?>">
            <span class="icon">📊</span>
            Dashboard
        </a>

        <div class="sidebar-divider"></div>

        <a href="settings.php?menu=profil" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'profil') ? 'active' : ''; ?>">
            <span class="icon">👤</span>
            Edit Profil
        </a>
        <a href="settings.php?menu=password" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'password') ? 'active' : ''; ?>">
            <span class="icon">🔐</span>
            Ubah Password
        </a>
        <a href="settings.php?menu=lain" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'lain') ? 'active' : ''; ?>">
            <span class="icon">⚙️</span>
            Pengaturan Lainnya
        </a>
    </div>

    <!-- FOOTER DENGAN TOMBOL KEMBALI -->
    <div class="sidebar-footer">
        <a href="../../index.php" class="btn-home">
            <span>🏠</span>
            Kembali ke Beranda
        </a>
    </div>
</div>