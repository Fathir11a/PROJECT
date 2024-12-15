<?php
session_start();
include 'koneksi.php';

// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Pastikan pengguna sudah login (username ada di session)
if (!isset($_SESSION['username'])) {
    die("Anda harus login terlebih dahulu.");
}

// Menentukan project_id default jika tidak ada yang dipilih
$default_project_id = 1; // Anda bisa menyesuaikan dengan id proyek yang ada di database

// Fungsi untuk Top-Up Saldo
function topUp($username, $amount, $conn, $id)
{
    if ($amount <= 0) {
        echo "<p class='error'>Jumlah top-up harus lebih besar dari nol.</p>";
        return;
    }

    // Menggunakan id default jika tidak dipilih
    $stmt = $conn->prepare("INSERT INTO topup_requests (username, amount, project_id, status) VALUES (?, ?, ?, 'pending')");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("sii", $username, $amount, $id); // Bind id proyek
    $stmt->execute();
    echo "<p class='success'>Permintaan top-up telah diajukan dan menunggu verifikasi admin.</p>";
}

// Fungsi untuk Kirim Saldo
function sendBalance($sender_username, $receiver_username, $amount, $conn)
{
    if ($amount <= 0 || empty($receiver_username)) {
        echo "<p class='error'>Jumlah saldo harus lebih besar dari nol dan username penerima harus diisi.</p>";
        return;
    }

    // Verifikasi apakah receiver_username ada di database
    $stmt = $conn->prepare("SELECT coin_balance FROM project WHERE username = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("s", $receiver_username);  // Verifikasi receiver_username
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        echo "<p class='error'>Username penerima tidak ditemukan.</p>";
        return;
    }

    // Mengambil saldo proyek pengirim berdasarkan sender_username
    $stmt = $conn->prepare("SELECT coin_balance FROM project WHERE username = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("s", $sender_username);  // Gunakan sender_username
    $stmt->execute();
    $result = $stmt->get_result();
    $sender_project = $result->fetch_assoc();

    // Mengecek apakah saldo cukup
    if ($sender_project['coin_balance'] < $amount) {
        echo "<p class='error'>Saldo proyek tidak cukup.</p>";
        return;
    }

    // Mengurangi saldo proyek pengirim
    $stmt = $conn->prepare("UPDATE project SET coin_balance = coin_balance - ? WHERE username = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("is", $amount, $sender_username);  // Gunakan sender_username
    $stmt->execute();

    // Menambah saldo ke proyek penerima
    $stmt = $conn->prepare("UPDATE project SET coin_balance = coin_balance + ? WHERE username = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("is", $amount, $receiver_username);  // Gunakan receiver_username
    $stmt->execute();

    // Menyimpan transaksi untuk verifikasi admin
    $stmt = $conn->prepare("INSERT INTO transactions (sender_username, receiver_username, amount, status) VALUES (?, ?, ?, 'pending')");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("ssi", $sender_username, $receiver_username, $amount);
    $stmt->execute();

    echo "<p class='success'>Permintaan kirim saldo telah diajukan dan menunggu verifikasi admin.</p>";
}

// Proses Top-Up
if (isset($_POST['topup'])) {
    $topup_amount = $_POST['topup-amount'];
    $username = $_SESSION['username']; // Mengambil username dari session
    topUp($username, $topup_amount, $conn, $default_project_id); // Menambahkan id proyek default
}

// Proses Kirim Saldo
if (isset($_POST['send_balance'])) {
    $sender_username = $_SESSION['username']; // Mengambil username dari session
    $receiver_username = $_POST['username'];
    $amount = $_POST['send-amount'];

    sendBalance($sender_username, $receiver_username, $amount, $conn);
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Saldo & Top-Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }

        .back-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px;
            background-color: red;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        input[type="number"],
        input[type="text"],
        select {
            width: calc(100% - 20px);
            height: 40px;
            padding: 0 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: white;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="button"] {
            background-color: #008CBA;
        }

        h3 {
            text-align: center;
            font-size: 24px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
        }

        .form-container {
            margin-top: 30px;
        }

        .form-container h2 {
            font-size: 20px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <a href="hello.php#profile" class="back-button">Back</a>

    <div class="container">
        <!-- Form untuk Top-Up Saldo -->
        <h3>Top-Up Saldo</h3>
        <form method="POST" action="">
            <label for="topup-amount">Jumlah Top-Up:</label>
            <input type="number" id="topup-amount" name="topup-amount" required min="1">
            <button type="submit" name="topup">Top-Up</button>
        </form>
    </div>

    <div class="container form-container">
        <!-- Form untuk Kirim Saldo -->
        <h2>Kirim Saldo</h2>
        <form action="" method="post">
            <label for="username">Username Penerima:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="send-amount">Jumlah Saldo:</label>
            <input type="number" id="send-amount" name="send-amount" required min="1"><br><br>

            <button type="submit" name="send_balance">Kirim Saldo</button>
        </form>
    </div>
</body>
</html>
