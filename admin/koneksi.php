<?php
// Data koneksi
$host = "localhost";
$username = "neoz6813_administrator-tb-k4";
$password = "administrator-online-store";
$database = "neoz6813_TB-K4-Online-Store";

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tutup koneksi
$conn->close();
