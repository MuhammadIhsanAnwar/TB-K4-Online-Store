<?php
// Tidak ada proses di sini, hanya form untuk memasukkan email
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../images/logo/logo.png">
    <style>
        /* ================= PALETTE ================= */
        :root {
            --blue: #1E5DAC;
            --beige: #E8D3C1;
            --alley: #B7C5DA;
            --misty: #EAE2E4;
            --white: #ffffff;
        }

        /* ================= BASE ================= */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-image: url("../images/Background dan Logo/bg regis.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: "Poppins", sans-serif;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg,
                    rgba(30, 93, 172, 0.40),
                    rgba(183, 197, 218, 0.30));
            z-index: -1;
        }

        /* ================= CARD ================= */
        .card {
            border-radius: 22px;
            background: rgba(234, 226, 228, 0.45);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 25px 50px rgba(30, 93, 172, 0.35);
            padding: 40px;
            max-width: 500px;
            width: 100%;
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

        .card h2 {
            color: var(--blue);
            font-weight: 700;
            letter-spacing: 0.6px;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .card-subtitle {
            color: #5a6b80;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .info-box {
            background: rgba(30, 93, 172, 0.1);
            border-left: 3px solid var(--blue);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            color: #2d3a4a;
            line-height: 1.6;
        }

        /* ================= FORM ================= */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            color: #2d3a4a;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #B7C5DA;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.8);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.25);
            background: var(--white);
        }

        .form-control::placeholder {
            color: #B7C5DA;
        }

        small {
            color: #5a6b80;
            display: block;
            margin-top: 6px;
            font-size: 0.85rem;
        }

        /* ================= BUTTON ================= */
        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, var(--blue), var(--alley));
            border: none;
            border-radius: 12px;
            padding: 12px;
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: 0.3s ease;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--alley), var(--blue));
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 93, 172, 0.45);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .back-link {
            display: inline-block;
            color: var(--blue);
            text-decoration: none;
            font-weight: 500;
            margin-top: 1rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            text-align: center;
            width: 100%;
        }

        .back-link:hover {
            color: #0d3a7a;
            text-decoration: underline;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo-container img {
            max-width: 120px;
            height: auto;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 576px) {
            .card {
                padding: 30px 20px;
            }

            .card h2 {
                font-size: 1.5rem;
            }

            .form-control {
                font-size: 16px;
                padding: 14px 12px;
            }

            .btn-primary {
                padding: 14px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>

    <div class="card">
        <!-- LOGO -->
        <div class="logo-container">
            <img src="../../images/icon/logo.png" alt="Urban Hype Logo">
        </div>

        <h2>🔐 Ubah Password</h2>
        <p class="card-subtitle">Kirimkan link perubahan password ke email Anda untuk proses perubahan yang aman</p>

        <!-- INFO BOX -->
        <div class="info-box">
            <strong>📧 Cara kerja:</strong><br>
            Kami akan mengirimkan link aman ke email Anda. Klik link tersebut untuk mengatur password baru Anda. Link berlaku selama 1 jam.
        </div>

        <!-- FORM -->
        <form action="../proses_kirim_reset.php" method="POST">
            <div class="form-group">
                <label>Email Akun Anda</label>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Masukkan email akun Anda"
                    value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                    required>
                <small>Kami akan mengirimkan link perubahan password ke email ini</small>
            </div>

            <button type="submit" class="btn-primary">
                📧 Kirim Link Perubahan Password
            </button>

            <a href="settings.php" class="back-link">← Kembali ke Settings</a>
        </form>
    </div>

</body>

</html>