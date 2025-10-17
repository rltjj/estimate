<?php
require_once __DIR__ . '/../bootstrap.php';

if (!isset($_GET['code'])) {
    exit('Google OAuth: code 없음');
}

$code = $_GET['code'];

try {
    $token_url = "https://oauth2.googleapis.com/token";
    $data = [
        'code' => $code,
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($token_url, false, $context);

    if ($response === false) {
        throw new Exception('Google 토큰 요청 실패');
    }

    $token = json_decode($response, true);
    if (empty($token['access_token'])) {
        throw new Exception('Access token이 비어있습니다.');
    }

    $access_token = $token['access_token'];

    $userinfo_response = file_get_contents("https://www.googleapis.com/oauth2/v2/userinfo?access_token={$access_token}");
    if ($userinfo_response === false) {
        throw new Exception('사용자 정보 요청 실패');
    }

    $userinfo = json_decode($userinfo_response, true);
    $email = $userinfo['email'] ?? null;
    $name = $userinfo['name'] ?? '';
    $provider = 'google';

    if (!$email) {
        throw new Exception('이메일 정보가 없습니다.');
    }

    $pdo = Database::getInstance();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, provider, role, created_at) VALUES (?, ?, ?, 'USER', NOW())");
        $stmt->execute([$name, $email, $provider]);
        $user_id = $pdo->lastInsertId();
        $role = 'USER';
    } else {
        $user_id = $user['id'];
        $role = $user['role'] ?? 'USER';
    }

    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $role;

    if ($role === 'ADMIN') {
        header('Location: /estimate/app/views/admin.html');
    } else {
        header('Location: /estimate/app/views/customer.html');
    }
    exit;

} catch (Exception $e) {
    error_log('[Google Login Error] ' . $e->getMessage());
    exit('로그인 중 오류가 발생했습니다: ' . htmlspecialchars($e->getMessage()));
}
