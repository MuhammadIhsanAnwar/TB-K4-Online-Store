<?php
session_start();  // HARUS PERTAMA
include "../../admin/koneksi.php";

// Ambil data user jika sudah login
$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($result);
}

// Ambil ID produk dari URL
if (!isset($_GET['id'])) {
    header("Location: shop.php");
    exit;
}

$product_id = intval($_GET['id']);

// Ambil data produk
$query = "SELECT * FROM products WHERE id='$product_id'";
$result = mysqli_query($koneksi, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: shop.php");
    exit;
}

$product = mysqli_fetch_assoc($result);

// PROSES TAMBAH KOMENTAR
if (isset($_POST['add_comment'])) {
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu']);
        exit;
    }

    $komentar = mysqli_real_escape_string($koneksi, $_POST['komentar']);
    $rating = intval($_POST['rating']);

    if (empty($komentar) || strlen($komentar) < 5) {
        echo json_encode(['status' => 'error', 'message' => 'Komentar minimal 5 karakter']);
        exit;
    }

    if ($rating < 1 || $rating > 5) {
        $rating = 5;
    }

    $insert_comment = "INSERT INTO komentar_produk (product_id, user_id, komentar, rating, created_at) 
                       VALUES ('$product_id', '$user_id', '$komentar', '$rating', NOW())";

    if (mysqli_query($koneksi, $insert_comment)) {
        echo json_encode(['status' => 'success', 'message' => 'Komentar berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan komentar']);
    }
    exit;
}

// PROSES EDIT KOMENTAR
if (isset($_POST['edit_comment'])) {
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu']);
        exit;
    }

    $comment_id = intval($_POST['comment_id']);
    $komentar = mysqli_real_escape_string($koneksi, $_POST['komentar']);
    $rating = intval($_POST['rating']);

    if (empty($komentar) || strlen($komentar) < 5) {
        echo json_encode(['status' => 'error', 'message' => 'Komentar minimal 5 karakter']);
        exit;
    }

    if ($rating < 1 || $rating > 5) {
        $rating = 5;
    }

    // Verifikasi bahwa komentar milik user yang sedang login
    $check_query = "SELECT user_id FROM komentar_produk WHERE id='$comment_id'";
    $check_result = mysqli_query($koneksi, $check_query);
    $comment_data = mysqli_fetch_assoc($check_result);

    if (!$comment_data || $comment_data['user_id'] != $user_id) {
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk mengedit komentar ini']);
        exit;
    }

    $update_query = "UPDATE komentar_produk SET komentar='$komentar', rating='$rating', updated_at=NOW() WHERE id='$comment_id'";

    if (mysqli_query($koneksi, $update_query)) {
        echo json_encode(['status' => 'success', 'message' => 'Komentar berhasil diperbarui']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui komentar']);
    }
    exit;
}

// PROSES DELETE KOMENTAR
if (isset($_POST['delete_comment'])) {
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu']);
        exit;
    }

    $comment_id = intval($_POST['comment_id']);

    // Verifikasi bahwa komentar milik user yang sedang login
    $check_query = "SELECT user_id FROM komentar_produk WHERE id='$comment_id'";
    $check_result = mysqli_query($koneksi, $check_query);
    $comment_data = mysqli_fetch_assoc($check_result);

    if (!$comment_data || $comment_data['user_id'] != $user_id) {
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini']);
        exit;
    }

    $delete_query = "DELETE FROM komentar_produk WHERE id='$comment_id'";

    if (mysqli_query($koneksi, $delete_query)) {
        echo json_encode(['status' => 'success', 'message' => 'Komentar berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus komentar']);
    }
    exit;
}

// PROSES TAMBAH KE KERANJANG
if (isset($_POST['add_to_cart'])) {
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu']);
        exit;
    }

    $quantity = intval($_POST['quantity']);

    if ($quantity <= 0 || $quantity > $product['stok']) {
        echo json_encode(['status' => 'error', 'message' => 'Jumlah produk tidak valid']);
        exit;
    }

    // Cek apakah produk sudah ada di keranjang
    $check_query = "SELECT * FROM keranjang WHERE user_id='$user_id' AND product_id='$product_id'";
    $check_result = mysqli_query($koneksi, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Update quantity
        $existing = mysqli_fetch_assoc($check_result);
        $new_quantity = $existing['quantity'] + $quantity;

        if ($new_quantity > $product['stok']) {
            echo json_encode(['status' => 'error', 'message' => 'Stok tidak mencukupi']);
            exit;
        }

        $update_query = "UPDATE keranjang SET quantity='$new_quantity' WHERE user_id='$user_id' AND product_id='$product_id'";
        mysqli_query($koneksi, $update_query);
    } else {
        // Insert ke keranjang
        $insert_query = "INSERT INTO keranjang (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
        mysqli_query($koneksi, $insert_query);
    }

    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang']);
    exit;
}

