<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Akun</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        /* Biar card turun sedikit dari atas */
        .center-box {
            margin-top: 40px;
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
                    class="d-block text-decoration-none text-secondary mt-2">Administrator</a>

                <a href="../index.php" class="d-block text-decoration-none mt-2">Kembali ke Beranda</a>
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