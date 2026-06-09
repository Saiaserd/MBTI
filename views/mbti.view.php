<?php
/**
 * MBTI 詳細頁的共用 HTML 模板
 * 被 16 個 MBTI stub（enfj.php 等）載入。
 *
 * 進入頁傳進來的變數：
 *   $type → 小寫 4 字母 MBTI 代碼，例如 'enfj'
 *
 * 透過 content_repo 的 get_mbti_types() 撈出該型號的：
 *   functions（八維順序陣列）、desc（介紹文字）、prev/next（上一/下一型號）
 */

require_once __DIR__ . '/../includes/content_repo.php';
$types = get_mbti_types();

// 防呆：給了奇怪的 type 直接 404，不要 PHP warning
if (!isset($types[$type])) {
    http_response_code(404);
    echo '<p>找不到此 MBTI 類型。</p>';
    return;
}

$info       = $types[$type];
$type_upper = strtoupper($type);                // 顯示用大寫 (ENFJ)
$prev_upper = strtoupper($info['prev']);
$next_upper = strtoupper($info['next']);

// ── 浮動 AI 助手：讓每個人格頁都有，且讀得到「當前型號」的完整內容 ──
// （$page_extra_body 會在 footer.php 才輸出，所以這裡設定來得及）
$float_page_data = [
    $type_upper => [
        'desc'   => $info['desc'],
        'detail' => $info['detail'] ?? '',
    ],
];
$float_context = "使用者正在瀏覽 MBTI「{$type_upper}」型的詳細介紹頁，"
    . "頁面包含此型的榮格八維功能順序、人格介紹與完整功能說明。"
    . "請優先針對 {$type_upper} 這個型號回答問題。";

$page_extra_body =
    '<link rel="stylesheet" href="assets/css/floatChatCSS.css">' .
    '<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>' .
    '<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>' .
    '<script>' .
        'window.floatChatPageData = ' . json_encode($float_page_data, JSON_UNESCAPED_UNICODE) . ';' .
        'window.FLOAT_CHAT_CONFIG = ' . json_encode([
            'title'       => $type_upper . ' AI 助手',
            'pageContext' => $float_context,
        ], JSON_UNESCAPED_UNICODE) . ';' .
    '</script>' .
    '<script src="assets/js/float-chat.js"></script>';
?>
<div class="mbti-detail-container">

    <!-- 左半：標題 + 角色圖 -->
    <div class="detail-left-section">
        <h1 class="mbti-title"><?= $type_upper ?></h1>
        <div class="detail-img-box">
            <img src="assets/images/<?= $type ?>.jpg" alt="<?= $type_upper ?>">
        </div>
    </div>

    <!-- 右半：八維 + 介紹 -->
    <div class="detail-right-section">
        <div class="functions-box">
            <h3>榮格八維</h3>
            <div class="functions-grid">
                <!-- 1 位到 8 位依序印出 -->
                <?php foreach ($info['functions'] as $fn): ?>
                <div class="function-item"><?= htmlspecialchars($fn) ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="description-box">
            <h3>人格介紹</h3>
            <p><?= htmlspecialchars($info['desc']) ?></p>
        </div>

        <?php if (!empty($info['detail'])): ?>
        <div class="detail-box">
            <h3>完整功能說明</h3>
            <div class="detail-content"><?= htmlspecialchars($info['detail']) ?></div>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- 上一個 / 下一個型號的導覽列 -->
<div class="nav-buttons-container">
    <a href="<?= $info['prev'] ?>.php" class="nav-button">← 上一個 (<?= $prev_upper ?>)</a>
    <a href="<?= $info['next'] ?>.php" class="nav-button">下一個 (<?= $next_upper ?>) →</a>
</div>
