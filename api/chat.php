<?php
/**
 * Gemini AI 代理端點
 * --------------------------------
 * 前端 chat.js 不能直接呼叫 Google Gemini API（會把金鑰暴露在瀏覽器），
 * 所以這支檔負責：把前端的訊息 → 加上 MBTI 主題限制 + 使用者的八維測驗結果 → 轉送給 Google →
 * 把結果原封不動回傳給前端。
 */

// ---------- 0. 載入 session（讀使用者的測驗結果）----------
require_once __DIR__ . '/../includes/bootstrap.php';

// ---------- 1. 載入 API Key ----------
// key.env 內容範例： GEMINI_API_KEY=你的金鑰
// 用 parse_ini_file 讀檔，再用 putenv 寫進環境變數，避免金鑰直接出現在程式碼裡
if (file_exists(__DIR__ . '/../config/key.env')) {
    $env = parse_ini_file(__DIR__ . '/../config/key.env');
    foreach ($env as $key => $value) {
        putenv("$key=" . trim($value));
    }
}

// ---------- 2. 設定回應的 HTTP Header ----------
// CORS 開放（讓前端能呼叫）+ 告訴瀏覽器我們回傳的是 JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// ---------- 3. 拿到金鑰，沒有就直接報錯 ----------
$API_KEY = getenv('GEMINI_API_KEY');
if (!$API_KEY) {
    http_response_code(500);
    echo json_encode(["error" => "API Key 未設定"]);
    exit;
}
$MODEL = "gemini-3-flash-preview"; // 要換模型只改這一行

// ---------- 4. 接收前端 POST 過來的 JSON ----------
// php://input 是讀 POST body 的原始內容
// 前端傳來的格式：{ contents: [{role, parts: [{text}]}] }
$inputData   = file_get_contents("php://input");
$decodedData = json_decode($inputData, true);

if (!$decodedData) {
    echo json_encode(["error" => "無效的請求資料"]);
    exit;
}

// ---------- 5. 注入系統提示（關鍵安全機制 + 個人化報告）----------
// 限制 AI 只能討論 MBTI / 榮格八維 + 把使用者的八維測驗結果當「健康報告」帶進去，
// 這樣 AI 就能像醫生看著報告解讀一樣，給出個人化的分析。
$system_text =
    '你是一位專精於 MBTI 人格類型與榮格認知功能（八維：Ni、Ne、Si、Se、Ti、Te、Fi、Fe）的專家助手。' .
    '請只回答與 MBTI 16 型人格、榮格認知功能、人格理論、刻板印象分析、類型相容性等相關的問題。' .
    '若使用者的問題與 MBTI 或榮格八維完全無關，請禮貌地告知你只能討論 MBTI 相關主題，並引導對方提出相關問題。' .
    '回答時請使用繁體中文，語氣專業但親切。';

// 若使用者已完成八維測驗，把報告嵌進 system prompt
if (!empty($_SESSION['assessment'])) {
    $a = $_SESSION['assessment'];
    $s = $a['scores'];
    $stack_str = implode(' > ', $a['stack']);
    $system_text .=
        "\n\n===== 使用者的八維測驗報告 =====\n" .
        "MBTI 類型：{$a['type']}\n" .
        "功能堆疊（1~8 位）：{$stack_str}\n" .
        "八維分數（0~100）：" .
        "Ni={$s['Ni']}, Ne={$s['Ne']}, Si={$s['Si']}, Se={$s['Se']}, " .
        "Ti={$s['Ti']}, Te={$s['Te']}, Fi={$s['Fi']}, Fe={$s['Fe']}\n" .
        "================================\n" .
        '請以這份報告為基礎進行個人化分析。回答時可主動引用報告中的分數與功能位置（例如「你的 Ni 分數 78 偏高，這代表…」），' .
        '把對話定位為「使用者拿著體檢報告諮詢醫生」的場景。若使用者問題很開放，可參考報告主動提出他可能會關心的解讀方向。';
}

$decodedData['systemInstruction'] = [
    'parts' => [['text' => $system_text]]
];

$bodyToSend = json_encode($decodedData, JSON_UNESCAPED_UNICODE);

// ---------- 6. 用 cURL 把請求送到 Google ----------
$url = "https://generativelanguage.googleapis.com/v1beta/models/{$MODEL}:generateContent?key={$API_KEY}";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyToSend);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 回傳當字串而不是直接 echo

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ---------- 7. 把 Google 的回應丟回前端 ----------
// 保留原本的 HTTP 狀態碼，前端才能判斷成功/失敗
http_response_code($httpCode);
echo $response;
