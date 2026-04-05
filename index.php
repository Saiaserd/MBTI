<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>主頁</title>
    <link rel="stylesheet" href="css/sidebarCSS.css">
    <link rel="stylesheet" href="css/indexCSS.css">
</head>
<body>

    <?php include 'sidebar.html'; ?>

    <main class="main-content">
    <div class="about-header">
        <h1>八維介紹</h1>
    </div>

    <div class="grid-container">
    <div class="function-pair">
        <a href="javascript:void(0)" class="card ti" data-type="Ti"><h3>Ti</h3><p>內向思考</p></a>
        <a href="javascript:void(0)" class="card te" data-type="Te"><h3>Te</h3><p>外向思考</p></a>
    </div>

    <div class="function-pair">
        <a href="javascript:void(0)" class="card si" data-type="Si"><h3>Si</h3><p>內向實感</p></a>
        <a href="javascript:void(0)" class="card se" data-type="Se"><h3>Se</h3><p>外向實感</p></a>
    </div>

    <div class="function-pair">
        <a href="javascript:void(0)" class="card ni" data-type="Ni"><h3>Ni</h3><p>內向直覺</p></a>
        <a href="javascript:void(0)" class="card ne" data-type="Ne"><h3>Ne</h3><p>外向直覺</p></a>
    </div>

    <div class="function-pair">
        <a href="javascript:void(0)" class="card fi" data-type="Fi"><h3>Fi</h3><p>內向情感</p></a>
        <a href="javascript:void(0)" class="card fe" data-type="Fe"><h3>Fe</h3><p>外向情感</p></a>
    </div>
</div>

<div id="function-info-box" style="display: none; margin: 40px auto; max-width: 800px; padding: 25px; background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-left: 8px solid #007bff;">
    <h2 id="info-title" style="margin-top: 0; color: #333;"></h2>
    <p id="info-content" style="line-height: 1.8; color: #555; font-size: 1.1em;"></p>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const introData = {
        "Ti": "內向思考 (Ti)：追求邏輯的精確性。這類人喜歡拆解事物，了解其內在運作原理，並建立自己的邏輯架構。",
        "Te": "外向思考 (Te)：強調效率、結果與客觀事實。擅長組織外部環境，制定計畫並高效執行。",
        "Si": "內向實感 (Si)：重視過去的經驗、細節與穩定性。傾向於依賴已知的紀錄與傳統來處理當前事務。",
        "Se": "外向實感 (Se)：全然專注於當下的感官體驗。反應快、行動力強，喜歡冒險並與物理世界互動。",
        "Ni": "內向直覺 (Ni)：擅長洞察模式與預測未來趨勢。常有深刻的願景，並能看到事物背後的隱藏含義。",
        "Ne": "外向直覺 (Ne)：喜歡探索各種可能性與連結。點子多、思維跳躍，能從一個概念聯想到多個點子。",
        "Fi": "內向情感 (Fi)：基於內在價值觀做決策。非常在乎真實性與個人信念，不輕易隨波逐流。",
        "Fe": "外向情感 (Fe)：強調群體和諧與他人情緒。擅長建立連結、照顧他人需求並維繫社會規範。"
    };

    const cards = document.querySelectorAll('.card');
    const infoBox = document.getElementById('function-info-box');
    const infoTitle = document.getElementById('info-title');
    const infoContent = document.getElementById('info-content');

    cards.forEach(card => {
        card.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            
            if (introData[type]) {
                infoTitle.innerText = type;
                infoContent.innerText = introData[type];
                infoBox.style.display = 'block'; // 顯示框框
                
                // 平滑滾動到文字區塊
                infoBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
});
</script>

</body>
</html>