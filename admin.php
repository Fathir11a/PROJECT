<?php
session_start();

// Nonaktifkan cache halaman
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Cek apakah pengguna memiliki peran 'admin'
if ($_SESSION['role'] !== 'admin') {
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

// Fungsi untuk memperbarui data di database
function updateDatabase($query, $params, $conn) {
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        return "Error: " . htmlspecialchars($e->getMessage());
    }
}

// Proses permintaan POST
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? '';

    if (!$id || !in_array($action, ['approve_topup', 'reject_topup', 'approve_transaction', 'reject_transaction', 'update_role'])) {
        $message = "Input tidak valid.";
    } else {
        switch ($action) {
            case 'approve_topup':
            case 'reject_topup':
                $verification_status = $action === 'approve_topup' ? 'approve' : 'reject';
                try {
                    if ($verification_status === 'approve') {
                        $stmt = $conn->prepare("SELECT username, amount FROM topup_requests WHERE id = :id");
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        $topup = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($topup) {
                            $stmt = $conn->prepare("UPDATE project SET coin_balance = coin_balance + :amount WHERE username = :username");
                            $stmt->bindParam(':amount', $topup['amount']);
                            $stmt->bindParam(':username', $topup['username']);
                            $stmt->execute();

                            $stmt = $conn->prepare("DELETE FROM topup_requests WHERE id = :id");
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();

                            $message = "Top-up berhasil diverifikasi dan saldo telah diperbarui.";
                        } else {
                            $message = "Top-up tidak ditemukan.";
                        }
                    } elseif ($verification_status === 'reject') {
                        $stmt = $conn->prepare("DELETE FROM topup_requests WHERE id = :id");
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        $message = "Permintaan top-up telah ditolak.";
                    } else {
                        $message = "Status top-up tidak valid.";
                    }
                } catch (PDOException $e) {
                    $message = "Gagal memverifikasi top-up: " . htmlspecialchars($e->getMessage());
                }
                break;

            case 'approve_transaction':
            case 'reject_transaction':
                $status = $action === 'approve_transaction' ? 'approved' : 'rejected';
                $message = updateDatabase(
                    "UPDATE transactions SET status = :status WHERE id = :id",
                    [':status' => $status, ':id' => $id],
                    $conn
                ) ? "Transaksi berhasil $status." : "Gagal memproses transaksi.";
                break;

            case 'update_role':
                $new_role = filter_input(INPUT_POST, 'new_role', FILTER_SANITIZE_STRING);
                if (in_array($new_role, ['user', 'admin'])) {
                    $message = updateDatabase(
                        "UPDATE project SET role = :role WHERE id = :id",
                        [':role' => $new_role, ':id' => $id],
                        $conn
                    ) ? "Peran berhasil diperbarui." : "Gagal memperbarui peran.";
                } else {
                    $message = "Peran tidak valid.";
                }
                break;
        }
    }
}

// Ambil data tabel
$topups = $conn->query("SELECT id, amount FROM topup_requests WHERE status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);
$transactions = $conn->query("SELECT id, amount FROM transactions WHERE status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);
$users = $conn->query("SELECT id, username, role FROM project")->fetchAll(PDO::FETCH_ASSOC);

// Variabel baru yang ditambahkan
$welcomeMessage = "Welcome To Admin Dashboard Siber Security Indonesia";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Securiti Siber Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            display: flex;
            background-color: #f4f4f4;
        }

        .sideboard {
            width: 250px;
            background: #343a40;
            color: #fff;
            padding: 20px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sideboard .header {
            font-size: 1.5em;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .sideboard ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sideboard ul li {
            margin: 15px 0;
        }

        .sideboard ul li a {
            text-decoration: none;
            color: #fff;
            display: block;
            padding: 12px;
            text-align: center;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .sideboard ul li a i {
            margin-right: 10px;
        }

        .sideboard ul li a:hover {
            background: #00C29D;
        }

        .main-content {
            flex-grow: 1;
            padding: 25px;
            background: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin: 20px;
            border-radius: 12px;
        }

        h2 {
            color: #333;
            font-weight: 500;
            border-bottom: 2px solid #00C29D;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #343a40;
            color: white;
        }

        .btn-approve, .btn-reject, .btn-update {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 5px;
            transition: background 0.3s ease;
        }

        .btn-approve {
            background-color: #28a745;
            color: white;
        }

        .btn-approve:hover {
            background-color: #218838;
        }

        .btn-reject {
            background-color: #dc3545;
            color: white;
        }

        .btn-reject:hover {
            background-color: #c82333;
        }

        .btn-update {
            background-color: #007bff;
            color: white;
        }

        .btn-update:hover {
            background-color: #0069d9;
        }

    </style>
</head>

<body>
    <div class="sideboard">
        <div class="header">Admin Dashboard</div>
        <ul>
            <li><a href="?page=roles"><i class="fas fa-users"></i> Manage Roles</a></li>
            <li><a href="?page=transactions"><i class="fas fa-wallet"></i> Manage Transactions</a></li>
            <li><a href="hello.php#home"><i class="fas fa-user"></i> User Dashboard</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <!-- Menampilkan pesan selamat datang -->
        <h2 style="color: green; text-align: center;"><?php echo $welcomeMessage; ?></h2>

        <?php if (isset($message)) echo "<p style='color: #d9534f; font-weight: bold;'>$message</p>"; ?>
        <?php if ($_GET['page'] === 'roles') : ?>
            <h2>Manage User Roles</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <select name="new_role" required>
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button type="submit" name="action" value="update_role" class="btn-update">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif ($_GET['page'] === 'transactions') : ?>
            <h2>Manage Transactions</h2>
            
            <h3>Pending Top-Up Requests</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($topups as $topup) : ?>
                    <tr>
                        <td><?= htmlspecialchars($topup['id']) ?></td>
                        <td><?= htmlspecialchars($topup['amount']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $topup['id'] ?>">
                                <button type="submit" name="action" value="approve_topup" class="btn-approve">Approve</button>
                                <button type="submit" name="action" value="reject_topup" class="btn-reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h3>Pending Transactions</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($transactions as $transaction) : ?>
                    <tr>
                        <td><?= htmlspecialchars($transaction['id']) ?></td>
                        <td><?= htmlspecialchars($transaction['amount']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $transaction['id'] ?>">
                                <button type="submit" name="action" value="approve_transaction" class="btn-approve">Approve</button>
                                <button type="submit" name="action" value="reject_transaction" class="btn-reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>
