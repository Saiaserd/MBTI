<?php
/**
 * 八維介紹頁的 HTML 模板
 * 8 個卡片 + 一個隱藏的 modal。
 * 點卡片 → JS 把對應的介紹文塞進 modal 並 display:flex。
 * 介紹文資料、顏色配對、開關 modal 邏輯都在 assets/js/index.js。
 */
?>
<div class="about-header">
    <h1>八維介紹</h1>
</div>

<!-- 8 個卡片，兩兩一組（內/外向配對） -->
<!-- data-type 是 JS 用來查介紹文的 key -->
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

<!-- 點卡片才會顯示的 modal，預設 display:none -->
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

<script src="assets/js/index.js"></script>
