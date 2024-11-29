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

// Update the missing comma
$cyber_services = [
    ['title' => 'Cyber Security', 'desc' => 'Melindungi sistem informasi Anda dari ancaman siber.'],
    ['title' => 'Data Encryption', 'desc' => 'Mengamankan data penting dengan teknologi enkripsi terkini.'],
    ['title' => 'Penetration Testing', 'desc' => 'Mengidentifikasi celah keamanan dengan pengujian penetrasi.'],
    ['title' => 'Incident Response', 'desc' => 'Respon cepat untuk menangani insiden keamanan siber.'],
    ['title' => 'Threat Intelligence', 'desc' => 'Memahami ancaman terbaru untuk perlindungan lebih baik.'],
    ['title' => 'Cloud Security', 'desc' => 'Keamanan untuk aplikasi dan data berbasis cloud.'] // <-- added the missing comma here
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

// Proses pengiriman pesan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $sender_id = $user['id']; // ID pengirim
    $receiver_id = $_POST['receiver_id']; // ID penerima
    $message = $_POST['message']; // Pesan

    // Query untuk menyimpan pesan
    $query_send_message = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt_send_message = $conn->prepare($query_send_message);
    $stmt_send_message->bind_param('iis', $sender_id, $receiver_id, $message);

    if ($stmt_send_message->execute()) {
        echo "Pesan berhasil dikirim!";
    } else {
        echo "Gagal mengirim pesan.";
    }
}

// Proses filter pesan
$filter_user_id = isset($_GET['filter_user']) ? $_GET['filter_user'] : null;

// Query untuk pesan
$query_messages = "SELECT messages.message, messages.sender_id, messages.receiver_id, users.username 
                   FROM messages
                   JOIN project AS users ON messages.sender_id = users.id 
                   WHERE (messages.sender_id = ? OR messages.receiver_id = ?)";

if ($filter_user_id) {
    $query_messages .= " AND (messages.sender_id = ? OR messages.receiver_id = ?)";
}

$query_messages .= " ORDER BY messages.id DESC";
$stmt_messages = $conn->prepare($query_messages);

if ($filter_user_id) {
    $stmt_messages->bind_param('iiii', $user['id'], $user['id'], $filter_user_id, $filter_user_id);
} else {
    $stmt_messages->bind_param('ii', $user['id'], $user['id']);
}

