<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'membuat_laman_login';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_POST['receiver_id'], $_POST['message'], $_SESSION['username'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    // Ambil id pengirim
    $stmt = $conn->prepare("SELECT id FROM project WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $sender_id = $user_result->fetch_assoc()['id'];

        // Simpan pesan ke database
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
        $stmt->execute();
    }
}

$conn->close();
?>
