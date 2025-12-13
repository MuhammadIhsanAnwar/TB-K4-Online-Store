<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pengguna</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ================= BACKGROUND ================= */

        body.bg-light {
            background-image: url("background 2.jpg");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
        }

        body.bg-light::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.22);
            z-index: -1;
        }

        /* ================= LOGIN CARD ================= */

        .login-wrapper {
            position: sticky;
            top: 80px; /* jarak dari atas saat sticky */
        }

        .card {
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
            animation: fadeSlide 0.6s ease forwards;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ================= FORM ================= */

        .login-logo {
            width: 160px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.4));
        }

        .form-label,
        .card h4 {
            color: #fff;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 14px;
            border: none;
        }

        .btn-primary {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
        }

        .text-center.small a {
            color: #e5e5e5 !important;
        }
    </style>
</head>

<script>
(function () {
    'use strict';

    // ===== ELEMENTS =====
    const form = document.getElementById('loginForm');
    const btn = document.getElementById('loginBtn');

    if (!form || !btn) return;

    const spinner = btn.querySelector('.spinner-border');
    const text = btn.querySelector('.btn-text');
    const inputs = form.querySelectorAll('input');

    let isSubmitting = false;

    // ===== SUBMIT HANDLER =====
    form.addEventListener('submit', function (e) {
        if (isSubmitting) return;

        e.preventDefault();
        isSubmitting = true;

        // Disable UI
        btn.disabled = true;
        inputs.forEach(input => input.disabled = true);

        // Update UI
        if (spinner) spinner.classList.remove('d-none');
        if (text) text.textContent = 'Memproses...';

        // Force repaint
        btn.offsetHeight;

        // UX delay
        setTimeout(() => {
            form.submit();
        }, 300);
    });

    // ===== SAFETY: HANDLE BACK/FORWARD CACHE =====
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            isSubmitting = false;
            btn.disabled = false;
            inputs.forEach(input => input.disabled = false);

            if (spinner) spinner.classList.add('d-none');
            if (text) text.textContent = 'Masuk';
        }
    });
})();
</script>

<body class="bg-light d-flex flex-column min-vh-100">

    <!-- CONTENT -->
    <div class="container-fluid pt-5 pb-5">

        <div class="row justify-content-center">
            <div class="col-12 col-md-4">

                <!-- STICKY LOGIN -->
                <div class="login-wrapper">

                    <div class="card p-4">
                        <div class="text-center mb-3">
                            <img src="logo.png" class="login-logo">
                        </div>

                        <h4 class="text-center mb-3">Login</h4>

                        <form id="loginForm" action="proses_login.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <!-- BUTTON WITH LOADING -->
                            <button id="loginBtn" type="submit" class="btn btn-primary w-100">
                                <span class="btn-text">Masuk</span>
                                <span class="spinner-border spinner-border-sm ms-2 d-none"></span>
                            </button>
                        </form>

                        <div class="text-center small mt-3">
                            <a href="../admin/login_admin.php" class="d-block">Administrator</a>
                            <a href="register.php" class="d-block">Register Akun</a>
                            <a href="lupa_password.php" class="d-block mt-2">Lupa Password?</a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- FOOTER -->
     <footer class="bg-dark bg-opacity-75 text-light py-3">
        <div class="container text-center small">
            © <?= date('Y') ?> <strong>Urban Hype</strong> •
            <a href="../index.php" class="text-light text-decoration-none">Beranda</a> •
            <a href="#" class="text-light text-decoration-none">Kebijakan Privasi</a>
        </div>
    </footer>

    <!-- JS -->
  



</body>
</html>
