<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = getenv('DB_HOST') ?: "db.fr-roub1.bengt.wasmernet.com";
$port = getenv('DB_PORT') ?: "20184";
$db   = getenv('DB_NAME') ?: "reggae_trivia";
$user = getenv('DB_USERNAME') ?: "YOUR_DB_USER";
$pass = getenv('DB_PASSWORD') ?: "YOUR_DB_PASSWORD";

$conn = mysqli_connect($host, $user, $pass, $db, $port);

mysqli_query($conn, "SET time_zone = '+07:00'");

if (!$conn) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Koneksi database gagal.",
        "error" => mysqli_connect_error()
    ]);

    exit;
}

mysqli_set_charset($conn, "utf8mb4");

function response($success, $message = "", $data = null)
{
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

    exit;
}
