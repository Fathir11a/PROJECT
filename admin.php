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
            background: #f4f6f8;
            /* Background abu-abu ke putihan */
            color: #34495e;
            /* Warna teks biru keabu-abuan */
        }

        .navbar {
            background: #2c3e50;
            /* Biru keabu-abuan gelap */
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .navbar h1 {
            color: #1abc9c;
            /* Hijau mint elegan */
            margin: 0;
            font-size: 1.5rem;
        }

        .navbar a {
            text-decoration: none;
            color: #1abc9c;
            /* Hijau mint elegan */
            font-weight: bold;
            margin-left: 20px;
            transition: color 0.3s ease;
        }

        .navbar a:hover {
            color: #2ecc71;
            /* Hijau lebih cerah */
        }

        .dashboard {
            padding: 20px;
        }

        .dashboard h2 {
            text-align: center;
            border-bottom: 2px solid #1abc9c;
            /* Garis hijau mint */
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 1.8rem;
            color: #2c3e50;
            /* Warna heading biru keabu-abuan gelap */
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #ffffff;
            /* Background putih */
            color: #34495e;
            /* Warna teks biru keabu-abuan */
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            border: 1px solid #2c3e50;
            /* Kolom dengan border biru keabu-abuan gelap */
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #1abc9c;
            /* Hijau mint */
            color: #ffffff;
            /* Warna teks putih */
            font-size: 1rem;
        }

        table tr:nth-child(even) {
            background-color: #ecf0f1;
            /* Warna abu terang */
        }

        table tr:hover {
            background-color: #d5dbdb;
            /* Warna abu sedikit lebih gelap untuk hover */
            transition: background-color 0.2s ease;
        }

        select,
        button {
            background-color: #ffffff;
            /* Background putih */
            color: #34495e;
            /* Warna teks biru keabu-abuan */
            border: 1px solid #1abc9c;
            /* Hijau mint */
            border-radius: 4px;
            padding: 5px;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        select:hover,
        button:hover {
            background-color: #1abc9c;
            /* Hijau mint */
            color: #ffffff;
            /* Warna teks putih */
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background: #f8f9fa;
            /* Abu terang untuk footer */
            color: #7f8c8d;
            /* Abu medium */
            margin-top: 20px;
        }

        footer a {
            color: #1abc9c;
            /* Hijau mint */
            text-decoration: none;
        }

        footer a:hover {
            color: #2ecc71;
            /* Hijau lebih cerah */
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