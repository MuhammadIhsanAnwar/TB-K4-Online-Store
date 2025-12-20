<?php
?>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css_user/css_settings/sidebar.css">

<!-- Toggle Button -->
<button class="toggle-btn" id="sidebarToggle">
    <span></span>
    <span></span>
    <span></span>
</button>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="../../images/icon/logo.png" alt="Urban Hype Logo">
        </div>
        <div class="title">Settings</div>
    </div>

    <div class="sidebar-content">
        <a href="settings.php" class="<?php echo (!isset($_GET['menu'])) ? 'active' : ''; ?>">
            <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 2.5A2.5 2.5 0 0 1 3.5 0h8.5a2.5 2.5 0 0 1 2.5 2.5v9.092a2.5 2.5 0 0 1-.732 1.745L10 14.092l-2-.5L6 14.092l-3.768-.755A2.5 2.5 0 0 1 1 11.592V2.5zm1.5 0a1 1 0 0 0-1 1v9.092a1 1 0 0 0 .293.707l3.768.755a.5.5 0 0 0 .439-.032l2-1 2 1a.5.5 0 0 0 .439.032l3.768-.755A1 1 0 0 0 13.5 11.592V3.5a1 1 0 0 0-1-1h-8.5zM8 12a1 1 0 0 1-.5-.146L6 11.108l-2 .396a.5.5 0 0 1-.732-.42L3.5 11V2.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v8.5l-.268.18a.5.5 0 0 1-.732.066L8 12z"/>
                </svg>
            </span>
            Dashboard
        </a>

        <div class="sidebar-divider"></div>

        <a href="settings.php?menu=profil" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'profil') ? 'active' : ''; ?>">
            <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.336C3.154 12.014 3 12.754 3 13h10c0-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c2.21 0 3.542.588 4.168 1.336z"/>
                </svg>
            </span>
            Edit Profil
        </a>
        <a href="settings.php?menu=password" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'password') ? 'active' : ''; ?>">
            <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                </svg>
            </span>
            Ubah Password
        </a>
        <a href="settings.php?menu=lain" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'lain') ? 'active' : ''; ?>">
            <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                </svg>
            </span>
            Pengaturan Lainnya
        </a>
    </div>

    <div class="sidebar-footer">
        <a href="../../index.php" class="btn-home">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0L6 2.793V1.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v3.25L1.146 7.146a.5.5 0 0 0 0 .708l3 3A.5.5 0 0 0 4.5 11h6.25a.25.25 0 0 1 .25.25v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/>
                </svg>
            </span>
            Kembali ke Beranda
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        }

        // Tutup sidebar saat klik di luar (opsional, di mobile)
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 992 && 
                sidebar.classList.contains('collapsed') === false &&
                !sidebar.contains(e.target) && 
                e.target !== toggleBtn) {
                sidebar.classList.add('collapsed');
            }
        });
    });
</script>