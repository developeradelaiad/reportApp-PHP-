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
$username = $_POST['email'] ?? ''; // يمكنك استبدال هذا بكود لاستلام البيانات من POST
$password = $_POST['password'] ?? ''; // نفس الشيء هنا
$currentDate = date('Y-m-d H:i:s'); // تاريخ ووقت الحالي
$group_id = 1;
if (empty($username) || empty($password)) {
    die(json_encode(["error" => "Missing email or password."]));
}

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'connect2.php';

// التحقق من أن الاتصال تم بنجاح
if (!isset($conn)) {
    die(json_encode(["error" => "Database connection not initialized."]));
}

// استعلام التحقق من المستخدم
$sql = "SELECT * FROM `login` WHERE `email` = ? AND `password` = ? AND `group_id` = ?  ";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["error" => "Failed to prepare statement: " . $conn->error]));
}

// ربط المتغيرات وتشغيل الاستعلام
$stmt->bind_param("sss", $username, $password,$group_id);
$stmt->execute();
$result = $stmt->get_result();

// تحقق من وجود بيانات مطابقة
if ($result->num_rows > 0) {
    // تحديث تاريخ آخر استخدام
    $sql1 = "UPDATE `login` SET `last_used` = ? WHERE `email` = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ss", $currentDate, $username);
    $stmt1->execute();

    // إرجاع بيانات المستخدم
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data = [
            "id" => $row["id"],
            "username" => $row["username"],
            "email" => $row["email"],
            "address" => $row["address"],
            "password" => $row["password"],
            "confirm_password" => $row["confirm_password"],
            "Phone" => $row["Phone"],
            "last_used"=>$row["last_used"],
            "status"=>$row["status"]
        ];
    }

    // إرجاع النتائج بصيغة JSON
    echo json_encode($data);
} else {
    echo json_encode(["error" => "No matching user found."]);
}


// إغلاق الاتصال بقاعدة البيانات
// إغلاق الاتصال بقاعدة البيانات
$stmt->close();
$stmt1->close();
$conn->close();
?>