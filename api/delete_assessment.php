<?php
/**
 * 刪除測驗報告（連同它底下的所有對話）
 * 紀錄頁每張報告卡有「刪除報告」按鈕，POST 過來這裡。
 *
 * 刪除順序：
 *   1. 先把這份報告對應的 chat_logs 整批刪掉（同時驗證 user_id 防越權）
 *   2. 再刪 assessments 那一筆
 *   兩個動作包在 transaction 裡，任何一步失敗就整個 rollback。
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/db.php';

if (!is_logged_in()) {
    header("Location: ../user.php");
    exit;
}

if (!empty($_POST['assessment_id'])) {
    $user_id       = current_user_id();
    $assessment_id = (int)$_POST['assessment_id'];

    $conn->begin_transaction();
    try {
        // 1. 刪掉這份報告底下的所有對話
        $stmt = $conn->prepare(
            "DELETE FROM chat_logs WHERE user_id = ? AND assessment_id = ?"
        );
        $stmt->bind_param("si", $user_id, $assessment_id);
        $stmt->execute();

        // 2. 刪報告本身（同時比對 user_id，避免刪到別人的）
        $stmt = $conn->prepare(
            "DELETE FROM assessments WHERE user_id = ? AND id = ?"
        );
        $stmt->bind_param("si", $user_id, $assessment_id);
        $stmt->execute();

        $conn->commit();
    } catch (Throwable $e) {
        $conn->rollback();
    }
}

header("Location: ../log.php");
require_once __DIR__ . '/../includes/close.php';
