<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register Akun</title>

<link href="../css/bootstrap.min.css" rel="stylesheet">

<style>
/* ================= PALETTE ================= */
:root {
    --blue: #1E5DAC;      /* Mediterranean Blue */
    --beige: #E8D3C1;     /* Blush Beige */
    --alley: #B7C5DA;     /* Alley */
    --misty: #EAE2E4;     /* Misty */
    --white: #ffffff;
}

/* ================= BASE ================= */
html, body {
    height: 100%;
}

/* BACKGROUND FOTO TAJAM (NO BLUR) */
body {
    background-image: url("bg regis.jpeg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    font-family: "Poppins", sans-serif;
    position: relative;
}

/* OVERLAY PALETTE (TIDAK BLUR) */
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: linear-gradient(
        135deg,
        rgba(30, 93, 172, 0.40),
        rgba(183, 197, 218, 0.30)
    );
    z-index: -1;
}

/* POSISI */
.center-box {
    padding-top: 50px;
    padding-bottom: 70px;
}

/* ================= CARD ================= */
.card {
    border-radius: 22px;
    background: rgba(234, 226, 228, 0.45); /* Misty */
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border: 1px solid rgba(255,255,255,0.45);
    box-shadow: 0 25px 50px rgba(30,93,172,0.35);
}

/* JUDUL */
.card h4 {
    color: var(--blue);
    font-weight: 700;
    letter-spacing: 0.6px;
}

/* LABEL */
.form-label {
    color: #2d3a4a;
    font-weight: 500;
}

/* INPUT */
.form-control,
.form-select {
    border-radius: 12px;
    padding: 12px 14px;
    border: none;
    background: var(--white);
}

.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 3px rgba(30,93,172,0.25);
}

/* TEXT INFO */
small.text-muted {
    color: #5a6b80 !important;
}

/* ================= BUTTON ================= */
.btn-primary {
    background: linear-gradient(
        135deg,
        var(--blue),
        var(--alley)
    );
    border: none;
    border-radius: 14px;
    padding: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(
        135deg,
        var(--alley),
        var(--blue)
    );
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(30,93,172,0.45);
}

/* LINK */
.text-center a {
    color: var(--blue);
    text-decoration: none;
    transition: 0.2s;
}

.text-center a:hover {
    color: #000;
    font-weight: 600;
}
</style>
</head>

<body>

<div class="container d-flex justify-content-center align-items-start center-box">
    <div class="card p-4" style="max-width:420px;width:100%;">

        <h4 class="text-center mb-3">Register Akun Baru</h4>

        <form action="proses_register.php" method="POST" onsubmit="return cekPassword()">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
                <small class="text-muted">
                    Minimal 8 karakter, huruf besar, huruf kecil, angka, dan simbol.
                </small>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Buat Akun
            </button>
        </form>

        <div class="text-center mt-3 small">
            Sudah punya akun? <a href="login_user.php">Login</a>
            <a href="../admin/login_admin.php" class="d-block mt-2">Administrator</a>
            <a href="../index.php" class="d-block mt-2">Kembali ke Beranda</a>
        </div>

    </div>
</div>

<script>
function cekPassword() {
    let pw = document.getElementById("password").value;
    let pola = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (!pola.test(pw)) {
        alert("Password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial.");
        return false;
    }
    return true;
}
</script>

</body>
</html>
