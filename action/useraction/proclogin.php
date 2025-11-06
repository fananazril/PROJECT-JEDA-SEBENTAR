<?php
session_start();
require_once __DIR__ . '/../../database/database.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (empty($username) || empty($password)) {
        header("Location: /../action/login/login.php?error=Username dan password wajib diisi!");
        exit;
    }
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            if ($password== $row['password']) {
                session_regenerate_id(true);
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                header("Location: /../beranda/home.php");
                exit;
            } else {
                header("Location: /../action/login/login.php?error=Username atau password salah!");
                exit;
            }
        } else {
            header("Location: /../action/login/login.php?error=Username atau password salah!");
            exit;
        }
    } else {
        error_log("MySQLi prepare error: " . mysqli_error($conn));
        header("Location: /../action/login/login.php?error=Terjadi kesalahan sistem.");
        exit;
    }

} else {
    header("Location: /../action/login/login.php");
    exit;
}
if ($conn) {
    mysqli_close($conn);
}
?>