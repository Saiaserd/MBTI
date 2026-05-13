<?php
/** @var bool  $logged_in */
/** @var array $log_sessions  list of ['session_id'=>..., 'started_at'=>..., 'messages'=>[...]] */
?>
<div class="log-container">
    <h2>聊天紀錄</h2>

    <?php if (!$logged_in): ?>
        <p class="log-hint">請先<a href="user.php">登入</a>才能查看紀錄。</p>
    <?php elseif (empty($log_sessions)): ?>
        <p class="log-hint">還沒有任何聊天紀錄。</p>
    <?php else: ?>
        <?php foreach ($log_sessions as $idx => $session):
            $sid = $session['session_id'];
        ?>
        <div class="log-session">
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
