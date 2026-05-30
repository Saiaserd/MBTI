<?php
/**
 * 儲存聊天紀錄端點
 * chat.js 每次 AI 回完話，會 fetch() 這個檔把一輪對話存進 chat_logs。
 * 回傳 JSON 給前端（不是 HTML）。
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');

// 未登入不能存紀錄
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(["error" => "未登入"]);
    exit;
}

// 前端是用 JSON body 傳資料（不是表單），所以要從 php://input 讀
$data = json_decode(file_get_contents("php://input"), true);
if (!$data || empty($data['session_id']) || empty($data['user_message']) || empty($data['ai_response'])) {
    http_response_code(400);
    echo json_encode(["error" => "資料不完整"]);
    exit;
}

$user_id       = current_user_id();
$session_id    = $data['session_id'];   // 同一次對話有同一個 session_id，用來區分不同對話串
$user_message  = $data['user_message'];
$ai_response   = $data['ai_response'];
// 把對話綁到當下使用者看的測驗報告上，紀錄頁才能按報告分組
// 訪客在登入前若有舊狀態 / 前端沒傳 → NULL
$assessment_id = isset($data['assessment_id']) && $data['assessment_id'] !== null
    ? (int)$data['assessment_id']
    : null;

$stmt = $conn->prepare(
    "INSERT INTO chat_logs (user_id, session_id, assessment_id, user_message, ai_response)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssiss", $user_id, $session_id, $assessment_id, $user_message, $ai_response);
$stmt->execute();

echo json_encode(["success" => true]);

require_once __DIR__ . '/../includes/close.php';