$stmt_messages->execute();
$result_messages = $stmt_messages->get_result();
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

    /* Pesan - Container */
    .messages {
        margin-top: 20px;
        padding: 15px;
        background-color: #ecf0f1; /* Latar belakang putih lembut */
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Bayangan halus */
        color: #34495e; /* Teks warna abu gelap */
    }

    /* Pesan - Tiap Pesan */
    .message {
        margin-bottom: 15px;
        padding: 10px;
        background-color: #ffffff; /* Latar belakang putih */
        border-left: 4px solid #1abc9c; /* Garis hijau terang */
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Bayangan halus */
        color: #34495e; /* Warna teks abu gelap */
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .message:hover {
        transform: scale(1.03); /* Sedikit zoom saat di-hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); /* Bayangan lebih tajam */
    }

    /* Nama Pengirim */
    .message strong {
        color: #1abc9c; /* Hijau terang */
        font-weight: bold;
    }

    /* Form Kirim Pesan */
    form {
        background-color: #ecf0f1; /* Latar belakang putih lembut */
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Bayangan halus */
        color: #34495e; /* Teks abu gelap */
    }

    form label {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 8px;
        display: block;
        color: #34495e; /* Abu gelap */
    }

    form select, form textarea {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #1abc9c; /* Hijau terang */
        background: #ffffff;
        color: #34495e; /* Abu gelap */
        margin-bottom: 15px;
    }

    form button {
        background: #1abc9c; /* Hijau terang */
        border: none;
        padding: 10px 20px;
        color: #ffffff; /* Teks putih */
        font-weight: bold;
        text-transform: uppercase;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    form button:hover {
        background: #16a085; /* Hijau lebih gelap */
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); /* Bayangan hover */
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
            <a href="#send_message" class="menu-item">
                <i class="fas fa-envelope"></i> Pesan
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
            <p>PT Sekuriti Siber Indonesia adalah perusahaan yang bergerak dalam bidang layanan keamanan siber untuk melindungi data dan sistem Anda.</p>
            <div class="service-card">
                <?php foreach ($cyber_services as $service): ?>
                    <div class="service">
                        <h3><?php echo $service['title']; ?></h3>
                        <p><?php echo $service['desc']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Bagian Profil -->
        <div id="profile" class="section hidden">
            <div class="profile-container">
                <div class="profile-content">
                    <div class="profile-photo">
                        <img src="path/to/photos/<?php echo htmlspecialchars($photo); ?>" alt="Foto Profil">
                    </div>
                    <h2><?php echo htmlspecialchars($name); ?></h2>
                    <p>Email: <?php echo htmlspecialchars($email); ?></p>
                    <p>Telepon: <?php echo htmlspecialchars($phone); ?></p>
                    <p>Alamat: <?php echo htmlspecialchars($address); ?></p>
                    <a href="edit_profile.php" class="btn">Edit Profil</a>
                </div>
            </div>
        </div>

        <!-- Bagian Pesan -->
        <div id="send_message" class="section hidden">
            <h2 style="color: #1abc9c">Pesan Anda</h2> 
            <form action="#send_message" method="POST">
                <label for="receiver_id">Kirim ke:</label>
                <select name="receiver_id" required>
                    <?php foreach ($users as $other_user): ?>
                        <option value="<?php echo $other_user['id']; ?>">
                            <?php echo htmlspecialchars($other_user['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="message">Pesan:</label>
                <textarea name="message" rows="5" required></textarea>
                <button type="submit">Kirim</button>
            </form>

            <form method="GET" action="#send_message">
                <label for="filter_user">Pilih Pengguna:</label>
                <select name="filter_user" onchange="this.form.submit()">
                    <option value="">Semua Pengguna</option>
                    <?php foreach ($users as $other_user): ?>
                        <option value="<?php echo $other_user['id']; ?>" 
                            <?php echo (isset($_GET['filter_user']) && $_GET['filter_user'] == $other_user['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($other_user['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="messages">
                <?php if ($result_messages->num_rows > 0): ?>
                    <?php while ($message = $result_messages->fetch_assoc()): ?>
                        <div class="message">
                            <strong><?php echo htmlspecialchars($message['username']); ?>:</strong>
                            <p><?php echo htmlspecialchars($message['message']); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Tidak ada pesan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Fungsi navigasi menu
const menuItems = document.querySelectorAll('.menu-item');
const sections = document.querySelectorAll('.section');

function setActiveSection(event) {
    event.preventDefault(); // Mencegah halaman berganti
    const activeSection = event.target.getAttribute('href'); // Ambil href menu yang diklik

    // Update URL di browser tanpa reload halaman
    window.location.hash = activeSection;

    // Menentukan menu aktif
    menuItems.forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('href') === activeSection) {
            item.classList.add('active');
        }
    });

    // Menampilkan konten yang sesuai
    sections.forEach(section => {
        section.classList.add('hidden');
        if ("#" + section.id === activeSection) {
            section.classList.remove('hidden');
        }
    });
}

menuItems.forEach(item => {
    item.addEventListener('click', setActiveSection);
});

document.addEventListener('DOMContentLoaded', () => {
    // Periksa hash yang ada di URL saat pertama kali dimuat
    const currentHash = window.location.hash || "#home";
    // Setkan menu yang aktif
    menuItems.forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('href') === currentHash) {
            item.classList.add('active');
        }
    });

    // Setkan section yang sesuai dengan hash
    sections.forEach(section => {
        section.classList.add('hidden');
        if ("#" + section.id === currentHash) {
            section.classList.remove('hidden');
        }
    });
});

    </script>
</body>
</html>
