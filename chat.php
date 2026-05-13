<?php
/**
 * 聊天頁進入點
 * - 第一次打開：產生新的 chat_session_id（之後存紀錄會用）
 * - 從紀錄頁點「繼續對話」：URL 帶 ?session_id=xxx，會把舊紀錄撈出來顯示
 *
 * 注意：對話的歷史顯示是這個 PHP 撈好交給 chat.js，
 *      送訊息給 AI 的動作則是 chat.js 直接呼叫 api/chat.php。
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/db.php';

// 預設：全新對話
$is_continue     = false;
$loaded_history  = [];
$chat_session_id = bin2hex(random_bytes(16)); // 隨機產生 32 字元的 ID

// 如果是登入狀態 + URL 帶 session_id → 繼續舊對話
if (!empty($_GET['session_id']) && is_logged_in()) {
    $chat_session_id = $_GET['session_id'];
    $is_continue     = true;
    $uid             = current_user_id();

    // 撈出這個對話串的所有訊息，按時間排序
    $stmt = $conn->prepare("SELECT user_message, ai_response FROM chat_logs WHERE user_id = ? AND session_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("ss", $uid, $chat_session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $loaded_history[] = $row;
    }
}

// 頁面設定
$page_title = '聊天主頁';
$page_css   = ['assets/css/chatCSS.css?v=2']; // ?v=2 是強制瀏覽器重抓的版本號

// 聊天頁需要兩個外部 JS 函式庫：
//   marked    → 把 AI 回的 Markdown 轉成 HTML
//   DOMPurify → 過濾 HTML 防止 XSS
$page_extra_head =
    '<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>' .
    '<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>';

require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/chat.view.php';   // view 會用到上面準備好的 $chat_session_id, $is_continue, $loaded_history
require __DIR__ . '/includes/footer.php';

require_once __DIR__ . '/includes/close.php';
