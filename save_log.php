<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "未登入"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || empty($data['session_id']) || empty($data['user_message']) || empty($data['ai_response'])) {
    http_response_code(400);
    echo json_encode(["error" => "資料不完整"]);
    exit;
}

$user_id      = $_SESSION['user_id'];
$session_id   = $data['session_id'];
$user_message = $data['user_message'];
$ai_response  = $data['ai_response'];

$stmt = $conn->prepare("INSERT INTO chat_logs (user_id, session_id, user_message, ai_response) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $user_id, $session_id, $user_message, $ai_response);
$stmt->execute();

echo json_encode(["success" => true]);
require_once 'close.php';
?>
