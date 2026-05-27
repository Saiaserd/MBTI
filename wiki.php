<?php
/**
 * 16 型人格總覽
 * 顯示 16 張卡片，點擊跳到對應的 MBTI 詳細頁（enfj.php / intp.php / ...）。
 * 卡片用迴圈產生，不用 16 個 hard-code 的 <a>。
 */
$page_title = '介紹主頁';
$page_css   = ['assets/css/wikiCSS.css', 'assets/css/floatChatCSS.css'];

// 把 16 型描述傳到前端，讓 AI 可以讀到頁面實際內容
$types = require __DIR__ . '/data/mbti_types.php';
$types_for_js = [];
foreach ($types as $key => $val) {
    $types_for_js[strtoupper($key)] = $val['desc'];
}

$page_extra_body =
    '<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>' .
    '<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>' .
    '<script>' .
        'window.floatChatPageData = ' . json_encode($types_for_js, JSON_UNESCAPED_UNICODE) . ';' .
        'window.FLOAT_CHAT_CONFIG = {' .
            'title: "16型人格 AI 助手",' .
            'pageContext: "使用者正在瀏覽「16型人格總覽」頁。頁面展示了 MBTI 16 個人格類型的卡片，分為 NT 分析家、NF 外交官、SJ 守護者、SP 探索者四組，點擊卡片可跳到各型別的詳細介紹頁。"' .
        '};' .
    '</script>' .
    '<script src="assets/js/float-chat.js"></script>';
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/wiki.view.php';
require __DIR__ . '/includes/footer.php';
