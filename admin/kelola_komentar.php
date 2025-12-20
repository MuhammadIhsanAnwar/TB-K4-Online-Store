<?php
require 'auth_check.php';
include 'koneksi.php';

if (isset($_POST['delete_comment'])) {
    $comment_id = intval($_POST['comment_id']);

    $delete_query = "DELETE FROM komentar_produk WHERE id='$comment_id'";
    if (mysqli_query($koneksi, $delete_query)) {
        echo json_encode(['status' => 'success', 'message' => 'Komentar berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus komentar']);
    }
    exit;
}

if (isset($_POST['approve_comment'])) {
    $comment_id = intval($_POST['comment_id']);

    $update_query = "UPDATE komentar_produk SET status='approved' WHERE id='$comment_id'";
    if (mysqli_query($koneksi, $update_query)) {
        echo json_encode(['status' => 'success', 'message' => 'Komentar disetujui']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyetujui komentar']);
    }
    exit;
}

$query = "SELECT k.*, p.nama as nama_produk, u.nama_lengkap, u.foto_profil
          FROM komentar_produk k
          JOIN products p ON k.product_id = p.id
          JOIN akun_user u ON k.user_id = u.id
          ORDER BY k.created_at DESC";
$result = mysqli_query($koneksi, $query);
$komentar = [];
while ($row = mysqli_fetch_assoc($result)) {
    $komentar[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Komentar - Admin</title>
    <link rel="icon" type="image/png" href="../images/icon/logo.png">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css_admin/kelola_komentar_style.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="container-fluid">
                <h1 class="page-title">
                    <i class="bi bi-chat-dots"></i> Kelola Komentar
                </h1>

                <div class="filter-section">
                    <div class="filter-title">Filter Status:</div>
                    <div class="filter-buttons">
                        <button class="filter-btn active" onclick="filterByStatus('semua')">Semua</button>
                        <button class="filter-btn" onclick="filterByStatus('pending')">Menunggu Review</button>
                        <button class="filter-btn" onclick="filterByStatus('approved')">Disetujui</button>
                    </div>
                </div>

                <div id="commentsList">
                    <?php if (empty($komentar)): ?>
                        <div class="empty-state">
                            <h3>Tidak ada komentar</h3>
                            <p>Belum ada komentar dari pelanggan.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($komentar as $comment): ?>
                            <div class="comment-card" data-status="<?php echo !empty($comment['status']) ? htmlspecialchars($comment['status']) : 'pending'; ?>">
                                <div class="comment-header">
                                    <img src="../foto_profil/<?php echo !empty($comment['foto_profil']) ? htmlspecialchars($comment['foto_profil']) : 'default.jpg'; ?>"
                                        alt="<?php echo htmlspecialchars($comment['nama_lengkap']); ?>"
                                        class="comment-avatar"
                                        onerror="this.src='https://via.placeholder.com/50'">

                                    <div class="comment-user-info">
                                        <div class="comment-user-name"><?php echo htmlspecialchars($comment['nama_lengkap']); ?></div>
                                        <div class="comment-product">
                                            Produk: <strong><?php echo htmlspecialchars($comment['nama_produk']); ?></strong>
                                        </div>
                                        <div class="comment-date">
                                            <i class="bi bi-calendar-event"></i>
                                            <?php echo date('d M Y H:i', strtotime($comment['created_at'])); ?>
                                        </div>
                                    </div>

                                    <span class="status-badge <?php echo !empty($comment['status']) && $comment['status'] === 'approved' ? 'status-approved' : 'status-pending'; ?>">
                                        <?php if (!empty($comment['status']) && $comment['status'] === 'approved'): ?>
                                            <i class="bi bi-check-circle"></i> Disetujui
                                        <?php else: ?>
                                            <i class="bi bi-hourglass-split"></i> Menunggu
                                        <?php endif; ?>
                                    </span>
                                </div>

                                <div class="comment-rating">
                                    <?php for ($i = 0; $i < $comment['rating']; $i++): ?>
                                        <i class="bi bi-star-fill star"></i>
                                    <?php endfor; ?>
                                    <span style="color:#999;font-size:.9rem;margin-left:.5rem;">
                                        <?php echo $comment['rating']; ?>/5
                                    </span>
                                </div>

                                <div class="comment-text">
                                    <?php echo nl2br(htmlspecialchars($comment['komentar'])); ?>
                                </div>

                                <div class="comment-actions">
                                    <button class="btn-action btn-delete" onclick="deleteComment(<?php echo $comment['id']; ?>)">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        

        function deleteComment(commentId) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Komentar?',
                text: 'Tindakan ini tidak dapat dibatalkan',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('delete_comment', '1');
                    formData.append('comment_id', commentId);

                    fetch('kelola_komentar.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonColor: '#1E5DAC'
                            }).then(() => location.reload());
                        }
                    });
                }
            });
        }

        function filterByStatus(status) {
            const cards = document.querySelectorAll('.comment-card');
            const buttons = document.querySelectorAll('.filter-btn');

            buttons.forEach(btn => btn.classList.remove('active'));

            buttons.forEach(btn => {
                if (
                    (status === 'semua' && btn.textContent.includes('Semua')) ||
                    (status === 'pending' && btn.textContent.includes('Menunggu')) ||
                    (status === 'approved' && btn.textContent.includes('Disetujui'))
                ) {
                    btn.classList.add('active');
                }
            });

            cards.forEach(card => {
                const cardStatus = card.dataset.status;
                if (
                    status === 'semua' ||
                    (status === 'pending' && (cardStatus === 'pending' || cardStatus === '')) ||
                    (status === 'approved' && cardStatus === 'approved')
                ) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
