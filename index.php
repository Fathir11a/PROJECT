<?php
include 'koneksi.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $sql = "SELECT * FROM project WHERE username = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($password === $row['password']) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role'];

            $role = strtolower(trim($row['role']));

            if ($role === 'admin') {
                header("Location: pilih.php");
            } else if ($role === 'user') {
                header("Location: hello.php#home");
            } else {
                $error_message = "Role tidak valid.";
            }
            exit;
        } else {
            $error_message = "Password salah.";
        }
    } else {
        $error_message = "Username atau email tidak ditemukan.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Cyber Security Indonesia</title>
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
            flex-direction: column;
            background-image: url('https://www.transparenttextures.com/patterns/connected.png');
            background-size: cover;
            background-repeat: repeat;
        }
        .container {
            width: 100%;
            max-width: 400px;
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        form {
            max-width: 100%;
            margin-bottom: 20px;
        }

        .form_group{
            margin-bottom: 20px;
        }
       
        h1 {
            font-size: 32px;
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
            padding: 10px;
            border: 1px solid #555;
            box-sizing: border-box;
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
            margin-top: 10px;
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
        .register-button {
            background-color: #006bb3;
            margin-top: 20px;
        }
        .register-button:hover {
            background-color: #005599;
        }
    </style>
</head>
<body>
    <h1>Welcome To Security Siber Indonesia</h1>
    <div class="container">
        <form action="" method="POST">
            <div class="form_group">
                <label for="username">Username:</label>
                <input type="text" name="username" required><br>
            </div>

            <div class="form_group">
                <label for="password">Password:</label>
                <input type="password" name="password" required><br>
            </div>
            <div class="form_group">
                <label for="email">Email:</label>
                <input type="email" name="email" required><br>
            </div>
            <button type="submit">Login</button>
        </form>

        <button 
            class="register-button" 
            onclick="window.location.href='register.php';">
            Register
        </button>

        <?php
        if (isset($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>
    </div>
</body>
</html>