// Ambil komentar produk
$comments_query = "SELECT k.*, u.nama_lengkap, u.foto_profil FROM komentar_produk k 
                   JOIN akun_user u ON k.user_id = u.id 
                   WHERE k.product_id = '$product_id' 
                   ORDER BY k.created_at DESC";
$comments_result = mysqli_query($koneksi, $comments_query);
$comments = [];
while ($row = mysqli_fetch_assoc($comments_result)) {
    $comments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../../images/icon/logo.png">
    <title><?php echo htmlspecialchars($product['nama']); ?> - UrbanHype</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../icons/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css_user/css_produk_pembayaran/product_detail.css">

</head>

<body>
    <?php include "../../navbar.php"; ?>
    <!-- BREADCRUMB -->
    <section class="breadcrumb-section">
        <div class="container-detail">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                    <li class="breadcrumb-item"><a href="shop.php?kategori=<?php echo htmlspecialchars($product['kategori']); ?>"><?php echo htmlspecialchars($product['kategori']); ?></a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['nama']); ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- PRODUCT DETAIL -->
    <main class="container-detail">
        <div class="product-detail">
            <!-- IMAGE SECTION -->
            <div class="product-image-section">
                <img src="../../foto_produk/<?php echo htmlspecialchars($product['foto_produk']); ?>"
                    alt="<?php echo htmlspecialchars($product['nama']); ?>" class="product-image-main">
            </div>

            <!-- INFO SECTION -->
            <div class="product-info-section">
                <span class="product-kategori">📂 <?php echo htmlspecialchars($product['kategori']); ?></span>
                <h1 class="product-name"><?php echo htmlspecialchars($product['nama']); ?></h1>
                <p class="product-merk">Brand: <?php echo htmlspecialchars($product['merk']); ?></p>
                <div class="product-rating">⭐⭐⭐⭐⭐ (4.8/5) · 125 Ulasan</div>

                <!-- PRICE -->
                <div class="product-harga">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></div>

                <!-- STOK STATUS -->
                <div class="stok-status <?php echo $product['stok'] > 10 ? 'stok-ready' : ($product['stok'] > 0 ? 'stok-low' : 'stok-out'); ?>">
                    <?php
                    if ($product['stok'] > 10) {
                        echo "✓ Stok Tersedia (" . $product['stok'] . ")";
                    } elseif ($product['stok'] > 0) {
                        echo "⚠ Stok Terbatas (" . $product['stok'] . ")";
                    } else {
                        echo "✗ Stok Habis";
                    }
                    ?>
                </div>

                <!-- DESCRIPTION -->
                <p class="product-description"><?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?></p>

                <!-- SPECIFICATIONS -->
                <div class="product-specs">
                    <div class="spec-item">
                        <span class="spec-label">SKU</span>
                        <span class="spec-value">PROD-<?php echo str_pad($product['id'], 5, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Kategori</span>
                        <span class="spec-value"><?php echo htmlspecialchars($product['kategori']); ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Merek</span>
                        <span class="spec-value"><?php echo htmlspecialchars($product['merk']); ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Ketersediaan</span>
                        <span class="spec-value">
                            <?php
                            if ($product['stok'] > 0) {
                                echo $product['stok'] . " Item Tersedia";
                            } else {
                                echo "Sedang Tidak Tersedia";
                            }
                            ?>
                        </span>
                    </div>
                </div>

                <!-- ADD TO CART -->
                <?php if ($product['stok'] > 0): ?>
                    <div class="add-to-cart-section">
                        <div class="quantity-selector">
                            <button class="quantity-btn" id="decreaseBtn" onclick="decreaseQuantity()">−</button>
                            <input type="number" id="quantityInput" class="quantity-input" value="1" min="1" max="<?php echo $product['stok']; ?>">
                            <button class="quantity-btn" id="increaseBtn" onclick="increaseQuantity()">+</button>
                        </div>
                        <button class="btn-add-cart" id="addCartBtn" onclick="addToCart()">
                            🛒 Tambah ke Keranjang
                        </button>
                    </div>
                <?php else: ?>
                    <button class="btn-add-cart" disabled style="opacity: 0.5;">
                        Stok Habis
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- COMMENTS SECTION -->
        <section class="comments-section">
            <h3>💬 Komentar Produk</h3>

            <?php if ($user): ?>
                <div class="comment-form">
                    <h4 style="color: var(--primary); margin-bottom: 1.5rem; font-weight: 600;">Berikan Komentar Anda</h4>

                    <form id="commentForm">
                        <div class="rating-input">
                            <label>Rating:</label>
                            <div id="ratingStars">
                                <span class="star" data-rating="1">★</span>
                                <span class="star" data-rating="2">★</span>
                                <span class="star" data-rating="3">★</span>
                                <span class="star" data-rating="4">★</span>
                                <span class="star" data-rating="5">★</span>
                            </div>
                            <input type="hidden" id="ratingValue" name="rating" value="5">
                        </div>

                        <textarea name="komentar" id="komentarText" placeholder="Tulis komentar Anda di sini... (minimal 5 karakter)" required></textarea>

                        <button type="submit" class="btn-submit-comment">Kirim Komentar</button>
                    </form>
                </div>
            <?php else: ?>
                <div style="background: linear-gradient(135deg, var(--light) 0%, rgba(183, 197, 218, 0.2) 100%); padding: 2rem; border-radius: 12px; text-align: center;">
                    <p style="color: var(--dark); margin-bottom: 1rem;">Silakan <a href="../../user/login_user.php" style="color: var(--primary); font-weight: 600;">login</a> untuk memberikan komentar.</p>
                </div>
            <?php endif; ?>

            <div class="comments-list" style="margin-top: 2rem;">
                <?php if (empty($comments)): ?>
                    <div class="no-comments">
                        <div class="no-comments-icon">📝</div>
                        <p>Belum ada komentar. Jadilah yang pertama memberikan komentar!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item" id="comment-<?php echo $comment['id']; ?>">
                            <div class="comment-header">
                                <div class="comment-user-section">
                                    <img src="<?php echo !empty($comment['foto_profil']) ? '../../foto_profil/' . htmlspecialchars($comment['foto_profil']) : 'https://via.placeholder.com/40'; ?>" alt="<?php echo htmlspecialchars($comment['nama_lengkap']); ?>" class="comment-avatar">
                                    <div class="comment-user-info">
                                        <div class="comment-user-name"><?php echo htmlspecialchars($comment['nama_lengkap']); ?></div>
                                        <div class="comment-date"><?php echo date('d M Y H:i', strtotime($comment['created_at'])); ?></div>
                                    </div>
                                </div>
                                <?php if ($user && $user['id'] == $comment['user_id']): ?>
                                    <div class="comment-actions">
                                        <button class="btn-edit-comment" onclick="showEditForm(<?php echo $comment['id']; ?>)" title="Edit Komentar">
                                            ✏️
                                        </button>
                                        <button class="btn-delete-comment" onclick="deleteComment(<?php echo $comment['id']; ?>)" title="Hapus Komentar">
                                            🗑️
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="comment-rating">
                                <?php for ($i = 0; $i < $comment['rating']; $i++): ?>
                                    <span class="star-display">★</span>
                                <?php endfor; ?>
                            </div>

                            <div class="comment-text" id="comment-text-<?php echo $comment['id']; ?>">
                                <?php echo htmlspecialchars($comment['komentar']); ?>
                            </div>

                            <?php if (!empty($comment['updated_at']) && $comment['updated_at'] != $comment['created_at']): ?>
                                <div class="comment-edited">
                                    Diubah pada <?php echo date('d M Y H:i', strtotime($comment['updated_at'])); ?>
                                </div>
                            <?php endif; ?>

                            <!-- EDIT FORM -->
                            <?php if ($user && $user['id'] == $comment['user_id']): ?>
                                <div class="edit-comment-form" id="edit-form-<?php echo $comment['id']; ?>">
                                    <div class="rating-input" style="margin-bottom: 1rem;">
                                        <label>Rating:</label>
                                        <div id="editRatingStars-<?php echo $comment['id']; ?>" class="edit-rating-stars">
                                            <span class="star" data-rating="1" data-comment-id="<?php echo $comment['id']; ?>">★</span>
                                            <span class="star" data-rating="2" data-comment-id="<?php echo $comment['id']; ?>">★</span>
                                            <span class="star" data-rating="3" data-comment-id="<?php echo $comment['id']; ?>">★</span>
                                            <span class="star" data-rating="4" data-comment-id="<?php echo $comment['id']; ?>">★</span>
                                            <span class="star" data-rating="5" data-comment-id="<?php echo $comment['id']; ?>">★</span>
                                        </div>
                                        <input type="hidden" class="edit-rating-value" data-comment-id="<?php echo $comment['id']; ?>" value="<?php echo $comment['rating']; ?>">
                                    </div>
                                    <textarea class="edit-komentar-text" data-comment-id="<?php echo $comment['id']; ?>"><?php echo htmlspecialchars($comment['komentar']); ?></textarea>
                                    <div class="edit-form-actions">
                                        <button class="btn-save-edit" onclick="saveEditComment(<?php echo $comment['id']; ?>)">Simpan Perubahan</button>
                                        <button class="btn-cancel-edit" onclick="cancelEditForm(<?php echo $comment['id']; ?>)">Batal</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container-detail">
            <p>&copy; 2025 UrbanHype. Kelompok 4 Tugas Besar Pemrograman Web. All rights reserved.</p>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="../../js/bootstrap.bundle.js"></script>

    <script>
        const maxStok = <?php echo $product['stok']; ?>;

        function decreaseQuantity() {
            const input = document.getElementById('quantityInput');
            if (input.value > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function increaseQuantity() {
            const input = document.getElementById('quantityInput');
            if (parseInt(input.value) < maxStok) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function addToCart() {
            <?php if (!$user): ?>
                Swal.fire({
                    icon: 'warning',
                    title: 'Anda Harus Login',
                    text: 'Silakan login terlebih dahulu untuk menambahkan produk ke keranjang',
                    confirmButtonText: 'Login',
                    confirmButtonColor: '#1E5DAC'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '../../user/login_user.php';
                    }
                });
                return;
            <?php endif; ?>

            const quantity = parseInt(document.getElementById('quantityInput').value);
            const addBtn = document.getElementById('addCartBtn');

            addBtn.disabled = true;
            addBtn.textContent = '⏳ Menambahkan...';

            const formData = new FormData();
            formData.append('add_to_cart', '1');
            formData.append('quantity', quantity);

            fetch('product_detail.php?id=<?php echo $product['id']; ?>', {
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
                        });
                        document.getElementById('quantityInput').value = 1;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message,
                            confirmButtonColor: '#1E5DAC'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada sistem',
                        confirmButtonColor: '#1E5DAC'
                    });
                })
                .finally(() => {
                    addBtn.disabled = false;
                    addBtn.textContent = '🛒 Tambah ke Keranjang';
                });
        }

        // ===== RATING STARS FUNCTIONALITY =====
        const ratingStars = document.querySelectorAll('#ratingStars .star');
        const ratingValue = document.getElementById('ratingValue');

        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingValue.value = rating;

                ratingStars.forEach(s => {
                    if (s.dataset.rating <= rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });

            star.addEventListener('mouseover', function() {
                const rating = this.dataset.rating;
                ratingStars.forEach(s => {
                    if (s.dataset.rating <= rating) {
                        s.style.color = '#ffc107';
                        s.style.transform = 'scale(1.2)';
                    } else {
                        s.style.color = '#ddd';
                        s.style.transform = 'scale(1)';
                    }
                });
            });
        });

        document.getElementById('ratingStars').addEventListener('mouseout', function() {
            ratingStars.forEach(star => {
                const rating = ratingValue.value;
                if (star.dataset.rating <= rating) {
                    star.style.color = '#ffc107';
                    star.style.transform = 'scale(1.2)';
                } else {
                    star.style.color = '#ddd';
                    star.style.transform = 'scale(1)';
                }
            });
        });

        // ===== COMMENT FORM SUBMISSION =====
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const komentar = document.getElementById('komentarText').value.trim();
            const rating = document.getElementById('ratingValue').value;

            if (komentar.length < 5) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Komentar Terlalu Pendek',
                    text: 'Komentar minimal harus 5 karakter',
                    confirmButtonColor: '#1E5DAC'
                });
                return;
            }

            if (!rating || rating < 1 || rating > 5) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Rating Belum Dipilih',
                    text: 'Silakan pilih rating terlebih dahulu',
                    confirmButtonColor: '#1E5DAC'
                });
                return;
            }

            const formData = new FormData();
            formData.append('add_comment', '1');
            formData.append('komentar', komentar);
            formData.append('rating', rating);

            fetch('product_detail.php?id=<?php echo $product['id']; ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Komentar Anda telah ditambahkan',
                            confirmButtonColor: '#1E5DAC'
                        }).then(() => {
                            document.getElementById('commentForm').reset();
                            document.getElementById('ratingValue').value = 5;

                            ratingStars.forEach(star => {
                                if (star.dataset.rating <= 5) {
                                    star.classList.add('active');
                                } else {
                                    star.classList.remove('active');
                                }
                            });

                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message,
                            confirmButtonColor: '#1E5DAC'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada sistem',
                        confirmButtonColor: '#1E5DAC'
                    });
                });
        });

        // ===== EDIT COMMENT FUNCTIONALITY =====
        function showEditForm(commentId) {
            const editForm = document.getElementById(`edit-form-${commentId}`);
            const commentText = document.getElementById(`comment-text-${commentId}`);
            editForm.style.display = 'block';
            commentText.style.display = 'none';

            // Initialize rating stars untuk edit form
            const editRatingStars = document.querySelectorAll(`#editRatingStars-${commentId} .star`);
            const editRatingValue = document.querySelector(`.edit-rating-value[data-comment-id="${commentId}"]`);
            const currentRating = editRatingValue.value;

            editRatingStars.forEach(star => {
                if (star.dataset.rating <= currentRating) {
                    star.classList.add('active');
                }

                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    editRatingValue.value = rating;

                    editRatingStars.forEach(s => {
                        if (s.dataset.rating <= rating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });

                star.addEventListener('mouseover', function() {
                    const rating = this.dataset.rating;
                    editRatingStars.forEach(s => {
                        if (s.dataset.rating <= rating) {
                            s.style.color = '#ffc107';
                            s.style.transform = 'scale(1.2)';
                        } else {
                            s.style.color = '#ddd';
                            s.style.transform = 'scale(1)';
                        }
                    });
                });
            });

            document.getElementById(`editRatingStars-${commentId}`).addEventListener('mouseout', function() {
                editRatingStars.forEach(star => {
                    const rating = editRatingValue.value;
                    if (star.dataset.rating <= rating) {
                        star.style.color = '#ffc107';
                        star.style.transform = 'scale(1.2)';
                    } else {
                        star.style.color = '#ddd';
                        star.style.transform = 'scale(1)';
                    }
                });
            });
        }

        function cancelEditForm(commentId) {
            const editForm = document.getElementById(`edit-form-${commentId}`);
            const commentText = document.getElementById(`comment-text-${commentId}`);
            editForm.style.display = 'none';
            commentText.style.display = 'block';
        }

        function saveEditComment(commentId) {
            const editForm = document.getElementById(`edit-form-${commentId}`);
            const komentarText = document.querySelector(`.edit-komentar-text[data-comment-id="${commentId}"]`).value.trim();
            const ratingValue = document.querySelector(`.edit-rating-value[data-comment-id="${commentId}"]`).value;

            if (komentarText.length < 5) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Komentar Terlalu Pendek',
                    text: 'Komentar minimal harus 5 karakter',
                    confirmButtonColor: '#1E5DAC'
                });
                return;
            }

            if (!ratingValue || ratingValue < 1 || ratingValue > 5) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Rating Belum Dipilih',
                    text: 'Silakan pilih rating terlebih dahulu',
                    confirmButtonColor: '#1E5DAC'
                });
                return;
            }

            const formData = new FormData();
            formData.append('edit_comment', '1');
            formData.append('comment_id', commentId);
            formData.append('komentar', komentarText);
            formData.append('rating', ratingValue);

            fetch('product_detail.php?id=<?php echo $product['id']; ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Komentar Anda telah diperbarui',
                            confirmButtonColor: '#1E5DAC'
                        }).then(() => {
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message,
                            confirmButtonColor: '#1E5DAC'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada sistem',
                        confirmButtonColor: '#1E5DAC'
                    });
                });
        }

        function deleteComment(commentId) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Komentar?',
                text: 'Apakah Anda yakin ingin menghapus komentar ini? Tindakan ini tidak dapat dibatalkan.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#d1d5db'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('delete_comment', '1');
                    formData.append('comment_id', commentId);

                    fetch('product_detail.php?id=<?php echo $product['id']; ?>', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Komentar Anda telah dihapus',
                                    confirmButtonColor: '#1E5DAC'
                                }).then(() => {
                                    document.getElementById(`comment-${commentId}`).remove();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message,
                                    confirmButtonColor: '#1E5DAC'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan pada sistem',
                                confirmButtonColor: '#1E5DAC'
                            });
                        });
                }
            });
        }

        // Initialize rating stars with default value (5)
        window.addEventListener('load', function() {
            const defaultRating = 5;
            ratingStars.forEach(star => {
                if (star.dataset.rating <= defaultRating) {
                    star.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>