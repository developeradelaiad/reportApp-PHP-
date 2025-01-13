<?php
header('Content-Type: text/html; charset=utf-8');

// استعراض الأخطاء للتشخيص
ini_set('display_errors', 1);
error_reporting(E_ALL);

// التحقق من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["error" => "Invalid request method."]));
}

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'connect2.php'; // Include your database connection file

// التحقق من حالة الاتصال
if (!isset($conn)) {
    die("Database connection not initialized.");
}

// التحقق من حالة الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// استعلام جلب البيانات
$sql = "SELECT * FROM `main_reports`"; // Retrieve all reports

// تنفيذ الاستعلام
$result = $conn->query($sql);

// التحقق إذا كانت هناك أي بيانات
if ($result->num_rows > 0) {
    echo "<h1>Reports List</h1>";
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr><th>Report Type</th><th>Report Name</th><th>File</th><th>Actions</th></tr>";

    while ($row = $result->fetch_assoc()) {
        $filePath = './report_file/' . $row['report_attach_file'];
        $fileName = $row['report_attach_file'];

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['report_type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['report_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['report_attach_file']) . "</td>";
        echo "<td><a href='download.php?file=" . urlencode($fileName) . "'>Download</a></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "No reports found.";
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
// // Set content type to JSON for API response
// header('Content-Type: application/json');

// // Include the database connection
// require_once 'connect2.php';

// // Check connection
// if ($conn->connect_error) {
//     die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
// }

// // Fetch the reports from the database
// $sql = "SELECT * FROM main_reports";  // Fetch all reports from the database
// $result = $conn->query($sql);

// if ($result->num_rows > 0) {
//     // Store the reports in an array
//     $reports = [];
//     while ($row = $result->fetch_assoc()) {
//         $reports[] = $row;
//     }

//     // Return reports as a JSON response
//     echo json_encode($reports);
// } else {
//     echo json_encode(["error" => "No reports found."]);
// }

// $conn->close();
?>