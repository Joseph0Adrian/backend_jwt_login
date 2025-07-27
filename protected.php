<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "Token no proporcionado"]);
    exit;
}

$token = $matches[1];
$key = trim(file_get_contents("secret.key"));

try {
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    echo json_encode(["message" => "Acceso concedido", "user" => $decoded->sub]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Token inválido o expirado", "detail" => $e->getMessage()]);
}
