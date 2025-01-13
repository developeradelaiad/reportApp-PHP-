<?php
header('Content-Type: application/json');

// التحقق من أن الطلب هو DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["error" => "Invalid request method."]));
}

// إعداد الاتصال بقاعدة البيانات
require_once 'connect2.php';

// التحقق من وجود الـ id
$id = $_POST['id'] ?? null;

if (empty($id)) {
    echo json_encode(['status' => 'error', 'message' => 'معرف المستخدم غير موجود!']);
    exit;
}

// التحقق من أن المستخدم يملك الحق في حذف البيانات
// (يمكن إضافة التحقق من صلاحيات المستخدم إذا لزم الأمر)

// تنفيذ استعلام الحذف
$stmt = $conn->prepare("DELETE FROM `login` WHERE `id` = ? AND `group_id` = 1");
$stmt->bind_param("s", $id);

// تنفيذ الاستعلام
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'تم حذف البيانات بنجاح!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'فشل الحذف']);
}

// إغلاق الاتصال
$stmt->close();
$conn->close();
?>