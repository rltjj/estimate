<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../bootstrap.php';

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY id");
    $stmt->execute();
    $products = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $products
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
