<?php
/**
 * 共用頁尾
 * 對應 header.php，關閉 <main>、<body>、<html>。
 * 進入頁可在 require 前設定 $page_extra_body，內容會在 </main> 之後印出
 * （例如要放整頁共用的 script）。
 */
?>
    </main>
<?php
$extra_body = $page_extra_body ?? '';
echo $extra_body;
?>
</body>
</html>
