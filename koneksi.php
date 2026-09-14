<?php
// ==========================================================================
// Koneksi Database Sederhana dengan mysqli
// Praktikum 3 - Pemrograman Web
// ==========================================================================

$host = "localhost";
$user = "root";
$pass = "";
$db   = "portfolio_db";

// Membuka koneksi ke MySQL menggunakan mysqli_connect
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
