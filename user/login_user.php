<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Pengguna</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" />
    <style>
/* ================= ROOT ================= */
:root {
    --glass-bg: rgba(255, 255, 255, 0.14);
    --glass-border: rgba(255, 255, 255, 0.28);
    --accent: #ffffff;
    --muted: rgba(255,255,255,0.7);
}

/* ================= BACKGROUND ================= */
body.bg-light {
    background-image: url("background 2.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    font-family: 'Poppins', system-ui, sans-serif;
    letter-spacing: 0.2px;
}

body.bg-light::before {
    content: "";
    position: fixed;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.35),
        rgba(0,0,0,0.55)
    );
    z-index: -1;
}

/* ================= LAYOUT ================= */
.login-wrapper {
    position: sticky;
    top: 96px;
}

.card {
    border-radius: 28px;
    background: var(--glass-bg);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border: 1px solid var(--glass-border);
    box-shadow:
        0 40px 80px rgba(0,0,0,0.45),
        inset 0 1px 0 rgba(255,255,255,0.15);
    padding: 2.2rem;
    animation: fadeUp 0.7s ease both;
}

/* ================= ANIMATION ================= */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(24px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ================= LOGO ================= */
.login-logo {
    width: 150px;
    margin-bottom: 1rem;
    filter: drop-shadow(0 10px 20px rgba(0,0,0,0.6));
}

/* ================= TYPOGRAPHY ================= */
.card h4 {
    color: var(--accent);
    font-weight: 700;
    letter-spacing: 1px;
    margin-bottom: 1.6rem;
}

.form-label {
    color: var(--muted);
    font-size: 0.9rem;
    margin-bottom: 6px;
}

/* ================= INPUT ================= */
.form-control {
    border-radius: 14px;
    padding: 14px 16px;
    border: none;
    background: rgba(255,255,255,0.9);
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.form-control:focus {
    background: #fff;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.35);
    transform: translateY(-1px);
}

/* ================= BUTTON ================= */
.btn-primary {
    border-radius: 16px;
    padding: 14px;
    font-weight: 600;
    letter-spacing: 1px;
    background: linear-gradient(
        to right,
        #ffffff,
        #e6e6e6
    );
    color: #000;
    border: none;
    transition: all 0.25s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 40px rgba(0,0,0,0.35);
    background: #fff;
}

.btn-primary:disabled {
    opacity: 0.85;
}

/* ================= LINKS ================= */
.text-center.small a {
    color: var(--muted) !important;
    font-size: 0.85rem;
    transition: color 0.2s ease;
}

.text-center.small a:hover {
    color: #ffffff !important;
}

/* ================= FOOTER ================= */
footer {
    backdrop-filter: blur(10px);
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
      const form = document.getElementById("loginForm");
      const btn = document.getElementById("loginBtn");
      const spinner = btn.querySelector(".spinner-border");
      const text = btn.querySelector(".btn-text");
      form.addEventListener("submit", function () {
        btn.disabled = true;
        spinner.classList.remove("d-none");
        text.textContent = "Memproses...";
      });
    </script>
  </body>
</html>
