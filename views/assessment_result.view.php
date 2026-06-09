<?php
/**
 * 結果報告 + AI 聊天合併模板（2026-05-17 改版）
 *
 *  需要的變數：
 *    $assessment       → 8 維報告（從 $_SESSION 來）
 *    $chat_session_id  → 本次聊天的 ID
 *    $is_continue      → 固定 false（結果頁不繼續舊對話）
 *    $loaded_history   → 固定空陣列
 *
 *  視覺結構：
 *    [左欄：類型卡 + 8 維分數]  [右欄：認知功能介紹]
 *    ─────────────────────────────────────────
 *    [AI 聊天區（全寬）]
 *    使用者由上往下滾，自然走完「看報告 → 找 AI 諮詢」的動線。
 */

// 8 維中英對照 + 主題色（與 index.js 一致）
$fn_meta = [
    'Ni' => ['name' => '內向直覺', 'color' => '#9b59b6'],
    'Ne' => ['name' => '外向直覺', 'color' => '#9b59b6'],
    'Si' => ['name' => '內向實感', 'color' => '#2ecc71'],
    'Se' => ['name' => '外向實感', 'color' => '#2ecc71'],
    'Ti' => ['name' => '內向思考', 'color' => '#3498db'],
    'Te' => ['name' => '外向思考', 'color' => '#3498db'],
    'Fi' => ['name' => '內向情感', 'color' => '#e74c3c'],
    'Fe' => ['name' => '外向情感', 'color' => '#e74c3c'],
];

// 主導/輔助/第三/劣勢的角色說明
$stack_roles = [
    0 => ['label' => '主導功能 (Dominant)',  'note' => '你最自然、最常使用的核心能力，幾乎無需思考就會發動。'],
    1 => ['label' => '輔助功能 (Auxiliary)', 'note' => '主導的最佳搭檔，協助你與外界互動、平衡主導功能。'],
    2 => ['label' => '第三功能 (Tertiary)',  'note' => '青少年時期才慢慢發展，是壓力下的放鬆出口，也是潛在成長點。'],
    3 => ['label' => '劣勢功能 (Inferior)',  'note' => '你最不擅長、最容易帶來焦慮的領域，但也是最深層的成長動力。'],
];

$scores = $assessment['scores'];
$stack  = $assessment['stack'];
$type   = $assessment['type'];

// 該型的「世界刻板標籤」——讓使用者進報告前先親手撕掉
require_once __DIR__ . '/../includes/content_repo.php';
$all_stereotypes = get_stereotypes();
$stereotypes     = $all_stereotypes[strtolower($type)] ?? [];

// 人格分組 → CSS class（決定類型卡顏色）
$type_group_map = [
    'INTJ' => 'nt', 'INTP' => 'nt', 'ENTJ' => 'nt', 'ENTP' => 'nt',
    'INFJ' => 'nf', 'INFP' => 'nf', 'ENFJ' => 'nf', 'ENFP' => 'nf',
    'ISTJ' => 'sj', 'ISFJ' => 'sj', 'ESTJ' => 'sj', 'ESFJ' => 'sj',
    'ISTP' => 'sp', 'ISFP' => 'sp', 'ESTP' => 'sp', 'ESFP' => 'sp',
];
$hero_class = 'hero-' . ($type_group_map[strtoupper($type)] ?? 'nt');

$is_guest = empty($_SESSION['user_id']);
?>

