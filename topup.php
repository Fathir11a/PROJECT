<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login (username ada di session)
if (!isset($_SESSION['username'])) {
    die("Anda harus login terlebih dahulu.");
}

// Username Pengirim (diperoleh dari session)
$sender_username = $_SESSION['username'];

// Fungsi untuk Top-Up Saldo
function topUp($username, $amount, $conn) {
    if ($amount <= 0) {
        echo "<p class='error'>Jumlah top-up harus lebih besar dari nol.</p>";
        return;
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE project SET coin_balance = coin_balance + ? WHERE username = ?");
        $stmt->bind_param("is", $amount, $username);
        $stmt->execute();
        $conn->commit();

        echo "<p class='success'>Top-up berhasil!</p>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p class='error'>Top-up gagal: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Fungsi untuk Mengirim Koin
function sendCoins($sender_username, $receiver_username, $amount, $conn) {
    if ($sender_username == $receiver_username) {
        echo "<p class='error'>Pengirim dan penerima tidak boleh sama.</p>";
        return;
    }
    if ($amount <= 0) {
        echo "<p class='error'>Jumlah koin harus lebih besar dari nol.</p>";
        return;
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("SELECT coin_balance FROM project WHERE username = ?");
        $stmt->bind_param("s", $sender_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            throw new Exception("Pengirim tidak ditemukan.");
        }
        $sender_balance = $result->fetch_assoc()['coin_balance'];

        if ($sender_balance < $amount) {
            throw new Exception("Saldo tidak mencukupi.");
        }

        $stmt1 = $conn->prepare("UPDATE project SET coin_balance = coin_balance - ? WHERE username = ?");
        $stmt1->bind_param("is", $amount, $sender_username);
        $stmt1->execute();

        $stmt2 = $conn->prepare("UPDATE project SET coin_balance = coin_balance + ? WHERE username = ?");
        $stmt2->bind_param("is", $amount, $receiver_username);
        $stmt2->execute();

        $stmt3 = $conn->prepare("INSERT INTO transactions (sender_username, receiver_username, amount) VALUES (?, ?, ?)");
        $stmt3->bind_param("ssi", $sender_username, $receiver_username, $amount);
        $stmt3->execute();

        $conn->commit();

        echo "<p class='success'>Koin berhasil dikirim!</p>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p class='error'>Pengiriman koin gagal: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['topup'])) {
        $amount = intval($_POST['amount']);
        topUp($sender_username, $amount, $conn);
    } elseif (isset($_POST['send_coins'])) {
        $receiver_username = $_POST['receiver_username'];
        $amount = intval($_POST['amount']);
        sendCoins($sender_username, $receiver_username, $amount, $conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top-Up & Kirim Koin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h3 {
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background: #5cb85c;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #4cae4c;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        .back-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .back-button:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <!-- Tombol Back -->
    <a href="hello.php#profile" class="back-button">Back</a>

    <div class="container">
        <h3>Top-Up Saldo</h3>
        <form method="POST" action="">
            <label for="amount">Jumlah Top-Up:</label>
            <input type="number" id="amount" name="amount" required>
            <button type="submit" name="topup">Top-Up</button>
        </form>

        <h3>Kirim Koin</h3>
        <form method="POST" action="">
            <label for="receiver_username">Username Penerima:</label>
            <input type="text" id="receiver_username" name="receiver_username" required>
            <label for="amount">Jumlah Koin:</label>
            <input type="number" id="amount" name="amount" required>
            <button type="submit" name="send_coins">Kirim Koin</button>
        </form>
    </div>
</body>
</html>
