<?php
// Include file konfigurasi database
include 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Aktifkan penanganan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Validasi input dan CSRF
    if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
        die("Permintaan tidak valid.");
    }

    if (!empty($username) && !empty($password) && !empty($email) && !empty($full_name)) {
        // Validasi kekuatan password (tanpa huruf kapital dan tanpa karakter khusus)
        if (strlen($password) < 8 || !preg_match('/[0-9]/', $password)) {
            $error_message = "Kata sandi harus minimal 8 karakter dan mengandung angka.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Format email tidak valid.";
        } else {
            // Password tidak di-hash, langsung digunakan
            $hashed_password = $password;

            // Query untuk menyimpan data ke database
            $stmt = $conn->prepare("INSERT INTO project (username, password, email, full_name) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                die("Kesalahan prepare statement: " . $conn->error);
            }

            $stmt->bind_param("ssss", $username, $hashed_password, $email, $full_name);

            // Eksekusi query
            if ($stmt->execute()) {
                // Redirect ke index.php setelah berhasil
                header("Location: index.php");
                exit;
            } else {
                $error_message = "Terjadi kesalahan: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        $error_message = "Semua kolom wajib diisi.";
    }
}

// Buat CSRF token baru
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - Cyber Security Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #1b1b1b;
            color: #ffffff;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('https://www.transparenttextures.com/patterns/connected.png');
            background-size: cover;
            background-repeat: repeat;
        }
        form {
            background: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 300px;
            text-align: center;
        }
        h2 {
            color: #0a8f3f;
            margin-bottom: 20px;
        }
        label {
            font-size: 16px;
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #555;
            background: #333;
            color: #fff;
            border-radius: 5px;
        }
        button {
            padding: 12px 20px;
            background-color: #0a8f3f;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #06702f;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <form action="" method="POST">
        <h2>Formulir Registrasi</h2>
        <label for="username">Username:</label>
        <input type="text" name="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="full_name">Nama Lengkap:</label>
        <input type="text" name="full_name" required>

        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <button type="submit">Daftar</button>

        <?php
        if (isset($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>
    </form>
</body>
</html>
