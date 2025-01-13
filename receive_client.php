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

// استعلام لجلب البيانات
$sql = "SELECT * FROM `login` WHERE `group_id`=0";
$result = $conn->query($sql);

if (!$result) {
    error_log("Database query failed: " . $conn->error);  // Log the error
    die(json_encode(["error" => "An error occurred. Please try again later."]));
}

// التحقق من وجود بيانات
if ($result->num_rows > 0) {
    $reports = [];
    
    // تخزين البيانات في مصفوفة
    while ($row = $result->fetch_assoc()) {
        $reports[] = [
            "id" => $row["id"],
            "user_name" => $row["user_name"],
            "email" => $row["email"],
            "address" => $row["address"],
            "Phone" => $row["Phone"],
            "last_used" => $row["last_used"],
            "status" => $row["status"]
        ];
    }

    // تحويل المصفوفة إلى JSON مع حالة الاستجابة
    echo json_encode([$reports]);
} else {
    echo json_encode(["error" => "No reports found."]);
}

$conn->close();
?>