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
    <link rel="stylesheet" href="css_admin/balas_pesan_style.css">
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