<?php
require_once 'db.php'; // 確保連線正常

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 嚴格對應你指定的名稱
    $id   = $_POST['id'];   // HTML 裡的 name="id"
    $name = $_POST['name']; // HTML 裡的 name="name"
    $pass = $_POST['pass']; // HTML 裡的 name="pass"

    // 密碼加密
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    // 寫入資料庫，欄位順序為 id, name, pass
    $stmt = $conn->prepare("INSERT INTO users (id, name, pass) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $id, $name, $hashed_pass);

    if ($stmt->execute()) {
        echo "<script>alert('註冊成功！'); location.href='user.php';</script>";
    } else {
        echo "註冊失敗：" . $stmt->error;
    }
}
require_once 'close.php';
?>