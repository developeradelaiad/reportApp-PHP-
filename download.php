<?php
// Set content type for JSON responses
header('Content-Type: application/json');

// التحقق من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["error" => "Invalid request method."]));
}

// Directory where reports are stored
$uploadDir = './report_file/';

// Check if a file parameter is passed
if (isset($_GET['file'])) {
    $fileName = basename($_GET['file']);  // Sanitize file name
    $filePath = $uploadDir . $fileName;   // Full path to the file

    // Check if the file exists
    if (file_exists($filePath)) {
        // Set headers for downloading the file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        
        // Read and output the file
        readfile($filePath);
        exit;
    } else {
        echo json_encode(["error" => "File not found."]);
    }
} else {
    echo json_encode(["error" => "No file specified."]);
}
?>