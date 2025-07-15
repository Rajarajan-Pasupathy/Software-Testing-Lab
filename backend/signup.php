<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$data = json_decode(file_get_contents("php://input"), true);
$username = $data["username"] ?? '';
$password = $data["password"] ?? '';

$usersFile = "users.json";
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

if (isset($users[$username])) {
    echo json_encode(["status" => "error", "message" => "User already exists"]);
    exit;
}

$users[$username] = password_hash($password, PASSWORD_DEFAULT);
file_put_contents($usersFile, json_encode($users));

echo json_encode(["status" => "success", "message" => "Signup successful"]);
