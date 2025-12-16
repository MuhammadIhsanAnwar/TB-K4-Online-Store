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

            .danger-zone {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="page-header">Pengaturan Lainnya</h1>
        <p class="page-subtitle">Kelola akun Anda</p>

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
