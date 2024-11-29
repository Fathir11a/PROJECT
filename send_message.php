<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iis', $sender_id, $receiver_id, $message);
    
    if ($stmt->execute()) {
        echo "Pesan berhasil dikirim!";
        header("Location: hello.php#send_message");
        exit();
    } else {
        echo "Gagal mengirim pesan.";
    }
}
?>
