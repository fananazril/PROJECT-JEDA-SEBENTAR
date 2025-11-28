<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../action/login/login.php"); 
    exit;
}
$currentUserId = $_SESSION['user_id'] ?? null;
if (!$currentUserId) {
     header("Location: ../action/login/login.php?error=Sesi_tidak_valid");
     exit;
}

require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../action/jurnalaction/listjurnal.php';
require_once __DIR__ . '/../action/jurnalaction/carijurnal.php';

$allowedSortColumns = ['tanggal', 'dibuat'];
$allowedSortOrders = ['ASC', 'DESC'];

$sortBy = isset($_GET['sort_by']) && in_array($_GET['sort_by'], $allowedSortColumns) ? $_GET['sort_by'] : 'tanggal';
$sortOrder = isset($_GET['sort_order']) && in_array(strtoupper($_GET['sort_order']), $allowedSortOrders) ? strtoupper($_GET['sort_order']) : 'DESC';

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
<link rel="stylesheet" href="home.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<header class="header">
    <div class="logo">
        <img src="../assets/Logo1.png" alt="logo" class="logo-img">
    </div>
    
    <div class="top-bar">
        <form class="search-form" method="GET" action="">
            <input type="text" name="keyword" placeholder="Cari jurnal..." id="searchInput" value="<?php echo htmlspecialchars($keyword); ?>">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>
        
        <form class="sort-form" method="GET" action="">
            <?php if (!empty($keyword)): ?>
                <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
            <?php endif; ?>
           <select name="sort_by" onchange="this.form.submit()">
                    <option value="dibuat" <?php echo $sortBy === 'dibuat' ? 'selected' : ''; ?>>Tanggal </option>
            </select>
            <select name="sort_order" onchange="this.form.submit()">
                <option value="DESC" <?php echo $sortOrder === 'DESC' ? 'selected' : ''; ?>>Terbaru</option>
                <option value="ASC" <?php echo $sortOrder === 'ASC' ? 'selected' : ''; ?>>Terlama</option>
            </select>
        </form>
        
        <button class="add-btn" id="addJurnalBtn">
            <i class="fas fa-plus"></i> Tambah
        </button>
    </div>
    
    <nav>
        <a href="../action/useraction/logout.php" class="logout-btn" onclick="return confirm('Yakin ingin logout?');">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</header>

<?php if (!empty($pesan)): ?>
    <div class="search-info"><?php echo $pesan; ?></div>
<?php endif; ?>

<div class="jurnal-list">
    <?php if (!empty($daftarJurnalTampil)):
        foreach ($daftarJurnalTampil as $jurnal):
            $jurnalId = $jurnal['idjurnal'] ?? $jurnal['id'] ?? null;
            $judul = htmlspecialchars($jurnal['judul']);
            $tanggal = htmlspecialchars($jurnal['tanggal']);
            $isi = htmlspecialchars($jurnal['isi']);
            $dibuat = htmlspecialchars($jurnal['dibuat'] ?? '');
    ?>
    <div class="jurnal-card"
        data-id="<?php echo $jurnalId; ?>"
        data-judul="<?php echo $judul; ?>"
        data-tanggal="<?php echo $tanggal; ?>"
        data-isi="<?php echo $isi; ?>"
        data-dibuat="<?php echo $dibuat; ?>">
      
        <div class="jurnal-left">
            <div class="jurnal-title"><?php echo $judul; ?></div>
            <div class="jurnal-tanggal">
                    <i class="far fa-calendar-alt"></i> <?php echo date('d M Y H:i', strtotime($dibuat)); ?>
            </div>
            <div class="jurnal-preview"><?php echo mb_substr($isi, 0, 120) . (mb_strlen($isi) > 120 ? '...' : ''); ?></div>
        </div>

        <?php if ($jurnalId): ?>
        <div class="jurnal-actions">
            <button class="jurnal-actions-btn" onclick="event.stopPropagation()">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu">
                <a href="#" class="dropdown-item edit-link">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="../action/jurnalaction/hapusjurnal.php?id=<?php echo $jurnalId; ?>"
                   class="dropdown-item delete-link"
                   onclick="return confirm('Hapus jurnal ini?');">
                    <i class="fas fa-trash"></i> Hapus
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; 
    else: ?>
    <div class="no-jurnal">
        <i class="fas fa-book-open"></i>
        <p>Belum ada jurnal!</p>
    </div>
    <?php endif; ?>
</div>

<div id="detailJurnalModal" class="modal">
    <div class="modal-content modal-detail">
        <span class="close-btn">&times;</span>
        <div class="detail-header">
            <h2 id="detail-judul"></h2>
        </div>
        <div class="detail-tanggal">
            <div class="detail-tanggal-wrapper">
                <span id="detail-tanggal"></span>
            </div>
            <div class="detail-actions">
                <button class="btn-edit" id="detailEditBtn" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-delete" id="detailDeleteBtn" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="detail-divider"></div>
        <div class="detail-isi" id="detail-isi"></div>
        <div class="detail-timestamp" id="detail-timestamp"></div>
    </div>
</div>

<div id="addJurnalModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2><i class="fas fa-plus-circle"></i> Tambah Jurnal Baru</h2>
        <form action="../action/jurnalaction/tambahjurnal.php" method="POST">
            <label for="judul">Judul</label>
            <input type="text" id="judul" name="judul" placeholder="Masukkan judul jurnal" required>

            <label for="tanggal">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>

            <label for="isi">Isi Jurnal</label>
            <textarea id="isi" name="isi" rows="8" placeholder="Tulis isi jurnal Anda..." required></textarea>

            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Simpan Jurnal
            </button>
        </form>
    </div>
</div>

<div id="editJurnalModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2><i class="fas fa-edit"></i> Edit Jurnal</h2>
        <form action="../action/jurnalaction/editjurnal.php" method="POST">
            <input type="hidden" id="edit-jurnal-id" name="id">

            <label for="edit-judul">Judul</label>
            <input type="text" id="edit-judul" name="judul" placeholder="Masukkan judul jurnal" required>

            <label for="edit-tanggal">Tanggal</label>
            <input type="date" id="edit-tanggal" name="tanggal" required>

            <label for="edit-isi">Isi Jurnal</label>
            <textarea id="edit-isi" name="isi" rows="8" placeholder="Tulis isi jurnal Anda..." required></textarea>

            <button type="submit" class="btn-submit">
                <i class="fas fa-check"></i> Update Jurnal
            </button>
        </form>
    </div>
</div>

<script src="../script/script.js"></script>

<footer class="footer"> 
    <p>&copy; 2025 Jeda Sebentar | Catat Momen Berharga Anda</p>
</footer>

</body>
</html>