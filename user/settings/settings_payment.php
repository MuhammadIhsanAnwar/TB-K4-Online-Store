<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metode Pembayaran</title>
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
            background: linear-gradient(135deg, #EAE2E4 0%, #B7C5DA 50%, #1E5DAC 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(30, 93, 172, 0.15);
            backdrop-filter: blur(10px);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: #1E5DAC;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        hr {
            border: none;
            height: 2px;
            background: linear-gradient(90deg, #1E5DAC 0%, #B7C5DA 100%);
            margin-bottom: 40px;
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 32px;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: 0.95rem;
            color: #2c3e50;
            margin-bottom: 12px;
            letter-spacing: 0.3px;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            border: 2px solid #E8D3C1;
            border-radius: 12px;
            background: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #2c3e50;
        }

        .form-control:focus {
            outline: none;
            border-color: #1E5DAC;
            box-shadow: 0 4px 20px rgba(30, 93, 172, 0.15);
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #B7C5DA;
        }

        .btn-save {
            background: linear-gradient(135deg, #1E5DAC 0%, #2d71c9 100%);
            color: white;
            border: none;
            padding: 16px 40px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 24px rgba(30, 93, 172, 0.3);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }

        .btn-save::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(30, 93, 172, 0.4);
        }

        .btn-save:hover::before {
            left: 100%;
        }

        .btn-save:active {
            transform: translateY(0);
        }

        .back-link {
            display: inline-block;
            color: #1E5DAC;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 8px;
        }

        .back-link:hover {
            background: rgba(30, 93, 172, 0.1);
            transform: translateX(-4px);
        }

        .info-card {
            background: linear-gradient(135deg, rgba(30, 93, 172, 0.05) 0%, rgba(183, 197, 218, 0.1) 100%);
            border-left: 4px solid #1E5DAC;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 32px;
        }

        .info-card p {
            color: #5a6c7d;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }

        @media (max-width: 768px) {
            .container {
                padding: 32px 24px;
            }

            h3 {
                font-size: 2rem;
            }

            .btn-save {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="settings.php" class="back-link">← Kembali ke Pengaturan</a>
        
        <h3>Metode Pembayaran</h3>
        <hr>

        <div class="info-card">
            <p>Tambahkan informasi metode pembayaran Anda untuk mempercepat proses checkout. Anda dapat menambahkan nomor rekening bank atau e-wallet favorit Anda.</p>
        </div>

        <form action="update_payment.php" method="POST">
            <div class="form-group">
                <label>Bank / E-Wallet</label>
                <input type="text" name="pembayaran" class="form-control"
                    placeholder="Contoh: BRI 1234xxxx, Dana 08xxxx"
                    value="<?php echo $user['pembayaran'] ?? ''; ?>">
            </div>

            <button type="submit" class="btn-save">Simpan Metode Pembayaran</button>
        </form>
    </div>
</body>

</html>
