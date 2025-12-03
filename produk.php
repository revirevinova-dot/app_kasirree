<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/db.php';
 $method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['ProdukID']) && $data['ProdukID'] > 0) {
            // UPDATE
            $sql = "UPDATE kasir_produk SET NamaProduk = :NamaProduk, Harga = :Harga, Stok = :Stok WHERE ProdukID = :ProdukID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':ProdukID', $data['ProdukID'], PDO::PARAM_INT);
            $stmt->bindParam(':NamaProduk', $data['NamaProduk']);
            $stmt->bindParam(':Harga', $data['Harga']);
            $stmt->bindParam(':Stok', $data['Stok']);
            $stmt->execute();
            echo json_encode(['message' => 'Produk berhasil diperbarui.']);
        } else {
            // TAMBAH
            $sql = "INSERT INTO kasir_produk (NamaProduk, Harga, Stok) VALUES (:NamaProduk, :Harga, :Stok)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':NamaProduk', $data['NamaProduk']);
            $stmt->bindParam(':Harga', $data['Harga']);
            $stmt->bindParam(':Stok', $data['Stok']);
            $stmt->execute();
            echo json_encode(['message' => 'Produk baru berhasil ditambahkan.']);
        }
    } elseif ($method === 'DELETE') {
        $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($productId > 0) {
            $sql = "DELETE FROM kasir_produk WHERE ProdukID = :ProdukID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':ProdukID', $productId, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(['message' => 'Produk berhasil dihapus.']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID produk tidak valid.']);
        }
    } elseif ($method === 'GET') {
        $stmt = $pdo->query("SELECT ProdukID, NamaProduk, Harga, Stok FROM kasir_produk ORDER BY NamaProduk ASC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Metode tidak diizinkan.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error di database: ' . $e->getMessage()]);
}
?>