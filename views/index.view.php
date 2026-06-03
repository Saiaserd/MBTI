<?php
/**
 * 八維介紹頁的 HTML 模板
 * 標題與方格文字已全面更新為「打破刻板印象」的深度解析文案
 */
?>
<style>
/* ==========================================================================
   1. 主標題樣式
   ========================================================================== */
.about-header {
    text-align: center; 
    margin-top: 40px;
    margin-bottom: 20px;
}

.about-header h1 {
    font-size: 2.2rem;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 8px;
    letter-spacing: 1px;
}

.about-header .subtitle {
    font-size: 1.1rem;
    color: #666;
    font-weight: 500;
}

/* ==========================================================================
   2. 八維卡片整體容器：4欄 × 2列（完美置中 + 間距加大 + 整體下移）
   ========================================================================== */
.grid-container {
    display: grid !important; 
    grid-template-columns: repeat(4, minmax(140px, 1fr)) !important; 
    gap: 35px 30px !important;            
    padding: 20px;
    max-width: 1200px;    
    margin: 60px auto !important; 
    width: 90%;          
    box-sizing: border-box;
}

/* ==========================================================================
   3. 個別卡片基礎樣式（調整內襯托以容納新文案）
   ========================================================================== */
.card {
    padding: 30px 18px;
    text-align: center;
    border-radius: 14px; 
    background-color: #f8f9fa;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s, background-color 0.2s;
    border-top: 6px solid #ccc; 
    text-decoration: none; 
    color: inherit;        
    display: block;        
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    background-color: #ffffff; 
}

/* 卡片內部文字層級設計 */
.card h3 {
    margin: 0 0 4px 0;
    font-size: 1.9rem;
    font-weight: bold;
}

.card .chinese-name {
    font-size: 0.95rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 12px;
}

.card .stereotype {
    font-size: 0.8rem;
    color: #999;
    margin-bottom: 6px;
    letter-spacing: -0.5px;
}

.card .declaration {
    font-size: 0.85rem;
    color: #444;
    font-weight: 600;
    line-height: 1.4;
}

/* ==========================================================================
   4. 為不同功能組別上色
   ========================================================================== */
