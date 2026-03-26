<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>問卷頁面</title>
    <link rel="stylesheet" href="css/sidebarCSS.css">
    <link rel="stylesheet" href="css/surveyCSS.css">
</head>
<body>

    <?php include 'sidebar.html'; ?>

    <main class="main-content">
    <div class="feedback-container">
        <div class="feedback-card">
            <h3>意見反饋問卷</h3>
            <p class="subtitle">您的建議是我們進步的動力，請花一分鐘告訴我們您的感受。</p>
            
            <form action="submit_feedback.php" method="POST">
                <div class="question-group">
                    <label>您對本次 MBTI 分析準確度的評分？</label>
                    <div class="rating-stars">
                        <input type="radio" name="rating" value="5" id="r5"><label for="r5">★</label>
                        <input type="radio" name="rating" value="4" id="r4"><label for="r4">★</label>
                        <input type="radio" name="rating" value="3" id="r3"><label for="r3">★</label>
                        <input type="radio" name="rating" value="2" id="r2"><label for="r2">★</label>
                        <input type="radio" name="rating" value="1" id="r1"><label for="r1">★</label>
                    </div>
                </div>

                <div class="question-group">
                    <label>有什麼想對我們說的嗎？</label>
                    <textarea name="comments" placeholder="請輸入您的建議或遇到的問題..." rows="5"></textarea>
                </div>

                <button type="submit" class="submit-btn">送出問卷</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>