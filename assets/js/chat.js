/**
 * 聊天頁前端邏輯
 *
 * 跟 PHP 配合的方式：
 *   - 進頁面時 chat.php 把對話資料寫進 window.CHAT_BOOT
 *   - 送訊息給 AI → fetch('api/chat.php') → 回應顯示在畫面上
 *   - 每輪對話結束 → fetch('api/save_log.php') 把這輪存進資料庫
 */

const chatInput   = document.getElementById('chatInput');
const sendBtn     = document.getElementById('sendBtn');
const chatMessages = document.getElementById('chatMessages');

const chatSessionId  = window.CHAT_BOOT.sessionId;
const assessmentId   = window.CHAT_BOOT.assessmentId || null; // 訪客 / 舊資料為 null

// Gemini API 要求的對話歷史格式：[{role: 'user'|'model', parts: [{text}]}, ...]
// 每次送請求都會把整個歷史傳過去，AI 才會記得前面說過的話
const conversationHistory = [];

// 如果是從紀錄頁點「繼續對話」進來，把舊訊息塞進畫面跟歷史
if (window.CHAT_BOOT.isContinue && Array.isArray(window.CHAT_BOOT.history)) {
    // 把預設的歡迎語藏掉，避免「歡迎...」浮在舊對話上面看起來很怪
    const welcome = chatMessages.querySelector('.message-welcome');
    if (welcome) welcome.style.display = 'none';

    window.CHAT_BOOT.history.forEach(function (row) {
        displayMessage(row.user_message, 'user');
        displayMessage(row.ai_response, 'ai');
        conversationHistory.push({ role: 'user', parts: [{ text: row.user_message }] });
        conversationHistory.push({ role: 'model', parts: [{ text: row.ai_response }] });
    });
}

// 送出按鈕 + Enter 鍵都會觸發送訊息（Shift+Enter 例外，當作換行）
sendBtn.addEventListener('click', sendMessage);
chatInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

async function sendMessage() {
    const userMessage = chatInput.value.trim();
    if (!userMessage) return;

    chatInput.value = '';
    displayMessage(userMessage, 'user');
    sendBtn.disabled = true; // 等回應期間不能再按

    // 把這句話加進歷史
    conversationHistory.push({
        role: 'user',
        parts: [{ text: userMessage }]
    });

    const contentsToSend = [...conversationHistory];

    // 顯示「正在輸入」的三個跳動點點
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'message message-ai message-loading';
    loadingDiv.innerHTML = '<div class="bubble bubble-ai"><span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></div>';
    chatMessages.appendChild(loadingDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    try {
        // 呼叫後端代理（後端會幫我們把請求轉發給 Gemini）
        const response = await fetch('api/chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ contents: contentsToSend })
        });

        const data = await response.json();
        loadingDiv.remove();

        if (data.error) {
            // API 回錯
            const errMsg = typeof data.error === 'object'
                ? (data.error.message || JSON.stringify(data.error))
                : data.error;
            displayMessage('❌ 錯誤: ' + errMsg, 'error');
            conversationHistory.pop(); // 把剛剛 push 的使用者訊息退掉，讓使用者可以重試
        } else if (data.candidates && data.candidates[0]?.content?.parts[0]?.text) {
            // 成功：取出 AI 的回應文字
            const aiResponse = data.candidates[0].content.parts[0].text;
            displayMessage(aiResponse, 'ai');

            // 加進歷史，下一輪會一起送出
            conversationHistory.push({
                role: 'model',
                parts: [{ text: aiResponse }]
            });

            // 同步把這輪對話存進資料庫（不等回應）
            // 帶 assessment_id 讓紀錄頁可以按報告分組
            fetch('api/save_log.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    session_id:    chatSessionId,
                    assessment_id: assessmentId,
                    user_message:  userMessage,
                    ai_response:   aiResponse
                })
            });
        } else {
            // 格式預期外的回應
            displayMessage('❌ 無法取得回應，請稍後重試', 'error');
            conversationHistory.pop();
        }
    } catch (error) {
        // 網路/JSON parse 錯誤
        loadingDiv.remove();
        console.error('錯誤:', error);
        displayMessage('❌ 網路錯誤: ' + error.message, 'error');
        conversationHistory.pop();
    } finally {
        sendBtn.disabled = false;
        chatInput.focus();
    }
}

// Markdown 解析設定（AI 回應的 ** 粗體、列表等格式才會渲染）
marked.setOptions({
    breaks: true,  // \n 自動轉 <br>
    gfm: true,     // GitHub 風格的 Markdown
});

/**
 * 在對話區顯示一則訊息
 * @param {string} message 訊息內容
 * @param {string} sender 'user' | 'ai' | 'error'
 */
function displayMessage(message, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message message-${sender}`;

    if (sender === 'ai') {
        // AI 訊息支援 Markdown，但要用 DOMPurify 清掉危險 HTML 防 XSS
        const bubble = document.createElement('div');
        bubble.className = 'bubble bubble-ai markdown-body';
        bubble.innerHTML = DOMPurify.sanitize(marked.parse(message));
        messageDiv.appendChild(bubble);
    } else if (sender === 'user') {
        // 使用者訊息純文字，textContent 自動轉義
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
    chatMessages.scrollTop = chatMessages.scrollHeight; // 自動捲到最下面
}
