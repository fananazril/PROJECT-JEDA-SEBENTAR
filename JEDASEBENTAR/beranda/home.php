<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Pribadi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>📖 Jurnal Pribadi</h1>
        <p>Tempat menulis perjalanan pikiran dan perasaan.</p>
    </header>

    <main class="container">
        <?php
        $result = $conn->query("SELECT * FROM jurnal ORDER BY tanggal DESC");
        while ($row = $result->fetch_assoc()) {
        ?>
            <div class="card" onclick="openModal('<?php echo $row['judul']; ?>', '<?php echo date('d M Y', strtotime($row['tanggal'])); ?>', '<?php echo addslashes($row['isi']); ?>', '<?php echo $row['gambar']; ?>')">
                <img src="<?php echo $row['gambar']; ?>" alt="Gambar Jurnal">
                <h3><?php echo $row['judul']; ?></h3>
                <p class="tanggal"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></p>
            </div>
        <?php } ?>
    </main>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <img id="modal-img" src="" alt="">
            <h2 id="modal-judul"></h2>
            <p id="modal-tanggal" class="tanggal"></p>
            <div id="modal-isi" class="isi"></div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
