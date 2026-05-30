<?php
/**
 * 聊天頁 HTML 模板
 * 由 chat.php 載入，需要的變數：
 *   $chat_session_id → 這次對話的唯一 ID
 *   $is_continue     → 是否繼續舊對話
 *   $loaded_history  → 舊對話的訊息陣列（is_continue 為 false 時是空陣列）
 *
 * 對話進行的邏輯都在 chat.js，PHP 只負責把上面三個變數透過 window.CHAT_BOOT 傳給 JS。
 */
?>
<div class="chat-container">
    <!-- 對話訊息顯示區 -->
    <div class="chat-messages" id="chatMessages">
        <div class="message-welcome">
            <p>👋 歡迎來到 MBTI AI 聊天助手</p>
        </div>
    </div>

    <!-- 輸入區：textarea + 送出鈕 -->
    <div class="input-box-wrapper">
        <textarea class="chat-input" id="chatInput" placeholder="請輸入MBTI......" rows="1"></textarea>
        <div class="input-actions">
            <button class="send-btn" id="sendBtn">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    // 把 PHP 端的資料丟給 JS（chat.js 會讀這個全域物件）
    window.CHAT_BOOT = {
        sessionId: <?= json_encode($chat_session_id) ?>,
        isContinue: <?= $is_continue ? 'true' : 'false' ?>,
        history: <?= json_encode($loaded_history, JSON_UNESCAPED_UNICODE) ?>
    };
</script>
<script src="assets/js/chat.js"></script>
