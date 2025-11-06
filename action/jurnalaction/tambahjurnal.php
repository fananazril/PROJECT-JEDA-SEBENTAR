<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: /../login/login.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];
require_once __DIR__ . '/../../database/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d'); 
    $dibuat = date('Y-m-d H:i:s'); 

    // Validasi sederhana
    if (!empty($judul) && !empty($isi)) {
        $sql = "INSERT INTO jurnal (id_user, judul, isi, tanggal, dibuat) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issss", $currentUserId, $judul, $isi, $tanggal, $dibuat);
            if (!mysqli_stmt_execute($stmt)) {
                error_log("Error execute statement: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Error prepare statement: " . mysqli_error($conn));
        }
    }
}

if ($conn) {
    mysqli_close($conn);
}

header("Location: /../../beranda/home.php");
exit;
?>
