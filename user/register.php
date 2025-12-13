<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Akun</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ================= REGISTER USER STYLE ================= */

        body {
            background: url("bg regis.jpg") no-repeat center center / cover;
            font-family: 'Poppins', sans-serif;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(4px);
            z-index: -1;
        }

        /* posisi card */
        .center-box {
            margin-top: 40px;
        }

        /* Card */
        .card {
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
        }

        /* Judul */
        .card h4 {
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* Label */
        .form-label {
            color: #fff;
            font-weight: 500;
        }

        /* Input & textarea & select */
        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px 14px;
            border: none;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus,
        .form-select:focus {
            background: #fff;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        }

        textarea.form-control {
            resize: none;
        }

        /* Text bantuan password */
        small.text-muted {
            color: #ddd !important;
        }

        /* Tombol */
        .btn-primary {
            background: linear-gradient(to right, #0a1a3f, #112d63);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: 0.25s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(to right, #122752, #1b3b79);
        }

        /* Link bawah */
        .text-center a {
            color: #e5e5e5;
            text-decoration: none;
        }

        .text-center a:hover {
            color: #fff;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-start vh-100 center-box">
        <div class="card shadow-sm p-4" style="max-width: 420px; width: 100%;">
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
                        Minimal 1 huruf besar, 1 huruf kecil, 1 angka, dan 1 simbol.
                    </small>
                </div>

                <button class="btn btn-primary w-100">Buat Akun</button>
            </form>

            <div class="text-center mt-3 small">
                Sudah punya akun? <a href="login_user.php">Login</a>

                <a href="../admin/login_admin.php"
                    class="d-block text-decoration-none mt-2">Administrator</a>

                <a href="../index.php" class="d-block text-decoration-none mt-2">
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>

    <script>
        function cekPassword() {
            let pw = document.getElementById("password").value;
            let pola = /^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_]).{8,}$/;

            if (!pola.test(pw)) {
                alert("Password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial.");
                return false;
            }
            return true;
        }
    </script>

</body>

</html>