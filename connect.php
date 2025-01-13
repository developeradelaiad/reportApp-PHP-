<?php
// إعداد الاتصال بقاعدة البيانات باستخدام PDO
try {
    $dsn = 'mysql:host=127.0.0.1;dbname=loginreports'; // اسم قاعدة البيانات
    $username = 'root'; // اسم المستخدم
    $password = ''; // كلمة المرور (غالبًا تكون فارغة في XAMPP)
    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', // لدعم النصوص العربية
    );
    $conn = new PDO($dsn, $username, $password, $options);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 //   echo "تم الاتصال بقاعدة البيانات بنجاح";
} catch (PDOException $e) {
    die('فشل الاتصال بقاعدة البيانات: ' . $e->getMessage());
}
?>
