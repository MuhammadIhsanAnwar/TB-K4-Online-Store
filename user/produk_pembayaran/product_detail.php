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

    <style>
        :root {
            --primary: #1E5DAC;
            --secondary: #B7C5DA;
            --accent: #E8D3C1;
            --light: #EAE2E4;
            --dark: #2d3748;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(30, 93, 172, 0.1);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--light) 0%, #f5f5f5 100%);
            color: var(--dark);
        }

        /* BREADCRUMB */
        .breadcrumb-section {
            background: white;
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--light);
            margin-bottom: 2rem;
            margin-top: 80px;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        /* CONTAINER */
        .container-detail {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* PRODUCT DETAIL */
        .product-detail {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 3rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }

        .product-image-section {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-image-main {
            width: 100%;
            max-width: 400px;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            border: 2px solid var(--light);
        }

        .product-info-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product-kategori {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 1rem;
            width: fit-content;
            letter-spacing: 0.5px;
        }

        .product-name {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .product-merk {
            font-size: 1.1rem;
            color: var(--secondary);
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .product-harga {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .product-rating {
            color: #fbbf24;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .product-description {
            color: #6b7280;
            line-height: 1.8;
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        .product-specs {
            background: var(--light);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .spec-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(30, 93, 172, 0.1);
        }

        .spec-item:last-child {
            border-bottom: none;
        }

        .spec-label {
            font-weight: 600;
            color: var(--primary);
        }

        .spec-value {
            color: var(--dark);
        }

        /* QUANTITY & ADD TO CART */
        .add-to-cart-section {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 2rem;
        }

        .quantity-selector {
            display: flex;
            border: 2px solid var(--secondary);
            border-radius: 8px;
            overflow: hidden;
        }

        .quantity-btn {
            width: 45px;
            height: 45px;
            border: none;
            background: white;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .quantity-btn:hover {
            background: var(--light);
        }

        .quantity-input {
            width: 60px;
            border: none;
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
            color: var(--primary);
        }

        .quantity-input:focus {
            outline: none;
        }

        .btn-add-cart {
            flex: 1;
            padding: 15px 30px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
        }

        .btn-add-cart:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 93, 172, 0.4);
        }

        .btn-add-cart:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .stok-status {
            padding: 12px 16px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
        }

        .stok-ready {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .stok-low {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .stok-out {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        /* COMMENTS SECTION */
        .comments-section {
            margin-top: 4rem;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
        }

        .comments-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 2rem;
            font-weight: 700;
        }

        .comment-form {
            background: linear-gradient(135deg, var(--light) 0%, rgba(183, 197, 218, 0.2) 100%);
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .comment-form textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--secondary);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            resize: vertical;
            min-height: 100px;
            transition: all 0.3s ease;
        }

        .comment-form textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.1);
        }

        .rating-input {
            margin: 1rem 0;
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .rating-input label {
            font-weight: 600;
            color: var(--primary);
            margin-right: 1rem;
        }

        .star {
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #ddd;
        }

        .star.active {
            color: #ffc107;
            transform: scale(1.2);
        }

        .star:hover {
            color: #ffc107;
            transform: scale(1.2);
        }

        .btn-submit-comment {
            background: linear-gradient(135deg, var(--primary) 0%, #164a8a 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-submit-comment:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
        }

        .comments-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .comment-item {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid var(--primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
        }

        .comment-item:hover {
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.1);
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.8rem;
            justify-content: space-between;
        }

        .comment-user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--secondary);
        }

        .comment-user-info {
            flex: 1;
        }

        .comment-user-name {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.95rem;
        }

        .comment-date {
            font-size: 0.85rem;
            color: #999;
        }

        .comment-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit-comment,
        .btn-delete-comment {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-edit-comment {
            color: #3b82f6;
        }

        .btn-edit-comment:hover {
            background: rgba(59, 130, 246, 0.1);
        }

        .btn-delete-comment {
            color: #ef4444;
        }

        .btn-delete-comment:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .comment-rating {
            display: flex;
            gap: 0.2rem;
            margin-bottom: 0.8rem;
        }

        .comment-rating .star-display {
            color: #ffc107;
            font-size: 1rem;
        }

        .comment-text {
            color: var(--dark);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .comment-edited {
            font-size: 0.8rem;
            color: #999;
            margin-top: 0.5rem;
            font-style: italic;
        }

        .no-comments {
            text-align: center;
            padding: 2rem;
            color: #999;
        }

        .no-comments-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        /* EDIT FORM MODAL */
        .edit-comment-form {
            display: none;
            background: linear-gradient(135deg, var(--light) 0%, rgba(183, 197, 218, 0.2) 100%);
            padding: 1.5rem;
            border-radius: 12px;
            margin-top: 1rem;
        }

        .edit-comment-form textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--secondary);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            resize: vertical;
            min-height: 80px;
            transition: all 0.3s ease;
        }

        .edit-comment-form textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 93, 172, 0.1);
        }

        .edit-form-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .btn-save-edit,
        .btn-cancel-edit {
            flex: 1;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-save-edit {
            background: linear-gradient(135deg, var(--primary) 0%, #164a8a 100%);
            color: white;
        }

        .btn-save-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 93, 172, 0.3);
        }

        .btn-cancel-edit {
            background: #e5e7eb;
            color: var(--dark);
        }

        .btn-cancel-edit:hover {
            background: #d1d5db;
        }

        /* FOOTER */
        .footer {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2rem 0;
            text-align: center;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .breadcrumb-section {
                margin-top: 70px;
            }

            .product-detail {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 1.5rem;
            }

            .product-name {
                font-size: 1.8rem;
            }

            .product-harga {
                font-size: 1.5rem;
            }

            .product-image-main {
                max-width: 100%;
            }

            .add-to-cart-section {
                flex-direction: column;
            }

            .btn-add-cart {
                width: 100%;
            }

            .comment-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .comment-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
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