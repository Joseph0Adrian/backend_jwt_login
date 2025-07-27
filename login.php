<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// Cargar usuarios
$users = json_decode(file_get_contents("users.json"), true);

$user = array_filter($users, fn($u) => $u['username'] === $username);

if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Usuario no encontrado"]);
    exit;
}

$user = array_values($user)[0];

if (password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(["error" => "Contraseña incorrecta"]);
    exit;
}

// Crear JWT
$key = trim(file_get_contents("secret.key"));
$payload = [
    "sub" => $username,
    "iat" => time(),
    "exp" => time() + 60, // 1 minuto
];

$jwt = JWT::encode($payload, $key, 'HS256');
echo json_encode(["token" => $jwt]);
