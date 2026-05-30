-- 把聊天紀錄與測驗報告做關聯
-- 一輪聊天屬於某次測驗（assessment_id）；舊資料沒有所屬報告 → 允許 NULL。
-- 訪客的對話本來就不寫進這張表，所以這支 SQL 只影響登入者。

ALTER TABLE `chat_logs`
    ADD COLUMN `assessment_id` INT UNSIGNED NULL DEFAULT NULL AFTER `session_id`,
    ADD KEY `idx_assessment` (`assessment_id`);

-- 如果之後想加 FOREIGN KEY（assessments.id 刪掉時 chat_logs.assessment_id 設成 NULL）：
-- ALTER TABLE `chat_logs`
--     ADD CONSTRAINT `fk_chat_logs_assessment`
--     FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`id`) ON DELETE SET NULL;
