<?php
//ملف الاتصال بقاعدة البيانات
// تأسيس اتصال بقاعدة البيانات
$servername = "127.0.0.1";
$username_db = "root";
$password_db = "";
//اسم قاعدة البيانات
$dbname = "loginreports";

// إنشاء اتصال بقاعدة البيانات
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
    // echo "تم الاتصال بقاعدة البيانات بنجاح";

// التحقق من وجود الاتصال بقاعدة البيانات
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>