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
        <a href="ti.php" class="card ti"><h3>Ti</h3><p>內向思考</p></a>
        <a href="te.php" class="card te"><h3>Te</h3><p>外向思考</p></a>
    </div>

    <div class="function-pair">
        <a href="si.php" class="card si"><h3>Si</h3><p>內向實感</p></a>
        <a href="se.php" class="card se"><h3>Se</h3><p>外向實感</p></a>
    </div>

    <div class="function-pair">
        <a href="ni.php" class="card ni"><h3>Ni</h3><p>內向直覺</p></a>
        <a href="ne.php" class="card ne"><h3>Ne</h3><p>外向直覺</p></a>
    </div>

    <div class="function-pair">
        <a href="fi.php" class="card fi"><h3>Fi</h3><p>內向情感</p></a>
        <a href="fe.php" class="card fe"><h3>Fe</h3><p>外向情感</p></a>
    </div>
</div>
</main>

</body>
</html>