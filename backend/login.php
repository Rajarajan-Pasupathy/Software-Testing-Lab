<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$data = json_decode(file_get_contents("php://input"), true);
$username = $data["username"] ?? '';
$password = $data["password"] ?? '';

$usersFile = "users.json";
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

if (!isset($users[$username])) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}

if (!password_verify($password, $users[$username])) {
    echo json_encode(["status" => "error", "message" => "Invalid password"]);
    exit;
}

echo json_encode(["status" => "success", "message" => "Login successful"]);
