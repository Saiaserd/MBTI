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

<div id="function-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        
        <div class="modal-body">
            <div class="modal-left-section">
                <h1 id="modal-title"></h1>
                <div class="modal-decoration">
                    <span>💡</span>
                </div>
            </div>

            <div class="modal-right-section">
                <div id="modal-desc-box" class="intro-text-box">
                    <h3>功能詳解</h3>
                    <p id="modal-content"></p>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. 資料庫與顏色對照
    const introData = {
        "Ti": "內向思考 (Ti)：追求邏輯的精確性。這類人喜歡拆解事物，了解其內在運作原理。",
        "Te": "外向思考 (Te)：強調效率、結果與客觀事實。擅長組織外部環境，制定計畫。",
        "Si": "內向實感 (Si)：重視過去的經驗、細節與穩定性。",
        "Se": "外向實感 (Se)：全然專注於當下的感官體驗。反應快、行動力強。",
        "Ni": "內向直覺 (Ni)：擅長洞察模式與預測未來趨勢。常有深刻的願景。",
        "Ne": "外向直覺 (Ne)：喜歡探索各種可能性與連結。點子多、思維跳躍。",
        "Fi": "內向情感 (Fi)：基於內在價值觀做決策。非常在乎真實性與信念。",
        "Fe": "外向情感 (Fe)：強調群體和諧與他人情緒。擅長建立連結。"
    };

    const colorMap = {
        "Ti": "#3498db", "Te": "#3498db",
        "Si": "#2ecc71", "Se": "#2ecc71",
        "Ni": "#9b59b6", "Ne": "#9b59b6",
        "Fi": "#e74c3c", "Fe": "#e74c3c"
    };

    // 2. 抓取彈窗相關 DOM 元素
    const modal = document.getElementById('function-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-content');
    const modalDescBox = document.getElementById('modal-desc-box'); 
    const closeBtn = document.querySelector('.close-modal');

    // 3. 點擊卡片開啟彈窗邏輯
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            if (introData[type]) {
                // 填入文字與變換顏色
                modalTitle.innerText = type;
                modalContent.innerText = introData[type];
                
                const themeColor = colorMap[type];
                modalTitle.style.color = themeColor;
                if (modalDescBox) {
                    modalDescBox.style.borderLeftColor = themeColor;
                }

                // 顯示彈窗
                modal.style.display = 'flex'; 
            }
        });
    });

    // 4. 關閉彈窗邏輯
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>

</body>
</html>