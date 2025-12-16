<?php
?>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .sidebar {
        width: 280px;
        height: 100vh;
        background: linear-gradient(135deg, #1E5DAC 0%, #2c6bc9 100%);
        color: #fff;
        position: fixed;
        left: 0;
        top: 0;
        padding: 30px 0;
        box-shadow: 4px 0 20px rgba(30, 93, 172, 0.15);
        z-index: 1000;
    }

    .sidebar .title {
        font-family: 'Playfair Display', serif;
        font-size: 28px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 40px;
        color: #fff;
        letter-spacing: 1px;
        position: relative;
        padding-bottom: 15px;
    }

    .sidebar .title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, transparent, #E8D3C1, transparent);
        border-radius: 2px;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        padding: 16px 28px;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        margin: 6px 15px;
        border-radius: 12px;
        letter-spacing: 0.3px;
    }

    .sidebar a::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: #E8D3C1;
        border-radius: 0 4px 4px 0;
        transform: scaleY(0);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar a:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        transform: translateX(8px);
        padding-left: 32px;
    }

    .sidebar a:hover::before {
        transform: scaleY(1);
    }

    .sidebar a.active {
        background: rgba(232, 211, 193, 0.2);
        color: #fff;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .sidebar a.active::before {
        transform: scaleY(1);
    }

    .sidebar a .icon {
        width: 20px;
        height: 20px;
        margin-right: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar a:hover .icon {
        transform: scale(1.15) rotate(5deg);
    }

    .content {
        margin-left: 280px;
        padding: 30px;
        min-height: 100vh;
        background: linear-gradient(135deg, #EAE2E4 0%, #f5f5f5 100%);
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 240px;
        }
        
        .content {
            margin-left: 240px;
        }
    }
</style>

<div class="sidebar">
    <div class="title">Settings</div>

    <a href="settings.php?menu=profil" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'profil') ? 'active' : ''; ?>">
        <span class="icon">👤</span>
        Edit Profil
    </a>
    <a href="settings.php?menu=payment" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'payment') ? 'active' : ''; ?>">
        <span class="icon">💳</span>
        Metode Pembayaran
    </a>
    <a href="settings.php?menu=lain" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'lain') ? 'active' : ''; ?>">
        <span class="icon">⚙️</span>
        Pengaturan Lainnya
    </a>
</div>
