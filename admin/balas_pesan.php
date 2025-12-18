<?php
require 'auth_check.php';
include "../admin/koneksi.php";

// Cek parameter
if (!isset($_GET['id']) || !isset($_GET['email']) || !isset($_GET['nama'])) {
    header("Location: data_pesan.php");
    exit;
}

$id = intval($_GET['id']);
$email_penerima = htmlspecialchars($_GET['email']);
$nama_pengirim = htmlspecialchars($_GET['nama']);

// Ambil data pesan original
$query = mysqli_query($koneksi, "SELECT * FROM pesan_kontak WHERE id='$id'");
$pesan_original = mysqli_fetch_assoc($query);

if (!$pesan_original) {
    header("Location: data_pesan.php");
    exit;
}

$pesan_asli = htmlspecialchars($pesan_original['pesan']);
$tanggal_asli = date('d/m/Y H:i', strtotime($pesan_original['created_at']));

// Proses submit balasan
$status_proses = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = isset($_POST['subject']) ? mysqli_real_escape_string($koneksi, $_POST['subject']) : '';
    $pesan = isset($_POST['pesan']) ? mysqli_real_escape_string($koneksi, $_POST['pesan']) : '';

    // Validasi
    if (empty($subject) || empty($pesan)) {
        $status_proses = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Subject dan pesan tidak boleh kosong.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
    } else {
        // Simpan balasan ke database
        $query_insert = "INSERT INTO pesan_balasan (id, subject, pesan, email_tujuan, created_at) 
                         VALUES ('$id', '$subject', '$pesan', '$email_penerima', NOW())";

        if (mysqli_query($koneksi, $query_insert)) {
            // Tandai pesan original sebagai sudah dibalas
            mysqli_query($koneksi, "UPDATE pesan_kontak SET status='dibalas' WHERE id='$id'");

            // Include fungsi email dan kirim email
            include 'email_balas_pesan.php';
            $email_berhasil = kirimEmailBalasan($email_penerima, $nama_pengirim, $subject, $pesan);

            if ($email_berhasil) {
                $status_proses = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>✅ Sukses!</strong> Balasan pesan telah disimpan dan email telah dikirim ke ' . htmlspecialchars($email_penerima) . '.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                  </div>';
            } else {
                $status_proses = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <strong>⚠️ Peringatan!</strong> Balasan pesan telah disimpan, namun gagal mengirim email ke ' . htmlspecialchars($email_penerima) . '.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                  </div>';
            }
        } else {
            $status_proses = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> Gagal menyimpan balasan: ' . mysqli_error($koneksi) . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              </div>';
        }
    }
}

// Cek apakah sudah ada balasan sebelumnya
$query_balasan = mysqli_query($koneksi, "SELECT * FROM pesan_balasan WHERE pesan_id='$id' ORDER BY created_at DESC");
$sudah_dibalas = mysqli_num_rows($query_balasan) > 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balas Pesan - Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="../images/icon/logo.png">

    <style>
        :root {
            --primary: #1e5dac;
            --bg: #f3eded;
            --white: #ffffff;
        }

        body {
            margin: 0;
            background: var(--bg);
            font-family: Poppins, system-ui, sans-serif;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100vh;
            background: linear-gradient(180deg, #1e63b6, #0f3f82);
            padding: 18px 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow-y: auto;
        }

        .logo-box {
            text-align: center;
            padding: 10px 0 18px;
        }

        .logo-box img {
            width: 72px;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, .25));
            transition: .3s ease;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 4px 12px;
            border-radius: 10px;
            transition: .25s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, .32);
            font-weight: 600;
        }

        .sidebar .logout {
            margin-top: auto;
            background: rgba(255, 80, 80, .15);
            color: #ffd6d6;
            font-weight: 600;
            text-align: center;
            border-radius: 14px;
            transition: .3s ease;
            margin-bottom: 12px;
        }

        .sidebar .logout:hover {
            background: #ff4d4d;
            color: #fff;
        }

        /* ===== CONTENT ===== */
        .content {
            margin-left: 220px;
            padding: 30px 40px;
            animation: fade .5s ease;
        }

        @keyframes fade {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 20px;
        }

        h3 {
            color: var(--primary);
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        /* ===== CARD ===== */
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            border: none;
        }

        .card-original {
            background: #f8f9fa;
            border-left: 4px solid var(--primary);
        }

        .info-pesan {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .info-value {
            color: #333;
            margin-top: 5px;
        }

        .pesan-text {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* ===== FORM ===== */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-family: Poppins, sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 200px;
        }

        /* ===== BUTTONS ===== */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 12px 24px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #1a4d8f);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 93, 172, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-back {
            background: #f0f0f0;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-back:hover {
            background: var(--primary);
            color: white;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        /* ===== RIWAYAT BALASAN ===== */
        .balasan-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
        }

        .balasan-header {
            font-weight: 600;
            color: #28a745;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .balasan-text {
            line-height: 1.6;
            white-space: pre-wrap;
            color: #333;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 20px 15px;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h2><i class="bi bi-reply"></i> Balas Pesan Kontak</h2>

        <?php echo $status_proses; ?>

        <!-- PESAN ORIGINAL -->
        <div class="card card-original">
            <h3><i class="bi bi-chat-left"></i> Pesan Original</h3>

            <div class="info-pesan">
                <div class="info-label">Dari:</div>
                <div class="info-value"><?= $nama_pengirim ?> (<?= $email_penerima ?>)</div>
            </div>

            <div class="info-pesan">
                <div class="info-label">Tanggal:</div>
                <div class="info-value"><?= $tanggal_asli ?></div>
            </div>

            <div class="info-pesan">
                <div class="info-label">Pesan:</div>
                <div class="pesan-text"><?= $pesan_asli ?></div>
            </div>
        </div>

        <!-- RIWAYAT BALASAN (Jika ada) -->
        <?php if ($sudah_dibalas): ?>
            <div class="card">
                <h3><i class="bi bi-chat-right"></i> Riwayat Balasan</h3>

                <?php while ($balasan = mysqli_fetch_assoc($query_balasan)): ?>
                    <div class="balasan-item">
                        <div class="balasan-header">
                            <i class="bi bi-check-circle"></i>
                            Balasan dari URBANHYPE - <?= date('d/m/Y H:i', strtotime($balasan['created_at'])) ?>
                        </div>
                        <strong style="color: #333;">Subject: <?= htmlspecialchars($balasan['subject']) ?></strong>
                        <div class="balasan-text" style="margin-top: 10px;">
                            <?= nl2br(htmlspecialchars($balasan['pesan'])) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <!-- FORM BALAS PESAN -->
        <div class="card">
            <h3><i class="bi bi-pencil"></i> Tulis Balasan Baru</h3>

            <form method="POST">
                <div class="form-group">
                    <label for="subject">Subject <span style="color: red;">*</span></label>
                    <input type="text" id="subject" name="subject" placeholder="Contoh: Balasan atas pertanyaan Anda" required>
                </div>

                <div class="form-group">
                    <label for="pesan">Pesan <span style="color: red;">*</span></label>
                    <textarea id="pesan" name="pesan" placeholder="Tulis balasan pesan di sini..." required></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Kirim Balasan
                    </button>
                    <a href="data_pesan.php" class="btn btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>