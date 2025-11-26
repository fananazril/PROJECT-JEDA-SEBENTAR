<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: /../../login/login.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];
require_once __DIR__ . '/../../database/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idJurnal = intval($_POST['id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

    // Validasi
    if ($idJurnal > 0 && !empty($judul) && !empty($isi)) {
        // Update jurnal hanya jika milik user yang login
        $sql = "UPDATE jurnal SET judul = ?, isi = ?, tanggal = ? WHERE idjurnal = ? AND id_user = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssii", $judul, $isi, $tanggal, $idJurnal, $currentUserId);
            if (!mysqli_stmt_execute($stmt)) {
                error_log("Error execute update statement: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Error prepare update statement: " . mysqli_error($conn));
        }
    }
}

if ($conn) {
    mysqli_close($conn);
}

header("Location: /../../beranda/home.php");
exit;
?>