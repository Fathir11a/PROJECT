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
} else {
    echo "Data pengguna tidak ditemukan.";
    exit();
}

// Tambahan variabel untuk menu Home
$cyber_services = [
    ['title' => 'Cyber Security', 'desc' => 'Melindungi sistem informasi Anda dari ancaman siber.'],
    ['title' => 'Data Encryption', 'desc' => 'Mengamankan data penting dengan teknologi enkripsi terkini.'],
    ['title' => 'Penetration Testing', 'desc' => 'Mengidentifikasi celah keamanan dengan pengujian penetrasi.'],
    ['title' => 'Incident Response', 'desc' => 'Respon cepat untuk menangani insiden keamanan siber.'],
    ['title' => 'Threat Intelligence', 'desc' => 'Memahami ancaman terbaru untuk perlindungan lebih baik.'],
    ['title' => 'Cloud Security', 'desc' => 'Keamanan untuk aplikasi dan data berbasis cloud.'],
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proses unggah foto profil
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($fileExtension), $allowedExtensions)) {
            $uploadDir = 'uploads/';
            $newFileName = uniqid() . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $query = "UPDATE project SET profile_picture = ? WHERE username = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $newFileName, $username);
                if ($stmt->execute()) {
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo "Gagal mengupdate foto.";
                }
            } else {
                echo "Gagal mengupload file.";
            }
        } else {
            echo "Jenis file tidak diizinkan.";
        }
    }
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

        .sidebar a:hover, .sidebar a.active {
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
            margin-bottom: 20px;
        }

        .section h2 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 15px;
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
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Bagian Home -->
        <div id="home" class="section">
            <h2>PT Sekuriti Siber Indonesia</h2>
            <p>PT Sekuriti Siber Indonesia adalah perusahaan yang bergerak di bidang keamanan siber dan teknologi informasi. Kami memberikan layanan berikut:</p>
            <div class="service-card">
                <?php foreach ($cyber_services as $service): ?>
                    <div class="service">
                        <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                        <p><?php echo htmlspecialchars($service['desc']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Bagian Profil -->
        <div id="profile" class="section hidden">
            <div class="profile-container">
                <div class="profile-content">
                    <div class="profile-photo">
                        <?php if ($photo && file_exists("uploads/$photo")): ?>
                            <img src="uploads/<?php echo htmlspecialchars($photo); ?>" alt="Foto Profil">
                        <?php else: ?>
                            <p>Tidak ada foto.</p>
                        <?php endif; ?>
                    </div>
                    <h2>Profil Pengguna</h2>
                    <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($name); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($phone); ?></p>
                    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($address); ?></p>
                    <a href="change_profil.php" class="btn">Ubah Profil</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sections = document.querySelectorAll('.section');
        const menuItems = document.querySelectorAll('.menu-item');

        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                menuItems.forEach(menu => menu.classList.remove('active'));
                this.classList.add('active');

                const target = this.getAttribute('href').substring(1);
                sections.forEach(section => section.classList.add('hidden'));
                document.getElementById(target).classList.remove('hidden');
            });
        });
    </script>
</body>
</html>
