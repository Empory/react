<?php
include 'db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept');

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}

if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $user_id = $_SESSION['user_id'];
    $upload_dir = 'uploads/';
    $upload_file = $upload_dir . basename($file['name']);

    if (move_uploaded_file($file['tmp_name'], $upload_file)) {
        $stmt = $conn->prepare("INSERT INTO uploads (user_id, file_name, file_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $file['name'], $upload_file);

        if ($stmt->execute()) {
            echo json_encode(["message" => "File uploaded successfully"]);
        } else {
            echo json_encode(["message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["message" => "File upload failed"]);
    }
} else {
    echo json_encode(["message" => "No file uploaded"]);
}

$conn->close();
?>
