<?php
session_start();
require 'auth_check.php'; 
include 'koneksi.php'; 

// Proses hapus komentar
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
// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Proses hapus komentar
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

// Proses approve komentar (update status)
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

// Ambil semua komentar dengan info produk dan user
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
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .comment-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #1E5DAC;
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .comment-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #1E5DAC;
            margin-right: 1rem;
        }

        .comment-user-info {
            flex: 1;
        }

        .comment-user-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.3rem;
        }

        .comment-product {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.3rem;
        }

        .comment-date {
            font-size: 0.85rem;
            color: #999;
        }

        .comment-rating {
            display: flex;
            gap: 0.2rem;
            margin: 0.5rem 0;
        }

        .star {
            color: #ffc107;
            font-size: 0.9rem;
        }

        .comment-text {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            line-height: 1.6;
            color: #333;
        }

        .comment-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-approve:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 1rem;
        }

        .status-approved {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .filter-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border: 2px solid #1E5DAC;
            background: white;
            color: #1E5DAC;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: #1E5DAC;
            color: white;
        }

        .filter-btn:hover {
            background: #1E5DAC;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 12px;
            color: #999;
        }

        @media (max-width: 768px) {
            .comment-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .comment-avatar {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }

            .comment-actions {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="container-fluid">
                <h1 class="page-title">💬 Kelola Komentar</h1>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-title">Filter Status:</div>
                    <div class="filter-buttons">
                        <button class="filter-btn active" onclick="filterByStatus('semua')">Semua</button>
                        <button class="filter-btn" onclick="filterByStatus('pending')">Menunggu Review</button>
                        <button class="filter-btn" onclick="filterByStatus('approved')">Disetujui</button>
                    </div>
                </div>

                <!-- Comments List -->
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
                                        <div class="comment-product">Produk: <strong><?php echo htmlspecialchars($comment['nama_produk']); ?></strong></div>
                                        <div class="comment-date">Tanggal: <?php echo date('d M Y H:i', strtotime($comment['created_at'])); ?></div>
                                    </div>
                                    <span class="status-badge <?php echo !empty($comment['status']) && $comment['status'] === 'approved' ? 'status-approved' : 'status-pending'; ?>">
                                        <?php echo !empty($comment['status']) && $comment['status'] === 'approved' ? '✓ Disetujui' : '⏳ Menunggu'; ?>
                                    </span>
                                </div>

                                <div class="comment-rating">
                                    <?php for ($i = 0; $i < $comment['rating']; $i++): ?>
                                        <span class="star">★</span>
                                    <?php endfor; ?>
                                    <span style="color: #999; font-size: 0.9rem; margin-left: 0.5rem;"><?php echo $comment['rating']; ?>/5</span>
                                </div>

                                <div class="comment-text">
                                    <?php echo nl2br(htmlspecialchars($comment['komentar'])); ?>
                                </div>

                                <div class="comment-actions">
                                    <?php if (empty($comment['status']) || $comment['status'] !== 'approved'): ?>
                                        <button class="btn-action btn-approve" onclick="approveComment(<?php echo $comment['id']; ?>)">
                                            ✓ Setujui
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn-action btn-delete" onclick="deleteComment(<?php echo $comment['id']; ?>)">
                                        🗑️ Hapus
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
        function approveComment(commentId) {
            Swal.fire({
                icon: 'question',
                title: 'Setujui Komentar?',
                text: 'Komentar akan ditampilkan di halaman produk',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1E5DAC'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('approve_comment', '1');
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
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        });
                }
            });
        }

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
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        });
                }
            });
        }

        function filterByStatus(status) {
            const cards = document.querySelectorAll('.comment-card');
            const buttons = document.querySelectorAll('.filter-btn');

            // Update button active state
            buttons.forEach(btn => {
                btn.classList.remove('active');
                if ((status === 'semua' && btn.textContent.includes('Semua')) ||
                    (status === 'pending' && btn.textContent.includes('Menunggu')) ||
                    (status === 'approved' && btn.textContent.includes('Disetujui'))) {
                    btn.classList.add('active');
                }
            });

            // Filter cards
            cards.forEach(card => {
                const cardStatus = card.dataset.status;
                if (status === 'semua') {
                    card.style.display = 'block';
                } else if (status === 'pending' && (cardStatus === 'pending' || cardStatus === '')) {
                    card.style.display = 'block';
                } else if (status === 'approved' && cardStatus === 'approved') {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>