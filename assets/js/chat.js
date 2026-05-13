const chatInput = document.getElementById('chatInput');
const sendBtn = document.getElementById('sendBtn');
const chatMessages = document.getElementById('chatMessages');
const modeSelect = document.getElementById('modeSelect');

const chatSessionId = window.CHAT_BOOT.sessionId;
const conversationHistory = [];

if (window.CHAT_BOOT.isContinue && Array.isArray(window.CHAT_BOOT.history)) {
    window.CHAT_BOOT.history.forEach(function (row) {
        displayMessage(row.user_message, 'user');
        displayMessage(row.ai_response, 'ai');
        conversationHistory.push({ role: 'user', parts: [{ text: row.user_message }] });
        conversationHistory.push({ role: 'model', parts: [{ text: row.ai_response }] });
    });
}

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

    const mode = modeSelect.value;
    chatInput.value = '';
    displayMessage(userMessage, 'user');
    sendBtn.disabled = true;

    conversationHistory.push({
        role: 'user',
        parts: [{ text: userMessage }]
    });

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
        const response = await fetch('api/chat.php', {
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
            conversationHistory.pop();
        } else if (data.candidates && data.candidates[0]?.content?.parts[0]?.text) {
            const aiResponse = data.candidates[0].content.parts[0].text;
            displayMessage(aiResponse, 'ai');

            conversationHistory.push({
                role: 'model',
                parts: [{ text: aiResponse }]
            });

            fetch('api/save_log.php', {
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

marked.setOptions({
    breaks: true,
    gfm: true,
});

function displayMessage(message, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message message-${sender}`;

    if (sender === 'ai') {
        const bubble = document.createElement('div');
        bubble.className = 'bubble bubble-ai markdown-body';
        bubble.innerHTML = DOMPurify.sanitize(marked.parse(message));
        messageDiv.appendChild(bubble);
    } else if (sender === 'user') {
        const bubble = document.createElement('div');
        bubble.className = 'bubble bubble-user';
        bubble.textContent = message;
        messageDiv.appendChild(bubble);
    } else {
        const bubble = document.createElement('div');
        bubble.className = 'bubble bubble-error';
        bubble.textContent = message;
        messageDiv.appendChild(bubble);
    }

    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}
