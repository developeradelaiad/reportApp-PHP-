<?php
header('Content-Type: application/json');

// عرض الأخطاء للتشخيص
ini_set('display_errors', 1);
error_reporting(E_ALL);

// التحقق من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["error" => "Invalid request method."]));
}

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'connect2.php';

// التحقق من أن الاتصال تم بنجاح
if (!isset($conn)) {
    die(json_encode(["error" => "Database connection not initialized."]));
}

// استرجاع الطلبات التي تحتوي على group_id = 0
$sql = "SELECT * FROM `main_reports` WHERE `group_id` = 0";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// تحويل النتيجة إلى مصفوفة
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// إذا كان هناك طلبات جديدة، نرسلها كـ JSON
if (!empty($orders)) {
    echo json_encode(['status' => 'success', 'orders' => $orders]);
} else {
    echo json_encode(['status' => 'no_new_orders']);
}

$conn->close();
?>