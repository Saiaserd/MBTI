-- 八維認知功能測驗結果表
-- 訪客不會寫進來（只放在 $_SESSION），這張表只記錄登入者的歷史報告。
-- 一個帳號可以做很多次測驗（重測），所以不是 PRIMARY KEY(user_id)。

CREATE TABLE IF NOT EXISTS `assessments` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     VARCHAR(32) NOT NULL,
    `ni_score`    TINYINT UNSIGNED NOT NULL,
    `ne_score`    TINYINT UNSIGNED NOT NULL,
    `si_score`    TINYINT UNSIGNED NOT NULL,
    `se_score`    TINYINT UNSIGNED NOT NULL,
    `ti_score`    TINYINT UNSIGNED NOT NULL,
    `te_score`    TINYINT UNSIGNED NOT NULL,
    `fi_score`    TINYINT UNSIGNED NOT NULL,
    `fe_score`    TINYINT UNSIGNED NOT NULL,
    `mbti_type`   CHAR(4) NOT NULL,
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_created` (`user_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
