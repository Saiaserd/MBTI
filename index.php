<?php
/**
 * 八維介紹頁
 * 顯示 8 個榮格認知功能的卡片，點擊會彈出 modal 顯示詳細解釋。
 * 卡片資料 + modal 邏輯都在 assets/js/index.js。
 */
$page_title = '主頁';
$page_css   = ['assets/css/indexCSS.css', 'assets/css/floatChatCSS.css'];
$page_extra_body =
    '<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>' .
    '<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>' .
    '<script>window.FLOAT_CHAT_CONFIG = {' .
        'title: "八維功能 AI 助手",' .
        'pageContext: "使用者正在瀏覽「八維認知功能介紹」頁。頁面有 8 個功能卡片（Ti、Te、Si、Se、Ni、Ne、Fi、Fe），點擊可查看該功能在 1~8 位置的詳細說明。頁面的完整介紹文字已另外提供在 floatChatPageData 中，請優先根據那些內容回答使用者的疑問。"' .
    '};</script>' .
    '<script src="assets/js/float-chat.js"></script>';
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/index.view.php';
require __DIR__ . '/includes/footer.php';
