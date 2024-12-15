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
</head>

<body>
    <style>
        /* Global Styling */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f7f8fc;
    color: #333;
    line-height: 1.6;
}

/* Navbar */
.navbar {
    background-color: #00C29D;
    color: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.navbar h1 {
    font-size: 1.8rem;
    margin: 0;
}

.navbar a {
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    margin: 0 12px;
    border-radius: 5px;
    font-size: 1rem;
    transition: background-color 0.3s, transform 0.2s;
}

.navbar a:hover {
    background-color: #495057;
    transform: scale(1.05);
}

/* Dashboard Container */
.dashboard {
    padding: 40px 25px;
    max-width: 1200px;
    margin: 20px auto;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.dashboard h2 {
    text-align: center;
    color: #343a40;
    font-size: 2rem;
    margin-bottom: 20px;
}

/* Success and Error Messages */
.success, .error {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    font-weight: bold;
    text-align: center;
}

.success {
    background-color: #d4edda;
    color: #155724;
}

.error {
    background-color: #f8d7da;
    color: #721c24;
}

/* Table Styling */
.table-container {
    margin-top: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
    border-radius: 5px;
    overflow: hidden;
}

table th,
table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    font-size: 1rem;
}

table th {
    background-color: #f8f9fa;
    color: #495057;
    font-size: 1.1rem;
}

table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tbody tr:hover {
    background-color: #f1f3f5;
}

/* Form Styling */
form {
    display: inline;
}

button,
select {
    padding: 10px 20px;
    margin-top: 10px;
    border: none;
    background-color: #00C29D;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
    font-size: 1rem;
}

button:hover,
select:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

button[type="submit"] {
    cursor: pointer;
}

select {
    padding: 8px;
    font-size: 1rem;
    border: 1px solid #ddd;
    border-radius: 5px;
}

/* Responsive Styling */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .navbar a {
        margin: 8px 0;
        padding: 10px;
        font-size: 1.1rem;
    }

    .dashboard {
        padding: 25px;
    }

    table th,
    table td {
        font-size: 14px;
        padding: 12px;
    }

    button, select {
        width: 100%;
        padding: 12px;
    }
}

    </style>
    <div class="navbar">
        <h1>Securiti Siber Indonesia</h1>
        <div>
            <a href="hello.php#home">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="dashboard">
        <h2>Admin Dashboard</h2>

        <?php if (!empty($message)) {
            echo "<p class='message'>$message</p>";
        } ?>

        <div class="table-container">
            <h3>Top-Up Requests</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topups as $topup) { ?>
                        <tr>
                            <td><?= htmlspecialchars($topup['id']) ?></td>
                            <td><?= htmlspecialchars($topup['amount']) ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($topup['id']) ?>" />
                                    <button type="submit" name="verification_status" value="approve">Approve</button>
                                    <button type="submit" name="verification_status" value="reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h3>Transactions</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction) { ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['id']) ?></td>
                            <td><?= htmlspecialchars($transaction['amount']) ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($transaction['id']) ?>" />
                                    <button type="submit" name="transaction_status" value="approve">Approve</button>
                                    <button type="submit" name="transaction_status" value="reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h3>Manage User Roles</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>" />
                                    <select name="new_role">
                                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button type="submit">Update Role</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>