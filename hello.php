<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'koneksi.php'; // Koneksi ke database
$username = $_SESSION['username'];

// Query untuk mendapatkan data user
$query = "SELECT * FROM project WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $email = $user['email'];
    $name = $user['full_name'];
    $phone = $user['phone'];
    $address = $user['address'];
    $photo = $user['profile_picture']; // Nama file foto profil
    $role = $user['role']; // Role pengguna
    $user_id = $user['user_id']; // Asumsikan ada kolom user_id

    // Pastikan user_id adalah integer dan sesuai dengan struktur tabel
    $sql_coin = "SELECT coin_balance FROM project WHERE username = ?";
    $stmt_coin = $conn->prepare($sql_coin);
    $stmt_coin->bind_param('i', $username);  // 'i' berarti integer
    $stmt_coin->execute();
    $result_coin = $stmt_coin->get_result();

    // Periksa apakah hasil query ada
    if ($result_coin->num_rows > 0) {
        // Ambil nilai coin_balance
        $coin_balance = $result_coin->fetch_assoc()['coin_balance'];
    } else {
        // Jika tidak ada data saldo coin, set nilai default
        $coin_balance = 0;
    }

    // Debug: Tampilkan saldo coin untuk memastikan
    echo "Saldo Coin: " . $coin_balance;
} else {
    echo "Data pengguna tidak ditemukan.";
    exit();
}

// Data layanan siber
$cyber_services = [
    ['title' => 'Cyber Security', 'desc' => 'Melindungi sistem informasi Anda dari ancaman siber.'],
    ['title' => 'Data Encryption', 'desc' => 'Mengamankan data penting dengan teknologi enkripsi terkini.'],
    ['title' => 'Penetration Testing', 'desc' => 'Mengidentifikasi celah keamanan dengan pengujian penetrasi.'],
    ['title' => 'Incident Response', 'desc' => 'Respon cepat untuk menangani insiden keamanan siber.'],
    ['title' => 'Threat Intelligence', 'desc' => 'Memahami ancaman terbaru untuk perlindungan lebih baik.'],
    ['title' => 'Cloud Security', 'desc' => 'Keamanan untuk aplikasi dan data berbasis cloud.']
];

// Query untuk mendapatkan semua pengguna (selain yang sedang login)
$query_users = "SELECT id, username FROM project WHERE username != ?";
$stmt_users = $conn->prepare($query_users);
$stmt_users->bind_param('s', $username);
$stmt_users->execute();
$result_users = $stmt_users->get_result();
$users = [];

