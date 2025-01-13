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

// استعلام لاسترجاع جميع المستخدمين
$sql = "SELECT * FROM `login` WHERE `group_id` = 0";
$result = $conn->query($sql);

if (!$result) {
    die(json_encode(["error" => "Database query failed: " . $conn->error]));
}

// تحقق من وجود بيانات
if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id" => $row["id"],
            "username" => $row["username"],
            "email" => $row["email"],
            "address" => $row["address"],
            "phone" => $row["Phone"],
            "last_used" => $row["last_used"],
            "status" => $row["status"]
        ];
    }

    // إرسال البيانات في استجابة JSON
    echo json_encode([$data]);
} else {
    // لا توجد حسابات
    echo json_encode(["status" => "success", "message" => "No accounts found."]);
}

// إغلاق الاتصال
$conn->close();
?>