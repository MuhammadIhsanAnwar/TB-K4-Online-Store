<?php
session_start();
include "admin/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email  = mysqli_real_escape_string($koneksi, $_POST['email']);
    $subjek = mysqli_real_escape_string($koneksi, $_POST['subjek']);
    $pesan  = mysqli_real_escape_string($koneksi, $_POST['pesan']);

    $query = "INSERT INTO pesan_kontak (nama, email, subjek, pesan)
              VALUES ('$nama', '$email', '$subjek', '$pesan')";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['notif'] = 'success';
        $_SESSION['msg']   = 'Pesan berhasil dikirim. Terima kasih!';
    } else {
        $_SESSION['notif'] = 'error';
        $_SESSION['msg']   = 'Pesan gagal dikirim.';
    }

    header("Location: index.php#contact");
    exit;
}
?>