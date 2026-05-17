/**
 * 頂欄漢堡按鈕（僅窄畫面有作用）
 * 點漢堡圖示 → 在 <nav> 上 toggle .open class，
 * 實際的展開動畫由 sidebarCSS.css 的 @media 區塊處理。
 */
const topnav = document.getElementById('topnav');
const btn = document.getElementById('toggle-btn');

if (btn && topnav) {
    btn.onclick = function () {
        topnav.classList.toggle('open');
    };
}
