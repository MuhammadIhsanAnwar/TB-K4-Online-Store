<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Akun</title>
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

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
        }

        body {
            background-image: url("../images/Background dan Logo/bg regis.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: "Poppins", sans-serif;
            position: relative;
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

        .center-box {
            padding-top: 50px;
            padding-bottom: 70px;
        }

        /* ================= CARD ================= */
        .card {
            border-radius: 22px;
            background: rgba(234, 226, 228, 0.45);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 25px 50px rgba(30, 93, 172, 0.35);
        }

        .card h4 {
            color: var(--blue);
            font-weight: 700;
            letter-spacing: 0.6px;
        }

        .form-label {
            color: #2d3a4a;
            font-weight: 500;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px 14px;
            border: none;
            background: var(--white);
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.25);
            border: none;
        }

        small.text-muted {
            color: #5a6b80 !important;
        }

        /* ================= BUTTON ================= */
        .btn-primary {
            background: linear-gradient(135deg, var(--blue), var(--alley));
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--alley), var(--blue));
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 93, 172, 0.45);
        }

        .text-center a {
            color: var(--blue);
            text-decoration: none;
            transition: 0.2s;
        }

        .text-center a:hover {
            color: #000;
            font-weight: 600;
        }

        /* ================= FOTO PROFIL ================= */
        .foto-profil-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .foto-profil-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid var(--blue);
            object-fit: cover;
            margin: 0 auto 15px;
            display: none;
        }

        .foto-profil-preview.show {
            display: block;
        }

        .upload-btn {
            background: var(--blue);
            color: white;
            border-radius: 12px;
            padding: 8px 16px;
            cursor: pointer;
            transition: 0.3s;
            display: inline-block;
        }

        .upload-btn:hover {
            background: var(--alley);
        }

        #fotoInput {
            display: none;
        }

        /* ================= MODAL CROPPER ================= */
        .modal-content {
            border-radius: 15px;
            background: rgba(234, 226, 228, 0.95);
        }

        .modal-header {
            border-bottom: 1px solid rgba(30, 93, 172, 0.2);
        }

        .modal-title {
            color: var(--blue);
            font-weight: 700;
        }

        #imageToCrop {
            max-width: 100%;
            max-height: 400px;
        }

        /* ================= PASSWORD STRENGTH ================= */
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

        .form-two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 576px) {
            .form-two-column {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-start center-box">
        <div class="card p-4" style="max-width:520px;width:100%;">

            <div class="text-center mb-3">
                <img src="../images/Background dan Logo/logo.png" alt="Urban Hype Logo" class="login-logo" style="max-width: 160px; height: auto;" />
            </div>

            <h4 class="text-center mb-4">Register Akun</h4>

            <form action="proses_register.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">

                <!-- FOTO PROFIL -->
                <div class="foto-profil-container">
                    <img id="fotoProfil" class="foto-profil-preview" alt="Preview Foto">
                    <label class="upload-btn">
                        📷 Pilih Foto Profil
                        <input type="file" id="fotoInput" name="foto_profil" accept=".jpg,.jpeg,.png">
                    </label>
                    <small class="text-muted d-block mt-2">JPG/PNG max 5MB</small>
                    <small id="fotoStatus" class="text-muted d-block" style="color: #f44336 !important;">⚠️ Foto profil harus dipilih</small>
                    <input type="hidden" id="fotoCropped" name="foto_cropped">
                </div>

                <!-- USERNAME -->
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>" required pattern="^[a-zA-Z0-9_]{3,20}$" title="3-20 karakter, hanya huruf, angka, underscore">
                    <small id="usernameStatus" class="text-muted"></small>
                </div>

                <script>
                    // Check username availability
                    document.getElementById('username').addEventListener('blur', function() {
                        const username = this.value.trim();
                        const statusMsg = document.getElementById('usernameStatus');

                        if (username.length < 3) {
                            statusMsg.textContent = '';
                            return;
                        }

                        fetch('check_username.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'username=' + encodeURIComponent(username)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.exists) {
                                    statusMsg.textContent = '✗ Username sudah digunakan';
                                    statusMsg.style.color = '#f44336';
                                } else {
                                    statusMsg.textContent = '✓ Username tersedia';
                                    statusMsg.style.color = '#4caf50';
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    });
                </script>

                <!-- NAMA LENGKAP -->
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="<?php echo isset($_SESSION['form_data']['nama_lengkap']) ? htmlspecialchars($_SESSION['form_data']['nama_lengkap']) : ''; ?>" required>
                </div>

                <!-- JENIS KELAMIN -->
                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="L" <?php echo (isset($_SESSION['form_data']['jenis_kelamin']) && $_SESSION['form_data']['jenis_kelamin'] === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?php echo (isset($_SESSION['form_data']['jenis_kelamin']) && $_SESSION['form_data']['jenis_kelamin'] === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>

                <!-- TANGGAL LAHIR -->
                <div class="mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="<?php echo isset($_SESSION['form_data']['tanggal_lahir']) ? htmlspecialchars($_SESSION['form_data']['tanggal_lahir']) : ''; ?>" required>
                    <small id="ageWarning" class="text-muted" style="display: none; color: #f44336 !important;">⚠️ Usia minimal harus 13 tahun</small>
                </div>

                <script>
                    // Set max date to 13 years ago
                    const today = new Date();
                    const maxDate = new Date(today.getFullYear() - 13, today.getMonth(), today.getDate());
                    document.getElementById('tanggal_lahir').max = maxDate.toISOString().split('T')[0];

                    // Validasi usia saat user mengubah tanggal
                    document.getElementById('tanggal_lahir').addEventListener('change', function() {
                        const birthDate = new Date(this.value);
                        const age = today.getFullYear() - birthDate.getFullYear();
                        const monthDiff = today.getMonth() - birthDate.getMonth();
                        const dayDiff = today.getDate() - birthDate.getDate();

                        const actualAge = monthDiff < 0 || (monthDiff === 0 && dayDiff < 0) ? age - 1 : age;
                        const warning = document.getElementById('ageWarning');

                        if (actualAge < 13) {
                            warning.style.display = 'block';
                        } else {
                            warning.style.display = 'none';
                        }
                    });
                </script>

                <!-- PROVINSI, KABUPATEN, KECAMATAN (2 KOLOM) -->
                <div class="form-two-column mb-3">
                    <div>
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="provinsi" class="form-control" value="<?php echo isset($_SESSION['form_data']['provinsi']) ? htmlspecialchars($_SESSION['form_data']['provinsi']) : ''; ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten_kota" class="form-control" value="<?php echo isset($_SESSION['form_data']['kabupaten_kota']) ? htmlspecialchars($_SESSION['form_data']['kabupaten_kota']) : ''; ?>" required>
                    </div>
                </div>

                <!-- KECAMATAN, KELURAHAN (2 KOLOM) -->
                <div class="form-two-column mb-3">
                    <div>
                        <label class="form-label">Kecamatan</label>
                        <input type="text" name="kecamatan" class="form-control" value="<?php echo isset($_SESSION['form_data']['kecamatan']) ? htmlspecialchars($_SESSION['form_data']['kecamatan']) : ''; ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Kelurahan/Desa</label>
                        <input type="text" name="kelurahan_desa" class="form-control" value="<?php echo isset($_SESSION['form_data']['kelurahan_desa']) ? htmlspecialchars($_SESSION['form_data']['kelurahan_desa']) : ''; ?>" required>
                    </div>
                </div>

                <!-- KODE POS -->
                <div class="mb-3">
                    <label class="form-label">Kode Pos</label>
                    <input type="text" name="kode_pos" class="form-control" value="<?php echo isset($_SESSION['form_data']['kode_pos']) ? htmlspecialchars($_SESSION['form_data']['kode_pos']) : ''; ?>" required pattern="[0-9]{5}" title="5 digit angka">
                </div>

                <!-- ALAMAT -->
                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" required><?php echo isset($_SESSION['form_data']['alamat']) ? htmlspecialchars($_SESSION['form_data']['alamat']) : ''; ?></textarea>
                </div>

                <!-- EMAIL -->
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required>
                    <small id="emailStatus" class="text-muted"></small>
                </div>

                <script>
                    // Check email availability
                    document.getElementById('email').addEventListener('blur', function() {
                        const email = this.value.trim();
                        const statusMsg = document.getElementById('emailStatus');

                        if (!email) {
                            statusMsg.textContent = '';
                            return;
                        }

                        fetch('check_email.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'email=' + encodeURIComponent(email)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.exists) {
                                    statusMsg.textContent = '✗ Email sudah terdaftar';
                                    statusMsg.style.color = '#f44336';
                                } else {
                                    statusMsg.textContent = '✓ Email tersedia';
                                    statusMsg.style.color = '#4caf50';
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    });
                </script>

                <!-- PASSWORD -->
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required onkeyup="checkPasswordStrength()">
                    <div id="passwordStrength" class="password-strength"></div>
                    <small class="text-muted">
                        Min 8 karakter, 1 huruf besar, 1 huruf kecil, 1 angka, 1 simbol.
                    </small>
                </div>

                <!-- KONFIRMASI PASSWORD -->
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
                    <small id="passwordMatch" class="text-muted"></small>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Buat Akun
                </button>
            </form>

            <div class="text-center mt-3 small">
                Sudah punya akun? <a href="login_user.php">Login</a>
                <a href="../admin/login_admin.php" class="d-block mt-2">Login Administrator</a>
                <a href="../index.php" class="d-block mt-2">Kembali ke Beranda</a>
            </div>

        </div>
    </div>

    <!-- MODAL CROPPER -->
    <div class="modal fade" id="cropperModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Foto Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img id="imageToCrop" src="" alt="Image">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="cropImage()">Crop</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>

    <script>
        let cropper = null;
        const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

        // Handle foto profil upload
        document.getElementById('fotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (!file) return;

            // Validasi tipe file
            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                alert('Hanya JPG atau PNG yang diizinkan!');
                this.value = '';
                return;
            }

            // Validasi ukuran file
            if (file.size > MAX_FILE_SIZE) {
                alert('Ukuran file maksimal 5MB!');
                this.value = '';
                return;
            }

            // Tampilkan modal cropper
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = document.getElementById('imageToCrop');
                img.src = event.target.result;

                if (cropper) cropper.destroy();
                cropper = new Cropper(img, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 0.8,
                    responsive: true,
                    guides: false,
                    highlight: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: true
                });

                new bootstrap.Modal(document.getElementById('cropperModal')).show();
            };
            reader.readAsDataURL(file);
        });

        // Check password strength
        function checkPasswordStrength() {
            const pw = document.getElementById('password').value;
            const strength = document.getElementById('passwordStrength');

            if (pw.length < 8) {
                strength.className = 'password-strength';
                return;
            }

            const hasUpper = /[A-Z]/.test(pw);
            const hasLower = /[a-z]/.test(pw);
            const hasNumber = /\d/.test(pw);
            const hasSymbol = /[\W_]/.test(pw);

            const score = [hasUpper, hasLower, hasNumber, hasSymbol].filter(Boolean).length;

            if (score <= 2) strength.className = 'password-strength weak';
            else if (score === 3) strength.className = 'password-strength medium';
            else strength.className = 'password-strength strong';
        }

        // Monitor password confirmation
        document.getElementById('password_confirm').addEventListener('keyup', function() {
            const pw = document.getElementById('password').value;
            const confirm = this.value;
            const match = document.getElementById('passwordMatch');

            if (confirm === '') {
                match.textContent = '';
                match.style.color = '#5a6b80';
            } else if (pw === confirm) {
                match.textContent = '✓ Password cocok';
                match.style.color = '#4caf50';
            } else {
                match.textContent = '✗ Password tidak cocok';
                match.style.color = '#f44336';
            }
        });

        // Validasi form
        function validateForm() {
            const pw = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
            const fotoCropped = document.getElementById('fotoCropped').value;
            const pola = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

            // Validasi foto profil
            if (!fotoCropped) {
                alert('Foto profil harus dipilih dan di-crop terlebih dahulu!');
                return false;
            }

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

        // Update status foto profil ketika crop selesai
        // Update status foto profil ketika crop selesai
        function cropImage() {
            if (!cropper) return;

            try {
                const canvas = cropper.getCroppedCanvas({
                    maxWidth: 400,
                    maxHeight: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                // Convert ke circular image
                const circleCanvas = document.createElement('canvas');
                const size = 400;
                circleCanvas.width = size;
                circleCanvas.height = size;

                const ctx = circleCanvas.getContext('2d');
                ctx.beginPath();
                ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
                ctx.clip();
                ctx.drawImage(canvas, 0, 0, size, size);

                // Display preview
                const previewImg = document.getElementById('fotoProfil');
                previewImg.src = circleCanvas.toDataURL('image/png');
                previewImg.classList.add('show');

                // Store data
                document.getElementById('fotoCropped').value = circleCanvas.toDataURL('image/png');

                // Update status menjadi berhasil
                const fotoStatus = document.getElementById('fotoStatus');
                fotoStatus.textContent = '✓ Foto profil berhasil dipilih';
                fotoStatus.style.color = '#4caf50 !important';

                // Tutup modal langsung
                const cropperModal = bootstrap.Modal.getInstance(document.getElementById('cropperModal'));
                if (cropperModal) {
                    cropperModal.hide();
                }
            } catch (error) {
                console.error('Error cropping image:', error);
                alert('Terjadi kesalahan saat memproses foto!');
            }
        }
    </script>

</body>

</html>