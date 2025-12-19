<?php
include "../admin/koneksi.php";

$token = $_GET['token'] ?? '';

// Validasi token
if (empty($token)) {
    showErrorPage('Token tidak ditemukan!', 'forgot.php');
}

// Ambil token
$cek = mysqli_query($koneksi, "SELECT * FROM reset_password WHERE token='$token'");
$data = mysqli_fetch_assoc($cek);

if (!$data) {
    showErrorPage('Token tidak valid!', 'forgot.php');
}

// Cek expired
if (strtotime($data['expired']) < time()) {
    showErrorPage('Token sudah kedaluwarsa! Silakan minta link reset baru.', 'forgot.php');
}

$email = $data['email'];

function showErrorPage($message, $redirect)
{
?>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Error</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../css_user/reset_password.css">
    </head>

    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: '<?php echo $message; ?>',
                confirmButtonColor: '#1E5DAC',
                confirmButtonText: 'Kembali',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '<?php echo $redirect; ?>';
                }
            });
        </script>
    </body>

    </html>
<?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
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

        .reset-container {
            padding: 20px;
            max-width: 450px;
            width: 100%;
        }

        /* ================= CARD ================= */
        .card {
            border-radius: 22px;
            background: rgba(234, 226, 228, 0.45);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 25px 50px rgba(30, 93, 172, 0.35);
            padding: 40px 30px;
        }

        .card h3 {
            color: var(--blue);
            font-weight: 700;
            letter-spacing: 0.6px;
            margin-bottom: 10px;
            text-align: center;
        }

        .card .subtitle {
            color: #5a6b80;
            font-size: 14px;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-label {
            color: #2d3a4a;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 14px;
            border: none;
            background: var(--white);
            font-size: 15px;
            transition: 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.25);
            border: none;
            outline: none;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .password-requirements {
            background: rgba(30, 93, 172, 0.1);
            border-left: 3px solid var(--blue);
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #2d3a4a;
        }

        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }

        .password-requirements li {
            margin-bottom: 4px;
        }

        .password-strength {
            height: 6px;
            border-radius: 3px;
            margin-top: 8px;
            background: #e0e0e0;
            transition: 0.3s;
        }

        .password-strength.weak {
            background: #f44336;
            width: 33%;
        }

        .password-strength.medium {
            background: #ff9800;
            width: 66%;
        }

        .password-strength.strong {
            background: #4caf50;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--blue), var(--alley));
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: 0.3s ease;
            font-size: 15px;
            width: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--alley), var(--blue));
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 93, 172, 0.45);
            color: white;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .text-center-link {
            text-align: center;
            margin-top: 20px;
        }

        .text-center-link a {
            color: var(--blue);
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
            font-size: 14px;
        }

        .text-center-link a:hover {
            color: #0d3a7a;
            font-weight: 600;
        }

        small.text-muted {
            color: #5a6b80 !important;
            font-size: 13px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-container img {
            max-width: 140px;
            height: auto;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 576px) {
            .card {
                padding: 30px 20px;
            }

            .card h3 {
                font-size: 24px;
            }

            .form-control {
                font-size: 16px;
                padding: 14px 12px;
            }

            .reset-container {
                padding: 15px;
            }
        }

        @media (max-width: 400px) {
            .card {
                padding: 25px 18px;
                border-radius: 18px;
            }

            .card h3 {
                font-size: 22px;
                margin-bottom: 8px;
            }

            .card .subtitle {
                font-size: 13px;
                margin-bottom: 25px;
            }

            .password-requirements {
                font-size: 12px;
                padding: 10px 12px;
            }

            .btn-primary {
                padding: 11px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="reset-container">
        <div class="card">
            <!-- LOGO -->
            <div class="logo-container">
                <img src="../images/Background dan Logo/logo.png" alt="Urban Hype Logo">
            </div>

            <h3>Reset Password</h3>
            <p class="subtitle">Buat password baru untuk akun Anda</p>

            <!-- PASSWORD REQUIREMENTS -->
            <div class="password-requirements">
                <strong>Password harus mengandung:</strong>
                <ul>
                    <li>Minimal 8 karakter</li>
                    <li>Minimal 1 huruf besar (A-Z)</li>
                    <li>Minimal 1 huruf kecil (a-z)</li>
                    <li>Minimal 1 angka (0-9)</li>
                    <li>Minimal 1 simbol/karakter spesial (!@#$%^&*)</li>
                </ul>
            </div>

            <form action="proses_reset_password.php" method="POST" onsubmit="return validateForm()">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                <!-- PASSWORD BARU -->
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" id="password" class="form-control" required onkeyup="checkPasswordStrength()">
                    <div id="passwordStrength" class="password-strength"></div>
                    <small class="text-muted d-block mt-2" id="passwordStrengthText">Masukkan password yang kuat</small>
                </div>

                <!-- KONFIRMASI PASSWORD -->
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" required onkeyup="checkPasswordMatch()">
                    <small id="passwordMatch" class="text-muted d-block mt-2"></small>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Password
                </button>
            </form>

            <div class="text-center-link">
                <a href="login_user.php">Kembali ke Login</a>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
        // Check password strength
        function checkPasswordStrength() {
            const pw = document.getElementById('password').value;
            const strength = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('passwordStrengthText');

            if (pw.length === 0) {
                strength.className = 'password-strength';
                strengthText.textContent = 'Masukkan password yang kuat';
                return;
            }

            if (pw.length < 8) {
                strength.className = 'password-strength weak';
                strengthText.textContent = '⚠️ Password terlalu pendek (min 8 karakter)';
                return;
            }

            const hasUpper = /[A-Z]/.test(pw);
            const hasLower = /[a-z]/.test(pw);
            const hasNumber = /\d/.test(pw);
            const hasSymbol = /[\W_]/.test(pw);

            const score = [hasUpper, hasLower, hasNumber, hasSymbol].filter(Boolean).length;

            if (score <= 2) {
                strength.className = 'password-strength weak';
                strengthText.textContent = '❌ Password lemah - tambahkan huruf besar, kecil, angka, dan simbol';
            } else if (score === 3) {
                strength.className = 'password-strength medium';
                strengthText.textContent = '⚠️ Password sedang - hampir sempurna!';
            } else if (score === 4) {
                strength.className = 'password-strength strong';
                strengthText.textContent = '✅ Password kuat!';
            }
        }

        // Check password match
        function checkPasswordMatch() {
            const pw = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
            const match = document.getElementById('passwordMatch');

            if (confirm === '') {
                match.textContent = '';
                return;
            }

            if (pw === confirm) {
                match.textContent = '✓ Password cocok';
                match.style.color = '#4caf50';
            } else {
                match.textContent = '✗ Password tidak cocok';
                match.style.color = '#f44336';
            }
        }

        // Validasi form
        function validateForm() {
            const pw = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
            const pola = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

            if (!pola.test(pw)) {
                alert('Password harus: min 8 karakter, 1 huruf besar, 1 huruf kecil, 1 angka, 1 simbol!');
                return false;
            }

            if (pw !== confirm) {
                alert('Password tidak cocok!');
                return false;
            }

            return true;
        }
    </script>

</body>

</html>