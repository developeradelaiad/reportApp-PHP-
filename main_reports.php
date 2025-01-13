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
$report_type = $_POST['report_type'] ?? '';
$report_name = $_POST['report_name'] ?? '';
$report_address = $_POST['report_address'] ?? '';
$report_details = $_POST['report_details'] ?? '';
$report_attach_file = $_FILES['report_attach_file']['name'] ?? "";
$report_page_numper = $_POST['report_page_numper'] ?? '';
$report_status = "Acceptable";
$id = $_POST['id']?? '';
$username = $_POST['username']??'';
$email = $_POST['email']??'';
$group_id = 0;

// // التحقق من وجود الملف المرفق
// if (!isset($_FILES['report_attach_file']) || $_FILES['report_attach_file']['error'] !== UPLOAD_ERR_OK) {
//     die(json_encode(["error" => "No file uploaded or there was an upload error."]));
// }

// المسار الذي سيتم حفظ الملف فيه
$uploadFileDir = './report_file/';
$fileTmpName = $_FILES['report_attach_file']['tmp_name'];
$fileName = basename($_FILES['report_attach_file']['name']);
$dest_path = $uploadFileDir . $fileName;

// نقل الملف إلى المسار المحدد
if (move_uploaded_file($fileTmpName, $dest_path)) {
    echo json_encode(["status" => "success", "message" => "File is successfully uploaded."]);
} else {
    die(json_encode(["error" => "There was an error moving the uploaded file."]));
}

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'connect2.php'; // Include database connection file

// التحقق من أن الاتصال تم بنجاح
if (!isset($conn)) {
    die(json_encode(["error" => "Database connection not initialized."]));
}

// التحقق من حالة الاتصال
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// استعلام إدراج البيانات
$sql = "INSERT INTO `main_reports`(`report_type`, `report_name`, `report_address`, `report_details`, `report_attach_file`, `report_page_numper`,`report_date`, `report_time`,`report_status`,`id`,`username`,`email`,`group_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare the statement after the SQL query is defined
$stmt = $conn->prepare($sql);

// تنسيق التاريخ والوقت
$date = date('Y-m-d'); // تنسيق التاريخ
$time = date('H:i:s'); // تنسيق الوقت

// ربط المتغيرات وتشغيل الاستعلام
$stmt->bind_param("sssssssssssss", $report_type, $report_name, $report_address, $report_details, $report_attach_file, $report_page_numper, $date, $time, $report_status,$id,$username,$email,$group_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registration successful!"]);
} else {
    echo json_encode(["error" => "Database error: " . $stmt->error]);
}

// إغلاق الاتصال بقاعدة البيانات
$stmt->close();
$conn->close();

?>
// عرض الأخطاء للتشخيص
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// التحقق من أن الطلب هو POST
// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     die(json_encode(["error" => "Invalid request method."]));
// }

// استلام البيانات والتحقق منها
// $report_type = $_POST['report_type'] ?? '';
// $report_name = $_POST['report_name'] ?? '';
// $report_address = $_POST['report_address'] ?? '';
// $report_details = $_POST['report_details'] ?? '';
// $report_attach_file = $_FILES['report_attach_file']['tmp_name'];
// $report_page_numper = $_POST['report_page_numper'] ?? '';

// if (empty($report_type) || empty($report_name) || empty($report_address) || empty($report_details) || empty($report_attach_file) || empty($report_page_numper)) {
//     die(json_encode(["error" => "Missing required fields."]));
// }
     // حدد المسار الذي تريد حفظ الملف فيه
// $uploadFileDir = './report_file/';
// $dest_path = $uploadFileDir . $report_attach_file;

        // نقل الملف إلى المسار المحدد
// if(move_uploaded_file($report_attach_file, $dest_path)) {
//     echo json_encode(["status" => "success", "message" => "File is successfully uploaded."]);
// } else {
//     echo json_encode(["error" => "There was an error moving the uploaded file."]);
// } else {
//     echo json_encode(["error" => "No file uploaded or there was an upload error."]);
// }

// تضمين ملف الاتصال بقاعدة البيانات
// require_once 'connect2.php';

// التحقق من أن الاتصال تم بنجاح
// if (!isset($conn)) {
//     die(json_encode(["error" => "Database connection not initialized."]));
// }

// استعلام إدراج المستخدم
// $sql = "INSERT INTO `main_reports`(`report_type`, `report_name`, `report_address`, `report_details`, `report_attach_file`, `report_page_numper`) VALUES (?, ?, ?, ?, ?, ?)";
// $stmt = $conn->prepare($sql);

// if ($conn->connect_error) {
//     die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
// }

// ربط المتغيرات وتشغيل الاستعلام
// $stmt->bind_param("ssssss", $report_type, $report_name, $report_address, $report_details,$report_attach_file, $report_page_numper);

// if ($stmt->execute()) {
//     echo json_encode(["status" => "success", "message" => "Registration successful!"]);
// } else {
//     echo json_encode(["error" => "Database error: " . $stmt->error]);
// }

// إغلاق الاتصال بقاعدة البيانات
// $stmt->close();
// $conn->close();