.ti, .te { border-top-color: #3498db; } /* 藍色系：思考 */
.si, .se { border-top-color: #2ecc71; } /* 綠色系：實感 */
.ni, .ne { border-top-color: #9b59b6; } /* 紫色系：直覺 */
.fi, .fe { border-top-color: #e74c3c; } /* 紅色系：情感 */


/* ==========================================================================
   5. 彈窗遮罩與內容樣式
   ========================================================================== */
.modal-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.6); 
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000; 
    backdrop-filter: blur(3px); 
}

.modal-content.advanced-modal {
    background: #fff;
    max-width: 700px; 
    width: 90%;
    padding: 40px;    
    border-radius: 20px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: popUp 0.3s ease-out;
}

.close-modal {
    position: absolute;
    top: 15px; right: 20px;
    font-size: 28px;
    cursor: pointer;
    color: #999;
    z-index: 10;
}
.close-modal:hover { color: #333; }

@keyframes popUp {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.modal-simple-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
}

.modal-simple-header h1 {
    font-size: 3.5rem;
    margin: 0;
    font-weight: bold;
}

.modal-icon { font-size: 2.2rem; }

.tab-navigation {
    display: flex;
    gap: 12px;        
    margin-bottom: 28px;
    width: 100%;
    max-width: 90%;   
}

.tab-btn {
    flex: 1;
    padding: 16px 10px;     
    border: none;
    background: #e9ecef;
    color: #495057;
    font-weight: 800;       
    border-radius: 12px;    
    cursor: pointer;
    font-size: 1.15rem;     
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.tab-btn:hover {
    background: #dee2e6;
    transform: translateY(-2px); 
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
}

.intro-text-box {
    background: #f8f9fa;
    padding: 22px;
    border-radius: 12px;
    border-left: 6px solid #3498db; 
    max-height: 400px; 
    overflow-y: auto;  
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.02);
}

#content-subtitle {
    margin-top: 0;
    margin-bottom: 12px;
    font-size: 1.3rem;
    color: #111;
    font-weight: bold;
}

#modal-content {
    white-space: pre-line; 
    line-height: 1.8;      
    text-align: left;      
    color: #444;
}

/* 📱 行動版響應式 RWD */
@media (max-width: 768px) {
    .grid-container {
        grid-template-columns: repeat(2, 1fr) !important; 
        gap: 20px !important;
        margin: 40px auto !important;
    }
    .tab-navigation {
        flex-wrap: wrap;
        gap: 8px;
    }
    .tab-btn {
        flex: calc(25% - 8px); 
        padding: 10px 4px;
        font-size: 0.95rem;
    }
}
</style>

<!-- 🚀 新的大標題與副標題 -->
<div class="about-header">
    <h1>重新認識自己</h1>
    <div class="subtitle">榮格八維深度解析</div>
</div>

<!-- 🚀 方格內容全面更新為深度解析文案 -->
<div class="grid-container">
    <!-- 第一排：內傾功能 (I) -->
    <a href="javascript:void(0)" class="card ni" data-type="Ni">
        <h3>Ni</h3>
        <div class="chinese-name">內傾直覺</div>
        <div class="stereotype">脫離現實？憂思過度？</div>
        <div class="declaration">洞見本質與預見未來難得可貴！</div>
    </a>
    <a href="javascript:void(0)" class="card ti" data-type="Ti">
        <h3>Ti</h3>
        <div class="chinese-name">內傾思考</div>
        <div class="stereotype">冷漠無情？過度挑剔？</div>
        <div class="declaration">執著邏輯自洽只為追尋客觀真相！</div>
    </a>
    <a href="javascript:void(0)" class="card fi" data-type="Fi">
        <h3>Fi</h3>
        <div class="chinese-name">內傾情感</div>
        <div class="stereotype">固執己見？難以相處？</div>
        <div class="declaration">堅守本心與底線從來不是缺陷！</div>
    </a>
    <a href="javascript:void(0)" class="card si" data-type="Si">
        <h3>Si</h3>
        <div class="chinese-name">內傾實感</div>
        <div class="stereotype">死板守舊？畏懼革新？</div>
        <div class="declaration">守護經驗與規律從來不是懦弱！</div>
    </a>

    <!-- 第二排：外傾功能 (E) -->
    <a href="javascript:void(0)" class="card ne" data-type="Ne">
        <h3>Ne</h3>
        <div class="chinese-name">外傾直覺</div>
        <div class="stereotype">思維跳躍？毫無紀律？</div>
        <div class="declaration">無限遐想正是創新的源頭！</div>
    </a>
    <a href="javascript:void(0)" class="card te" data-type="Te">
        <h3>Te</h3>
        <div class="chinese-name">外傾思考</div>
        <div class="stereotype">功利至上？不近人情？</div>
        <div class="declaration">講求效率與目標是負責任的表現！</div>
    </a>
    <a href="javascript:void(0)" class="card fe" data-type="Fe">
        <h3>Fe</h3>
        <div class="chinese-name">外傾情感</div>
        <div class="stereotype">討好他人？丟失自我？</div>
        <div class="declaration">顧全群體和諧亦是溫柔的擔當！</div>
    </a>
    <a href="javascript:void(0)" class="card se" data-type="Se">
        <h3>Se</h3>
        <div class="chinese-name">外傾實感</div>
        <div class="stereotype">貪圖享樂？目光短視？</div>
        <div class="declaration">專注當下也是一種生活智慧！</div>
    </a>
</div>

<!-- 彈窗維持原有機制 -->
<div id="function-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content advanced-modal">
        <span class="close-modal">&times;</span>
        
        <div class="modal-simple-header">
            <h1 id="modal-title">Ti</h1>
            <span class="modal-icon">💡</span>
        </div>

        <div class="tab-navigation">
            <button class="tab-btn" data-pos="1">1位</button>
            <button class="tab-btn" data-pos="2">2位</button>
            <button class="tab-btn" data-pos="3">3位</button>
            <button class="tab-btn" data-pos="4">4位</button>
            <button class="tab-btn" data-pos="5">5位</button>
            <button class="tab-btn" data-pos="6">6位</button>
            <button class="tab-btn" data-pos="7">7位</button>
            <button class="tab-btn" data-pos="8">8位</button>
        </div>
        
        <div id="modal-desc-box" class="intro-text-box">
            <h3 id="content-subtitle">功能詳解</h3>
            <p id="modal-content"></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 這裡維持原先的 JS 邏輯與資料庫，不影響點擊彈窗的功能
    const introData = {
        "Ti": [
            "Ti在1位（ISTP/INTP）：核心功能，追尋邏輯自洽與原理精準，習慣獨立拆解分析，思維冷靜客觀，凡事以內在邏輯體系為判斷標準。",
            "Ti在2位（ESTP/ENTP）：輔助功能，靈活應對現實，用邏輯快速找出問題核心與漏洞，思考敏捷且務實。",
            "Ti在3位（INFJ/ISFJ）：少年功能，平時不明顯，壓力下表現出挑剔與批判性，易陷入理性鑽牛角尖。",
            "Ti在4位（ENFJ/ESFJ）：劣勢功能，對複雜邏輯感到吃力，壓力下懷疑自身不夠理智，思維混亂。",
            "Ti在5位（ENTJ/ESTJ）：陰影功能，潛在意識質疑自身邏輯不夠嚴謹，時常內耗是否判斷草率。",
            "Ti在6位（INTJ/ISTJ）：批判功能，壓力時發出警示，習慣檢視他人方案的邏輯漏洞。",
            "Ti在7位（ESFP/ENFP）：愚者功能，用片面邏輯掩飾真實情緒，講理偏頗且前後矛盾。",
            "Ti在8位（ISFP/INFP）：魔鬼功能，輕視客觀邏輯與系統分析，難以進行獨立理性思辨。"
        ],
        "Te": [
            "Te在1位（ESTJ/ENTJ）：主導功能，目標導向、講求效率，重視規劃與執行，果斷直接。",
            "Te在2位（ISTJ/INTJ）：輔助落實願景，將邏輯與判斷轉為行動，做事務實有條理。",
            "Te在3位（ENFP/ESFP）：少年功能，偶爾展現果斷，壓力下變得急躁強勢，習慣用命令處理問題。",
            "Te在4位（INFP/ISFP）：劣勢功能，不擅長嚴厲決策，壓力下過度自我要求，變得苛刻挑剔。",
            "Te在5位（INTP/ISTP）：陰影功能，質疑自己不夠高效，內耗是否不夠務實。",
            "Te在6位（ENTP/ESTP）：批判功能，提醒注重效率與現實，壓力下顯得冷漠僵硬。",
            "Te在7位（INFJ/ISFJ）：愚者功能，強行控制他人，用理性壓制情感，導致人際緊張。",
            "Te在8位（ENFJ/ESFJ）：魔鬼功能，輕視效率與結構，被動拖延，難以果斷推動事務。"
        ],
        "Si": [
            "Si在1位（ISTJ/ISFJ）：主導功能，重視經驗、規則與穩定，記憶細節、做事踏實，追求安全可靠。",
            "Si在2位（ESTJ/ESFJ）：輔助執行，用經驗建立秩序，善於落實規範、照顧細節。",
            "Si在3位（INFP/INTP）：少年功能，偶爾回歸穩定，壓力下變得謹慎保守，抗拒改變。",
            "Si在4位（ENFP/ENTP）：劣勢功能，不耐繁瑣規則與重複，壓力下過度擔心細節出錯。",
            "Si在5位（ESFP/ESTP）：陰影功能，質疑自己不夠穩重，內疚缺乏計畫，強迫守規卻不自在。",
            "Si在6位（ISFP/ISTP）：批判功能，提醒注意細節與教訓，壓力下過度謹慎、不敢嘗試新事物。",
            "Si在7位（ENFJ/ENTJ）：愚者功能，固執舊經驗拒絕新方法，忽視現實變化。",
            "Si在8位（INTJ/INFJ）：魔鬼功能，輕視日常細節與傳統，容易忽略身體感受。"
        ],
        "Se": [
            "Se在1位（ESFP/ESTP）：主導功能，專注當下感官體驗，反應敏捷、行動力強，享受現實樂趣。",
            "Se在2位（ISFP/ISTP）：輔助價值與邏輯，讓判斷更貼近現實，善於動手操作、觀察環境。",
            "Se在3位（ENFJ/ENTJ）：少年功能，偶爾放鬆享受當下，壓力下變得衝動享樂。",
            "Se在4位（INFJ/INTJ）：劣勢功能，不善處理即時細節，壓力下急躁衝動。",
            "Se在5位（ISTJ/ISFJ）：陰影功能，質疑自己太呆板，內疚不懂享受。",
            "Se在6位（ESTJ/ESFJ）：批判功能 nudge，提醒關注現實變化，壓力下過度在意表面回饋。",
            "Se在7位（INFP/INTP）：愚者功能，易被感官誘惑分心，衝動行事後又後悔。",
            "Se在8位（ENFP/ENTP）：魔鬼功能，輕視當下體驗，過度沉浸想像，脫離現實行動。"
        ],
        "Ni": [
            "Ni在1位（INTJ/INFJ）：主導核心，深度洞察、長期預見，思維收斂聚焦，行動圍繞核心願景。",
            "Ni在2位（ENTJ/ENFJ）：輔助執行，將洞見落地，平衡理想與現實，用遠景驅動團隊。",
            "Ni在3位（ISTP/ISFP）：少年功能，偶有深刻直覺，用於豐富體驗，壓力下易悲觀。",
            "Ni在4位（ESTP/ESFP）：劣勢功能，排斥抽象預測，壓力下易災難化想像、偏執。",
            "Ni在5位（ENFP/ENTP）：陰影功能 nudge，壓力下內在質疑不夠深刻，易引發自我內耗。",
            "Ni在6位（INFP/INTP）：批判功能，壓力下提供隱憂預感，易反思過度。",
            "Ni在7位（ESFJ/ESTJ）：愚者功能，易被誤導、偏執，忽視現實細節。",
            "Ni在8位（ISFJ/ISTJ）：魔鬼功能，難以觸及，易固執經驗、抗拒變化。"
        ],
        "Ne": [
            "Ne在1位（ENFP/ENTP）：主導功能，思維跳躍、聯想豐富，熱愛可能性與創意，對新事物好奇。",
            "Ne在2位（INFP/INTP）：輔助思考與價值判斷，提供多元視角，善於多角度解讀。",
            "Ne在3位('ESTJ/ESFJ'): 少年功能，偶爾擺發點子，壓力下易胡思亂想、過度擔憂壞結果。",
            "Ne在4位（ISTJ/ISFJ）：劣勢功能，不喜歡不確定與變動，壓力下焦慮失控。",
            "Ne在5位（INTJ/INFJ）：陰影功能，質疑自身太狹隘，內耗是否錯失可能。",
            "Ne在6位（ENFJ/ENTJ）：批判功能，提醒別過度追求選項，導致猶豫不決。",
            "Ne在7位（ISFP/ISTP）：愚者功能，隨意聯想偏離現實，易被新奇事物吸引而分心。",
            "Ne在8位（ESFP/ESTP）：魔鬼功能，輕視長遠規劃，只專注當下，忽視未來風險。"
        ],
        "Fi": [
            "Fi在1位（ISFP/INFP）：主導功能，忠於內心價值與真實感受，以個人信念作為判斷核心。",
            "Fi在2位（ESFP/ENFP）：輔助功能，依據內心喜惡做選擇，溫柔且有底線。",
            "Fi在3位（INTJ/ISTJ）：少年功能，平時強勢，壓力下變得敏感脆弱，易固執己見。",
            "Fi在4位（ENTJ/ESTJ）：劣勢功能，不善觸碰深層情感，壓力下用衝動行為掩飾不安。",
            "Fi在5位（ESFJ/ENFJ）：陰影功能，質疑自己過度迎合，內耗是否失去本心。",
            "Fi在6位（INFJ/ISFJ）：批判功能，壓力時過度敏感自我保護，容易受傷封閉。",
            "Fi在7位（ESTP/ENTP）：愚者功能，固執個人感受卻不自知，用情緒否定合理邏輯。",
            "Fi在8位（INTP/ISTP）：魔鬼功能，壓抑個人情感與真實需求，顯得冷酷不近人情。"
        ],
        "Fe": [
            "Fe在1位（ESFJ/ENFJ）：主導功能，以人際和諧為核心，敏於察決氛圍，主動維繫關係。",
            "Fe在2位（ISFJ/INFJ）：輔助功能，判斷與洞見用於關懷他人，讓決策兼顧群體感受。",
            "Fe在3位（ESTP/ENTP）：少年功能，過度在意他人評價，刻意迎合避免衝突。",
            "Fe在4位（ISTP/INTP）：劣勢功能，不善處理人際衝突，壓力下害怕被排斥。",
            "Fe在5位（INFP/ISFP）：陰影功能，內疚忽略他人感受，勉強迎合卻不自然。",
            "Fe在6位（ESFP/ENFP）：批判功能，壓力時容易過度遷就他人，失去自身立場。",
            "Fe在7位('INTJ/ISTJ'): 愚者功能，假裝合群尋求認可，內心抗拒卻表面配合。",
            "Fe在8位（ENTJ/ESTJ）：魔鬼功能，忽視他人情緒，不易察覺人際緊張與矛盾。"
        ]
    };

    const colorMap = {
        "Ti": "#3498db", "Te": "#3498db", "Si": "#2ecc71", "Se": "#2ecc71",
        "Ni": "#9b59b6", "Ne": "#9b59b6", "Fi": "#e74c3c", "Fe": "#e74c3c"
    };

    let currentType = "Ti";
    let currentPos = 1;

    const modal = document.getElementById('function-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-content');
    const modalDescBox = document.getElementById('modal-desc-box');
    const contentSubtitle = document.getElementById('content-subtitle');
    const closeBtn = document.querySelector('.close-modal');
    const tabBtns = document.querySelectorAll('.tab-btn');

    function updateModalDisplay() {
        modalTitle.innerText = currentType;
        contentSubtitle.innerText = `${currentType} 功能詳解 (第 ${currentPos} 位)`;
        modalContent.innerText = introData[currentType][currentPos - 1];

        const themeColor = colorMap[currentType];
        modalTitle.style.color = themeColor;
        if (modalDescBox) modalDescBox.style.borderLeftColor = themeColor;

        tabBtns.forEach(btn => {
            if (parseInt(btn.getAttribute('data-pos')) === currentPos) {
                btn.style.backgroundColor = themeColor;
                btn.style.color = '#fff';
            } else {
                btn.style.backgroundColor = '#e9ecef';
                btn.style.color = '#495057';
            }
        });
    }

    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', function() {
            currentType = this.getAttribute('data-type');
            currentPos = 1;
            updateModalDisplay();
            modal.style.display = 'flex';
        });
    });

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currentPos = parseInt(this.getAttribute('data-pos'));
            updateModalDisplay();
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', () => modal.style.display = 'none');
    }
    window.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });
});
</script>