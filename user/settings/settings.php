<?php
session_start();
include "../admin/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$q = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
$user = mysqli_fetch_assoc($q);

// include sidebar
include "sidebar.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Urban Hype</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1E5DAC;
            --secondary: #E8D3C1;
            --accent: #B7C5DA;
            --neutral: #EAE2E4;
            --text-dark: #1a1a1a;
            --text-light: #666;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Added Urban Hype styled navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, #2a6bbf 100%);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(30, 93, 172, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
            height: 50px;
            width: auto;
            filter: brightness(0) invert(1);
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
            align-items: center;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
            font-weight: 500;
        }

        /* Styled main container with cards */
        .main-container {
            max-width: 1400px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        .settings-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Styled sidebar menu */
        .sidebar-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            height: fit-content;
            position: sticky;
            top: 120px;
        }

        .sidebar-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--accent);
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar-menu a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-menu a:hover {
            background: linear-gradient(135deg, rgba(30, 93, 172, 0.08) 0%, rgba(183, 197, 218, 0.08) 100%);
            transform: translateX(5px);
            color: var(--primary);
        }

        .sidebar-menu a:hover::before {
            transform: scaleY(1);
        }

        .sidebar-menu a.active {
            background: linear-gradient(135deg, var(--primary) 0%, #2a6bbf 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.3);
        }

        /* Styled content area */
        .content-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            min-height: 500px;
        }

        .content-header {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--accent);
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            .settings-layout {
                grid-template-columns: 1fr;
            }

            .sidebar-card {
                position: static;
            }

            .navbar-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                gap: 1rem;
            }
        }

        /* Added smooth loading animation */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .content-card > * {
            animation: slideIn 0.5s ease-out backwards;
        }
    </style>
</head>

<body>
    <!-- Added Urban Hype navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo-container">
                <img src="/images/black-20and-20white-20minimalist-20professional-20initial-20logo-20251211-190157-0000.png" alt="Urban Hype Logo" class="logo">
                <span class="brand-name">URBAN HYPE</span>
            </div>
            <ul class="nav-links">
                <li><a href="../index.php" class="nav-link">Home</a></li>
                <li><a href="../products.php" class="nav-link">Shop</a></li>
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
            </ul>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($user['username'] ?? 'User'); ?></span>
                <a href="../logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Restructured layout with styled cards -->
    <div class="main-container">
        <div class="settings-layout">
            <!-- Sidebar -->
            <aside class="sidebar-card">
                <h2 class="sidebar-title">Settings</h2>
                <ul class="sidebar-menu">
                    <li>
                        <a href="?menu=profil" class="<?php echo (!isset($_GET['menu']) || $_GET['menu'] == 'profil') ? 'active' : ''; ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Profile Settings
                        </a>
                    </li>
                    <li>
                        <a href="?menu=payment" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'payment') ? 'active' : ''; ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            Payment Methods
                        </a>
                    </li>
                    <li>
                        <a href="?menu=lain" class="<?php echo (isset($_GET['menu']) && $_GET['menu'] == 'lain') ? 'active' : ''; ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M12 1v6m0 6v6m8.66-10l-5.2 3M8.54 14l-5.2 3m14.32 0l-5.2-3M8.54 10l-5.2-3"></path>
                            </svg>
                            Other Settings
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Content Area -->
            <main class="content-card">
                <?php
                // routing menu
                $menu = $_GET['menu'] ?? 'profil';

                switch ($menu) {
                    case 'profil':
                        include "settings_profil.php";
                        break;

                    case 'payment':
                        include "settings_payment.php";
                        break;

                    case 'lain':
                        include "settings_lain.php";
                        break;

                    default:
                        echo "<h4>Menu tidak ditemukan.</h4>";
                }
                ?>
            </main>
        </div>
    </div>
</body>

</html>
