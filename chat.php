<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>聊天主頁</title>
    <link rel="stylesheet" href="css/sidebarCSS.css">
    <link rel="stylesheet" href="css/chatCSS.css">
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

        // 發送按鈕點擊事件
        sendBtn.addEventListener('click', sendMessage);
        
        // Enter 鍵發送
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
            
            // 清空輸入聊天框
            chatInput.value = '';
            
            // 顯示用戶消息
            displayMessage(userMessage, 'user');
            
            // 禁用按鈕（防止重複點擊）
            sendBtn.disabled = true;

            // 顯示 AI 載入中提示
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'message message-ai message-loading';
            loadingDiv.innerHTML = '<p><span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></p>';
            chatMessages.appendChild(loadingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                // 構造發送給 Gemini API 的格式
                const requestData = {
                    contents: [
                        {
                            parts: [
                                {
                                    text: userMessage
                                }
                            ]
                        }
                    ]
                };

                // 同時考慮模式的附加提示
                let systemPrompt = '';
                if (mode === 'analysis') {
                    systemPrompt = '請進行深度分析';
                } else if (mode === 'casual') {
                    systemPrompt = '請以輕鬆的語氣回答';
                }

                if (systemPrompt) {
                    requestData.contents[0].parts.unshift({
                        text: systemPrompt
                    });
                }

                // 發送請求到 key.php
                const response = await fetch('key.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                const data = await response.json();

                // 移除載入中提示
                loadingDiv.remove();

                // 檢查是否有錯誤
                if (data.error) {
                    displayMessage('❌ 錯誤: ' + data.error, 'error');
                } else if (data.candidates && data.candidates[0]?.content?.parts[0]?.text) {
                    // 取得 AI 的回應
                    const aiResponse = data.candidates[0].content.parts[0].text;
                    displayMessage(aiResponse, 'ai');
                } else {
                    displayMessage('❌ 無法取得回應，請稍後重試', 'error');
                }
            } catch (error) {
                loadingDiv.remove();
                console.error('錯誤:', error);
                displayMessage('❌ 網路錯誤: ' + error.message, 'error');
            } finally {
                sendBtn.disabled = false;
                chatInput.focus();
            }
        }

        function displayMessage(message, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message message-${sender}`;
            messageDiv.innerHTML = `<p>${escapeHtml(message)}</p>`;
            chatMessages.appendChild(messageDiv);
            
            // 自動滾動到最底部
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // 防止 XSS 攻擊
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

</body>
</html>