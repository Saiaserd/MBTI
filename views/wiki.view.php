<?php
/**
 * 16 型總覽頁的 HTML 模板
 * 用 4 個分組（NT / NF / SJ / SP）依序 foreach 印出 16 張卡片，
 * 不用 hard-code 16 個 <a>，要改順序或新增分類只改下面的 $grid 陣列。
 */
$grid = [
    'NT' => ['intj', 'intp', 'entj', 'entp'], // 分析家
    'NF' => ['infj', 'infp', 'enfj', 'enfp'], // 外交官
    'SJ' => ['istj', 'isfj', 'estj', 'esfj'], // 守護者
    'SP' => ['istp', 'isfp', 'estp', 'esfp'], // 探索者
];
?>
<div class="about-header">
    <h1>16 型人格介紹</h1>
</div>

<div class="mbti-grid">
    <?php foreach ($grid as $group => $types): ?>
        <?php foreach ($types as $t): $upper = strtoupper($t); ?>
        <!-- class 用分組名稱 (mbti-card-NT 等) 才能套不同顏色 -->
        <a href="<?= $t ?>.php" class="mbti-card-<?= $group ?>">
            <div class="mbti-img-box"><img src="assets/images/<?= $t ?>.jpg" alt="<?= $upper ?>"></div>
            <div class="mbti-name"><?= $upper ?></div>
        </a>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
