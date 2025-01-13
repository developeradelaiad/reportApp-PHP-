<?php

header('Content-Type: application/json');

// عرض الأخطاء للتشخيص
ini_set('display_errors', 1);
error_reporting(E_ALL);

// التحقق من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["error" => "Invalid request method."]));
}

// استلام البيانات والتحقق منها
$report_id = $_POST['report_id'] ?? '';  // استلام معرّف التقرير المراد حذفه

// التحقق من وجود معرّف التقرير
if (empty($report_id)) {
    die(json_encode(["error" => "Missing report ID."]));
}

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'connect2.php'; // تضمين ملف الاتصال بقاعدة البيانات

// التحقق من أن الاتصال تم بنجاح
if (!isset($conn)) {
    die(json_encode(["error" => "Database connection not initialized."]));
}

// التحقق من حالة الاتصال
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// استعلام لاستخراج اسم الملف من قاعدة البيانات بناءً على معرّف التقرير
$sql = "SELECT `report_attach_file` FROM `main_reports` WHERE `report_id` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($file_path);
$stmt->fetch();

// التحقق من وجود الملف المرفق وحذفه إذا كان موجودًا
if (!empty($file_path) && file_exists('./report_file/' . $file_path)) {
    // مسار الملف الكامل لحذفه
    $full_file_path = './report_file/' . $file_path;

    if (unlink($full_file_path)) {
        // إذا تم حذف الملف بنجاح
        echo json_encode(["status" => "success", "message" => "File deleted from the server."]);
    } else {
        echo json_encode(["error" => "Failed to delete the file from the server."]);
    }
}

// إغلاق البيان
$stmt->close();

// استعلام لحذف السجل بناءً على report_id
$sql_delete = "DELETE FROM `main_reports` WHERE `report_id` = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $report_id);

// تنفيذ الاستعلام لحذف السجل
if ($stmt_delete->execute()) {
    if ($stmt_delete->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Report and its file deleted successfully."]);
    } else {
        echo json_encode(["error" => "No report found with the given ID."]);
    }
} else {
    echo json_encode(["error" => "Database error: " . $stmt_delete->error]);
}

// إغلاق الاتصال بقاعدة البيانات
$stmt_delete->close();
$conn->close();

?>