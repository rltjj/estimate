<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../bootstrap.php';

$pdo = Database::getInstance();

$response = ['application' => null, 'products' => []];

$appId = $_GET['id'] ?? $_GET['estimateId'] ?? null;

if (!$appId) {
    $response['error'] = 'ID가 없습니다.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$appId = intval($appId);

try {
    $stmt = $pdo->prepare("
        SELECT a.id AS id, a.user_id AS user_id, u.name AS user_name, u.company_name, u.phone, u.email, a.status
        FROM applications a
        JOIN users u ON a.user_id = u.id
        WHERE a.id = :id
    ");
    $stmt->execute(['id' => $appId]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        $response['error'] = '해당 ID의 신청 내역이 없습니다.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt2 = $pdo->prepare("
        SELECT ap.product_id, p.name, ap.quantity, ap.price
        FROM application_products ap
        JOIN products p ON ap.product_id = p.id
        WHERE ap.application_id = :id
    ");
    $stmt2->execute(['id' => $appId]);
    $products = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $response['application'] = $application;
    $response['products'] = $products ?: [];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
