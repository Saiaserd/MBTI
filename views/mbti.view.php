<?php
/** @var string $type   小寫 4 字母 MBTI 代碼 (從進入頁傳入) */
$types = require __DIR__ . '/../data/mbti_types.php';
if (!isset($types[$type])) {
    http_response_code(404);
    echo '<p>找不到此 MBTI 類型。</p>';
    return;
}
$info       = $types[$type];
$type_upper = strtoupper($type);
$prev_upper = strtoupper($info['prev']);
$next_upper = strtoupper($info['next']);
?>
<div class="mbti-detail-container">

    <div class="detail-left-section">
        <h1 class="mbti-title"><?= $type_upper ?></h1>
        <div class="detail-img-box">
            <img src="assets/images/<?= $type ?>.jpg" alt="<?= $type_upper ?>">
        </div>
    </div>

    <div class="detail-right-section">
        <div class="functions-box">
            <h3>榮格八維</h3>
            <div class="functions-grid">
                <?php foreach ($info['functions'] as $fn): ?>
                <div class="function-item"><?= htmlspecialchars($fn) ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="description-box">
            <h3>人格介紹</h3>
            <p><?= htmlspecialchars($info['desc']) ?></p>
        </div>
    </div>

</div>

<div class="nav-buttons-container">
    <a href="<?= $info['prev'] ?>.php" class="nav-button">← 上一個 (<?= $prev_upper ?>)</a>
    <a href="<?= $info['next'] ?>.php" class="nav-button">下一個 (<?= $next_upper ?>) →</a>
</div>
