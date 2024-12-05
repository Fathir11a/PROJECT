<?php
session_start();

// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pastikan pengguna terautentikasi sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Jika form disubmit, lakukan redirect berdasarkan pilihan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : '';

    // Validasi pilihan dan redirect
    if ($redirect_to === 'hello') {
        header("Location: hello.php#home");
        exit;
    } elseif ($redirect_to === 'admin dashboard') {
        header("Location: admin.php");
        exit;
    } else {
        $message = "<p style='color: red;'>Pilih halaman untuk diarahkan.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirect Selection</title>
    <!-- Link to Google Fonts for better typography -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Source+Code+Pro:wght@400;600&display=swap" rel="stylesheet">
    <!-- Add some styling for Cybersecurity Theme -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: url('https://img.pikbest.com/wp/202408/neural-network-futuristic-cyber-networking-abstract-background-mesh-of-data-transfer_9852346.jpg!sw800') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h1 {
            color: #00d1b2;
            font-size: 36px;
            font-weight: 500;
            text-align: center;
            margin-bottom: 40px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        }

        .options {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .option-box {
            background-color: rgba(44, 47, 56, 0.8); /* Slightly transparent */
            color: #ffffff;
            padding: 30px;
            width: 250px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.3s ease;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.3);
        }

        .option-box:hover {
            background-color: #00d1b2;
            transform: translateY(-10px);
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            color: #ffffff;
            font-size: 14px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.6);
        }

        .footer a {
            color: #00d1b2;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Cyber Security Admin Panel</h1>
    <div class="options">
        <div class="option-box" onclick="window.location.href='hello.php';">
            <h3>Hello Page</h3>
            <p>Go to the Hello page.</p>
        </div>
        <div class="option-box" onclick="window.location.href='admin.php';">
            <h3>Admin Dashboard</h3>
            <p>Go to the Admin Dashboard page.</p>
        </div>
    </div>

    <!-- Tampilkan pesan jika ada kesalahan -->
    <?php
    if (isset($message)) {
        echo "<div class='error-message'>" . $message . "</div>";
    }
    ?>

    <div class="footer">
        <p>&copy; 2024 Cyber Security Admin Panel. All rights reserved. | <a href="logout.php">Logout</a></p>
    </div>

</body>
</html>
