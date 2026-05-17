<?php
/**
 * 紀錄頁模板（按報告分組）
 *
 *  需要的變數：
 *    $logged_in     → 是否登入
 *    $assessments   → 報告陣列（DESC by created_at），每筆含 chat_groups[]
 *    $orphan_chats  → 沒綁報告的聊天串（舊資料用）
 */

// 8 維中英對照 + 主題色（沿用結果頁配色）
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

// 預覽用：取對話的第一句使用者訊息當摘要
function chat_preview(array $messages): string {
    if (empty($messages)) return '（空對話）';
    $first = $messages[0]['user_message'] ?? '';
    $first = mb_substr($first, 0, 40);
    return $first ? $first . (mb_strlen($messages[0]['user_message']) > 40 ? '…' : '') : '（空對話）';
}
?>
<div class="log-container">
    <h2>紀錄</h2>

    <?php if (!$logged_in): ?>
        <p class="log-hint">請先<a href="user.php">登入</a>才能查看紀錄。訪客的測驗結果跟對話都不會被保留。</p>

    <?php elseif (empty($assessments) && empty($orphan_chats)): ?>
        <p class="log-hint">
            你還沒有任何紀錄。<a href="assessment.php">先去做八維測驗</a>，做完之後可以跟 AI 聊聊。
        </p>

    <?php else: ?>
        <?php /* ───────── 報告分組 ───────── */ ?>
        <?php foreach ($assessments as $a):
            $aid    = $a['id'];
            $scores = $a['scores'];
            // 排序：最高分到最低分
            arsort($scores);
        ?>
        <article class="report-card">
            <header class="report-card-head">
                <div class="report-type">
                    <span class="type-badge"><?= htmlspecialchars($a['mbti_type']) ?></span>
                    <span class="report-date"><?= htmlspecialchars($a['created_at']) ?></span>
                </div>
                <div class="report-actions">
                    <a class="btn-view-report"
                       href="assessment_result.php?assessment_id=<?= $aid ?>">
                        查看完整報告
                    </a>
                    <form method="POST" action="api/delete_assessment.php"
                          onsubmit="return confirm('要刪除這份報告嗎？\n底下 <?= count($a['chat_groups']) ?> 串對話也會一起被刪掉，無法復原。')">
                        <input type="hidden" name="assessment_id" value="<?= $aid ?>">
                        <button type="submit" class="btn-delete-report">刪除報告</button>
                    </form>
                </div>
            </header>

            <!-- 8 維分數小條（簡化版，紀錄頁不需要全部展開） -->
            <div class="mini-scores">
                <?php foreach ($scores as $fn => $sc):
                    $meta = $fn_meta[$fn];
                ?>
                    <div class="mini-score" title="<?= $meta['name'] ?>">
                        <span class="mini-fn" style="background:<?= $meta['color'] ?>"><?= $fn ?></span>
                        <div class="mini-bar-wrap">
                            <div class="mini-bar" style="width:<?= $sc ?>%; background:<?= $meta['color'] ?>;"></div>
                        </div>
                        <span class="mini-val"><?= $sc ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- 該報告下的聊天串 -->
            <?php if (empty($a['chat_groups'])): ?>
                <p class="no-chat-hint">這份報告還沒有衍生對話。</p>
            <?php else: ?>
                <div class="chat-list">
                    <p class="chat-list-label">基於這份報告的對話（共 <?= count($a['chat_groups']) ?> 串）</p>
                    <?php foreach ($a['chat_groups'] as $i => $sess):
                        $sid = $sess['session_id'];
                    ?>
                        <div class="chat-item">
                            <div class="chat-item-main"
                                 onclick="toggleSession('<?= htmlspecialchars($sid) ?>')">
                                <span class="chat-item-title">對話 #<?= $i + 1 ?></span>
                                <span class="chat-item-preview"><?= htmlspecialchars(chat_preview($sess['messages'])) ?></span>
                                <span class="chat-item-time"><?= htmlspecialchars($sess['started_at']) ?></span>
                                <span class="log-toggle-icon">▼</span>
                            </div>
                            <div class="chat-item-actions">
                                <a href="assessment_result.php?session_id=<?= urlencode($sid) ?>"
                                   class="btn-continue">繼續對話</a>
                                <form method="POST" action="api/delete_log.php"
                                      onsubmit="return confirm('確定要刪除這串對話？')">
                                    <input type="hidden" name="session_id" value="<?= htmlspecialchars($sid) ?>">
                                    <button type="submit" class="btn-delete">刪除</button>
                                </form>
                            </div>
                            <!-- 收合的訊息內容 -->
                            <div class="chat-item-body collapsed" id="session-<?= htmlspecialchars($sid) ?>">
                                <?php foreach ($sess['messages'] as $msg): ?>
                                    <div class="log-bubble log-user">
                                        <span>你</span>
                                        <p><?= htmlspecialchars($msg['user_message']) ?></p>
                                    </div>
                                    <div class="log-bubble log-ai">
                                        <span>AI</span>
                                        <p><?= htmlspecialchars($msg['ai_response']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>

        <?php /* ───────── 沒綁報告的舊資料 ───────── */ ?>
        <?php if (!empty($orphan_chats)): ?>
        <section class="orphan-section">
            <h3>未綁定報告的舊對話</h3>
            <p class="orphan-hint">這些對話是在本站加入「測驗報告」功能之前進行的，所以沒有對應的報告。</p>
            <?php foreach ($orphan_chats as $i => $sess):
                $sid = $sess['session_id'];
            ?>
                <div class="chat-item">
                    <div class="chat-item-main"
                         onclick="toggleSession('<?= htmlspecialchars($sid) ?>')">
                        <span class="chat-item-title">舊對話 #<?= $i + 1 ?></span>
                        <span class="chat-item-preview"><?= htmlspecialchars(chat_preview($sess['messages'])) ?></span>
                        <span class="chat-item-time"><?= htmlspecialchars($sess['started_at']) ?></span>
                        <span class="log-toggle-icon">▼</span>
                    </div>
                    <div class="chat-item-actions">
                        <form method="POST" action="api/delete_log.php"
                              onsubmit="return confirm('確定要刪除這串對話？')">
                            <input type="hidden" name="session_id" value="<?= htmlspecialchars($sid) ?>">
                            <button type="submit" class="btn-delete">刪除</button>
                        </form>
                    </div>
                    <div class="chat-item-body collapsed" id="session-<?= htmlspecialchars($sid) ?>">
                        <?php foreach ($sess['messages'] as $msg): ?>
                            <div class="log-bubble log-user">
                                <span>你</span>
                                <p><?= htmlspecialchars($msg['user_message']) ?></p>
                            </div>
                            <div class="log-bubble log-ai">
                                <span>AI</span>
                                <p><?= htmlspecialchars($msg['ai_response']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="assets/js/log.js"></script>
