<?php
/**
 * 舊 chat.php 已被 assessment_result.php 取代
 * （結果頁同時承擔報告 + 聊天 + 繼續舊對話的功能）
 *
 * 這支檔留下來只是為了向後相容：舊的書籤 / 連結（例如以前紀錄頁的 chat.php?session_id=xxx）
 * 還是能用，會被自動導到新位置。
 */
require_once __DIR__ . '/includes/bootstrap.php';

$qs = '';
if (!empty($_GET['session_id'])) {
    $qs = '?session_id=' . urlencode($_GET['session_id']);
}

header('Location: assessment_result.php' . $qs);
exit;
