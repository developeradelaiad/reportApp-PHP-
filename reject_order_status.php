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

// التحقق من أن `report_id` تم إرساله في الطلب
if (!isset($_POST['report_id'])) {
    die(json_encode(["error" => "report_id is required."]));
}

$report_id = $_POST['report_id'];
$report_status = 'Rejected';
$group_id = 2;

try {
    // تحديث حالة الطلب
    $sql = "UPDATE `main_reports` SET `report_status` = ?, `group_id` = ? WHERE `report_id` = ?";
    $stmt = $conn->prepare($sql);

    // ربط المتغيرات بأنواع البيانات الصحيحة
    $stmt->bind_param('sis', $report_status, $group_id, $report_id);  // 's' للنصوص و 'i' للأعداد الصحيحة

    // تنفيذ الاستعلام
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Order status updated successfully']);
    } else {
        echo json_encode(['message' => 'Failed to update order']);
    }

} catch (Exception $e) {
    // إذا حدث استثناء، قم بإرجاع رسالة الخطأ
    echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
} finally {
    // إغلاق الاتصال بقاعدة البيانات
    $conn = null;
}
$conn->close();
?>