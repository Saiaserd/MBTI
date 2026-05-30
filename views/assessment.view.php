<?php
/**
 * 八維測驗 HTML 模板
 * 需要的變數：
 *   $questions  → 70 題的陣列（從 data/questions.php 來）
 *
 * 設計：
 *   - 把 70 題切成 7 頁，每頁 10 題（避免一次顯示太長嚇跑使用者）
 *   - 每頁切換用 JS 控制（assessment.js）
 *   - 表單最終以 POST 送到 api/submit_assessment.php
 *
 * 不顯示題目屬於哪一維，避免使用者刻意配分。
 */
$labels = [
    1 => '完全不符合',
    2 => '不太符合',
    3 => '普通',
    4 => '還算符合',
    5 => '完全符合',
];

// 切頁：每 10 題一頁
$per_page = 10;
$pages    = array_chunk($questions, $per_page);
$total    = count($questions);
?>
<div class="assessment-container">
    <header class="assessment-header">
        <h1>八維認知功能測驗</h1>
        <p class="subtitle">共 <?= $total ?> 題，請依直覺作答，無需思考太久。</p>
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill" style="width:0%"></div>
        </div>
        <p class="progress-text">
            進度 <span id="answeredCount">0</span> / <?= $total ?>
        </p>
    </header>

    <form id="assessmentForm" action="api/submit_assessment.php" method="POST">
        <?php foreach ($pages as $page_idx => $page_qs): ?>
            <section class="page <?= $page_idx === 0 ? 'active' : '' ?>" data-page="<?= $page_idx ?>">
                <?php foreach ($page_qs as $q): ?>
                    <fieldset class="question" data-qid="<?= $q['id'] ?>">
                        <legend>
                            <span class="qnum"><?= $q['id'] ?>.</span>
                            <?= htmlspecialchars($q['text']) ?>
                        </legend>
                        <div class="likert">
                            <?php foreach ($labels as $val => $label): ?>
                                <label class="likert-option">
                                    <input type="radio"
                                           name="q<?= $q['id'] ?>"
                                           value="<?= $val ?>"
                                           required>
                                    <span class="likert-circle"><?= $val ?></span>
                                    <span class="likert-text"><?= $label ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>

        <nav class="page-nav">
            <button type="button" id="prevBtn" class="nav-btn" disabled>上一頁</button>
            <span class="page-indicator">
                第 <span id="currentPage">1</span> / <?= count($pages) ?> 頁
            </span>
            <button type="button" id="nextBtn" class="nav-btn">下一頁</button>
            <button type="submit" id="submitBtn" class="submit-btn" style="display:none;">
                送出測驗
            </button>
        </nav>
    </form>
</div>

<script src="assets/js/assessment.js"></script>
