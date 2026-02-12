<?php
$host = 'localhost';
$dbname = 'qris_system';  // Ganti dengan nama database Anda
$username = 'root';       // Ganti dengan username MySQL Anda
$password = '';           // Ganti dengan password MySQL Anda

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
