<?php
$host = "localhost";
$username = "neoz6813_administrator-tb-k4";
$password = "administrator-online-store";
$database = "neoz6813_TB-K4-Online-Store";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
