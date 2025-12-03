<?php
// Izinkan akses dari berbagai sumber (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Sertakan file koneksi database
require_once '../config/db.php';

// Ambil method request
 $method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        // --- LOGIKA UNTUK TAMBAH/UPDATE PELANGGAN ---
        $data = json_decode(file_get_contents("php://input"), true);

        // Cek apakah ini update (ada PelangganID) atau tambah baru
        if (isset($data['PelangganID']) && $data['PelangganID'] > 0) {
            // --- UPDATE PELANGGAN ---
            $sql = "UPDATE kasir_pelanggan SET NamaPelanggan = :NamaPelanggan, Alamat = :Alamat, NomorTelepon = :NomorTelepon WHERE PelangganID = :PelangganID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':PelangganID', $data['PelangganID'], PDO::PARAM_INT);
            $stmt->bindParam(':NamaPelanggan', $data['NamaPelanggan']);
            $stmt->bindParam(':Alamat', $data['Alamat']);
            $stmt->bindParam(':NomorTelepon', $data['NomorTelepon']);
            $stmt->execute();
            echo json_encode(['message' => 'Data pelanggan berhasil diperbarui.']);
        } else {
            // --- TAMBAH PELANGGAN BARU ---
            $sql = "INSERT INTO kasir_pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES (:NamaPelanggan, :Alamat, :NomorTelepon)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':NamaPelanggan', $data['NamaPelanggan']);
            $stmt->bindParam(':Alamat', $data['Alamat']);
            $stmt->bindParam(':NomorTelepon', $data['NomorTelepon']);
            $stmt->execute();
            echo json_encode(['message' => 'Pelanggan baru berhasil ditambahkan.']);
        }
    } elseif ($method === 'DELETE') {
        // --- LOGIKA UNTUK HAPUS PELANGGAN ---
        $customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($customerId > 0) {
            $sql = "DELETE FROM kasir_pelanggan WHERE PelangganID = :PelangganID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':PelangganID', $customerId, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(['message' => 'Pelanggan berhasil dihapus.']);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'ID pelanggan tidak valid untuk dihapus.']);
        }
    } elseif ($method === 'GET') {
        // --- LOGIKA UNTUK AMBIL SEMUA DATA PELANGGAN ---
        $stmt = $pdo->query("SELECT PelangganID, NamaPelanggan, Alamat, NomorTelepon FROM kasir_pelanggan ORDER BY NamaPelanggan ASC");
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($customers);
    } else {
        // Method tidak diizinkan
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Metode permintaan tidak diizinkan.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error di database: ' . $e->getMessage()]);
}
?>