<div class="result-container">

    <!-- ══════ 第一幕：質疑標籤（蓋在報告上方，走完才露出報告） ══════ -->
    <?php if (!empty($stereotypes)): ?>
    <section class="teardown" id="teardown" data-total="<?= count($stereotypes) ?>">
        <div class="teardown-inner">
            <p class="teardown-eyebrow">測驗完成 · 先別急著看結果</p>
            <h1 class="teardown-title">這是世界貼在「<?= htmlspecialchars($type) ?>」身上的標籤</h1>
            <p class="teardown-sub">
                網路把這個類型簡化成這幾個字。<br>
                逐一看過——<strong>哪些你覺得沾得上邊，哪些根本不是你？</strong>
            </p>

            <!-- 卡片堆：一次一張，使用者判斷「有點像 / 完全不像」 -->
            <div class="judge-deck" id="judgeDeck">
                <?php foreach ($stereotypes as $i => $s): ?>
                    <article class="judge-card" data-idx="<?= $i ?>"
                             data-label="<?= htmlspecialchars($s['label'], ENT_QUOTES) ?>"
                             data-fn="<?= htmlspecialchars($s['fn'], ENT_QUOTES) ?>"
                             data-truth="<?= htmlspecialchars($s['truth'], ENT_QUOTES) ?>">
                        <span class="judge-quote">別人說你是</span>
                        <span class="judge-label">「<?= htmlspecialchars($s['label']) ?>」</span>
                        <span class="judge-hint">這個標籤，像你嗎？</span>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- 判斷按鈕 -->
            <div class="judge-actions" id="judgeActions">
                <button type="button" class="judge-btn judge-rip" id="judgeRip">
                    <span class="judge-btn-icon">✕</span> 完全不是我，撕掉
                </button>
                <button type="button" class="judge-btn judge-keep" id="judgeKeep">
                    <span class="judge-btn-icon">✓</span> 有點像我，留著
                </button>
            </div>

            <div class="teardown-progress">
                <div class="teardown-bar"><div class="teardown-bar-fill" id="teardownFill"></div></div>
                <p class="teardown-count"><span id="teardownDone">0</span> / <?= count($stereotypes) ?> 已判斷</p>
            </div>
        </div>
    </section>

    <!-- 第二幕：撕完後的過場句（依「撕掉/留下」數量動態帶入文字） -->
    <div class="teardown-bridge" id="teardownBridge" aria-hidden="true">
        <p class="bridge-line1">你撕掉了 <span id="bridgeRipped">0</span> 個不屬於你的標籤。</p>
        <p class="bridge-strong">沒有人能用幾個字定義你。</p>
        <p class="bridge-line3">剩下的，讓我們看看它們底下<br>真正在運作的是什麼。</p>
        <button type="button" class="bridge-go" id="bridgeGo">看我的真實報告 →</button>
    </div>
    <?php endif; ?>

    <!-- ══════ 第三幕：真實報告（質疑＋撕完才顯示） ══════ -->
    <div class="report-reveal<?= empty($stereotypes) ? ' revealed' : '' ?>" id="reportReveal">

    <?php if (!empty($stereotypes)): ?>
    <!-- 留下標籤的「翻面重新詮釋」：JS 依使用者判斷結果填入 -->
    <section class="reframe-section" id="reframeSection" hidden>
        <h2 class="reframe-title">你留下的標籤，其實是這樣運作的</h2>
        <p class="section-hint reframe-sub" id="reframeSub"></p>
        <div class="reframe-list" id="reframeList"></div>
        <p class="reframe-note">
            這些不是缺陷，是你認知功能的「副作用」。看懂機制，標籤就再也綁不住你。
        </p>
    </section>
    <?php endif; ?>

    <!-- ────── 報告主體：左欄(類型+分數) + 右欄(功能介紹) ────── -->
    <div class="result-layout">
        <div class="result-left">
            <!-- 人格標籤（類型卡） -->
            <header class="result-hero <?= $hero_class ?>">
                <p class="hero-eyebrow">你的人格類型</p>
                <h1 class="hero-type"><?= htmlspecialchars($type) ?></h1>
                <p class="hero-desc"><?= htmlspecialchars($assessment['desc']) ?></p>
                <p class="hero-time">測驗時間：<?= htmlspecialchars($assessment['created_at']) ?></p>
                <a href="#chat" class="hero-jump">跳到 AI 諮詢 ↓</a>
            </header>

            <!-- 8 維分數長條：依人格認知功能順序（$stack 1~8 位）排列 -->
            <section class="score-section">
                <h2>八維認知功能分數</h2>
                <p class="section-hint">由你的人格 1~8 位順序排列，數字為本次填答計算後的分數（0~100）。</p>
                <div class="score-list">
                    <?php foreach ($stack as $i => $fn):
                        $meta  = $fn_meta[$fn] ?? null;
                        $score = $scores[$fn] ?? 0;
                        if (!$meta) continue;
                    ?>
                        <div class="score-row">
                            <div class="score-label">
                                <span class="fn-rank">第 <?= $i + 1 ?> 位</span>
                                <span class="fn-code" style="background:<?= $meta['color'] ?>"><?= $fn ?></span>
                                <span class="fn-name"><?= $meta['name'] ?></span>
                            </div>
                            <div class="score-bar-wrap">
                                <div class="score-bar"
                                     style="width:<?= $score ?>%; background:<?= $meta['color'] ?>;"></div>
                            </div>
                            <div class="score-value"><?= $score ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <!-- 右欄：認知功能介紹（前四位的角色說明） -->
        <aside class="result-right">
            <section class="stack-section">
                <h2>認知功能介紹</h2>
                <p class="section-hint">榮格八維理論認為，前四個功能形成「四面體」，定義了你性格的核心運作方式。</p>
                <div class="stack-list">
                    <?php for ($i = 0; $i < 4; $i++):
                        $fn = $stack[$i] ?? null;
                        if (!$fn) continue;
                        $meta = $fn_meta[$fn];
                        $role = $stack_roles[$i];
                    ?>
                        <article class="stack-card" style="border-left: 4px solid <?= $meta['color'] ?>;">
                            <header class="stack-card-head">
                                <span class="stack-pos">第 <?= $i + 1 ?> 位</span>
                                <span class="stack-fn" style="color: <?= $meta['color'] ?>"><?= $fn ?></span>
                            </header>
                            <h3 class="stack-role"><?= htmlspecialchars($role['label']) ?></h3>
                            <p class="stack-fn-name"><?= htmlspecialchars($meta['name']) ?></p>
                            <p class="stack-note"><?= htmlspecialchars($role['note']) ?></p>
                        </article>
                    <?php endfor; ?>
                </div>
            </section>
        </aside>
    </div>

    <!-- ────── AI 聊天區（取代原本獨立的 chat.php） ────── -->
    <section class="chat-section" id="chat">
        <h2>帶著你的報告找 AI 諮詢</h2>
        <p class="section-hint">
            AI 已收到你上方的 8 維分數與類型，請開始提問。
            <?php if ($is_guest): ?>
                <br>
                <span class="guest-notice">
                    ⚠️ 你目前是訪客身分：報告與對話只存在這次瀏覽中，關閉瀏覽器後就會消失，且本站不會保留任何紀錄。
                    對話內容仍會送往 Google Gemini 以產生回應。
                    需要長期保存可<a href="user.php">登入或註冊</a>。
                </span>
            <?php endif; ?>
        </p>

        <!-- 聊天主體：複用 chat.view.php 的版型（用全域 CHAT_BOOT 傳資料給 chat.js） -->
        <div class="chat-container">
            <div class="chat-messages" id="chatMessages">
                <div class="message-welcome">
                    <p>👋 我是你的 MBTI 諮詢助手，已收到你的 <?= htmlspecialchars($type) ?> 報告。試著問我：</p>
                    <ul class="example-questions">
                        <li>「我這個類型的主要優勢和盲點是什麼？」</li>
                        <li>「為什麼我 Ti / Fi / Ni 分數那麼高？」</li>
                        <li>「我這型在感情/職場容易遇到什麼挑戰？」</li>
                    </ul>
                </div>
            </div>

            <div class="input-box-wrapper">
                <textarea class="chat-input" id="chatInput" placeholder="輸入你想諮詢的問題……" rows="1"></textarea>
                <div class="input-actions">
                    <button class="send-btn" id="sendBtn">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- 重做測驗的小提示 -->
    <p class="redo-link"><a href="assessment.php">想再做一次測驗？</a></p>
    </div><!-- /.report-reveal -->
</div>

<script>
    // 把 PHP 端的聊天資料丟給 chat.js
    window.CHAT_BOOT = {
        sessionId:    <?= json_encode($chat_session_id) ?>,
        assessmentId: <?= json_encode($assessment['id'] ?? null) ?>,
        isContinue:   <?= $is_continue ? 'true' : 'false' ?>,
        history:      <?= json_encode($loaded_history, JSON_UNESCAPED_UNICODE) ?>
    };
</script>
<script src="assets/js/teardown.js"></script>
<script src="assets/js/chat.js"></script>
