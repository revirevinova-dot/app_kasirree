<?php
 $host = 'localhost';
 $user = 'root';     // Ganti dengan username MySQL Anda
 $pass = '';         // Ganti dengan password MySQL Anda
 $db   = 'db_kasir'; // Ganti dengan nama database Anda

try {
    // Buat koneksi PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Set mode error PDO ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
    die("Koneksi database gagal: " . $e->getMessage());
}
?>