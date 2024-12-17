<?php
session_start();

// Cek apakah pengguna sudah login, jika belum, arahkan ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil username dari session
$username = $_SESSION['username'];

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'membuat_laman_login';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fetch daftar pengguna lain berdasarkan username
$stmt = $conn->prepare("SELECT id, username FROM project WHERE username != ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$users = $stmt->get_result();

// Ambil id pengguna yang login
$stmt_user_id = $conn->prepare("SELECT id FROM project WHERE username = ?");
$stmt_user_id->bind_param("s", $username);
$stmt_user_id->execute();
$user_result = $stmt_user_id->get_result();

$user_id = null;
if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $user_id = $user_data['id'];
}

// Ambil receiver_id dari URL jika tersedia
$receiver_id = $_GET['receiver_id'] ?? $_SESSION['receiver_id'] ?? null;

// Fetch pesan antara pengguna
$messages = [];
if ($receiver_id) {
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
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Massage</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            background-color: #f4f4f9;
        }
        .user-list {
            width: 30%;
            background-color: #ffffff;
            border-right: 1px solid #ddd;
            padding: 20px;
            height: 100vh;
            overflow-y: auto;
        }
        .user-list h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #1abc9c;
        }
        .user-list button {
            width: 100%;
            padding: 12px;
            background-color: #1abc9c;
            color: white;
            border: none;
            text-align: left;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }
        .user-list button:hover {
            background-color: #16a085;
        }
        .chat-box {
            flex: 1;
            background-color: #ffffff;
            padding: 20px;
            height: 100vh;
            overflow-y: auto;
        }
        .chat-box h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .message {
            position: relative;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .message strong {
            color: #1abc9c;
        }
        .timestamp {
            position: absolute;
            bottom: 5px;
            right: 10px;
            font-size: 12px;
            color: #888;
        }
        .chat-box form {
            display: flex;
            align-items: center;
        }
        .chat-box input[type="text"] {
            flex: 1;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        .chat-box button {
            padding: 12px 20px;
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="user-list" style="display: flex; flex-direction: column; height: 100vh;">
    <div style="flex: 1; overflow-y: auto;">
        <h3>Daftar Pengguna</h3>
        <ul>
            <?php while ($user = $users->fetch_assoc()) { ?>
                <li>
                    <form method="GET" action="pesan.php">
                        <input type="hidden" name="receiver_id" value="<?= $user['id'] ?>">
                        <button type="submit"><?= htmlspecialchars($user['username']) ?></button>
                    </form>
                </li>
            <?php } ?>
        </ul>
    </div>

    <!-- Button di Bagian Bawah Sidebar -->
    <div style="padding: 20px; background-color: #fff; box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);">
        <button onclick="window.location.href='hello.php#home'" style="
            width: 100%;
            padding: 12px;
            background-color: #e74c3c;
            color: white;
            border: none;
            text-align: center;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        ">
            Dashboard
        </button>
    </div>
</div>

    <!-- Chat Box -->
    <div class="chat-box">
        <h3>Chat</h3>
        <?php if ($receiver_id): ?>
        <div class="chat-messages">
            <?php if ($messages instanceof mysqli_result) {
                while ($msg = $messages->fetch_assoc()) { ?>
                    <div class="message">
                        <p><strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong> <?= htmlspecialchars($msg['message']) ?></p>
                        <span class="timestamp"><?= date('H:i', strtotime($msg['timestamp'])) ?></span>
                    </div>
            <?php }
            } else {
                echo "<p>No messages available.</p>";
            } ?>
        </div>

        <!-- Input Pesan -->
        <form id="chatForm" method="POST" action="send_message.php">
            <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
            <input type="text" name="message" placeholder="Ketik pesan..." required>
            <button type="submit">Kirim</button>
        </form>
        <?php else: ?>
            <p>Pilih pengguna untuk memulai percakapan.</p>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById("chatForm")?.addEventListener("submit", function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            fetch("send_message.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.querySelector("input[name='message']").value = "";
                loadMessages();
            });
        });

        function loadMessages() {
            const receiverId = document.querySelector("input[name='receiver_id']").value;
            fetch("pesan.php?receiver_id=" + receiverId)
                .then(response => response.text())
                .then(data => {
                    document.querySelector(".chat-messages").innerHTML = data;
                });
        }
    </script>
</body>
</html>
