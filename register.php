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
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$phone = $_POST['Phone'] ?? '';
$group_id= 0;
$status='active';

if (empty($username) || empty($email) || empty($address) || empty($password) || empty($confirm_password) || empty($phone)) {
    die(json_encode(["error" => "Missing required fields."]));
}

// التحقق من تطابق كلمتي المرور
if ($password !== $confirm_password) {
    die(json_encode(["error" => "Passwords do not match."]));
}

// تشفير كلمة المرور
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'connect2.php';

// التحقق من أن الاتصال تم بنجاح
if (!isset($conn)) {
    die(json_encode(["error" => "Database connection not initialized."]));
}

// التحقق مما إذا كان البريد الإلكتروني موجودًا
$sqlCheck = "SELECT * FROM `login` WHERE `email` = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("s", $email);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    echo json_encode(["error" => "البريد الإلكتروني موجود بالفعل."]);
} else {
    // استعلام إدراج المستخدم
    $sql = "INSERT INTO `login` (`username`, `email`, `address`, `password`,`confirm_password`, `Phone`,`status`,`group_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die(json_encode(["error" => "Failed to prepare statement: " . $conn->error]));
    }

    // ربط المتغيرات وتشغيل الاستعلام
    $stmt->bind_param("ssssssss", $username, $email, $address,$password, $confirm_password, $phone,$status,$group_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful!"]);
    } else {
        echo json_encode(["error" => "Registration failed. Please try again later."]);
    }
}

// إغلاق الاتصال بقاعدة البيانات
$stmt->close();
$conn->close();
?>