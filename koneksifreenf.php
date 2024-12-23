<?php
// Mengatur informasi koneksi database
$servername = "sql100.infinityfree.com";  // Gantilah dengan hostname server MySQL Anda (dapat ditemukan di panel kontrol InfinityFree)
$username = "if0_37854751";  // Gantilah dengan username MySQL Anda
$password = "oIUE7k7dcdX1xh";  // Gantilah dengan password MySQL Anda
$dbname = "if0_37854751_XXX";  // Gantilah dengan nama database Anda

// Membuat koneksi ke MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa apakah koneksi berhasil
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>