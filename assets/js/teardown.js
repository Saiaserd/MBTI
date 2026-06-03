/**
 * 「先質疑、再撕、留下重新詮釋」互動（測驗結果頁三幕）
 *
 * 第一幕 質疑：卡片堆一次顯示一張刻板標籤，使用者判斷
 *              「完全不是我 → 撕掉」或「有點像我 → 留著」。
 * 第二幕 過場：統計撕掉/留下數量，播一段過場句。
 * 第三幕 報告：留下的標籤在報告頂端「翻面」，揭露它底下其實是哪個
 *              認知功能在運作（資料來自每張卡片的 data-fn / data-truth）。
 *
 * DOM 由 views/assessment_result.view.php 產生。
 * 沒有 #teardown（該型沒設標籤）時報告已預設 .revealed，本檔 no-op。
 */
(function () {
    const teardown = document.getElementById('teardown');
    const reveal   = document.getElementById('reportReveal');
    if (!teardown || !reveal) return;

    const deck     = document.getElementById('judgeDeck');
    const ripBtn   = document.getElementById('judgeRip');
    const keepBtn  = document.getElementById('judgeKeep');
    const fill     = document.getElementById('teardownFill');
    const doneEl   = document.getElementById('teardownDone');
    const bridge   = document.getElementById('teardownBridge');
    const bridgeGo  = document.getElementById('bridgeGo');
    const bridgeRippedEl = document.getElementById('bridgeRipped');

    const cards = Array.from(deck.querySelectorAll('.judge-card'));
    const total = cards.length;
    let cursor  = 0;          // 目前在判斷第幾張
    let busy    = false;      // 動畫進行中，鎖住按鈕
    const kept   = [];        // 使用者「留著」的卡片資料
    let ripped  = 0;          // 撕掉數

    // 初始化卡片堆疊位置（後面的卡微微露出，做出一疊的感覺）
    function layoutDeck() {
        cards.forEach((card, i) => {
            const rel = i - cursor;
            if (rel < 0) return;                 // 已判斷過的不管
            if (rel > 2) { card.style.display = 'none'; return; }
            card.style.display = '';
            card.style.zIndex  = String(total - rel);
            card.style.transform = `translateY(${rel * 10}px) scale(${1 - rel * 0.04})`;
            card.style.opacity = rel === 0 ? '1' : '0.55';
        });
    }

    function currentCard() {
        return cards[cursor] || null;
    }

    // 判斷一張：keep=true 留著 / false 撕掉
    function judge(keep) {
        if (busy) return;
        const card = currentCard();
        if (!card) return;
        busy = true;

        if (keep) {
            kept.push({
                label: card.dataset.label,
                fn:    card.dataset.fn,
                truth: card.dataset.truth,
            });
            card.classList.add('judged-keep');
        } else {
            ripped++;
            card.classList.add('judged-rip');
        }

        card.addEventListener('animationend', function () {
            card.style.display = 'none';
            cursor++;
            updateProgress();
            if (cursor >= total) {
                finish();
            } else {
                layoutDeck();
                busy = false;
            }
        }, { once: true });
    }

    function updateProgress() {
        const pct = total ? Math.round((cursor / total) * 100) : 100;
        if (fill)   fill.style.width = pct + '%';
        if (doneEl) doneEl.textContent = cursor;
    }

    // ── 第二幕：過場 ──
    function finish() {
        if (teardown.dataset.finished) return;
        teardown.dataset.finished = '1';

        buildReframe();   // 先把第三幕內容準備好（仍隱藏）

        teardown.classList.add('teardown-hide');

        if (bridge) {
            if (bridgeRippedEl) bridgeRippedEl.textContent = ripped;
            // 留下 0 張時，過場第三句改個說法
            if (kept.length === 0) {
                const l3 = bridge.querySelector('.bridge-line3');
                if (l3) l3.innerHTML = '一個都沒留下。<br>那就直接看看，你真正是怎麼運作的。';
            }
            setTimeout(() => bridge.classList.add('show'), 400);
            // 不自動跳，等使用者按「看報告」鈕（也設一個保險自動跳）
        } else {
            showReport();
        }
    }

    // ── 第三幕：把留下的標籤翻面成「真實解讀」 ──
    function buildReframe() {
        const section = document.getElementById('reframeSection');
        const list    = document.getElementById('reframeList');
        const sub     = document.getElementById('reframeSub');
        if (!section || !list) return;

        if (kept.length === 0) {
            // 一張都沒留 → 不顯示翻面區
            section.hidden = true;
            return;
        }

        sub.textContent = `你承認這 ${kept.length} 個標籤有點像你。但它們的真相，可能和你想的不一樣——`;

        list.innerHTML = kept.map(item => `
            <div class="reframe-card" tabindex="0">
                <div class="reframe-face reframe-front">
                    <span class="reframe-label">「${escapeHtml(item.label)}」</span>
                    <span class="reframe-flip-hint">點一下，看真相 ↻</span>
                </div>
                <div class="reframe-face reframe-back">
                    <span class="reframe-fn">${escapeHtml(item.fn)}</span>
                    <p class="reframe-truth">${escapeHtml(item.truth)}</p>
                </div>
            </div>
        `).join('');

        // 點擊翻面
        list.querySelectorAll('.reframe-card').forEach(c => {
            const flip = () => c.classList.toggle('flipped');
            c.addEventListener('click', flip);
            c.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); flip(); }
            });
        });

        section.hidden = false;
    }

    function showReport() {
        if (bridge) bridge.classList.remove('show');
        teardown.style.display = 'none';
        reveal.classList.add('revealed');
        reveal.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, c =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    // ── 綁定 ──
    if (ripBtn)  ripBtn.addEventListener('click',  () => judge(false));
    if (keepBtn) keepBtn.addEventListener('click', () => judge(true));
    if (bridgeGo) bridgeGo.addEventListener('click', showReport);

    // 鍵盤：← 撕掉 / → 留著
    document.addEventListener('keydown', e => {
        if (teardown.dataset.finished) return;
        if (e.key === 'ArrowLeft')  judge(false);
        if (e.key === 'ArrowRight') judge(true);
    });

    layoutDeck();
    updateProgress();
})();
