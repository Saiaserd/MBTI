/**
 * 側邊欄收合按鈕
 * 點漢堡圖示 → 在 <nav> 上 toggle .collapsed class，
 * 實際的收合動畫由 sidebarCSS.css 處理。
 */
const sidebar = document.getElementById('sidebar');
const btn = document.getElementById('toggle-btn');

btn.onclick = function () {
    sidebar.classList.toggle('collapsed');
};
