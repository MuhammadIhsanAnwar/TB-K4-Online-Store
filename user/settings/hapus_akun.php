<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
        $user_id = $_SESSION['user_id'];

        // Database connection
        include '../../admin/koneksi.php';

        // Delete user account
        $sql = "DELETE FROM akun_user WHERE id = '$user_id'";

        if (mysqli_query($conn, $sql)) {
            session_destroy();
            header("Location: ../index.php?status=deleted");
            exit();
        } else {
            $error = "Error: " . mysqli_error($conn);
        }

        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Akun</title>
    <link rel="icon" type="image/png" href="../images/Background dan Logo/logo.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 400px;
            text-align: center;
        }

        .icon-warning {
            font-size: 50px;
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
        }

        p {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        button {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cancel {
            background: #e0e0e0;
            color: #333;
        }

        .btn-cancel:hover {
            background: #d0d0d0;
        }

        .btn-delete {
            background: #ff6b6b;
            color: white;
        }

        .btn-delete:hover {
            background: #ee5a52;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }
    </style>
</head>

<body>
    <div class=" container">
        <div class="icon-warning">⚠️</div>
        <h2>Hapus Akun Anda?</h2>
        <p>Tindakan ini tidak dapat dibatalkan. Semua data akun Anda akan dihapus secara permanen.</p>

        <div class="button-group">
            <button class="btn-cancel" onclick="window.history.back()">Batal</button>
            <form method="POST" style="margin: 0;">
                <input type="hidden" name="confirm_delete" value="yes">
                <button type="submit" class="btn-delete">Hapus Akun</button>
            </form>
        </div>
    </div>
</body>

</html>