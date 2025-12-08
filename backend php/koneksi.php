<?php
// Data koneksi
$host = "localhost"; // biasanya localhost untuk cPanel
$username = "neoz6813_administrator-tb-k4";
$password = "administrator-online-store";
$database = "neoz6813_TB-K4-Online-Store";

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
} else {
    echo "Koneksi berhasil ke database!";
}

// Tutup koneksi
$conn->close();
