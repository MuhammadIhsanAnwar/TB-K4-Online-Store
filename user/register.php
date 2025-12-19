<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Akun</title>
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <link rel="stylesheet" href="css_user/register.css">
</head>

<body>

    <div class="container d-flex justify-content-center align-items-start center-box">
        <div class="card p-4" style="max-width:520px;width:100%;">

            <div class="text-center mb-3">
                <img src="../images/icon/logo.png" alt="Urban Hype Logo" class="login-logo" style="max-width: 160px; height: auto;" />
            </div>

            <h4 class="text-center mb-4">Register Akun</h4>

            <form action="proses_register.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">

                <div class="foto-profil-container">
                    <img id="fotoProfil" class="foto-profil-preview" alt="Preview Foto">
                    <label class="upload-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.25em; margin-right: 4px;">
                            <path d="M1.5 1a1.5 1.5 0 0 0-1.5 1.5v9A1.5 1.5 0 0 0 1.5 13h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 1h-13zM14 2a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zM6 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm7 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        </svg>
                        Pilih Foto Profil
                        <input type="file" id="fotoInput" name="foto_profil" accept=".jpg,.jpeg,.png">
                    </label>
                    <small class="text-muted d-block mt-2">JPG/PNG max 5MB</small>
                    <small id="fotoStatus" class="text-muted d-block" style="color: #f44336 !important;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        Foto profil harus dipilih
                    </small>
                    <input type="hidden" id="fotoCropped" name="foto_cropped">
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>" required pattern="^[a-zA-Z0-9_]{8,20}$" title="8-20 karakter, hanya huruf, angka, underscore">
                </div>

                <script>
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
                                    statusMsg.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg> Username sudah digunakan';
                                    statusMsg.style.color = '#f44336';
                                } else {
                                    statusMsg.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg> Username tersedia';
                                    statusMsg.style.color = '#4caf50';
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    });
                </script>

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="<?php echo isset($_SESSION['form_data']['nama_lengkap']) ? htmlspecialchars($_SESSION['form_data']['nama_lengkap']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="L" <?php echo (isset($_SESSION['form_data']['jenis_kelamin']) && $_SESSION['form_data']['jenis_kelamin'] === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?php echo (isset($_SESSION['form_data']['jenis_kelamin']) && $_SESSION['form_data']['jenis_kelamin'] === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="<?php echo isset($_SESSION['form_data']['tanggal_lahir']) ? htmlspecialchars($_SESSION['form_data']['tanggal_lahir']) : ''; ?>" required>
                    <small id="ageWarning" class="text-muted" style="display: none; color: #f44336 !important;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        Usia minimal harus 13 tahun
                    </small>
                </div>

                <script>
                    const today = new Date();
                    const maxDate = new Date(today.getFullYear() - 13, today.getMonth(), today.getDate());
                    document.getElementById('tanggal_lahir').max = maxDate.toISOString().split('T')[0];

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

                <div class="mb-3">
                    <label class="form-label">Kode Pos</label>
                    <input type="text" name="kode_pos" class="form-control" value="<?php echo isset($_SESSION['form_data']['kode_pos']) ? htmlspecialchars($_SESSION['form_data']['kode_pos']) : ''; ?>" required pattern="[0-9]{5}" title="5 digit angka">
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" required><?php echo isset($_SESSION['form_data']['alamat']) ? htmlspecialchars($_SESSION['form_data']['alamat']) : ''; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor HP</label>
                    <input type="tel" name="nomor_hp" id="nomor_hp" class="form-control" value="<?php echo isset($_SESSION['form_data']['nomor_hp']) ? htmlspecialchars($_SESSION['form_data']['nomor_hp']) : ''; ?>" required pattern="[0-9]{10,13}" title="10-13 digit angka" placeholder="08123456789">
                    <small class="text-muted d-block">Contoh: 08123456789 (10-13 digit)</small>
                    <small id="nomorHpStatus" class="text-muted"></small>
                </div>

                <script>
                    const nomorHpInput = document.getElementById('nomor_hp');

                    // Filter real-time: hanya angka
                    nomorHpInput.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/[^0-9]/g, '');
                        e.target.value = value;
                        
                        const statusMsg = document.getElementById('nomorHpStatus');
                        const length = value.length;
                        
                        if (length > 0) {
                            if (length >= 10 && length <= 13) {
                                statusMsg.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg> Format nomor HP benar (' + length + ' digit)';
                                statusMsg.style.color = '#4caf50';
                            } else if (length < 10) {
                                statusMsg.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg> Minimal 10 digit (sekarang ' + length + ')';
                                statusMsg.style.color = '#ff9800';
                            } else {
                                statusMsg.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg> Maksimal 13 digit (sekarang ' + length + ')';
                                statusMsg.style.color = '#f44336';
                            }
                        } else {
                            statusMsg.textContent = '';
                        }
                        
                        console.log('Nomor HP:', value, 'Panjang:', length);
                    });

                    // Validasi saat blur
                    nomorHpInput.addEventListener('blur', function() {
                        const value = this.value.trim();
                        if (value && (value.length < 10 || value.length > 13)) {
                            alert('⚠️ Nomor HP harus 10-13 digit!\nAnda input: ' + value.length + ' digit');
                            this.focus();
                        }
                    });
                </script>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required>
                    <small id="emailStatus" class="text-muted"></small>
                </div>

                <script>
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
                                    statusMsg.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg> Email sudah terdaftar';
                                    statusMsg.style.color = '#f44336';
                                } else {
                                    statusMsg.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg> Email tersedia';
                                    statusMsg.style.color = '#4caf50';
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    });
                </script>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required onkeyup="checkPasswordStrength()">
                    <div id="passwordStrength" class="password-strength"></div>
                    <small class="text-muted">
                        Min 8 karakter, 1 huruf besar, 1 huruf kecil, 1 angka, 1 simbol.
                    </small>
                </div>

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
        const MAX_FILE_SIZE = 5 * 1024 * 1024;

        document.getElementById('fotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                alert('Hanya JPG atau PNG yang diizinkan!');
                this.value = '';
                return;
            }
            if (file.size > MAX_FILE_SIZE) {
                alert('Ukuran file maksimal 5MB!');
                this.value = '';
                return;
            }
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

        document.getElementById('password_confirm').addEventListener('keyup', function() {
            const pw = document.getElementById('password').value;
            const confirm = this.value;
            const match = document.getElementById('passwordMatch');
            if (confirm === '') {
                match.textContent = '';
                match.style.color = '#5a6b80';
            } else if (pw === confirm) {
                match.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg> Password cocok';
                match.style.color = '#4caf50';
            } else {
                match.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg> Password tidak cocok';
                match.style.color = '#f44336';
            }
        });

        function validateForm() {
            const pw = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
            const fotoCropped = document.getElementById('fotoCropped').value;
            const pola = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
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

        function cropImage() {
            if (!cropper) return;
            try {
                const canvas = cropper.getCroppedCanvas({
                    maxWidth: 400,
                    maxHeight: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });
                const circleCanvas = document.createElement('canvas');
                const size = 400;
                circleCanvas.width = size;
                circleCanvas.height = size;
                const ctx = circleCanvas.getContext('2d');
                ctx.beginPath();
                ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
                ctx.clip();
                ctx.drawImage(canvas, 0, 0, size, size);
                const previewImg = document.getElementById('fotoProfil');
                previewImg.src = circleCanvas.toDataURL('image/png');
                previewImg.classList.add('show');
                document.getElementById('fotoCropped').value = circleCanvas.toDataURL('image/png');
                const fotoStatus = document.getElementById('fotoStatus');
                fotoStatus.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -0.2em; margin-right: 4px;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg> Foto profil berhasil dipilih';
                fotoStatus.style.color = '#4caf50 !important';
                const cropperModal = bootstrap.Modal.getInstance(document.getElementById('cropperModal'));
                if (cropperModal) cropperModal.hide();
            } catch (error) {
                console.error('Error cropping image:', error);
                alert('Terjadi kesalahan saat memproses foto!');
            }
        }
    </script>

</body>

</html>