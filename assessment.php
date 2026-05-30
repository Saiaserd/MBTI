<?php
/**
 * 八維認知功能測驗頁
 * - 70 題 Likert（1~5 分），單一頁面分頁顯示。
 * - 表單送到 api/submit_assessment.php，由它算分、寫入 $_SESSION（登入者另寫 DB）後重導到 assessment_result.php。
 * - 不保留進度：重新整理即從頭開始（這是設計決策，不是 bug）。
 */
$page_title = '八維認知功能測驗';
$page_css   = ['assets/css/assessmentCSS.css'];
require __DIR__ . '/includes/bootstrap.php';

// 把題目陣列載進來丟給 view 渲染
$questions = require __DIR__ . '/data/questions.php';

require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/assessment.view.php';
require __DIR__ . '/includes/footer.php';
