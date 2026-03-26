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
                $stmt = $conn->prepare("SELECT user_message, ai_response, created_at FROM chat_logs WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0):
            ?>
                <p class="log-hint">還沒有任何聊天紀錄。</p>
            <?php else: while ($row = $result->fetch_assoc()): ?>
                <div class="log-entry">
                    <p class="log-time"><?= htmlspecialchars($row['created_at']) ?></p>
                    <div class="log-bubble log-user">
                        <span>你</span>
                        <p><?= htmlspecialchars($row['user_message']) ?></p>
                    </div>
                    <div class="log-bubble log-ai">
                        <span>AI</span>
                        <p><?= htmlspecialchars($row['ai_response']) ?></p>
                    </div>
                </div>
            <?php endwhile; endif; endif; ?>
        </div>
    </main>

</body>
</html>
<?php require_once 'close.php'; ?>
