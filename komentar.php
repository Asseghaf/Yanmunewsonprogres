<?php
// Sambungan database
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'portal_berita';

// Buat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tabel untuk komentar
$sql_create_table = "CREATE TABLE IF NOT EXISTS komentar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    berita_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    isi_komentar TEXT NOT NULL,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (berita_id) REFERENCES berita(id)
)";
$conn->query($sql_create_table);

// Fungsi untuk menambahkan komentar
function tambah_komentar($berita_id, $nama, $email, $isi_komentar) {
    global $conn;
    
    // Bersihkan input untuk mencegah SQL injection
    $berita_id = mysqli_real_escape_string($conn, $berita_id);
    $nama = mysqli_real_escape_string($conn, $nama);
    $email = mysqli_real_escape_string($conn, $email);
    $isi_komentar = mysqli_real_escape_string($conn, $isi_komentar);
    
    $sql = "INSERT INTO komentar (berita_id, nama, email, isi_komentar) 
            VALUES ('$berita_id', '$nama', '$email', '$isi_komentar')";
    
    return $conn->query($sql);
}

// Fungsi untuk mengambil komentar berdasarkan ID berita
function ambil_komentar($berita_id) {
    global $conn;
    
    $sql = "SELECT * FROM komentar 
            WHERE berita_id = '$berita_id' 
            ORDER BY tanggal DESC";
    
    $result = $conn->query($sql);
    return $result;
}

// Contoh penggunaan di halaman detail berita
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $berita_id = $_POST['berita_id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $isi_komentar = $_POST['isi_komentar'];
    
    if (tambah_komentar($berita_id, $nama, $email, $isi_komentar)) {
        echo "Komentar berhasil ditambahkan!";
    } else {
        echo "Gagal menambahkan komentar.";
    }
}
?>

<!-- Form Komentar -->
<form method="POST" action="">
    <input type="hidden" name="berita_id" value="<?php echo $id_berita_saat_ini; ?>">
    
    <label for="nama">Nama:</label>
    <input type="text" name="nama" required>
    
    <label for="email">Email:</label>
    <input type="email" name="email" required>
    
    <label for="isi_komentar">Komentar:</label>
    <textarea name="isi_komentar" required></textarea>
    
    <button type="submit">Kirim Komentar</button>
</form>

<!-- Menampilkan Komentar -->
<?php
$komentar = ambil_komentar($id_berita_saat_ini);
if ($komentar->num_rows > 0) {
    while ($row = $komentar->fetch_assoc()) {
        echo "<div class='komentar'>";
        echo "<strong>" . htmlspecialchars($row['nama']) . "</strong>";
        echo "<p>" . htmlspecialchars($row['isi_komentar']) . "</p>";
        echo "<small>" . $row['tanggal'] . "</small>";
        echo "</div>";
    }
} else {
    echo "Belum ada komentar.";
}
?>