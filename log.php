<?php
session_start();
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>聊天紀錄</title>
    <link rel="stylesheet" href="css/sidebarCSS.css">
    <link rel="stylesheet" href="css/logCSS.css">
</head>
<body>

    <?php include 'sidebar.html'; ?>

    <main class="main-content">
        <div class="log-container">
            <h2>聊天紀錄</h2>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <p class="log-hint">請先<a href="user.php">登入</a>才能查看紀錄。</p>

            <?php else:
                $user_id = $_SESSION['user_id'];

                // 取得所有 session，每個 session 只取第一筆的時間當標題
                $stmt = $conn->prepare("
                    SELECT session_id, MIN(created_at) as started_at
                    FROM chat_logs
                    WHERE user_id = ?
                    GROUP BY session_id
                    ORDER BY started_at DESC
                ");
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $sessions = $stmt->get_result();

                if ($sessions->num_rows === 0):
            ?>
                <p class="log-hint">還沒有任何聊天紀錄。</p>

            <?php else: $sessionCount = 0; while ($session = $sessions->fetch_assoc()):
                $sessionCount++;
                $sid = $session['session_id'];
                $started_at = $session['started_at'];

                // 取這個 session 的所有對話
                $stmt2 = $conn->prepare("SELECT user_message, ai_response FROM chat_logs WHERE user_id = ? AND session_id = ? ORDER BY created_at ASC");
                $stmt2->bind_param("ss", $user_id, $sid);
                $stmt2->execute();
                $messages = $stmt2->get_result();
            ?>
                <div class="log-session">
                    <div class="log-session-header" onclick="toggleSession('<?= $sid ?>')">
                        <span>對話 #<?= $sessionCount ?></span>
                        <span class="log-session-time"><?= htmlspecialchars($started_at) ?></span>
                        <a href="chat.php?session_id=<?= urlencode($sid) ?>" class="btn-continue" onclick="event.stopPropagation()">繼續對話</a>
                        <form method="POST" action="delete_log.php" onclick="event.stopPropagation()" onsubmit="return confirm('確定要刪除這筆對話？')">
                            <input type="hidden" name="session_id" value="<?= htmlspecialchars($sid) ?>">
                            <button type="submit" class="btn-delete">刪除</button>
                        </form>
                        <span class="log-toggle-icon">▼</span>
                    </div>
                    <div class="log-session-body" id="session-<?= $sid ?>">
                        <?php while ($msg = $messages->fetch_assoc()): ?>
                            <div class="log-bubble log-user">
                                <span>你</span>
                                <p><?= htmlspecialchars($msg['user_message']) ?></p>
                            </div>
                            <div class="log-bubble log-ai">
                                <span>AI</span>
                                <p><?= htmlspecialchars($msg['ai_response']) ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endwhile; endif; endif; ?>
        </div>
    </main>

    <script>
        function toggleSession(sid) {
            const body = document.getElementById('session-' + sid);
            body.classList.toggle('collapsed');
        }
    </script>

</body>
</html>
<?php require_once 'close.php'; ?>
