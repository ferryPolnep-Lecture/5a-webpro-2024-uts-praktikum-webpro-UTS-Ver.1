<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "uts5a";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Variabel untuk pesan
$pesan = "";

// Membuat tabel jika belum ada
$sql = "CREATE TABLE IF NOT EXISTS krs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nim VARCHAR(10) NOT NULL,
    kelas ENUM('5A', '5B', '5C', '5D', '5E') NOT NULL,
    mata_kuliah TEXT NOT NULL
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error membuat tabel: " . $conn->error;
}

// Update data jika ada permintaan edit
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $kelas = $_POST['kelas'];
    $mata_kuliah = isset($_POST['mata_kuliah']) ? implode(", ", $_POST['mata_kuliah']) : '';

    // Validasi data
    if (!preg_match("/^[a-zA-Z\s]+$/", $nama)) {
        $pesan = "Nama hanya boleh berisi huruf!<br>";
    } elseif (!preg_match("/^\d{10}$/", $nim)) {
        $pesan = "NIM harus berisi 10 digit angka!<br>";
    } elseif (empty($kelas)) {
        $pesan = "Kelas harus dipilih!<br>";
    } elseif (empty($mata_kuliah)) {
        $pesan = "Pilih minimal satu mata kuliah!<br>";
    } else {
        // Menyimpan data ke tabel
        $stmt = $conn->prepare("UPDATE krs SET nama = ?, nim = ?, kelas = ?, mata_kuliah = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nama, $nim, $kelas, $mata_kuliah, $id);

        if ($stmt->execute()) {
            $pesan = "Data berhasil diupdate!<br>";
        } else {
            $pesan = "Error: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }
}

// Delete data jika ada permintaan hapus
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM krs WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $pesan = "Data berhasil dihapus!<br>";
    } else {
        $pesan = "Error: " . $stmt->error . "<br>";
    }

    $stmt->close();
}

// Memproses data dari form untuk penambahan data baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $kelas = $_POST['kelas'];
    $mata_kuliah = isset($_POST['mata_kuliah']) ? implode(", ", $_POST['mata_kuliah']) : '';

    // Validasi data
    if (!preg_match("/^[a-zA-Z\s]+$/", $nama)) {
        $pesan = "Nama hanya boleh berisi huruf!<br>";
    } elseif (!preg_match("/^\d{10}$/", $nim)) {
        $pesan = "NIM harus berisi 10 digit angka!<br>";
    } elseif (empty($kelas)) {
        $pesan = "Kelas harus dipilih!<br>";
    } elseif (empty($mata_kuliah)) {
        $pesan = "Pilih minimal satu mata kuliah!<br>";
    } else {
        // Menyimpan data ke tabel
        $stmt = $conn->prepare("INSERT INTO krs (nama, nim, kelas, mata_kuliah) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $nim, $kelas, $mata_kuliah);

        if ($stmt->execute()) {
            $pesan = "Data berhasil disimpan!<br>";
        } else {
            $pesan = "Error: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }
}

// Ambil data jika sedang dalam mode edit
$nama = '';
$nim = '';
$kelas = '';
$mata_kuliah = [];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM krs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $nama = $row['nama'];
        $nim = $row['nim'];
        $kelas = $row['kelas'];
        $mata_kuliah = explode(", ", $row['mata_kuliah']);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form KRS Mahasiswa</title>
</head>
<body>
    <h2>Form KRS Mahasiswa</h2>
    <form action="" method="POST">
        <input type="hidden" name="id" value="<?php echo isset($_GET['edit']) ? $_GET['edit'] : ''; ?>">
        <label for="nama">Nama Mahasiswa:</label>
        <input type="text" id="nama" name="nama" required pattern="[A-Za-z\s]+" title="Nama hanya boleh berisi huruf" value="<?php echo htmlspecialchars($nama); ?>"><br><br>

        <label for="nim">NIM:</label>
        <input type="text" id="nim" name="nim" required pattern="\d{10}" title="NIM harus berisi 10 digit angka" value="<?php echo htmlspecialchars($nim); ?>"><br><br>

        <label for="kelas">Kelas:</label>
        <select id="kelas" name="kelas" required>
            <option value="">Pilih Kelas</option>
            <option value="5A" <?php echo ($kelas == '5A') ? 'selected' : ''; ?>>5A</option>
            <option value="5B" <?php echo ($kelas == '5B') ? 'selected' : ''; ?>>5B</option>
            <option value="5C" <?php echo ($kelas == '5C') ? 'selected' : ''; ?>>5C</option>
            <option value="5D" <?php echo ($kelas == '5D') ? 'selected' : ''; ?>>5D</option>
            <option value="5E" <?php echo ($kelas == '5E') ? 'selected' : ''; ?>>5E</option>
        </select><br><br>

        <label for="mata_kuliah">Mata Kuliah Pilihan:</label><br>
        <input type="checkbox" name="mata_kuliah[]" value="Web Application Development" <?php echo in_array("Web Application Development", $mata_kuliah) ? 'checked' : ''; ?>> Web Application Development<br>
        <input type="checkbox" name="mata_kuliah[]" value="Mobile Application Development" <?php echo in_array("Mobile Application Development", $mata_kuliah) ? 'checked' : ''; ?>> Mobile Application Development<br>
        <input type="checkbox" name="mata_kuliah[]" value="UI/UX Design" <?php echo in_array("UI/UX Design", $mata_kuliah) ? 'checked' : ''; ?>> UI/UX Design<br>
        <input type="checkbox" name="mata_kuliah[]" value="Software Engineering" <?php echo in_array("Software Engineering", $mata_kuliah) ? 'checked' : ''; ?>> Software Engineering<br>
        <input type="checkbox" name="mata_kuliah[]" value="Data Engineering" <?php echo in_array("Data Engineering", $mata_kuliah) ? 'checked' : ''; ?>> Data Engineering<br><br>

        <button type="submit" name="submit">Submit</button>
        <button type="submit" name="update">Update</button>
    </form>

    <!-- Tampilkan data KRS setelah form -->
    <?php
    
    // Menampilkan data yang ada di tabel
    $sql = "SELECT * FROM krs";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Data KRS Mahasiswa</h2>";
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Kelas</th>
                    <th>Mata Kuliah</th>
                    <th>Aksi</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["nama"] . "</td>
                    <td>" . $row["nim"] . "</td>
                    <td>" . $row["kelas"] . "</td>
                    <td>" . $row["mata_kuliah"] . "</td>
                    <td>
                        <a href='?edit=" . $row["id"] . "'>Edit</a> | 
                        <a href='?delete=" . $row["id"] . "'>Delete</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Tidak ada data.<br>";
    }

    // Tampilkan pesan di bawah tabel
    if (!empty($pesan)) {
        echo "<br><div>" . $pesan . "</div>";
    }

    $conn->close();
?>
</body>
</html>
