<?php
include "../admin/koneksi.php";

$token = $_GET['token'] ?? '';

if (empty($token)) {
    showErrorPage('Token tidak ditemukan!', 'forgot.php');
}

$cek = mysqli_query($koneksi, "SELECT * FROM reset_password WHERE token='$token'");
$data = mysqli_fetch_assoc($cek);

if (!$data) {
    showErrorPage('Token tidak valid!', 'forgot.php');
}

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
        <link rel="stylesheet" href="css_user/reset_password.css">
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
    <link rel="stylesheet" href="css_user/reset_password.css">
</head>

<body>
    <div class="reset-container">
        <div class="card">
            <div class="logo-container">
                <img src="../images/Background dan Logo/logo.png" alt="Urban Hype Logo">
            </div>

            <h3>Reset Password</h3>
            <p class="subtitle">Buat password baru untuk akun Anda</p>

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

                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" id="password" class="form-control" required onkeyup="checkPasswordStrength()">
                    <div id="passwordStrength" class="password-strength"></div>
                    <small class="text-muted d-block mt-2" id="passwordStrengthText">Masukkan password yang kuat</small>
                </div>

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
                strengthText.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg> Password terlalu pendek (min 8 karakter)';
                return;
            }

            const hasUpper = /[A-Z]/.test(pw);
            const hasLower = /[a-z]/.test(pw);
            const hasNumber = /\d/.test(pw);
            const hasSymbol = /[\W_]/.test(pw);

            const score = [hasUpper, hasLower, hasNumber, hasSymbol].filter(Boolean).length;

            if (score <= 2) {
                strength.className = 'password-strength weak';
                strengthText.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg> Password lemah - tambahkan huruf besar, kecil, angka, dan simbol';
            } else if (score === 3) {
                strength.className = 'password-strength medium';
                strengthText.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg> Password sedang - hampir sempurna!';
            } else if (score === 4) {
                strength.className = 'password-strength strong';
                strengthText.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg> Password kuat!';
            }
        }

        function checkPasswordMatch() {
            const pw = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
            const match = document.getElementById('passwordMatch');

            if (confirm === '') {
                match.textContent = '';
                return;
            }

            if (pw === confirm) {
                match.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg> Password cocok';
                match.style.color = '#4caf50';
            } else {
                match.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg> Password tidak cocok';
                match.style.color = '#f44336';
            }
        }

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