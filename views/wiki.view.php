<?php
$grid = [
    'NT' => ['intj', 'intp', 'entj', 'entp'],
    'NF' => ['infj', 'infp', 'enfj', 'enfp'],
    'SJ' => ['istj', 'isfj', 'estj', 'esfj'],
    'SP' => ['istp', 'isfp', 'estp', 'esfp'],
];
?>
<div class="about-header">
    <h1>16 型人格介紹</h1>
</div>

<div class="mbti-grid">
    <?php foreach ($grid as $group => $types): ?>
        <?php foreach ($types as $t): $upper = strtoupper($t); ?>
        <a href="<?= $t ?>.php" class="mbti-card-<?= $group ?>">
            <div class="mbti-img-box"><img src="assets/images/<?= $t ?>.jpg" alt="<?= $upper ?>"></div>
            <div class="mbti-name"><?= $upper ?></div>
        </a>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
