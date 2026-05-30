/**
 * 對話紀錄的展開/收合
 * 點對話標題列 → toggle .collapsed class，實際隱藏由 logCSS.css 處理。
 *
 * 在 log.view.php 用 onclick="toggleSession(...)" 直接呼叫。
 */
function toggleSession(sid) {
    const body = document.getElementById('session-' + sid);
    body.classList.toggle('collapsed');
}
