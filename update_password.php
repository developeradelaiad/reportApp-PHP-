<?php
// // عرض الأخطاء للتشخيص
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     die(json_encode(["error" => "Invalid request method."]));
// }

// $data = json_decode(file_get_contents("php://input"), true);
// $id = 1; // استخرج معرف المستخدم
// $password = "1234567891";
// $confirm_password = "1234567891";

// if (empty($id) || empty($password)||empty($confirm_password)) {
//     die(json_encode(["error" => "Missing parameters."]));
// }

// // تضمين ملف الاتصال بقاعدة البيانات
// require_once 'connect2.php';

// $stmt = $conn->prepare("UPDATE `login` SET `password` = ? `confirm_password`=? WHERE `id` = ?");
// $stmt->bind_param("sis", $password,$confirm_password, $id);
// $stmt->execute();

// if ($stmt->affected_rows > 0) {
//     echo json_encode(["success" => "Password updated successfully."]);
// } else {
//     echo json_encode(["error" => "Failed to update password."]);
// }

// $stmt->close();
// $conn->close();
header('Content-Type: application/json');

// التحقق من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["error" => "Invalid request method."]));
}

// إعداد الاتصال بقاعدة البيانات
require_once 'connect2.php';

// التحقق من وجود البيانات المدخلة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? null;
    $confirm_password=$_POST['confirm_password'] ?? null;
    $id = $_POST['id'] ?? null;  // تأكد من إرسال id عبر POST

    if (empty($password) || empty($id)|| empty($confirm_password)) {
        echo json_encode(['status' => 'error', 'message' => 'البيانات المدخلة غير صحيحة!']);
        exit; // إيقاف تنفيذ السكربت
    }

    // التحقق من البيانات المدخلة
    $stmt = $conn->prepare("UPDATE `login` SET `password` = ?,`confirm_password`=? WHERE `id` = ?");
    $stmt->bind_param("sss", $password,$confirm_password, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'تم تغيير البيانات بنجاح!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل التحديث']);
    }

    // إغلاق الاتصال
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'طريقة الطلب غير صحيحة!']);
}
?>