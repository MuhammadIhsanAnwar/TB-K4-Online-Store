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

        .center-box {
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-start vh-100 center-box">
        <div class="card shadow-sm p-4" style="max-width: 420px; width: 100%;">
            <h4 class="text-center mb-3">Register Akun Baru</h4>

            <form action="proses_register.php" method="POST" onsubmit="return cekForm()" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" minlength="8" required>
                    <small class="text-muted">Minimal 8 karakter.</small>
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
                    <label class="form-label">Provinsi</label>
                    <input type="text" name="provinsi" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kabupaten/Kota</label>
                    <input type="text" name="kabupaten_kota" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" name="kecamatan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kelurahan/Desa</label>
                    <input type="text" name="kelurahan_desa" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kode Pos</label>
                    <input type="number" name="kode_pos" class="form-control" required>
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
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>

                <small class="text-muted">
                    Minimal 1 huruf besar, 1 huruf kecil, 1 angka, 1 simbol, min. 8 karakter.
                </small>

                <div class="mb-3 mt-2">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*" required>
                    <small class="text-muted">Upload foto 1:1 (square). Akan dicrop otomatis.</small>
                </div>

                <button class="btn btn-primary w-100 mt-3">Buat Akun</button>

            </form>

            <script>
                function cekForm() {
                    let user = document.getElementById("username").value;
                    if (user.length < 8) {
                        alert("Username harus minimal 8 karakter!");
                        return false;
                    }

                    let pw = document.getElementById("password").value;
                    let cpw = document.getElementById("confirm_password").value;

                    let pola = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

                    if (!pola.test(pw)) {
                        alert("Password tidak sesuai ketentuan!");
                        return false;
                    }

                    if (pw !== cpw) {
                        alert("Konfirmasi password tidak sama!");
                        return false;
                    }

                    return true;
                }
            </script>
        </div>
    </div>

</body>

</html>