while ($row = $result_users->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            display: flex;
            color: #34495e;
        }

        .sidebar {
            width: 250px;
            background: #34495e;
            color: white;
            height: 100vh;
            position: fixed;
            display: flex;
            flex-direction: column;
        }

        .sidebar h1 {
            text-align: center;
            padding: 20px;
            background: #2c3e50;
            font-size: 24px;
            font-weight: bold;
            color: #ecf0f1;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            padding: 15px 20px;
            display: block;
            border-bottom: 1px solid #2c3e50;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #1abc9c;
        }

        .sidebar a i {
            font-size: 18px;
        }

        .content {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
            background: #ecf0f1;
            min-height: 100vh;
        }

        .section {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 40px 0;
            /* Jarak atas dan bawah */
        }

        .section h2 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .section footer p {
            margin-top: 40px;
        }

        .service-card {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .service {
            flex: 1;
            min-width: 200px;
            background: #16a085;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .service:hover {
            transform: translateY(-5px);
        }


        .profile-photo {
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            border-radius: 8px;
            overflow: hidden;
            border: 4px solid #16a085;
            background-color: #e0e0e0;
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hidden {
            display: none;
        }

        .menu-item.active {
            background-color: #1abc9c;
        }

        /* Profile Info Container */
        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Pusatkan teks secara horizontal */
            margin-top: 20px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: #34495e;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Table Style (Teks Sejajar dengan Nama) */
        .profile-info table {
            width: auto;
            /* Sesuaikan dengan isi */
            border-collapse: collapse;
            margin: 0 auto;
        }

        .profile-info table th,
        .profile-info table td {
            text-align: left;
            /* Teks rata kiri */
            font-size: 16px;
            font-weight: 500;
            padding: 5px 10px;
            /* Jarak horizontal antara teks */
            vertical-align: middle;
            color: #34495e;
        }

        .profile-info table th {
            font-weight: bold;
        }

        .profile-info table td:first-child {
            padding-right: 5px;
        }

        .profile-info table tr {
            height: 30px;
            /* Jarak vertikal antar baris */
        }


        /* Profil Center */
        .profile-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
            position: relative;
            padding: 20px;
        }

        .profile-content {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        .profile-content h2 {
            margin-bottom: 20px;
            font-size: 28px;
        }

        .profile-content p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .profile-content .btn {
            background-color: #16a085;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            display: inline-block;
        }

        .profile-content .btn:hover {
            background-color: #1abc9c;
        }

        .profile-button {
            background-color:#1abc9c;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .profile-button:hover {
            background-color:#1abc9c;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h1>Dashboard</h1>
        <div>
            <a href="#home" class="menu-item active">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="#profile" class="menu-item">
                <i class="fas fa-user"></i> Profil
            </a>
        </div>
        <a href="pesan.php">
            <i class="fas fa-envelope"></i> Pesan`
        </a>
        <a href="topup.php">
            <i class="fas fa-gem"></i> Topup
        </a>
        <a href="data.php">
            <i class="fas fa-database"></i> Data
        </a>

        <!-- Moved Admin Dashboard link below Data and above Logout -->
        <?php if ($role === 'admin'): ?>
            <a href="admin.php">
                <i class="fas fa-cogs"></i> Admin Dashboard
            </a>
        <?php endif; ?>

        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>


    <!-- Content -->
    <div class="content">
        <!-- Bagian Home -->
        <div id="home" class="section">
            <h2>PT Sekuriti Siber Indonesia</h2>
            <p>PT Sekuriti Siber Indonesia adalah perusahaan yang bergerak dalam bidang layanan keamanan siber untuk melindungi data dan sistem Anda.</p>
            <div class="service-card">
                <?php foreach ($cyber_services as $service): ?>
                    <div class="service">
                        <h3><?php echo $service['title']; ?></h3>
                        <p><?php echo $service['desc']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <footer>
                <p>© 2024 Securiti Dashboard. Built with passion for security. <a href="https://nemosecurity.com/">Learn more</a></p>
            </footer>
        </div>

        <!-- Bagian Profil -->
        <div id="profile" class="section hidden">
            <div class="profile-container">
                <div class="profile-content">
                    <div class="profile-photo">
                        <img src="path/to/photos/<?php echo htmlspecialchars($photo); ?>" alt="Foto Profil">
                    </div>
                    <h2><?php echo htmlspecialchars($name); ?></h2>
                    <div class="profile-info">
                        <table>
                            <tr>
                                <th>Email</th>
                                <td>: <?= htmlspecialchars($user['email']); ?></td>
                            </tr>
                            <tr>
                                <th>No Telepon</th>
                                <td>: <?= htmlspecialchars($user['phone']); ?></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>: <?= htmlspecialchars($user['address']); ?></td>
                            </tr>
                            <tr>
                                <th>Saldo</th>
                                <td>: Rp <?= number_format($user['coin_balance'], 0, ',', '.'); ?></td>
                            </tr>
                        </table>
                    </div>
                    <button class="profile-button" onclick="window.location.href='change_profil.php';">
                        Ganti Profil
                    </button>

                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Update hash di URL dan tampilkan section yang sesuai
        const links = document.querySelectorAll('.menu-item');
        const sections = document.querySelectorAll('.section');

        function changeSection() {
            const hash = window.location.hash;

            // Menyembunyikan semua bagian dan hanya menampilkan bagian yang sesuai dengan hash URL
            sections.forEach(section => {
                section.classList.add('hidden');
                if (hash && section.id === hash.substring(1)) {
                    section.classList.remove('hidden');
                }
            });

            // Menambahkan kelas 'active' pada link yang sesuai dengan hash URL
            links.forEach(link => {
                if (link.getAttribute('href') === hash) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        // Menjalankan fungsi changeSection ketika halaman dimuat dan saat hash di URL berubah
        window.addEventListener('load', changeSection);
        window.addEventListener('hashchange', changeSection);

        // Mengatur ulang hash URL ketika menu diklik, tanpa mereload halaman
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault(); // Mencegah reload halaman
                window.location.hash = this.getAttribute('href'); // Mengubah hash URL
            });
        });
    </script>

</body>

</html>