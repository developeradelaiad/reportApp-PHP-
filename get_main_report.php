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

// استعلام لجلب البيانات
$sql = "SELECT * FROM `main_reports` WHERE `id`=?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["error" => "Failed to prepare statement: " . $conn->error]));
 }
 
// ربط المعامل مع الاستعلام
$stmt->bind_param("i", $id); // إذا كان id عددًا صحيحًا استخدم "i"
$stmt->execute();
$result = $stmt->get_result();


// التحقق من وجود بيانات
if ($result->num_rows > 0) {
    $reports = [];
    
    // تخزين البيانات في مصفوفة
    while ($row = $result->fetch_assoc()) {
        $reports[] = [
            "report_id" => (int)$row['report_id'],
            "report_type" => $row['report_type'],
            "report_name" => $row['report_name'],
            "report_address" => $row['report_address'],
            "report_details" => $row['report_details'],
            "report_attach_file" => $row['report_attach_file'],
            "report_page_numper" => $row['report_page_numper'],
            "report_date" => $row['report_date'],
            "report_time" => $row['report_time'],
            "report_status"=>$row['report_status'],
            "id"=>$row['id'],
            "username"=>$row['username'],
            "email"=>$row['email'],
            "group_id"=>$row['group_id']
        ];
    }
    
    // تحويل المصفوفة إلى JSON
    echo json_encode(["reported"=>$reports]);
} else {
    echo json_encode(["message" => "No reports found."]);
}
// header('Content-Type: application/json');
// include 'connect.php';
// $stmt = $conn->prepare("SELECT * FROM `main_reports`");
// $stmt->execute();
// $reports = $stmt->fetchAll();
// $data = array();
// $data[] = $reports;
// echo json_encode($data);
// $conn = null;
//إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>

