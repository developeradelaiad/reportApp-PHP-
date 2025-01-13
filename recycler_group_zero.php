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

// التحقق من وجود المعامل id في POST
$id = $_POST['id'] ?? '';

if (empty($id)) {
    die(json_encode(["error" => "ID parameter is missing or empty."]));
}

// استعلام التحقق من المستخدم
$sql = "SELECT * FROM `main_reports` WHERE `group_id` = 0 AND `id` = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
   die(json_encode(["error" => "Failed to prepare statement: " . $conn->error]));
}

// ربط المعامل مع الاستعلام
$stmt->bind_param("i", $id); // إذا كان id عددًا صحيحًا استخدم "i"

$stmt->execute();
$result = $stmt->get_result(); // الحصول على النتيجة بعد التنفيذ

// التحقق من وجود بيانات مطابقة
if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "report_id" => $row['report_id'],
            "report_type" => $row['report_type'],
            "report_name" => $row['report_name'],
            "report_address" => $row['report_address'],
            "report_details" => $row['report_details'],
            "report_attach_file" => $row['report_attach_file'],
            "report_page_numper" => $row['report_page_numper'],
            "report_date" => $row['report_date'],
            "report_time" => $row['report_time'],
            "report_status" => $row['report_status'],
            "id" => $row['id'],
            "username" => $row['username'],
            "email" => $row['email'],
            "group_id" => $row['group_id']
        ];
    }

    // إرجاع النتائج بصيغة JSON
    echo json_encode(["zero" => $data]);
} else {
    echo json_encode(["error" => "No matching data found."]);
}

// إغلاق الاتصال بقاعدة البيانات
$stmt->close();
$conn->close();
?>