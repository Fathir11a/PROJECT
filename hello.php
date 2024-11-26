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
    $photo = $user['profile_picture']; // Sesuaikan nama kolom dengan database
} else {
    echo "Data pengguna tidak ditemukan.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cek jika file diupload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size']; // Perbaiki typo disini
        $fileType = $_FILES['profile_picture']['type'];

        // Tentukan ekstensi file yang diizinkan
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        if (in_array(strtolower($fileExtension), $allowedExtensions)) {
            // Tentukan lokasi penyimpanan file
            $uploadDir = 'uploads/';
            $newFileName = uniqid() . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            // Pindahkan file ke folder uploads
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Update foto di database
                $query = "UPDATE project SET profile_picture = ? WHERE username = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $newFileName, $username);
                if ($stmt->execute()) {
                    echo "Foto profil berhasil diubah.";
                    // Redirect untuk mencegah resubmission form
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

        .section p {
            line-height: 1.6;
            font-size: 16px;
            color: #7f8c8d;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            border: 4px solid #16a085;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background: #ecf0f1;
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        ul {
            list-style: disc;
            margin: 10px 20px;
        }

        ul li {
            font-size: 16px;
            color: #34495e;
            line-height: 1.8;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #16a085;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #1abc9c;
        }

        .hidden {
            display: none;
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
            <p>PT Sekuriti Siber Indonesia adalah perusahaan yang bergerak di bidang keamanan siber dan teknologi informasi.</p>
            <ul>
                <li>Cyber Security</li>
                <li>Data Encryption</li>
                <li>Penetration Testing</li>
                <li>Incident Response</li>
                <li>Threat Intelligence</li>
                <li>Cloud Security</li>
            </ul>
        </div>

        <!-- Bagian Profil -->
        <div id="profile" class="section hidden">
            <div class="profile-photo">
                <?php if ($photo): ?>
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

        <!-- Form Ubah Foto Profil -->
        <div id="change-profile" class="section hidden">
            <h2>Ubah Foto Profil</h2>
            <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                <label for="profile_picture">Pilih Foto:</label>
                <input type="file" name="profile_picture" id="profile_picture" required>
                <button type="submit">Ubah Foto</button>
            </form>
        </div>
    </div>

    <script>
        // Fungsi untuk mengatur visibilitas konten berdasarkan menu
        const sections = document.querySelectorAll('.section');
        const menuItems = document.querySelectorAll('.menu-item');

        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                // Atur menu aktif
                menuItems.forEach(menu => menu.classList.remove('active'));
                this.classList.add('active');

                // Tampilkan konten yang sesuai
                const target = this.getAttribute('href').substring(1);
                sections.forEach(section => section.classList.add('hidden'));
                document.getElementById(target).classList.remove('hidden');
            });
        });
    </script>
</body>
</html>
