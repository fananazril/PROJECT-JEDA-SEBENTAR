<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: /../action/login/login.php"); 
    exit;
}
$currentUserId = $_SESSION['user_id'] ?? null;
if (!$currentUserId) {
     header("Location: /../action/login/login.php?error=Sesi_tidak_valid");
     exit;
}

require_once __DIR__ . '/../database/database.php';
require_once 'functions_jurnal.php'; // berisi fungsi getAllJurnal, cariJurnalByJudul

$allowedSortColumns = ['id','judul','tanggal'];
$allowedSortOrders = ['ASC', 'DESC'];

$sortBy = isset($_GET['sort_by']) && in_array($_GET['sort_by'], $allowedSortColumns) ? $_GET['sort_by'] : 'judul';
$sortOrder = isset($_GET['sort_order']) && in_array(strtoupper($_GET['sort_order']), $allowedSortOrders) ? strtoupper($_GET['sort_order']) : 'ASC';

$daftarJurnalTampil = [];
$keyword = '';
$pesan = '';

if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
    $keyword = trim($_GET['keyword']);
    if (function_exists('cariJurnalByJudul')) {
        $daftarJurnalTampil = cariJurnalByJudul($conn, $keyword, $currentUserId, $sortBy, $sortOrder);
        if (empty($daftarJurnalTampil)) {
            $pesan = "Tidak ada jurnal ditemukan untuk: <strong>" . htmlspecialchars($keyword) . "</strong>";
        } else {
            $pesan = "Menampilkan hasil pencarian untuk: <strong>" . htmlspecialchars($keyword) . "</strong>";
        }
    } else {
        $pesan = "Error: Fungsi pencarian tidak ditemukan.";
    }
} else {
    if (function_exists('getAllJurnal')) {
        $daftarJurnalTampil = getAllJurnal($conn, $currentUserId, $sortBy, $sortOrder);
    } else {
        $pesan = "Error: Fungsi daftar jurnal tidak ditemukan.";
    }
}

if ($conn) mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jurnal Pribadi</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<header class="header">
    <h1>Jurnal Pribadi</h1>
    <nav>
        <button id="dark-toggle">🌙</button>
        <a href="/../action/useraction/logout.php" onclick="return confirm('Yakin ingin logout?');">Logout</a>
    </nav>
</header>

<?php if (!empty($pesan)): ?>
<div class="search-info"><?php echo $pesan; ?></div>
<?php endif; ?>

<div class="jurnal-list">
    <?php if (!empty($daftarJurnalTampil)):
        foreach ($daftarJurnalTampil as $jurnal):
            $jurnalId = $jurnal['id'] ?? null;
            $judul = htmlspecialchars($jurnal['judul']);
            $tanggal = htmlspecialchars($jurnal['tanggal']);
            $isi = htmlspecialchars($jurnal['isi']);
    ?>
    <div class="jurnal-card"
         data-id="<?php echo $jurnalId; ?>"
         data-judul="<?php echo $judul; ?>"
         data-tanggal="<?php echo $tanggal; ?>"
         data-isi="<?php echo $isi; ?>">
        <div class="jurnal-title"><?php echo $judul; ?></div>
        <div class="jurnal-tanggal"><?php echo $tanggal; ?></div>

        <?php if ($jurnalId): ?>
        <div class="jurnal-actions">
            <button class="jurnal-actions-btn"><i class="fas fa-ellipsis-v"></i></button>
            <div class="dropdown-menu">
                <a class="dropdown-item edit-link">Edit</a>
                <a href="/../action/jurnalaction/hapusjurnal.php echo $jurnalId; ?>"
                   class="dropdown-item delete-link"
                   onclick="return confirm('Hapus jurnal ini?');">Hapus</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; 
    else: ?>
    <div class="no-jurnal">Belum ada jurnal.</div>
    <?php endif; ?>
</div>

<!-- Modal Tambah -->
<div id="addJurnalModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Tambah Jurnal Baru</h2>
        <form action="/../action/jurnalaction/tambahjurnal.php" method="POST">
            <label for="judul">Judul:</label>
            <input type="text" id="judul" name="judul" required>

            <label for="tanggal">Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal" required>

            <label for="isi">Isi:</label>
            <textarea id="isi" name="isi" rows="6" required></textarea>

            <button type="submit">Simpan Jurnal</button>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editJurnalModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Edit Jurnal</h2>
        <form action="/../action/useraction/editjurnal.php" method="POST">
            <input type="hidden" id="edit-jurnal-id" name="id">

            <label for="edit-judul">Judul:</label>
            <input type="text" id="edit-judul" name="judul" required>

            <label for="edit-tanggal">Tanggal:</label>
            <input type="date" id="edit-tanggal" name="tanggal" required>

            <label for="edit-isi">Isi:</label>
            <textarea id="edit-isi" name="isi" rows="6" required></textarea>

            <button type="submit">Update Jurnal</button>
        </form>
    </div>
</div>

<script src="/../script/script.js"></script>
<footer>
<p>© 2025 Jurnal Pribadi</p>
</footer>
</body>
</html>
