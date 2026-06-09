-- ════════════════════════════════════════════════════════════
-- 內容資料表（原本散在 data/*.php 的靜態資料，改放 MySQL 統一管理）
--   mbti_types  ← data/mbti_types.php
--   questions   ← data/questions.php
--   stereotypes ← data/stereotypes.php
-- 這些是「參考資料」，網站啟動就存在，不會在使用者操作中變動。
-- 重新匯入：php scripts/migrate_content.php
-- ════════════════════════════════════════════════════════════

-- ── 16 型人格 ──
-- functions 八維順序存成逗號分隔字串（例：'Ni,Te,Fi,Se,Ne,Ti,Fe,Si'），
-- 讀取時用 explode(',', ...) 還原成陣列。
CREATE TABLE IF NOT EXISTS `mbti_types` (
    `code`        CHAR(4)      NOT NULL,            -- 大寫 4 字母，例 INTJ
    `functions`   VARCHAR(64)  NOT NULL,            -- 八維 1~8 位，逗號分隔
    `desc_text`   TEXT         NOT NULL,            -- 類型卡簡短摘要
    `detail_text` MEDIUMTEXT   NOT NULL,            -- 完整八維功能說明
    `prev_code`   CHAR(4)      NOT NULL,            -- 上一型（大寫）
    `next_code`   CHAR(4)      NOT NULL,            -- 下一型（大寫）
    `sort_order`  TINYINT UNSIGNED NOT NULL,        -- 原檔順序，維持顯示一致
    PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 測驗題庫 ──
CREATE TABLE IF NOT EXISTS `questions` (
    `id`        SMALLINT UNSIGNED NOT NULL,         -- 題號（沿用原檔 id）
    `function`  CHAR(2)  NOT NULL,                  -- 該題對應功能 Ni/Ne/.../Fe
    `text`      VARCHAR(255) NOT NULL,              -- 題目敘述
    PRIMARY KEY (`id`),
    KEY `idx_function` (`function`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 刻板印象標籤 ──
-- 一型多筆；sort_order 維持原檔順序。
CREATE TABLE IF NOT EXISTS `stereotypes` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type_code`  CHAR(4)      NOT NULL,             -- 所屬型號（大寫）
    `label`      VARCHAR(32)  NOT NULL,             -- 負面標籤
    `fn`         CHAR(2)      NOT NULL,             -- 對應的認知功能
    `truth`      VARCHAR(255) NOT NULL,             -- 翻面後的重新解讀
    `sort_order` TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_type` (`type_code`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
