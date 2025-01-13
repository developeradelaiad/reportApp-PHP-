<?php
header('Content-Type: application/json');

// عرض الأخطاء للتشخيص
ini_set('display_errors', 1);
error_reporting(E_ALL);

// التحقق من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["error" => "Invalid request method."]));
}
require_once 'connect2.php';  // تضمين الاتصال بقاعدة البيانات
if (isset($_POST['id'])) {
    $userId = $_POST['id'];
    echo suspendUser($userId);  // استدعاء دالة إيقاف الحساب
}
function suspendUser($userId) {
    global $conn;
    $sql = "UPDATE 'login' SET 'status' = 'suspended' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        return json_encode(["message" => "User suspended successfully"]);
    } else {
        return json_encode(["error" => "Failed to suspend user"]);
    }
}
?>