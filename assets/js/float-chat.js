/**
 * 浮動 AI 聊天 Widget
 *
 * 使用前在頁面設定：
 *   window.FLOAT_CHAT_CONFIG = {
 *     title:       '顯示在頂欄的標題',
 *     pageContext: '給 AI 的頁面背景說明',
 *   };
 *
 *   // 選用：頁面完整資料物件，會自動展開成 AI 可讀的文字
 *   window.floatChatPageData = { 'Ti': '...', 'Te': '...' };
 *
 * 依賴：marked.js + DOMPurify（頁面已載入才渲染 Markdown，否則純文字）
 */
(function () {
    const cfg         = window.FLOAT_CHAT_CONFIG || {};
    const title       = cfg.title       || 'AI 助手';
    const pageContext = cfg.pageContext  || '';

    // 本次 widget 的對話歷史（不持久化）
    const history = [];

    // ── 建立 DOM ──────────────────────────────────────────
    const wrap = document.createElement('div');
    wrap.className = 'float-chat-wrap';
    wrap.innerHTML = `
        <div class="float-chat-panel" id="floatPanel">
            <div class="float-panel-header">
                <div class="float-panel-title">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                    </svg>
                    ${escHtml(title)}
                </div>
                <button class="float-panel-close" id="floatClose" aria-label="關閉">✕</button>
            </div>
            <div class="float-chat-messages" id="floatMsgs">
                <div class="float-welcome" id="floatWelcome"></div>
            </div>
            <div class="float-input-row">
                <textarea class="float-input" id="floatInput" placeholder="輸入問題…" rows="1"></textarea>
                <button class="float-send-btn" id="floatSend" aria-label="送出">
                    <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                </button>
            </div>
        </div>
        <div class="float-btn-row">
            <button class="float-chat-btn" id="floatBtn" aria-label="開啟 AI 助手">
                <svg viewBox="0 0 24 24">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                </svg>
            </button>
        </div>
    `;
    document.body.appendChild(wrap);

    const panel   = document.getElementById('floatPanel');
    const msgs    = document.getElementById('floatMsgs');
    const input   = document.getElementById('floatInput');
    const send    = document.getElementById('floatSend');
    const welcome = document.getElementById('floatWelcome');

    // ── 歡迎訊息：根據有無頁面資料給不同提示 ──
    // 等 DOMContentLoaded 後頁面的 index.js 才會填 floatChatPageData，
    // 所以稍微延遲讀取，確保資料已載入
    function buildWelcome() {
        const data = window.floatChatPageData;
        if (data && typeof data === 'object') {
            const keys = Object.keys(data);
            const examples = keys.slice(0, 3).map(k => `「${k} 是什麼？」`).join('、');
            welcome.innerHTML =
                `💡 我已讀取本頁所有內容，可以針對頁面資訊回答你的問題。<br>` +
                `<span style="font-size:12px;color:#9ca3af">例如：${examples} 或任何頁面上的疑問。</span>`;
        } else {
            welcome.innerHTML = '💡 我是這頁的 AI 助手，有任何關於內容的疑問都可以問我。';
        }
    }
    // 延遲到下一個 tick，確保 index.js 的 DOMContentLoaded 已執行
    setTimeout(buildWelcome, 0);

    // ── 開關 ──────────────────────────────────────────────
    document.getElementById('floatBtn').addEventListener('click', function () {
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) input.focus();
    });
    document.getElementById('floatClose').addEventListener('click', function () {
        panel.classList.remove('open');
    });

    // ── 送出：Enter（Shift+Enter 換行）or 按鈕 ──────────
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    send.addEventListener('click', sendMessage);

    // 讓 textarea 自動長高
    input.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });

    // ── 組合頁面內容成 AI 可讀格式 ──────────────────────
    function buildFullContext() {
        let ctx = pageContext;
        const data = window.floatChatPageData;
        if (data && typeof data === 'object') {
            ctx += '\n\n===== 頁面完整內容 =====\n';
            for (const [key, value] of Object.entries(data)) {
                if (value && typeof value === 'object') {
                    // { desc, detail } 格式
                    ctx += `\n【${key}】\n`;
                    if (value.desc)   ctx += `摘要：${value.desc}\n`;
                    if (value.detail) ctx += `${value.detail}\n`;
                } else {
                    ctx += `\n【${key}】\n${value}\n`;
                }
            }
            ctx += '\n=========================';
        }
        return ctx;
    }

    // ── 核心：送訊息 ──────────────────────────────────────
    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        input.style.height = 'auto';
        addBubble(text, 'user');
        send.disabled = true;

        history.push({ role: 'user', parts: [{ text }] });

        // 打字中 loading
        const loading = document.createElement('div');
        loading.className = 'float-msg ai';
        loading.innerHTML = '<div class="float-bubble ai float-typing"><span class="float-dot"></span><span class="float-dot"></span><span class="float-dot"></span></div>';
        msgs.appendChild(loading);
        scrollBottom();

        try {
            const body = { contents: [...history] };
            const fullCtx = buildFullContext();
            if (fullCtx) body.pageContext = fullCtx;

            const res  = await fetch('api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await res.json();
            loading.remove();

            if (data.error) {
                const msg = typeof data.error === 'object'
                    ? (data.error.message || JSON.stringify(data.error))
                    : data.error;
                addBubble('❌ ' + msg, 'error');
                history.pop();
            } else if (data.candidates?.[0]?.content?.parts?.[0]?.text) {
                const reply = data.candidates[0].content.parts[0].text;
                addBubble(reply, 'ai');
                history.push({ role: 'model', parts: [{ text: reply }] });
            } else {
                addBubble('❌ 無法取得回應，請稍後再試', 'error');
                history.pop();
            }
        } catch (err) {
            loading.remove();
            addBubble('❌ 網路錯誤：' + err.message, 'error');
            history.pop();
        } finally {
            send.disabled = false;
            input.focus();
        }
    }

    // ── 工具函式 ─────────────────────────────────────────
    function addBubble(text, type) {
        const row = document.createElement('div');
        row.className = 'float-msg ' + type;

        const bubble = document.createElement('div');
        bubble.className = 'float-bubble ' + type;

        if (type === 'ai' && window.marked && window.DOMPurify) {
            bubble.innerHTML = DOMPurify.sanitize(marked.parse(text));
        } else {
            bubble.textContent = text;
        }

        row.appendChild(bubble);
        msgs.appendChild(row);
        scrollBottom();
    }

    function scrollBottom() {
        msgs.scrollTop = msgs.scrollHeight;
    }

    function escHtml(str) {
        return str.replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
})();
