<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Lainnya - Urban Hype</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #EAE2E4 0%, #B7C5DA 50%, #1E5DAC 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .page-header {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #1E5DAC;
            margin-bottom: 0.5rem;
            animation: fadeInDown 0.6s ease;
        }

        .page-subtitle {
            color: #6B7280;
            margin-bottom: 2rem;
            animation: fadeInDown 0.6s ease 0.1s backwards;
        }

        .settings-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(30, 93, 172, 0.1);
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease 0.2s backwards;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1E5DAC;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #E8D3C1;
        }

        .form-group {
            margin-bottom: 2rem;
        }

        /* Custom Toggle Switch */
        .toggle-switch {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .toggle-switch:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 93, 172, 0.1);
        }

        .toggle-label {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 500;
            color: #1f2937;
            font-size: 1rem;
        }

        .toggle-label svg {
            width: 24px;
            height: 24px;
            color: #1E5DAC;
        }

        /* Custom Checkbox Switch */
        .switch-input {
            position: relative;
            width: 60px;
            height: 32px;
            -webkit-appearance: none;
            appearance: none;
            background: #d1d5db;
            outline: none;
            border-radius: 32px;
            cursor: pointer;
            transition: 0.3s;
        }

        .switch-input:checked {
            background: #1E5DAC;
        }

        .switch-input::before {
            content: '';
            position: absolute;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            top: 3px;
            left: 3px;
            background: white;
            transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .switch-input:checked::before {
            left: 31px;
        }

        .btn {
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1E5DAC 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(30, 93, 172, 0.4);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .danger-zone {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(239, 68, 68, 0.1);
            border: 2px solid #fee2e2;
            animation: fadeInUp 0.6s ease 0.3s backwards;
        }

        .danger-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #dc2626;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .danger-description {
            color: #6B7280;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .btn-danger {
            background: white;
            color: #dc2626;
            border: 2px solid #dc2626;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        }

        .btn-danger:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(239, 68, 68, 0.3);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem 0.5rem;
            }

            .page-header {
                font-size: 2rem;
            }

            .settings-card,
            .danger-zone {
                padding: 1.5rem;
            }

            .toggle-switch {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="page-header">Pengaturan Lainnya</h1>
        <p class="page-subtitle">Kelola preferensi dan pengaturan akun Anda</p>

        <div class="settings-card">
            <h2 class="section-title">Preferensi Tampilan</h2>
            
            <form action="update_others.php" method="POST">
                <div class="form-group">
                    <div class="toggle-switch">
                        <label class="toggle-label">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <span>Aktifkan Mode Gelap</span>
                        </label>
                        <input 
                            class="switch-input" 
                            type="checkbox"
                            name="darkmode"
                            <?php echo ($user['darkmode'] ?? 0) ? "checked" : ""; ?>>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </form>
        </div>

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
