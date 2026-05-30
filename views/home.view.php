<div class="home-header">
    <h1>關於這個網站</h1>
</div>

<div class="stereotype-cloud-section">
    <p class="cloud-lead">這些是別人隨手貼在 MBTI 上的標籤——</p>
    <h2 class="cloud-title">而我們想帶你看得比標籤更深</h2>

    <div class="word-cloud" aria-label="MBTI 常見刻板印象負面形容文字雲">
        <?php
        // 外界常加在各人格上的刻板、負面形容（僅作「被誤解的標籤」展示用）
        $stereotypes = [
            '卷王', '偏執狂', '面具人', '冷酷無情', '情商低', '杠精',
            '臉皮厚', '愛冒犯', '大忙人', '控制狂', '愛指揮', '白切黑',
            '不真誠', '玻璃心', '拖延症', '愛擺爛', '多愁善感', '內耗',
            '道德綁架', '雙標', '和稀泥', '聖母', '自我中心', '三分鐘熱度',
            '快樂小狗', '老頑固', '死板', '無趣', '爛好人', '討好型',
            '爹味', '愛八卦', '社恐', '冷漠', '宅', 'emo',
            '衝動', '莽撞', '短視', '膚淺', '貪玩', '注意力渙散',
        ];
        // 隨機打散順序並指定大小級距，營造文字雲層次（每次載入略有不同）
        shuffle($stereotypes);
        foreach ($stereotypes as $word):
            $size = random_int(1, 5); // 1 最小 5 最大
        ?>
            <span class="cloud-word size-<?= $size ?>"><?= htmlspecialchars($word) ?></span>
        <?php endforeach; ?>
    </div>

    <p class="cloud-foot">
        標籤很省事，卻解釋不了你為什麼這樣思考。
        <a href="index.php">從八維認知功能開始 →</a>
    </p>
</div>

<div class="cta-section">
    <a href="assessment.php" class="cta-btn">開始八維測驗</a>
    <p class="cta-sub">先做完 40 題測驗，得到你的認知功能報告後，再帶著報告去和 AI 對話，像看健康報告問醫生一樣得到專屬於你的解讀。</p>
</div>
