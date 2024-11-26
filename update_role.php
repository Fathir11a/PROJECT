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

// Konfigurasi database
$host = 'localhost';
$db = 'membuat_laman_login';
$user = 'root';
$pass = '';

try {
    // Buat koneksi database dengan PDO
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Proses perubahan peran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi data dari form
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $new_role = isset($_POST['new_role']) ? trim($_POST['new_role']) : '';

    // Validasi input user_id dan role
    if ($user_id > 0 && in_array($new_role, ['user', 'admin'])) {
        try {
            // Perbarui peran pengguna di database
            $query = $conn->prepare("UPDATE project SET role = :role WHERE id = :id");
            $query->bindParam(':role', $new_role, PDO::PARAM_STR);
            $query->bindParam(':id', $user_id, PDO::PARAM_INT);

            if ($query->execute()) {
                // Redirect ke dashboard setelah berhasil
                header("Location: dashboard.php?success=1&user_id=$user_id&new_role=$new_role");
                exit;
            } else {
                $message = "<p style='color: red;'>Gagal mengupdate peran. Silakan coba lagi.</p>";
            }
        } catch (PDOException $e) {
            $message = "<p style='color: red;'>Terjadi kesalahan: " . $e->getMessage() . "</p>";
        }
    } else {
        $message = "<p style='color: red;'>Peran tidak valid atau ID pengguna tidak valid. Harap coba lagi.</p>";
    }
}
?>

