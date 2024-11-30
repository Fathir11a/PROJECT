<?php
// Direktori tempat file disimpan
$directory = "files/";

// Periksa apakah file yang dipilih ada di direktori
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Hindari path traversal
    $filepath = $directory . $file;

    if (file_exists($filepath)) {
        // Baca isi file
        $content = file_get_contents($filepath);
    } else {
        $error = "File tidak ditemukan!";
    }
}

// Ambil daftar file dalam direktori
$files = array_diff(scandir($directory), array('.', '..'));

if (isset($content) && pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
    // Parse CSV jika formatnya benar
    $rows = array_map('str_getcsv', explode("\n", $content));
    $header = $rows[0];
    $data = array_slice($rows, 1); // Hilangkan header untuk data
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca File dari Server</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffffff;
            color: #2c3e50;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        h1 {
            color: #1abc9c;
            font-size: 2.5rem;
            margin-top: 40px;
            text-align: center;
        }

        h2 {
            color: #1abc9c;
            font-size: 1.8rem;
            margin: 20px 0;
        }

        form {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        select, button {
            font-size: 1rem;
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 2px solid #1abc9c;
        }

        select {
            background-color: #f4f4f4;
            color: #2c3e50;
        }

        button {
            background-color: #1abc9c;
            color: #ffffff;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #16a085;
            transform: scale(1.05);
        }

        pre, table {
            margin-top: 20px;
            width: 80%;
            max-width: 1000px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        pre {
            background-color: #ecf0f1;
            color: #2c3e50;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 1rem;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 0; /* Menghapus border tabel */
        }

        th {
            background-color: #1abc9c;
            color: white;
        }

        td {
            background-color: #ffffff;
        }

        tr:nth-child(even) {
            background-color: #f4f4f4;
        }

        tr:hover {
            background-color: #dcdde1;
        }

        p {
            text-align: center;
            color: red;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <h1>Baca File dari Server</h1>

    <!-- Form untuk memilih file -->
    <form method="GET">
        <label for="file">Pilih File:</label>
        <select name="file" id="file">
            <option value="">-- Pilih File --</option>
            <?php foreach ($files as $f): ?>
                <option value="<?= htmlspecialchars($f) ?>" <?= isset($_GET['file']) && $_GET['file'] == $f ? 'selected' : '' ?>>
                    <?= htmlspecialchars($f) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Baca File</button>
    </form>

    <!-- Tampilkan isi file -->
    <?php if (isset($content)): ?>
        <h2>Isi File: <?= htmlspecialchars($file) ?></h2>
        <!-- Cek apakah file adalah CSV, jika bukan tampilkan sebagai teks biasa -->
        <?php if (pathinfo($file, PATHINFO_EXTENSION) === 'csv'): ?>
            <!-- Tampilkan Tabel jika file adalah CSV -->
            <table>
                <thead>
                    <tr>
                        <?php foreach ($header as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($row as $cell): ?>
                                <td><?= htmlspecialchars($cell) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <pre><?= htmlspecialchars($content) ?></pre>
        <?php endif; ?>
    <?php elseif (isset($error)): ?>
        <p><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</body>
</html>
