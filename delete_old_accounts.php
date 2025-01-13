<?php
// الاتصال بقاعدة البيانات
require_once 'connect2.php';  // تأكد من أن ملف الاتصال بالقاعدة موجود في نفس المسار أو قم بتحديد المسار الصحيح.

// التحقق من وجود اتصال بالقاعدة
if (!$conn) {
    die(json_encode(["error" => "فشل في الاتصال بقاعدة البيانات."]));
}

// التحقق من التاريخ المحسوب (تاريخ اليوم - 15 يومًا)
$thresholdDate = date('Y-m-d H:i:s', strtotime('-15 days'));
echo "Threshold Date: " . $thresholdDate;  // طباعة التاريخ المحسوب للمتابعة

// استعلام الحذف مع التعامل مع الحقول NULL
$sqlDelete = "DELETE FROM `login` WHERE `last_used` < ? OR `last_used` IS NULL";

// تحضير الاستعلام
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("s", $thresholdDate);

// تنفيذ الاستعلام
if ($stmtDelete->execute()) {
    if ($stmtDelete->affected_rows > 0) {
        echo json_encode(["message" => "تم حذف الحسابات غير المستخدمة بنجاح."]);
    } else {
        echo json_encode(["message" => "لا توجد حسابات قديمة لحذفها."]);
    }
} else {
    echo json_encode(["error" => "فشل في تنفيذ عملية الحذف: " . $stmtDelete->error]);
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>