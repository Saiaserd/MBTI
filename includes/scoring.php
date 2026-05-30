<?php
/**
 * 八維測驗計分邏輯（純函式，不依賴 session 或 DB）
 *
 * 流程：
 *   raw answers (題id => 1~5)
 *     ↓ aggregate_scores()      每題作答值 ×4 後加總
 *   8 維分數（每維 5 題 → 20~100）
 *     ↓ build_function_stack()  推導完整 8 維堆疊順序
 *   ['Ni','Te','Fi','Se','Ne','Ti','Fe','Si'] 之類
 *     ↓ stack_to_type()         由 (1位,2位) 查出 "INTJ"
 *
 * 規格重點：
 *   - 分數高低「只」決定第 1、2 位；第 3~8 位由配對與翻轉規則推出。
 *   - 同分時依固定優先序決定勝負（見 TIE_BREAK_ORDER），
 *     不可依賴 arsort 等受插入順序影響的排序。
 */

require_once __DIR__ . '/../data/questions.php';

/** 八個功能的固定屬性表 ───────────────────────────── */

// 感知功能 (P)：N/S 系；其餘為判斷功能 (J)：T/F 系
const PERCEIVING_FNS = ['Ni', 'Ne', 'Si', 'Se'];

// 完全相反配對（軸線兩端）
const OPPOSITE_PAIR = [
    'Ni' => 'Se', 'Se' => 'Ni',
    'Ne' => 'Si', 'Si' => 'Ne',
    'Ti' => 'Fe', 'Fe' => 'Ti',
    'Te' => 'Fi', 'Fi' => 'Te',
];

// 翻轉內外傾（同功能換態度 i↔e）
const FLIP_ATTITUDE = [
    'Ni' => 'Ne', 'Ne' => 'Ni',
    'Si' => 'Se', 'Se' => 'Si',
    'Ti' => 'Te', 'Te' => 'Ti',
    'Fi' => 'Fe', 'Fe' => 'Fi',
];

// 同分優先順序：越前面＝越優先（視為較高分）
const TIE_BREAK_ORDER = ['Si', 'Se', 'Fe', 'Fi', 'Ti', 'Te', 'Ne', 'Ni'];

/** 工具：是否為感知功能 (N/S) */
function is_perceiving(string $fn): bool {
    return in_array($fn, PERCEIVING_FNS, true);
}

/** 工具：內外傾態度，回傳 'i' 或 'e' */
function fn_attitude(string $fn): string {
    return $fn[1];
}

/**
 * 在候選功能中挑「分數最高」者，同分時依 TIE_BREAK_ORDER 決定。
 *
 * @param array $candidates  ['Te' => 75, 'Fe' => 75, ...]
 * @return string            勝出的功能代碼
 */
function pick_highest(array $candidates): string {
    $best = null;
    $best_score = null;
    $best_rank = null;
    foreach ($candidates as $fn => $score) {
        $rank = array_search($fn, TIE_BREAK_ORDER, true); // 越小越優先
        if ($best === null
            || $score > $best_score
            || ($score === $best_score && $rank < $best_rank)) {
            $best = $fn;
            $best_score = $score;
            $best_rank = $rank;
        }
    }
    return $best;
}

/**
 * 把使用者填答（題目id => 1~5）依功能聚合。
 * 每題作答值先 clamp 到 1~5，再 ×4 加總。
 *
 * @param array $answers  ['1' => 4, '2' => 3, ...]
 * @return array          ['Ni' => 80, 'Ne' => 60, ...]（每維 20~100）
 */
function aggregate_scores(array $answers): array {
    $questions = require __DIR__ . '/../data/questions.php';

    $totals = ['Ni' => 0, 'Ne' => 0, 'Si' => 0, 'Se' => 0,
               'Ti' => 0, 'Te' => 0, 'Fi' => 0, 'Fe' => 0];

    foreach ($questions as $q) {
        $val = $answers[$q['id']] ?? 0;
        $val = max(1, min(5, (int)$val)); // clamp 防呆
        $totals[$q['function']] += $val * 4;
    }
    return $totals;
}

/**
 * 由 8 維分數推導完整 8 維功能堆疊。
 *
 * 1. 第 1 位（主導）= 分數最高的功能
 * 2. 第 2 位（輔助）= 與主導「相反分類(P↔J) 且 相反內外傾(i↔e)」中分數較高者
 * 3. 第 3 位 = 第 2 位的完全相反配對
 * 4. 第 4 位 = 第 1 位的完全相反配對
 * 5. 第 5~8 位 = 第 1~4 位逐一翻轉內外傾
 *
 * @param array $scores  8 維分數
 * @return array         8 個功能代碼，依 1~8 位排列
 */
function build_function_stack(array $scores): array {
    // 第 1 位：全體取最高
    $dominant = pick_highest($scores);

    // 第 2 位：相反分類 + 相反態度的候選（必為 2 個）中取最高
    $aux_candidates = [];
    foreach ($scores as $fn => $score) {
        if ($fn === $dominant) continue;
        if (is_perceiving($fn) !== is_perceiving($dominant)
            && fn_attitude($fn) !== fn_attitude($dominant)) {
            $aux_candidates[$fn] = $score;
        }
    }
    $auxiliary = pick_highest($aux_candidates);

    // 第 3、4 位：配對的完全相反功能
    $third  = OPPOSITE_PAIR[$auxiliary];
    $fourth = OPPOSITE_PAIR[$dominant];

    $top4 = [$dominant, $auxiliary, $third, $fourth];

    // 第 5~8 位：前 4 位逐一翻轉內外傾
    $bottom4 = array_map(fn($fn) => FLIP_ATTITUDE[$fn], $top4);

    return array_merge($top4, $bottom4);
}

/**
 * 由功能堆疊的 (第1位, 第2位) 推出 MBTI 4 字代碼。
 */
function stack_to_type(array $stack): string {
    $dominant  = $stack[0];
    $auxiliary = $stack[1];

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
    return $lookup["{$dominant}|{$auxiliary}"] ?? '';
}

/**
 * 一鍵跑完整個計分流程：raw answers → 完整報告
 *
 * @return array{
 *   scores: array<string,int>,  // 8 維分數（20~100）
 *   type:   string,              // 4 字代碼，例如 INTJ
 *   stack:  array<int,string>,   // 由作答推導出的完整 8 維順序
 *   desc:   string               // 該類型描述
 * }
 */
function build_assessment_report(array $answers): array {
    $scores = aggregate_scores($answers);
    $stack  = build_function_stack($scores);
    $type   = stack_to_type($stack);

    $types = require __DIR__ . '/../data/mbti_types.php';
    $key   = strtolower($type);

    return [
        'scores' => $scores,
        'type'   => $type,
        'stack'  => $stack,
        'desc'   => $types[$key]['desc'] ?? '',
    ];
}
