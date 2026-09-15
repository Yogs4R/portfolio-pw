<?php

// Proses Logout - Praktikum 3 Pemrograman Web
// Menghapus session aktif pengguna

session_start();

// Mengosongkan seluruh variabel session
$_SESSION = [];

// Menghancurkan session
session_unset();
session_destroy();

// Alihkan kembali ke halaman login dengan parameter status
header("Location: login.php");
exit;
?>
