/* Global Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Navbar */
        .navbar {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            margin: 0;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #575757;
        }

        /* Dashboard Container */
        .dashboard {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard h2 {
            text-align: center;
            color: #333;
        }

        /* Success and Error Messages */
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        /* Table Styling */
        .table-container {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #e9ecef;
        }

        /* Form Styling */
        form {
            display: inline;
        }

        button,
        select {
            padding: 8px 15px;
            margin-top: 5px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover,
        select:hover {
            background-color: #45a049;
        }

        button[type="submit"] {
            cursor: pointer;
        }

        select {
            padding: 5px;
            border-radius: 5px;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar a {
                margin: 5px 0;
            }

            .dashboard {
                padding: 20px;
            }

            table th,
            table td {
                font-size: 14px;
                padding: 8px;
            }
        }


        // Ambil data untuk tabel
try {
    // Mengambil data topup_requests dengan username dari tabel project
    $topups = $conn->query("SELECT tr.id, tr.amount, p.username 
                            FROM topup_requests tr
                            JOIN project p ON tr.username = p.username
                            WHERE tr.status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);

    // Mengambil data transactions dengan id yang mengacu ke id pada tabel project
    $transactions = $conn->query("SELECT t.id, t.amount, p.username 
                                  FROM transactions t
                                  JOIN project p ON t.id = p.id  -- Ganti t.user_id dengan t.id dan p.id
                                  WHERE t.status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);

    $users = $conn->query("SELECT id, username, role FROM project")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data: " . htmlspecialchars($e->getMessage()));
}
