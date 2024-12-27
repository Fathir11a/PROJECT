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
    $role = $user['role']; // Role pengguna
    $user_id = $user['user_id']; // Asumsikan ada kolom user_id

    // Pastikan user_id adalah integer dan sesuai dengan struktur tabel
    $sql_coin = "SELECT coin_balance FROM project WHERE username = ?";
    $stmt_coin = $conn->prepare($sql_coin);
    $stmt_coin->bind_param('s', $username);  // 's' untuk string karena username adalah string
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

$profile_image = $user['profile_image'] ?? 'profil_image/default.jpg';

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

        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: #34495e;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .profile-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .profile-info table th,
        .profile-info table td {
            text-align: left;
            font-size: 16px;
            font-weight: 500;
            padding: 10px;
            vertical-align: middle;
            color: #34495e;
        }

        .profile-info table th {
            font-weight: bold;
        }

        .profile-info table tr {
            border-bottom: 1px solid #ecf0f1;
        }

        .hidden {
            display: none;
        }

        #image-selector {
            display: none;
            /* Sembunyikan dropdown secara default */
            margin-top: 10px;
            padding: 5px;
        }

        #edit-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #2c3e50;
            color: #ecf0f1;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        #profile-img {
            display: block;
            margin: 0 auto;
        }

        #change-profile-btn {
            display: block;
            margin: 20px auto;
            /* Memastikan tombol ada di tengah */
            background-color: #e74c3c;
            /* Warna merah */
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #change-profile-btn:hover {
            background-color: #c0392b;
            /* Warna merah lebih gelap saat hover */
        }
    </style>

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
            <i class="fas fa-envelope"></i> Pesan
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
            <div class="profile-info">
                <!-- Tampilkan Gambar Profil -->
                <div style="text-align: center; margin-bottom: 20px; position: relative;">
                    <img id="profile-img"
                        src="<?= htmlspecialchars($profile_image); ?>"
                        alt="Foto Profil"
                        style="width: 150px; height: 150px; border-radius: 50%; border: 2px solid #2c3e50; object-fit: cover;">


                    <!-- Ikon Edit -->
                    <div id="edit-icon">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                </div>

                <!-- Pilih Gambar -->
                <select id="image-selector">
                    <?php
                    // Mendapatkan daftar gambar dari folder profil_image
                    $images = glob('profil_image/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
                    foreach ($images as $image) {
                        $imageName = basename($image);
                        echo "<option value='profil_image/$imageName'>$imageName</option>";
                    }
                    ?>
                </select>

                <!-- Tabel Data Pengguna -->
                <table>
                    <tr>
                        <th>Nama</th>
                        <td>: <?= htmlspecialchars($name); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>: <?= htmlspecialchars($email); ?></td>
                    </tr>
                    <tr>
                        <th>No Telepon</th>
                        <td>: <?= htmlspecialchars($phone); ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>: <?= htmlspecialchars($address); ?></td>
                    </tr>
                    <tr>
                        <th>Saldo</th>
                        <td>: Rp <?= number_format($coin_balance, 0, ',', '.'); ?></td>
                    </tr>
                </table>
                <a id="change-profile-btn" href="change_profil.php">Ganti Profil</a>
            </div>
        </div>



        <!-- JavaScript -->
        <script>
            const links = document.querySelectorAll('.menu-item');
            const sections = document.querySelectorAll('.section');

            function changeSection() {
                const hash = window.location.hash || '#home'; // Default ke #home jika tidak ada hash

                // Menyembunyikan semua bagian
                sections.forEach(section => {
                    section.classList.add('hidden');
                });

                // Menampilkan bagian sesuai hash
                const activeSection = document.querySelector(hash);
                if (activeSection) {
                    activeSection.classList.remove('hidden');
                }

                // Menambahkan kelas 'active' pada menu yang sesuai
                links.forEach(link => {
                    if (link.getAttribute('href') === hash) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            }

            // Menjalankan fungsi changeSection ketika halaman dimuat dan hash berubah
            window.addEventListener('load', changeSection);
            window.addEventListener('hashchange', changeSection);

            // Menyesuaikan hash tanpa reload halaman
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.hash = this.getAttribute('href');
                });
            });

            // Skrip untuk mengganti gambar profil
            document.addEventListener('DOMContentLoaded', function() {
                const profileImg = document.getElementById('profile-img');
                const imageSelector = document.getElementById('image-selector');

                // Ubah gambar ketika opsi dipilih
                imageSelector.addEventListener('change', function() {
                    profileImg.src = this.value; // Mengatur src gambar ke nilai yang dipilih
                });

                // Klik pada gambar untuk membuka selector
                profileImg.addEventListener('click', function() {
                    imageSelector.focus();
                });
            });

            // JavaScript untuk mengganti gambar profil
            document.addEventListener('DOMContentLoaded', function() {
                const profileImg = document.getElementById('profile-img');
                const imageSelector = document.getElementById('image-selector');
                const editIcon = document.getElementById('edit-icon');

                // Ubah gambar ketika opsi dipilih
                imageSelector.addEventListener('change', function() {
                    profileImg.src = this.value; // Mengatur src gambar ke nilai yang dipilih
                });

                // Klik ikon edit untuk membuka selector
                editIcon.addEventListener('click', function() {
                    imageSelector.focus(); // Memfokuskan pada dropdown selector
                });
            });

            // Fungsi untuk berpindah bagian
            function changeSection() {
                const hash = window.location.hash || '#home';
                const sections = document.querySelectorAll('.section');
                const links = document.querySelectorAll('.menu-item');

                sections.forEach(section => section.classList.add('hidden'));
                const activeSection = document.querySelector(hash);
                if (activeSection) activeSection.classList.remove('hidden');

                links.forEach(link => {
                    if (link.getAttribute('href') === hash) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            }

            // Fungsi untuk mengatur gambar profil
            document.addEventListener('DOMContentLoaded', function() {
                const profileImg = document.getElementById('profile-img');
                const imageSelector = document.getElementById('image-selector');
                const editIcon = document.getElementById('edit-icon');

                editIcon.addEventListener('click', () => {
                    imageSelector.style.display = 'block';
                    imageSelector.focus();
                });

                imageSelector.addEventListener('change', function() {
                    profileImg.src = this.value;
                    imageSelector.style.display = 'none';
                });

                profileImg.addEventListener('click', () => imageSelector.focus());
            });

            window.addEventListener('load', changeSection);
            window.addEventListener('hashchange', changeSection);

            document.addEventListener('DOMContentLoaded', function() {
                const changeProfileBtn = document.getElementById('change-profile-btn');
                const imageSelector = document.getElementById('image-selector');

                changeProfileBtn.addEventListener('click', () => {
                    imageSelector.style.display = 'block';
                    imageSelector.focus();
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const profileImg = document.getElementById('profile-img');
                const imageSelector = document.getElementById('image-selector');

                imageSelector.addEventListener('change', function() {
                    const selectedImage = this.value;
                    profileImg.src = selectedImage;

                    // Kirim data ke server
                    fetch('save_profile_image.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `image_path=${encodeURIComponent(selectedImage)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status !== 'success') {
                                console.error('Error saving profile image:', data.message);
                            }
                        })
                        .catch(error => console.error('AJAX error:', error));
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
    const changeProfileBtn = document.getElementById('change-profile-btn');

    // Ketika tombol diklik, arahkan ke change_profile.php
    changeProfileBtn.addEventListener('click', function() {
        window.location.href = 'change_profile.php';
    });
});

        </script>

</body>

</html>