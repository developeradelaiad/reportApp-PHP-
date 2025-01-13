<?php
header('Content-Type: application/json');

// التحقق من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["error" => "Invalid request method."]));
}

// إعداد الاتصال بقاعدة البيانات
require_once 'connect2.php';

// التحقق من وجود البيانات المدخلة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استلام القيم من الطلب
    $username = $_POST['username'] ?? null;
    $address = $_POST['address'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;
    $id = $_POST['id'] ?? null;
    $phone = $_POST['Phone'] ?? null;

    // التحقق من وجود البيانات الأساسية
    if (empty($password) || empty($id) || empty($confirm_password)) {
        echo json_encode(['status' => 'error', 'message' => 'البيانات المدخلة غير صحيحة!']);
        exit; // إيقاف تنفيذ السكربت
    }

    // التحقق من تطابق كلمة المرور وتأكيد كلمة المرور
    if ($password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'كلمة المرور وتأكيد كلمة المرور غير متطابقتين!']);
        exit;
    }

    // التحقق من صحة البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'البريد الإلكتروني غير صالح!']);
        exit;
    }

    // تشفير كلمة المرور باستخدام password_hash
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // التحقق من البيانات المدخلة
    $stmt = $conn->prepare("UPDATE `login` SET `username` = ?, `address` = ?, `email` = ?, `Phone` = ?, `password` = ? ,`confirm_password` = ? WHERE `id` = ? AND `group_id` = 1");
    $stmt->bind_param("ssssss", $username, $address, $email, $phone, $password,$confirm_password, $id);

    // تنفيذ الاستعلام
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