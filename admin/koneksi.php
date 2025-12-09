<?php
// Data koneksi
$host = "localhost";
$username = "neoz6813_administrator-tb-k4";
$password = "administrator-online-store";
$database = "neoz6813_TB-K4-Online-Store";

// Membuat koneksi
$koneksi = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
} else {
    echo "Koneksi berhasil ke database!";
}

// Tutup koneksi
$koneksi->close();
