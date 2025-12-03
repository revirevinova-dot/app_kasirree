<?php
// Izinkan akses dari berbagai sumber (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Sertakan file koneksi database
require_once '../config/db.php';

// Mulai session
session_start();

// Handle preflight request untuk CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ambil data JSON dari request
 $data = json_decode(file_get_contents("php://input"));

// Cek apakah data username dan password ada
if (!isset($data->username) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username dan password harus diisi.']);
    exit();
}

 $username = $data->username;
 $password = $data->password;

try {
    // Cari user di database berdasarkan username
    $stmt = $pdo->prepare("SELECT UserID, Username, Password, NamaLengkap, Role FROM kasir_user WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifikasi user
    if ($user && password_verify($password, $user['Password'])) {
        // Jika password benar, buat session
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['nama_lengkap'] = $user['NamaLengkap'];
        $_SESSION['role'] = $user['Role'];

        echo json_encode(['success' => true, 'message' => 'Login berhasil.']);
    } else {
        // Jika user tidak ditemukan atau password salah
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Username atau password salah.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error di database: ' . $e->getMessage()]);
}
?>