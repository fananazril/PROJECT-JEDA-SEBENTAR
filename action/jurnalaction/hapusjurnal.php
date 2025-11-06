<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: /../../login/login.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];
require_once __DIR__ . '/../../database/database.php';

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $idJurnal = $_GET['id'];
    
    $sql = "DELETE FROM jurnal WHERE idjurnal = ? AND id_user = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $idJurnal, $currentUserId);
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Error execute delete statement: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Error prepare delete statement: " . mysqli_error($conn));
    }
}

if ($conn) {
    mysqli_close($conn);
}

header("Location: /../../beranda/home.php");
exit;
?>
