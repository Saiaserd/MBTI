/**
 * 八維測驗頁的互動邏輯
 *  - 切頁（上一頁/下一頁）
 *  - 即時更新進度條
 *  - 最後一頁顯示送出鈕，前面顯示下一頁
 *  - 沒答完不能送出（HTML 的 required 已經處理，這裡再加一層提示）
 */

(() => {
    const form         = document.getElementById('assessmentForm');
    const pages        = Array.from(form.querySelectorAll('.page'));
    const prevBtn      = document.getElementById('prevBtn');
    const nextBtn      = document.getElementById('nextBtn');
    const submitBtn    = document.getElementById('submitBtn');
    const currentPage  = document.getElementById('currentPage');
    const progressFill = document.getElementById('progressFill');
    const answeredCnt  = document.getElementById('answeredCount');

    const totalQs   = form.querySelectorAll('.question').length;
    const totalPg   = pages.length;
    let currentIdx  = 0;

    // ---------- 切頁 ----------
    const showPage = (idx) => {
        pages.forEach((p, i) => p.classList.toggle('active', i === idx));
        currentPage.textContent = idx + 1;

        prevBtn.disabled = (idx === 0);

        if (idx === totalPg - 1) {
            nextBtn.style.display   = 'none';
            submitBtn.style.display = 'inline-block';
        } else {
            nextBtn.style.display   = 'inline-block';
            submitBtn.style.display = 'none';
        }

        // 切頁後捲到頂端，避免使用者要往上找第一題
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // ---------- 進度條 ----------
    const updateProgress = () => {
        const answered = form.querySelectorAll('input[type="radio"]:checked').length;
        answeredCnt.textContent = answered;
        progressFill.style.width = `${(answered / totalQs) * 100}%`;

        // 答完的題目視覺反饋
        form.querySelectorAll('.question').forEach((q) => {
            const checked = q.querySelector('input[type="radio"]:checked');
            q.classList.toggle('answered', !!checked);
        });
    };

    // ---------- 事件綁定 ----------
    prevBtn.addEventListener('click', () => {
        if (currentIdx > 0) showPage(--currentIdx);
    });

    nextBtn.addEventListener('click', () => {
        // 本頁是否答完？沒答完就提示並滾到第一個未答題
        const unanswered = pages[currentIdx].querySelectorAll('.question:not(.answered)');
        if (unanswered.length > 0) {
            alert(`本頁還有 ${unanswered.length} 題沒作答喔！`);
            unanswered[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        if (currentIdx < totalPg - 1) showPage(++currentIdx);
    });

    form.addEventListener('change', (e) => {
        if (e.target.matches('input[type="radio"]')) {
            updateProgress();
        }
    });

    form.addEventListener('submit', (e) => {
        const answered = form.querySelectorAll('input[type="radio"]:checked').length;
        if (answered < totalQs) {
            e.preventDefault();
            alert(`還有 ${totalQs - answered} 題沒答完，請回頭檢查。`);
            // 把第一個未答題目滾進視野（也切到它所在那頁）
            for (let i = 0; i < pages.length; i++) {
                const unans = pages[i].querySelector('.question:not(.answered)');
                if (unans) {
                    currentIdx = i;
                    showPage(i);
                    setTimeout(() => unans.scrollIntoView({ behavior: 'smooth', block: 'center' }), 350);
                    break;
                }
            }
        }
    });

    // 初始
    showPage(0);
    updateProgress();
})();
