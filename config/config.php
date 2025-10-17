<?php
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 구글 OAuth 설정
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID']);
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET']);
define('GOOGLE_REDIRECT_URI', $_ENV['GOOGLE_REDIRECT_URI']);

// 네이버 OAuth 설정
define('NAVER_CLIENT_ID', $_ENV['NAVER_CLIENT_ID']);
define('NAVER_CLIENT_SECRET', $_ENV['NAVER_CLIENT_SECRET']);
define('NAVER_REDIRECT_URI', $_ENV['NAVER_REDIRECT_URI']);
