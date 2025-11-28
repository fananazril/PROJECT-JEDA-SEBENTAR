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

$allowedSortColumns = ['judul', 'tanggal', 'dibuat'];
$allowedSortOrders = ['ASC', 'DESC'];

$sortBy = isset($_GET['sort_by']) && in_array($_GET['sort_by'], $allowedSortColumns) ? $_GET['sort_by'] : 'dibuat';
$sortOrder = isset($_GET['sort_order']) && in_array(strtoupper($_GET['sort_order']), $allowedSortOrders) ? strtoupper($_GET['sort_order']) : 'DESC';

$itemsPerPage = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $itemsPerPage;

$daftarJurnalTampil = [];
$keyword = '';
$pesan = '';
$totalItems = 0;
$totalPages = 0;

if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
    $keyword = trim($_GET['keyword']);
    if (function_exists('cariJurnalByJudul')) {
        $totalItems = getTotalCariJurnal($conn, $keyword, $currentUserId);
        $totalPages = ceil($totalItems / $itemsPerPage);
        $daftarJurnalTampil = cariJurnalByJudul($conn, $keyword, $currentUserId, $sortBy, $sortOrder, $itemsPerPage, $offset);
        if (empty($daftarJurnalTampil)) {
            $pesan = "Tidak ada jurnal ditemukan untuk: <strong>" . htmlspecialchars($keyword) . "</strong>";
        } else {
            $pesan = "Menampilkan hasil pencarian untuk: <strong>" . htmlspecialchars($keyword) . "</strong> (Total: $totalItems jurnal)";
        }
    } else {
        $pesan = "Error: Fungsi pencarian tidak ditemukan.";
    }
} else {
    if (function_exists('getAllJurnal')) {
        $totalItems = getTotalJurnal($conn, $currentUserId);
        $totalPages = ceil($totalItems / $itemsPerPage);
        $daftarJurnalTampil = getAllJurnal($conn, $currentUserId, $sortBy, $sortOrder, $itemsPerPage, $offset);
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
            
            <div class="sort-group">
                <label for="sort_by"><i class="fas fa-sort"></i></label>
                <select name="sort_by" id="sort_by" onchange="this.form.submit()">
                    <option value="dibuat" <?php echo $sortBy === 'dibuat' ? 'selected' : ''; ?>>Tanggal Dibuat</option>
                    <option value="tanggal" <?php echo $sortBy === 'tanggal' ? 'selected' : ''; ?>>Tanggal Jurnal</option>
                    <option value="judul" <?php echo $sortBy === 'judul' ? 'selected' : ''; ?>>Judul (A-Z)</option>
                </select>
            </div>
            
            <div class="sort-group">
                <select name="sort_order" onchange="this.form.submit()">
                    <option value="DESC" <?php echo $sortOrder === 'DESC' ? 'selected' : ''; ?>>
                        <?php echo ($sortBy === 'judul') ? 'Z-A' : 'Terbaru'; ?>
                    </option>
                    <option value="ASC" <?php echo $sortOrder === 'ASC' ? 'selected' : ''; ?>>
                        <?php echo ($sortBy === 'judul') ? 'A-Z' : 'Terlama'; ?>
                    </option>
                </select>
            </div>
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
            $diupdate = htmlspecialchars($jurnal['diupdate'] ?? '');
    ?>
    <div class="jurnal-card"
        data-id="<?php echo $jurnalId; ?>"
        data-judul="<?php echo $judul; ?>"
        data-tanggal="<?php echo $tanggal; ?>"
        data-isi="<?php echo $isi; ?>"
        data-dibuat="<?php echo $dibuat; ?>"
        data-diupdate="<?php echo $diupdate; ?>">
      
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

<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php
    $params = [];
    if (!empty($keyword)) $params[] = 'keyword=' . urlencode($keyword);
    if ($sortBy !== 'dibuat') $params[] = 'sort_by=' . $sortBy;
    if ($sortOrder !== 'DESC') $params[] = 'sort_order=' . $sortOrder;
    $baseUrl = '?' . implode('&', $params);
    $separator = empty($params) ? '?' : '&';
    ?>
    
    <?php if ($page > 1): ?>
        <a href="<?php echo $baseUrl . $separator; ?>page=1" class="pagination-btn">
            <i class="fas fa-angle-double-left"></i>
        </a>
        <a href="<?php echo $baseUrl . $separator; ?>page=<?php echo $page - 1; ?>" class="pagination-btn">
            <i class="fas fa-angle-left"></i>
        </a>
    <?php endif; ?>
    
    <?php
    $startPage = max(1, $page - 2);
    $endPage = min($totalPages, $page + 2);
    
    for ($i = $startPage; $i <= $endPage; $i++):
    ?>
        <a href="<?php echo $baseUrl . $separator; ?>page=<?php echo $i; ?>" 
           class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
    
    <?php if ($page < $totalPages): ?>
        <a href="<?php echo $baseUrl . $separator; ?>page=<?php echo $page + 1; ?>" class="pagination-btn">
            <i class="fas fa-angle-right"></i>
        </a>
        <a href="<?php echo $baseUrl . $separator; ?>page=<?php echo $totalPages; ?>" class="pagination-btn">
            <i class="fas fa-angle-double-right"></i>
        </a>
    <?php endif; ?>
    
    <span class="pagination-info">
        Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?>
    </span>
</div>
<?php endif; ?>

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
        <div class="detail-timestamp">
            <div id="detail-dibuat"></div>
            <div id="detail-diupdate"></div>
        </div>
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

