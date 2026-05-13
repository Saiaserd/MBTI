<?php
/**
 * 意見問卷頁
 * 表單送出後會到 api/submit_feedback.php（目前尚未實作）。
 */
$page_title = '問卷頁面';
$page_css   = ['assets/css/surveyCSS.css'];
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/survey.view.php';
require __DIR__ . '/includes/footer.php';
