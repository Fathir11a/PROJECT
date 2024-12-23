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

// Fungsi untuk memperbarui peran pengguna
function updateRoleInProject($id, $new_role, $conn)
{
    try {
        $stmt = $conn->prepare("UPDATE project SET role = :new_role WHERE id = :id");
        $stmt->bindParam(':new_role', $new_role, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0
            ? "Peran berhasil diubah menjadi $new_role."
            : "Tidak ada perubahan pada role atau pengguna tidak ditemukan.";
    } catch (PDOException $e) {
        return "Gagal memperbarui role: " . htmlspecialchars($e->getMessage());
    }
}

// Proses permintaan POST
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        $message = "ID tidak valid.";
    } else {
        if (isset($_POST['new_role'])) {
            $new_role = filter_input(INPUT_POST, 'new_role', FILTER_SANITIZE_STRING);
            if (in_array($new_role, ['user', 'admin'])) {
                $message = updateRoleInProject($id, $new_role, $conn);
            } else {
                $message = "Peran tidak valid.";
            }
        }
        // top-up request 
        if (isset($_POST['verification_status'])) {
            $verification_status = filter_input(INPUT_POST, 'verification_status', FILTER_SANITIZE_STRING);

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
        }
        //Transaction request 
        if (isset($_POST['transaction_status'])) {
            $transaction_status = filter_input(INPUT_POST, 'transaction_status', FILTER_SANITIZE_STRING);

            try {
                if ($transaction_status === 'approve') {
                    $stmt = $conn->prepare(
                        "SELECT t.amount, t.id 
                         FROM transactions t
                         WHERE t.id = :id"
                    );
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($transaction) {
                        $stmt = $conn->prepare("UPDATE project SET coin_balance = coin_balance + :amount WHERE username = :username");
                        $stmt->bindParam(':amount', $transaction['amount']);
                        $stmt->bindParam(':username', $transaction['username']);
                        $stmt->execute();

                        $stmt = $conn->prepare("DELETE FROM transactions WHERE id = :id");
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        $message = "Transaksi berhasil diverifikasi dan saldo telah diperbarui.";
                    } else {
                        $message = "Transaksi tidak ditemukan atau tidak valid.";
                    }
                } elseif ($transaction_status === 'reject') {
                    $stmt = $conn->prepare("DELETE FROM transactions WHERE id = :id");
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();

                    $message = "Permintaan transaksi telah ditolak.";
                } else {
                    $message = "Status transaksi tidak valid.";
                }
            } catch (PDOException $e) {
                $message = "Gagal memverifikasi transaksi: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}

// Ambil data untuk tabel
$topups = $conn->query("SELECT id, amount FROM topup_requests WHERE status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);
$transactions = $conn->query("SELECT id, amount FROM transactions WHERE status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);
$users = $conn->query("SELECT id, username, role FROM project")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Securiti Siber Indonesia</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f8fc;
            color: #333;
            line-height: 1.6;
            display: flex;
        }

        .sideboard {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
            height: 100vh;
        }

        .sideboard h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #00C29D;
        }

        .sideboard ul {
            list-style: none;
            padding: 0;
        }

        .sideboard ul li {
            margin: 15px 0;
        }

        .sideboard ul li a {
            text-decoration: none;
            color: white;
            font-size: 1rem;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sideboard ul li a:hover,
        .sideboard ul li a.active {
            background-color: #00C29D;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px 25px;
        }

        .dashboard {
            text-align: center;
            background: linear-gradient(to bottom right, #00C29D, #02A47C);
            color: white;
            padding: 20px;
            border-radius: 10px;
        }

        .dashboard h1 {
            font-size: 2.5rem;
        }

        .dashboard p {
            font-size: 1.2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table th {
            background-color: #00C29D;
            color: white;
            text-align: left;
        }

        form button {
            margin: 5px;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            background-color: #00C29D;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #028A68;
        }
    </style>
</head>

<body>
    <div class="sideboard">
        <h2>Menu Admin</h2>
        <ul>
            <li><a href="?page=roles" class="<?= $_GET['page'] === 'roles' ? 'active' : '' ?>">Role</a></li>
            <li><a href="?page=transactions" class="<?= $_GET['page'] === 'transactions' ? 'active' : '' ?>">Transaksi</a></li>
            <li><a href="hello.php#home">User   Dashboard</a></li>
            <li><a href="logout.php">Keluar</a></li>
        </ul>
    </div>

    <div class="main-content">
        <?php
        $page = $_GET['page'] ?? 'dashboard';

        if ($page === 'roles') {
            echo '<h2>Manage User Roles</h2>';
            echo '<table>';
            echo '<thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Action</th></tr></thead>';
            echo '<tbody>';
            foreach ($users as $user) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($user['id']) . '</td>';
                echo '<td>' . htmlspecialchars($user['username']) . '</td>';
                echo '<td>' . htmlspecialchars($user['role']) . '</td>';
                echo '<td>';
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="id" value="' . htmlspecialchars($user['id']) . '" />';
                echo '<select name="new_role">';
                echo '<option value="user"' . ($user['role'] === 'user' ? ' selected' : '') . '>User</option>';
                echo '<option value="admin"' . ($user['role'] === 'admin' ? ' selected' : '') . '>Admin</option>';
                echo '</select>';
                echo '<button type="submit">Update Role</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } elseif ($page === 'transactions') {
            echo '<h2>Top-Up Requests</h2>';
            echo '<table>';
            echo '<thead><tr><th>ID</th><th>Amount</th><th>Action</th></tr></thead>';
            echo '<tbody>';
            foreach ($topups as $topup) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($topup['id']) . '</td>';
                echo '<td>' . htmlspecialchars($topup['amount']) . '</td>';
                echo '<td>';
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="id" value="' . htmlspecialchars($topup['id']) . '" />';
                echo '<button type="submit" name="verification_status" value="approve">Approve</button>';
                echo '<button type="submit" name="verification_status" value="reject">Reject</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';

            echo '<h2>Saldo Transactions</h2>';
            echo '<table>';
            echo '<thead><tr><th>ID</th><th>Amount</th><th>Action</th></tr></thead>';
            echo '<tbody>';
            foreach ($transactions as $transaction) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($transaction['id']) . '</td>';
                echo '<td>' . htmlspecialchars($transaction['amount']) . '</td>';
                echo '<td>';
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="id" value="' . htmlspecialchars($transaction['id']) . '" />';
                echo '<button type="submit" name="transaction_status" value="approve">Approve</button>';
                echo '<button type="submit" name="transaction_status" value="reject">Reject</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="dashboard">';
            echo '<h1>Welcome to Admin Dashboard</h1>';
            echo '<p>Manage users, roles, transactions, and top-ups effectively with this dashboard.</p>';
            echo '</div>';
        }
        ?>
    </div>
</body>

</html>
