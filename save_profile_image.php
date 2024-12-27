<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$username = $_SESSION['username'];
$image_path = $_POST['image_path'] ?? '';

if ($image_path) {
    $query = "UPDATE project SET profile_image = ? WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $image_path, $username);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No image path provided']);
}
?>
