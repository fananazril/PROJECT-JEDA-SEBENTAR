<?php
require_once __DIR__ . '../../../database/database.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        header("Location: /../action/register/register.php?status=error&msg=" . urlencode("Semua field harus diisi!"));
        exit;
    }
    if ($password !== $confirm) {
        header("Location: /../action/register/register.php?status=error&msg=" . urlencode("Konfirmasi password tidak cocok!"));
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: /../action/register/register.php?status=error&msg=" . urlencode("Format email tidak valid!"));
        exit;
    }
    if (strlen($password) < 6) {
        header("Location: /../action/register/register.php?status=error&msg=" . urlencode("Password minimal 6 karakter!"));
        exit;
    }
    $sqlCheckUser = "SELECT id FROM users WHERE username = ?";
    $stmtCheckUser = mysqli_prepare($conn, $sqlCheckUser);
    if ($stmtCheckUser) {
        mysqli_stmt_bind_param($stmtCheckUser, "s", $username);
        mysqli_stmt_execute($stmtCheckUser);
        mysqli_stmt_store_result($stmtCheckUser);
        if (mysqli_stmt_num_rows($stmtCheckUser) > 0) {
            mysqli_stmt_close($stmtCheckUser);
            header("Location: /../action/register/register.php?status=error&msg=" . urlencode("Username sudah digunakan!"));
            exit;
        }
        mysqli_stmt_close($stmtCheckUser);
    } else {
        error_log("Prepare statement gagal (cek username): " . mysqli_error($conn));
        header("Location: /../action/register/register.php?status=error&msg=" . urlencode("Terjadi kesalahan database (CU)."));
        exit;
    }
    $sqlCheckEmail = "SELECT id FROM users WHERE email = ?";
    $stmtCheckEmail = mysqli_prepare($conn, $sqlCheckEmail);
     if ($stmtCheckEmail) {
        mysqli_stmt_bind_param($stmtCheckEmail, "s", $email);
        mysqli_stmt_execute($stmtCheckEmail);
        mysqli_stmt_store_result($stmtCheckEmail);

        if (mysqli_stmt_num_rows($stmtCheckEmail) > 0) {
            mysqli_stmt_close($stmtCheckEmail);
            header("Location: /../action/register/register.php?status=error&msg=" . urlencode("Email sudah digunakan!"));
            exit;
        }
        mysqli_stmt_close($stmtCheckEmail);
    } else {
        error_log("Prepare statement gagal (cek email): " . mysqli_error($conn));
        header("Location: /../register/register.php?status=error&msg=" . urlencode("Terjadi kesalahan database (CE)."));
        exit;
    }
    $sqlInsert = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmtInsert = mysqli_prepare($conn, $sqlInsert);
    if ($stmtInsert) {
        mysqli_stmt_bind_param($stmtInsert, "sss", $username, $email, $password);
        if (mysqli_stmt_execute($stmtInsert)) {
            mysqli_stmt_close($stmtInsert);
            mysqli_close($conn);
            header("Location: /../action/register/register.php?status=success&msg=" . urlencode("Registrasi berhasil! Silakan login."));
            exit;
        } else {
            $errorMsg = mysqli_stmt_error($stmtInsert);
            mysqli_stmt_close($stmtInsert);
            error_log("Eksekusi statement gagal (insert user): " . $errorMsg);
            header("Location: /../action/register/register.php?status=error&msg=" . urlencode("Terjadi kesalahan saat menyimpan data."));
            exit;
        }
    } else {
        error_log("Prepare statement gagal (insert user): " . mysqli_error($conn));
        header("Location: /../action/register/register.php?status=error&msg=" . urlencode("Terjadi kesalahan database (I)."));
        exit;
    }
} else {
    header("Location: /../action/register/register.php");
    exit;
}
if ($conn) {
    mysqli_close($conn);
}
?>