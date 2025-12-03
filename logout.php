
<?php
// Mulai session untuk mengakses data sesi
session_start();

// Hancurkan semua data session
session_unset();
session_destroy();

// Arahkan pengguna ke halaman login
header('Location: ../login.php');
exit(); // Penting: hentikan eksekusi script setelah redirect
?>