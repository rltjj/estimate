<?php
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([]);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $role   = $_SESSION['user_role'] ?? 'USER';

    $appModel = new ApplicationModel();

    if ($role === 'ADMIN') {
        $applications = $appModel->getApplications();

        foreach ($applications as &$app) {
            $app['userName'] = $app['user_name'] ?? '';
            $app['company']  = $app['company_name'] ?? '';
            $app['date']     = $app['created_at'] ?? '';
            $app['state']    = $app['status'] ?? '';
            unset($app['user_name'], $app['company_name'], $app['created_at'], $app['status']);
        }
        unset($app);

    } else {
        $applications = $appModel->getApplications($userId);
        foreach ($applications as &$app) {
            $app['products']   = $app['products'] ?? '';
            $app['status']     = $app['status'] ?? '';
            $app['created_at'] = $app['created_at'] ?? '';
        }
        unset($app);
    }

    echo json_encode($applications);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    error_log('[get_applications.php Error] ' . $e->getMessage());
    exit;
}
