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
$report_id = $_POST['report_id'] ?? '';  // استلام معرّف التقرير المراد تحديثه
$file = $_FILES['report_attach_file'] ?? null;  // استلام الملف الجديد من الطلب
$report_status = "Completed";
$group_id = 3;

// التحقق من وجود معرّف التقرير
if (empty($report_id)) {
    die(json_encode(["error" => "Missing report ID."]));
}

// التحقق من وجود ملف جديد في الطلب
if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["error" => "No valid file uploaded or file error."]);
    exit;
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

// استعلام لاستخراج اسم الملف المرفق من قاعدة البيانات بناءً على معرّف التقرير
$sql = "SELECT `report_attach_file` FROM `main_reports` WHERE `report_id` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($file_path);
$stmt->fetch();

// التحقق من وجود الملف المرفق وحذفه إذا كان موجودًا
if (!empty($file_path)) {
    $full_file_path = './report_file/' . $file_path;
    
    // إضافة بعض رسائل التصحيح
    if (file_exists($full_file_path)) {
        // محاولة حذف الملف
        if (unlink($full_file_path)) {
            echo json_encode(["status" => "success", "message" => "Old file deleted from the server."]);
        } else {
            echo json_encode(["error" => "Failed to delete the old file from the server."]);
            exit; // تأكد من إيقاف العملية في حال فشل الحذف
        }
    } else {
        echo json_encode(["error" => "File does not exist on the server."]);
        exit; // تأكد من إيقاف العملية إذا لم يكن الملف موجودًا
    }
} else {
    echo json_encode(["error" => "No file found in database for the given report ID."]);
    exit;
}

// معالجة الملف الجديد
$new_file_name =basename($file['name']);
$new_file_path = './report_file/' . $new_file_name;

// رفع الملف الجديد إلى السيرفر
if (move_uploaded_file($file['tmp_name'], $new_file_path)) {
    // إضافة رسائل تصحيح لرؤية القيم الممررة
    error_log("Updating with file: $new_file_name, Status: $report_status, Group ID: $group_id");

    // تحديث مسار الملف في قاعدة البيانات
    $sql_update = "UPDATE `main_reports` SET `report_attach_file` = ?, `report_status` = ?, `group_id` = ? WHERE `report_id` = ?";
    $stmt_update = $conn->prepare($sql_update);
    
    // التحقق من القيم الممررة
    if ($stmt_update === false) {
        die(json_encode(["error" => "Failed to prepare update statement."]));
    }
    
    $stmt_update->bind_param("sssi", $new_file_name, $report_status, $group_id, $report_id);

    if ($stmt_update->execute()) {
        echo json_encode(["status" => "success", "message" => "Report file updated successfully."]);
    } else {
        echo json_encode(["error" => "Database error: " . $stmt_update->error]);
    }

    $stmt_update->close();
} else {
    echo json_encode(["error" => "Failed to upload the new file."]);
}

// إغلاق البيان
$stmt->close();
// إغلاق الاتصال بقاعدة البيانات
$conn->close();

?>