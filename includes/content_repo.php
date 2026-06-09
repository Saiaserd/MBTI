<?php
/**
 * 內容讀取層（repository）
 *
 * 取代原本 `require data/*.php` 的所有讀取。資料來源改為 MySQL，
 * 但「回傳的資料結構」刻意與舊 PHP 檔一模一樣，讓呼叫端幾乎不用改邏輯：
 *
 *   get_mbti_types()    ≅ 舊 data/mbti_types.php  （key 小寫 → ['functions','desc','detail','prev','next']）
 *   get_questions()     ≅ 舊 data/questions.php   （[ ['id','function','text'], ... ]）
 *   get_stereotypes()   ≅ 舊 data/stereotypes.php （key 小寫 → [ ['label','fn','truth'], ... ]）
 *
 * 每個查詢在同一次請求內只跑一次（static 快取）。
 */

require_once __DIR__ . '/db_connect.php';

/**
 * 16 型人格：回傳 key 為小寫代碼的關聯陣列，結構同舊 mbti_types.php。
 */
function get_mbti_types(): array {
    static $cache = null;
    if ($cache !== null) return $cache;

    $conn = db_connect();
    $res = $conn->query(
        "SELECT code, functions, desc_text, detail_text, prev_code, next_code
           FROM mbti_types ORDER BY sort_order"
    );

    $cache = [];
    while ($row = $res->fetch_assoc()) {
        $cache[strtolower($row['code'])] = [
            'functions' => explode(',', $row['functions']),
            'desc'      => $row['desc_text'],
            'detail'    => $row['detail_text'],
            'prev'      => strtolower($row['prev_code']),
            'next'      => strtolower($row['next_code']),
        ];
    }
    return $cache;
}

/**
 * 測驗題庫：回傳依題號排序的索引陣列，結構同舊 questions.php。
 */
function get_questions(): array {
    static $cache = null;
    if ($cache !== null) return $cache;

    $conn = db_connect();
    $res = $conn->query(
        "SELECT id, `function`, `text` FROM questions ORDER BY id"
    );

    $cache = [];
    while ($row = $res->fetch_assoc()) {
        $cache[] = [
            'id'       => (int)$row['id'],
            'function' => $row['function'],
            'text'     => $row['text'],
        ];
    }
    return $cache;
}

/**
 * 刻板印象標籤：回傳 key 為小寫代碼的關聯陣列，
 * 每型一個標籤陣列，結構同舊 stereotypes.php。
 */
function get_stereotypes(): array {
    static $cache = null;
    if ($cache !== null) return $cache;

    $conn = db_connect();
    $res = $conn->query(
        "SELECT type_code, label, fn, truth
           FROM stereotypes ORDER BY type_code, sort_order"
    );

    $cache = [];
    while ($row = $res->fetch_assoc()) {
        $key = strtolower($row['type_code']);
        $cache[$key][] = [
            'label' => $row['label'],
            'fn'    => $row['fn'],
            'truth' => $row['truth'],
        ];
    }
    return $cache;
}
