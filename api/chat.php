<?php
// 載入 API key 環境變數
if (file_exists(__DIR__ . '/../config/key.env')) {
    $env = parse_ini_file(__DIR__ . '/../config/key.env');
    foreach ($env as $key => $value) {
        putenv("$key=" . trim($value));
    }
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$API_KEY = getenv('GEMINI_API_KEY');
if (!$API_KEY) {
    http_response_code(500);
    echo json_encode(["error" => "API Key 未設定"]);
    exit;
}
$MODEL = "gemini-3-flash-preview";

$inputData   = file_get_contents("php://input");
$decodedData = json_decode($inputData, true);

if (!$decodedData) {
    echo json_encode(["error" => "無效的請求資料"]);
    exit;
}

// 注入系統提示，限制 AI 只討論 MBTI 與榮格八維
$decodedData['systemInstruction'] = [
    'parts' => [[
        'text' =>
            '你是一位專精於 MBTI 人格類型與榮格認知功能（八維：Ni、Ne、Si、Se、Ti、Te、Fi、Fe）的專家助手。' .
            '請只回答與 MBTI 16 型人格、榮格認知功能、人格理論、刻板印象分析、類型相容性等相關的問題。' .
            '若使用者的問題與 MBTI 或榮格八維完全無關，請禮貌地告知你只能討論 MBTI 相關主題，並引導對方提出相關問題。' .
            '回答時請使用繁體中文，語氣專業但親切。'
    ]]
];

$bodyToSend = json_encode($decodedData, JSON_UNESCAPED_UNICODE);

$url = "https://generativelanguage.googleapis.com/v1beta/models/{$MODEL}:generateContent?key={$API_KEY}";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyToSend);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
echo $response;
