<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once '../config/db.php';

// Pastikan parameter penjualanId ada
if (!isset($_GET['penjualanId']) || empty($_GET['penjualanId'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Parameter penjualanId diperlukan.']);
    exit();
}

 $penjualanId = (int)$_GET['penjualanId'];

try {
    // Query untuk mengambil detail item berdasarkan PenjualanID
    // Data diambil dari kasir_detailpenjualan dan kasir_produk
    $sql = "SELECT 
                pr.NamaProduk, 
                d.JumlahProduk, 
                d.Subtotal 
            FROM 
                kasir_detailpenjualan d
            JOIN 
                kasir_produk pr ON d.ProdukID = pr.ProdukID
            WHERE 
                d.PenjualanID = :penjualanId";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':penjualanId', $penjualanId, PDO::PARAM_INT);
    $stmt->execute();

    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($details);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error di database: ' . $e->getMessage()]);
}
?>