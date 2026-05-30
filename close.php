<?php
// 檢查連線是否存在，存在則關閉
if (isset($conn)) {
    $conn->close();
}
?>