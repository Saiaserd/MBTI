<?php
/** @var string $chat_session_id */
/** @var bool   $is_continue */
/** @var array  $loaded_history */
?>
<div class="chat-container">
    <div class="chat-messages" id="chatMessages">
        <div class="message-welcome">
            <p>👋 歡迎來到 MBTI AI 聊天助手</p>
        </div>
    </div>

    <div class="input-box-wrapper">
        <textarea class="chat-input" id="chatInput" placeholder="請輸入MBTI......" rows="1"></textarea>
        <div class="input-actions">
            <select class="mode-select" id="modeSelect">
                <option value="default">選擇模式</option>
                <option value="analysis">深度分析</option>
                <option value="casual">隨意聊天</option>
            </select>
            <button class="send-btn" id="sendBtn">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    window.CHAT_BOOT = {
        sessionId: <?= json_encode($chat_session_id) ?>,
        isContinue: <?= $is_continue ? 'true' : 'false' ?>,
        history: <?= json_encode($loaded_history, JSON_UNESCAPED_UNICODE) ?>
    };
</script>
<script src="assets/js/chat.js"></script>
