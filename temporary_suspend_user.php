<?php
header('Content-Type: application/json');

// عرض الأخطاء للتشخيص
ini_set('display_errors', 1);
error_reporting(E_ALL);

// التحقق من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["error" => "Invalid request method."]));
}
require_once 'connect2.php';
if (isset($_POST['id'])) {
    $userId = $_POST['id'];
    echo temporarySuspendUser($userId);  // استدعاء دالة إيقاف الحساب مؤقتًا
}
function temporarySuspendUser($userId) {
    global $conn;
    $sql = "UPDATE 'login' SET 'status' = 'temporary_suspended' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        return json_encode(["message" => "User temporarily suspended"]);
    } else {
        return json_encode(["error" => "Failed to temporarily suspend user"]);
    }
}
?>