<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uts5a";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Use the created database
$conn->select_db("uts5a"); 

// Ambil data dari form
$nama = $_POST['name'];
$nim = $_POST['nim'];
$kelas = $_POST['kelas'];
$matkul = implode(", ", $_POST['makul']);

// Validasi data
if (empty($nama) || empty($nim) || empty($kelas)) {
    echo "Semua field harus diisi";
    exit;
  }
  
  if (!preg_match("/^[a-zA-Z]+$/", $nama)) {
    echo "Nama hanya boleh berisi huruf";
    exit;
  }
  
  if (!preg_match("/^[0-9]{10}$/", $nim)) {
    echo "NIM harus terdiri dari 10 angka";
    exit;
  } 


// Kueri INSERT data
$sql = "INSERT INTO krs (nama, nim, kelas, matkul) VALUES ('$nama', '$nim', '$kelas', '$matkul')";

if ($conn->query($sql) === TRUE) {
  echo "Data berhasil disimpan";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

// Fungsi untuk membaca data 
function readData() {
  global $conn;
  $sql = "SELECT * FROM krs";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row["id"] . "</td>";
          echo "<td>" . $row["nama"] . "</td>";
          echo "<td>" . $row["nim"]  . "</td>";
          echo "<td>" . $row["kelas"] . "</td>";
          echo "<td>" . $row["matkul"] . "</td>";
          echo '<td><button onclick="editData(' . $row["id"] . ')">Edit</button> <button onclick="deleteData(' . $row["id"] . ')">Hapus</button></td>';
          echo "</tr>";
      }
  } else {
      echo "<tr><td colspan='6'>0 results</td></tr>";
  }
}

// Fungsi untuk memperbarui data 
function updateData($id, $namaBaru) {
  global $conn;
  $sql = "UPDATE krs SET nama='$namaBaru' WHERE id=$id";
  if ($conn->query($sql) === TRUE) {
      echo "Record updated successfully";
  } else {
      echo "Error updating record: " . $conn->error;
  }
}

// Fungsi untuk menghapus data 
function deleteData($id) {
  global $conn;
  $sql = "DELETE FROM krs WHERE id=$id";
  if ($conn->query($sql) === TRUE) {
      echo "Record deleted successfully";
  } else {
      echo "Error deleting record: " . $conn->error;
  }
}

// tutup koneksi
$conn->close();


?>
