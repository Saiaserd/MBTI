<?php
/**
 * 聊天紀錄頁的 HTML 模板
 * 由 log.php 載入，需要的變數：
 *   $logged_in    → 是否登入
 *   $log_sessions → 對話串陣列，每筆是 ['session_id', 'started_at', 'messages']
 *
 * 每串對話可以點 header 收合展開，旁邊有「繼續對話」「刪除」兩個按鈕。
 */
?>
<div class="log-container">
    <h2>聊天紀錄</h2>

    <?php if (!$logged_in): ?>
        <!-- 未登入時的提示 -->
        <p class="log-hint">請先<a href="user.php">登入</a>才能查看紀錄。</p>

    <?php elseif (empty($log_sessions)): ?>
        <!-- 登入但沒對話過 -->
        <p class="log-hint">還沒有任何聊天紀錄。</p>

    <?php else: ?>
        <?php foreach ($log_sessions as $idx => $session):
            $sid = $session['session_id'];
        ?>
        <div class="log-session">
            <!-- 對話標題列：點擊收合，行內按鈕 stopPropagation 避免觸發收合 -->
            <div class="log-session-header" onclick="toggleSession('<?= htmlspecialchars($sid) ?>')">
                <span>對話 #<?= $idx + 1 ?></span>
                <span class="log-session-time"><?= htmlspecialchars($session['started_at']) ?></span>
                <a href="chat.php?session_id=<?= urlencode($sid) ?>" class="btn-continue" onclick="event.stopPropagation()">繼續對話</a>
                <form method="POST" action="api/delete_log.php" onclick="event.stopPropagation()" onsubmit="return confirm('確定要刪除這筆對話？')">
                    <input type="hidden" name="session_id" value="<?= htmlspecialchars($sid) ?>">
                    <button type="submit" class="btn-delete">刪除</button>
                </form>
                <span class="log-toggle-icon">▼</span>
            </div>

            <!-- 訊息內容（一對對的「你」/「AI」泡泡） -->
            <div class="log-session-body" id="session-<?= htmlspecialchars($sid) ?>">
                <?php foreach ($session['messages'] as $msg): ?>
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
    <?php endif; ?>
</div>

<script src="assets/js/log.js"></script>
