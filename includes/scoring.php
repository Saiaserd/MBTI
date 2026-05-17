<?php
/**
 * 八維測驗計分邏輯（純函式，不依賴 session 或 DB）
 *
 * 流程：
 *   raw answers (題id => 1~5)
 *     ↓ aggregate_scores()
 *   8 維原始分（Ni=33, Ne=27, ...）
 *     ↓ normalize_scores()
 *   8 維 0~100 分
 *     ↓ derive_mbti_type()
 *   "INTJ" 之類的 4 字代碼
 */

require_once __DIR__ . '/../data/questions.php';

/**
 * 把使用者填答（題目id => 分數）依功能聚合，回傳 8 維原始總分。
 *
 * @param array $answers  ['1' => 4, '2' => 3, ...]
 * @return array          ['Ni' => 33, 'Ne' => 27, 'Si' => 28, ...]
 */
function aggregate_scores(array $answers): array {
    $questions = require __DIR__ . '/../data/questions.php';

    $totals = ['Ni' => 0, 'Ne' => 0, 'Si' => 0, 'Se' => 0,
               'Ti' => 0, 'Te' => 0, 'Fi' => 0, 'Fe' => 0];

    foreach ($questions as $q) {
        $val = $answers[$q['id']] ?? 0;
        $val = max(1, min(5, (int)$val)); // clamp 防呆
        $totals[$q['function']] += $val;
    }
    return $totals;
}

/**
 * 把 8 維原始分正規化成 0~100。
 * 因為各維題數不同（9 題 vs 8 題），最大可能分不同，
 * 所以要除以「該維題數 × 5」才能公平比較。
 */
function normalize_scores(array $raw_totals): array {
    $questions = require __DIR__ . '/../data/questions.php';

    // 算各維題數
    $counts = ['Ni' => 0, 'Ne' => 0, 'Si' => 0, 'Se' => 0,
               'Ti' => 0, 'Te' => 0, 'Fi' => 0, 'Fe' => 0];
    foreach ($questions as $q) {
        $counts[$q['function']]++;
    }

    $normalized = [];
    foreach ($raw_totals as $fn => $raw) {
        $min = $counts[$fn] * 1;   // 最低分（全填 1）
        $max = $counts[$fn] * 5;   // 最高分（全填 5）
        $normalized[$fn] = (int) round(($raw - $min) / ($max - $min) * 100);
    }
    return $normalized;
}

/**
 * 由 8 維分數推導 MBTI 4 字代碼。
 *
 * 規則：
 *   1. 主導 = 最高分功能
 *   2. 輔助 = 與主導「軸線互斥」的最高分功能
 *      - 主導是知覺功能(N/S) → 輔助必為判斷功能(T/F)
 *      - 主導是判斷功能(T/F) → 輔助必為知覺功能(N/S)
 *      - 兩者一定一內向(i)一外向(e)
 *   3. (主導, 輔助) 唯一決定 16 型其中一型
 */
function derive_mbti_type(array $scores): string {
    // 取最高分為主導
    arsort($scores);
    $dominant = array_key_first($scores);

    // 算合法輔助候選
    $is_perceiving = fn($f) => in_array($f[0], ['N', 'S'], true);
    $attitude      = fn($f) => $f[1]; // 'i' or 'e'

    $valid_aux = [];
    foreach ($scores as $fn => $score) {
        if ($fn === $dominant) continue;
        // 軸線互斥（P↔J）+ 內外向互斥
        if ($is_perceiving($fn) !== $is_perceiving($dominant)
            && $attitude($fn) !== $attitude($dominant)) {
            $valid_aux[$fn] = $score;
        }
    }
    arsort($valid_aux);
    $auxiliary = array_key_first($valid_aux);

    // (主導, 輔助) → MBTI 對照表
    $lookup = [
        'Ni|Te' => 'INTJ', 'Ni|Fe' => 'INFJ',
        'Ne|Ti' => 'ENTP', 'Ne|Fi' => 'ENFP',
        'Si|Te' => 'ISTJ', 'Si|Fe' => 'ISFJ',
        'Se|Ti' => 'ESTP', 'Se|Fi' => 'ESFP',
        'Ti|Ne' => 'INTP', 'Ti|Se' => 'ISTP',
        'Te|Ni' => 'ENTJ', 'Te|Si' => 'ESTJ',
        'Fi|Ne' => 'INFP', 'Fi|Se' => 'ISFP',
        'Fe|Ni' => 'ENFJ', 'Fe|Si' => 'ESFJ',
    ];
    return $lookup["{$dominant}|{$auxiliary}"];
}

/**
 * 一鍵跑完整個計分流程：raw answers → 完整報告
 *
 * @return array{
 *   scores: array<string,int>,  // 8 維 0~100
 *   type:   string,              // 4 字代碼，例如 INTJ
 *   stack:  array<int,string>,   // 該類型完整 8 維順序（取自 mbti_types.php）
 *   desc:   string               // 該類型描述
 * }
 */
function build_assessment_report(array $answers): array {
    $raw    = aggregate_scores($answers);
    $scores = normalize_scores($raw);
    $type   = derive_mbti_type($scores);

    $types = require __DIR__ . '/../data/mbti_types.php';
    $key   = strtolower($type);

    return [
        'scores' => $scores,
        'type'   => $type,
        'stack'  => $types[$key]['functions'] ?? [],
        'desc'   => $types[$key]['desc'] ?? '',
    ];
}
