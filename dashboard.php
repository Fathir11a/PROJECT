<?php
session_start();

// Nonaktifkan cache halaman untuk mencegah pengguna kembali menggunakan tombol back setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Redirect ke halaman login jika belum login
    header("Location: index.php");
    exit;
}

// Cek apakah pengguna memiliki peran 'admin'
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, tampilkan pesan akses ditolak
    echo "<h1 style='color: red; text-align: center;'>Akses Ditolak!</h1>";
    echo "<p style='text-align: center;'>Anda tidak memiliki izin untuk mengakses halaman ini.</p>";
    echo "<p style='text-align: center;'><a href='logout.php'>Logout</a></p>";
    exit;
}

// Konfigurasi database
$host = 'localhost';
$db = 'membuat_laman_login';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . htmlspecialchars($e->getMessage()));
}

// Proses update role jika form dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'], $_POST['new_role'])) {
        $user_id = intval($_POST['user_id']);
        $new_role = $_POST['new_role'];

        try {
            $stmt = $conn->prepare("UPDATE project SET role = :new_role WHERE id = :user_id");
            $stmt->bindParam(':new_role', $new_role);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $success_message = "Role pengguna berhasil diperbarui.";
        } catch (PDOException $e) {
            $error_message = "Gagal memperbarui role: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Ambil semua pengguna dari database
try {
    $query = $conn->prepare("SELECT id, username, role FROM project");
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data pengguna: " . htmlspecialchars($e->getMessage()));
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Securiti Siber Indonesia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #0e0e0e; 
            color: #00ff9d; 
        }

        .navbar {
            background: #151515;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .navbar h1 {
            color: #00ff9d;
            margin: 0;
        }

        .navbar a {
            text-decoration: none;
            color: #00ff9d;
            font-weight: bold;
            margin-left: 20px;
        }

        .navbar a:hover {
            color: #ff0066;
        }

        .dashboard {
            padding: 20px;
        }

        .dashboard h2 {
            text-align: center;
            border-bottom: 2px solid #00ff9d;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #00ff9d;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #151515;
            color: #00ff9d;
        }

        table tr:hover {
            background-color: #222;
        }

        select, button {
            background-color: #333;
            color: #00ff9d;
            border: 1px solid #00ff9d;
            padding: 5px;
        }

        button:hover {
            background-color: #00ff9d;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="dashboard">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

        <!-- Menampilkan Pesan -->
        <?php if (isset($success_message)) : ?>
            <p style="color: #00ff9d; text-align: center;"><?php echo htmlspecialchars($success_message); ?></p>
        <?php elseif (isset($error_message)) : ?>
            <p style="color: #ff0066; text-align: center;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <h3 style="text-align: center;">User Management</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Change Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <form action="dashboard.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <select name="new_role">
                                <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <button type="submit">Change</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>© 2024 Securiti Dashboard. Built with passion for security. <a href="https://nemosecurity.com/">Learn more</a></p>
    </footer>
</body>
</html>
