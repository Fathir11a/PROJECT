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

if (isset($_GET['receiver_id'], $_SESSION['username'])) {
    $receiver_id = $_GET['receiver_id'];

    // Ambil id pengirim
    $stmt = $conn->prepare("SELECT id FROM project WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user_id = $user_result->fetch_assoc()['id'];

        // Ambil pesan
        $stmt = $conn->prepare("
            SELECT messages.message, messages.timestamp, project.username AS sender_name
            FROM messages 
            JOIN project ON messages.sender_id = project.id
            WHERE (sender_id = ? AND receiver_id = ?) 
               OR (sender_id = ? AND receiver_id = ?)
            ORDER BY messages.timestamp ASC
        ");
        $stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
        $stmt->execute();
        $messages = $stmt->get_result();

        while ($msg = $messages->fetch_assoc()) {
            echo "<div class='message'>";
            echo "<p><strong>" . htmlspecialchars($msg['sender_name']) . ":</strong> " . htmlspecialchars($msg['message']) . "</p>";
            echo "<span class='timestamp'>" . date('H:i', strtotime($msg['timestamp'])) . "</span>";
            echo "</div>";
        }
    }
}
$conn->close();
?>
