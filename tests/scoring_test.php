<?php
/**
 * 計分邏輯驗收測試（CLI）
 * 執行： php tests/scoring_test.php
 * 註：題庫與 16 型資料已改存 MySQL，本測試會連 DB（需先 import 內容表）。
 */

require_once __DIR__ . '/../includes/scoring.php';
require_once __DIR__ . '/../includes/content_repo.php';

$pass = 0;
$fail = 0;

function check(string $name, $expected, $actual): void {
    global $pass, $fail;
    $e = is_array($expected) ? implode(' ', $expected) : (string)$expected;
    $a = is_array($actual)   ? implode(' ', $actual)   : (string)$actual;
    if ($e === $a) {
        echo "  ✅ $name\n";
        $pass++;
    } else {
        echo "  ❌ $name\n     期望: $e\n     實得: $a\n";
        $fail++;
    }
}

/* ── 直接用 8 維分數測 stack / type（繞過題目作答） ── */

echo "案例 A（一般情況：分數高 ≠ 排前面）\n";
$A = ['Ni' => 85, 'Ne' => 75, 'Si' => 30, 'Se' => 35,
      'Ti' => 50, 'Te' => 75, 'Fi' => 80, 'Fe' => 40];
$stackA = build_function_stack($A);
check('堆疊', ['Ni','Te','Fi','Se','Ne','Ti','Fe','Si'], $stackA);
check('型別', 'INTJ', stack_to_type($stackA));

echo "案例 B（第 1 位同分，驗證優先序 Fe > Ni）\n";
$B = ['Ni' => 90, 'Ne' => 50, 'Si' => 40, 'Se' => 30,
      'Ti' => 45, 'Te' => 55, 'Fi' => 60, 'Fe' => 90];
$stackB = build_function_stack($B);
check('堆疊', ['Fe','Ni','Se','Ti','Fi','Ne','Si','Te'], $stackB);
check('型別', 'ENFJ', stack_to_type($stackB));

echo "案例 C（第 2 位同分，驗證優先序 Fi > Ti）\n";
$C = ['Ni' => 40, 'Ne' => 30, 'Si' => 35, 'Se' => 88,
      'Ti' => 70, 'Te' => 50, 'Fi' => 70, 'Fe' => 45];
$stackC = build_function_stack($C);
check('堆疊', ['Se','Fi','Te','Ni','Si','Fe','Ti','Ne'], $stackC);
check('型別', 'ESFP', stack_to_type($stackC));

echo "案例 D（計分邊界：×4、滿分100）\n";
// 全填「非常同意」(5) → 每維 = 5*4*5題 = 100
$allFive = [];
foreach (get_questions() as $q) $allFive[$q['id']] = 5;
$scoreHi = aggregate_scores($allFive);
check('全 5 → Ni=100', 100, $scoreHi['Ni']);
check('全 5 → Fe=100', 100, $scoreHi['Fe']);
// 全填「不同意」(1) → 每維 = 1*4*5題 = 20
$allOne = [];
foreach (get_questions() as $q) $allOne[$q['id']] = 1;
$scoreLo = aggregate_scores($allOne);
check('全 1 → Ni=20', 20, $scoreLo['Ni']);

echo "案例 E（題庫結構：40 題、每維 5 題）\n";
$qs = get_questions();
check('總題數 40', 40, count($qs));
$counts = [];
foreach ($qs as $q) $counts[$q['function']] = ($counts[$q['function']] ?? 0) + 1;
check('每維題數皆為 5', '5 5 5 5 5 5 5 5', implode(' ', array_values($counts)));

echo "案例 F（堆疊與 mbti_types 的 functions 完全一致）\n";
// 用各型主導+輔助最高的分數，重建後比對官方堆疊
$types = get_mbti_types();
$mismatch = 0;
foreach ($types as $key => $info) {
    $official = $info['functions'];
    // 給官方第1位最高、第2位次高，其餘遞減，模擬一份會推出該型的分數
    $scores = ['Ni'=>10,'Ne'=>10,'Si'=>10,'Se'=>10,'Ti'=>10,'Te'=>10,'Fi'=>10,'Fe'=>10];
    $scores[$official[0]] = 100;
    $scores[$official[1]] = 90;
    $built = build_function_stack($scores);
    if ($built !== $official) {
        echo "     ❌ $key 期望 ".implode(' ',$official)." 實得 ".implode(' ',$built)."\n";
        $mismatch++;
    }
}
check('16 型堆疊全部一致', 0, $mismatch);

echo "\n────────────────────────\n";
echo "通過 {$pass}，失敗 {$fail}\n";
exit($fail === 0 ? 0 : 1);
