<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Pengguna</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" />
    <style>
:root {
    --glass: rgba(255,255,255,0.14);
    --border: rgba(255,255,255,0.25);
    --white-soft: rgba(255,255,255,0.85);
}

/* ===== BACKGROUND ===== */
body.bg-light {
    background: url("background 2.jpg") center/cover fixed no-repeat;
    font-family: 'Poppins', system-ui, sans-serif;
}

body.bg-light::before {
    content: "";
    position: fixed;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.3),
        rgba(0,0,0,0.6)
    );
    z-index: -1;
}

/* ===== LAYOUT ===== */
.login-wrapper {
    position: sticky;
    top: 90px;
}

.card {
    border-radius: 32px;
    background: var(--glass);
    backdrop-filter: blur(26px);
    border: 1px solid var(--border);
    box-shadow:
        0 60px 120px rgba(0,0,0,0.45),
        inset 0 1px 0 rgba(255,255,255,0.15);
    padding: 2.5rem;
    animation: enter 0.8s cubic-bezier(.2,.8,.2,1);
}

/* ===== ANIMATION ===== */
@keyframes enter {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* ===== LOGO ===== */
.login-logo {
    width: 150px;
    margin-bottom: 1.2rem;
    filter: drop-shadow(0 14px 24px rgba(0,0,0,0.6));
}

/* ===== TEXT ===== */
.card h4 {
    color: #fff;
    font-weight: 700;
    letter-spacing: 1.2px;
    margin-bottom: 1.8rem;
}

.form-label {
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
    margin-bottom: 6px;
}

/* ===== INPUT ===== */
.form-control {
    border-radius: 16px;
    padding: 14px 16px;
    border: none;
    background: var(--white-soft);
    transition: all .25s ease;
}

.form-control:focus {
    background: #fff;
    box-shadow:
        0 0 0 3px rgba(255,255,255,0.4),
        0 12px 30px rgba(0,0,0,0.25);
    transform: translateY(-1px);
}

/* ===== BUTTON ===== */
.btn-primary {
    border-radius: 18px;
    padding: 15px;
    font-weight: 600;
    letter-spacing: 1.5px;
    background: linear-gradient(to right, #fff, #eaeaea);
    color: #000;
    border: none;
    transition: all .3s ease;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 24px 50px rgba(0,0,0,0.45);
}

.btn-primary:disabled {
    opacity: .9;
}

/* ===== LINKS ===== */
.text-center.small a {
    color: rgba(255,255,255,0.7) !important;
    font-size: .85rem;
}

.text-center.small a:hover {
    color: #fff !important;
}

/* ===== FOOTER ===== */
footer {
    backdrop-filter: blur(12px);
    border-top: 1px solid rgba(255,255,255,0.15);
}
</style>


  </head>
  <body class="bg-light d-flex flex-column min-vh-100">
    <!-- CONTENT -->
    <div class="container-fluid flex-grow-1 pt-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-4">
          <!-- STICKY LOGIN -->
          <div class="login-wrapper">
            <div class="card p-4">
              <div class="text-center mb-3">
                <img src="logo.png" class="login-logo" />
              </div>
              <h4 class="text-center mb-3">Login</h4>
              <form id="loginForm" action="proses_login.php" method="POST">
                <div class="mb-3">
                  <label class="form-label">Username</label>
                  <input
                    type="text"
                    name="username"
                    class="form-control"
                    required
                  />
                </div>
                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <input
                    type="password"
                    name="password"
                    class="form-control"
                    required
                  />
                </div>
                <!-- BUTTON WITH LOADING -->
                <button
                  id="loginBtn"
                  type="submit"
                  class="btn btn-primary w-100"
                >
                  <span class="btn-text">Masuk</span>
                  <span
                    class="spinner-border spinner-border-sm ms-2 d-none"
                  ></span>
                </button>
              </form>
              <div class="text-center small mt-3">
                <a href="../admin/login_admin.php" class="d-block"
                  >Administrator</a
                >
                <a href="register.php" class="d-block">Register Akun</a>
                <a href="lupa_password.php" class="d-block mt-2"
                  >Lupa Password?</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- FOOTER -->
    <footer class="bg-dark bg-opacity-75 text-light py-3 mt-auto">
      <div class="container text-center small">
        ©
        <?= date('Y') ?>
        <strong>Urban Hype</strong> •
        <a href="../index.php" class="text-light text-decoration-none"
          >Beranda</a
        >
        •
        <a href="#" class="text-light text-decoration-none"
          >Kebijakan Privasi</a
        >
      </div>
    </footer>
    <!-- JS -->
     <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
const form = document.getElementById('loginForm');
const btn = document.getElementById('loginBtn');
const spinner = btn.querySelector('.spinner-border');
const text = btn.querySelector('.btn-text');
const inputs = form.querySelectorAll('.form-control');

form.addEventListener('submit', function (e) {
    e.preventDefault();

    btn.disabled = true;
    spinner.classList.remove('d-none');
    text.textContent = 'Processing';

    // soft lock inputs
    inputs.forEach(i => i.blur());

    btn.offsetHeight;

    setTimeout(() => {
        form.submit();
    }, 350);
});

// subtle focus UX
inputs.forEach(input => {
    input.addEventListener('focus', () => {
        input.parentElement.style.transform = 'translateY(-2px)';
    });
    input.addEventListener('blur', () => {
        input.parentElement.style.transform = 'translateY(0)';
    });
});
</script>
    

  </body>
</html>
