<?php
session_start();
require_once 'db.php';

// 如果帶有舊的 session_id，繼續該對話；否則產生新的
$is_continue = false;
$loaded_history = [];

if (!empty($_GET['session_id']) && isset($_SESSION['user_id'])) {
    $chat_session_id = $_GET['session_id'];
    $is_continue = true;

    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT user_message, ai_response FROM chat_logs WHERE user_id = ? AND session_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("ss", $uid, $chat_session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $loaded_history[] = $row;
    }
} else {
    $chat_session_id = bin2hex(random_bytes(16));
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>聊天主頁</title>
    <link rel="stylesheet" href="css/sidebarCSS.css">
    <link rel="stylesheet" href="css/chatCSS.css?v=2">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
</head>
<body>

    <?php include 'sidebar.html'; ?>

    <main class="main-content">
        <div class="chat-container">
            <!-- 對話記錄顯示區域 -->
            <div class="chat-messages" id="chatMessages">
                <div class="message-welcome">
                    <p>👋 歡迎來到 MBTI AI 聊天助手</p>
                </div>
            </div>

            <!-- 輸入框區域 -->
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
    </main>

    <script>
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendBtn');
        const chatMessages = document.getElementById('chatMessages');
        const modeSelect = document.getElementById('modeSelect');

        // 這次對話的唯一 ID（PHP 產生）
        const chatSessionId = '<?= $chat_session_id ?>';

        // 對話歷史，格式符合 Gemini 多輪對話規範
        const conversationHistory = [];

        // 如果是繼續舊對話，載入歷史紀錄
        <?php if ($is_continue && !empty($loaded_history)): ?>
        const loadedHistory = <?= json_encode($loaded_history, JSON_UNESCAPED_UNICODE) ?>;
        loadedHistory.forEach(function(row) {
            // 顯示到畫面上
            displayMessage(row.user_message, 'user');
            displayMessage(row.ai_response, 'ai');
            // 還原到 conversationHistory 讓 AI 記得
            conversationHistory.push({ role: 'user',  parts: [{ text: row.user_message }] });
            conversationHistory.push({ role: 'model', parts: [{ text: row.ai_response  }] });
        });
        <?php endif; ?>

        sendBtn.addEventListener('click', sendMessage);

        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        async function sendMessage() {
            const userMessage = chatInput.value.trim();
            if (!userMessage) return;

            const mode = modeSelect.value;
            chatInput.value = '';
            displayMessage(userMessage, 'user');
            sendBtn.disabled = true;

            // 把使用者這句話加入歷史
            conversationHistory.push({
                role: 'user',
                parts: [{ text: userMessage }]
            });

            // 如果有選模式，在最前面插入一筆系統提示（只插一次、不存進歷史）
            let contentsToSend = [...conversationHistory];
            if (mode === 'analysis' && conversationHistory.length === 1) {
                contentsToSend[0] = {
                    role: 'user',
                    parts: [{ text: '請進行深度分析。' }, { text: userMessage }]
                };
            } else if (mode === 'casual' && conversationHistory.length === 1) {
                contentsToSend[0] = {
                    role: 'user',
                    parts: [{ text: '請以輕鬆的語氣回答。' }, { text: userMessage }]
                };
            }

            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'message message-ai message-loading';
            loadingDiv.innerHTML = '<div class="bubble bubble-ai"><span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></div>';
            chatMessages.appendChild(loadingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                const response = await fetch('key.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ contents: contentsToSend })
                });

                const data = await response.json();
                loadingDiv.remove();

                if (data.error) {
                    const errMsg = typeof data.error === 'object'
                        ? (data.error.message || JSON.stringify(data.error))
                        : data.error;
                    displayMessage('❌ 錯誤: ' + errMsg, 'error');
                    // 錯誤時把剛加入的使用者訊息移除，讓使用者可以重試
                    conversationHistory.pop();
                } else if (data.candidates && data.candidates[0]?.content?.parts[0]?.text) {
                    const aiResponse = data.candidates[0].content.parts[0].text;
                    displayMessage(aiResponse, 'ai');

                    // 把 AI 回應也加入歷史，下一輪會一起送出
                    conversationHistory.push({
                        role: 'model',
                        parts: [{ text: aiResponse }]
                    });

                    // 儲存這輪對話到資料庫
                    fetch('save_log.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            session_id: chatSessionId,
                            user_message: userMessage,
                            ai_response: aiResponse
                        })
                    });
                } else {
                    displayMessage('❌ 無法取得回應，請稍後重試', 'error');
                    conversationHistory.pop();
                }
            } catch (error) {
                loadingDiv.remove();
                console.error('錯誤:', error);
                displayMessage('❌ 網路錯誤: ' + error.message, 'error');
                conversationHistory.pop();
            } finally {
                sendBtn.disabled = false;
                chatInput.focus();
            }
        }

        // 設定 marked 選項
        marked.setOptions({
            breaks: true,      // 換行符轉 <br>
            gfm: true,         // GitHub Flavored Markdown
        });

        function displayMessage(message, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message message-${sender}`;

            if (sender === 'ai') {
                // AI 訊息：解析 Markdown 並消毒
                const bubble = document.createElement('div');
                bubble.className = 'bubble bubble-ai markdown-body';
                bubble.innerHTML = DOMPurify.sanitize(marked.parse(message));
                messageDiv.appendChild(bubble);
            } else if (sender === 'user') {
                // 使用者訊息：純文字，防止 XSS
                const bubble = document.createElement('div');
                bubble.className = 'bubble bubble-user';
                bubble.textContent = message;
                messageDiv.appendChild(bubble);
            } else {
                // 錯誤訊息
                const bubble = document.createElement('div');
                bubble.className = 'bubble bubble-error';
                bubble.textContent = message;
                messageDiv.appendChild(bubble);
            }

            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    </script>

</body>
</html>
