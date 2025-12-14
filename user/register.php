<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Akun</title>

    <!-- Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ================= URBANHYPE REGISTER ================= */

        html, body {
            height: 100%;
        }

        body.bg-light {
            background-image: url("bg regis.jpeg");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            font-family: "Poppins", sans-serif;
            position: relative;
        }

        /* overlay TONE URBANHYPE (samain tema) */
        body.bg-light::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(30, 93, 172, 0.55),
                rgba(183, 197, 218, 0.45)
            );
            z-index: -1;
        }

        /* Container posisi */
        .center-box {
            padding-top: 50px;
            padding-bottom: 70px;
        }

        /* Card glass */
        .card {
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 20px 45px rgba(30, 93, 172, 0.35);
        }

        /* Judul */
        .card h4 {
            color: #ffffff;
            font-weight: 700;
            letter-spacing: 0.6px;
        }

        /* Label */
        .form-label {
            color: #eef3ff;
            font-weight: 500;
        }

        /* Input */
        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px 14px;
            border: none;
            background: rgba(255, 255, 255, 0.95);
        }

        .form-control:focus,
        .form-select:focus {
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.25);
        }

        textarea.form-control {
            resize: none;
        }

        small.text-muted {
            color: #e6ecff !important;
        }

        /* Button utama (SAMAIN HOME) */
        .btn-primary {
            background: linear-gradient(
                135deg,
                #1E5DAC,
                #B7C5DA
            );
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(
                135deg,
                #B7C5DA,
                #1E5DAC
            );
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 93, 172, 0.45);
        }

        /* Link bawah */
        .text-center a {
            color: #eef3ff;
            text-decoration: none;
            transition: 0.2s;
        }

        .text-center a:hover {
            color: #ffffff;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-start center-box">
        <div class="card p-4" style="max-width: 420px; width: 100%;">

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

                <a href="../admin/login_admin.php" class="d-block mt-2">
                    Administrator
                </a>

                <a href="../index.php" class="d-block mt-2">
                    Kembali ke Beranda
                </a>
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
