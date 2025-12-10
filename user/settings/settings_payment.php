<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metode Pembayaran</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h3>Metode Pembayaran</h3>
    <hr>

    <form action="update_payment.php" method="POST">

        <div class="mb-3">
            <label>Bank / E-Wallet</label>
            <input type="text" name="pembayaran" class="form-control"
                placeholder="Contoh: BRI 1234xxxx, Dana 08xxxx"
                value="<?php echo $user['pembayaran'] ?? ''; ?>">
        </div>

        <button class="btn btn-warning">Simpan Metode Pembayaran</button>
    </form>
</body>

</html>