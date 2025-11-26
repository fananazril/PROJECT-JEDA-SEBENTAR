<?php
session_start();
if(isset($_SESSION['username'])){
    header("Location: /../beranda/home.php"); // path ke beranda
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeda Sebentar - Daftar</title>
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="register-container"> 
        <div class="logo"> <img src="/../assets/Logo2.png/" alt="logo" class="logo-img"></div>
        <h2>Registrasi</h2>
        <form action="/../action/useraction/procreg.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
<div class="form-group">
    <label for="confirm">Konfirmasi Password:</label>
    <div class="password-wrapper">
        <input type="password" name="confirm" id="confirm" required>
        <button type="button" class="toggle-password" onclick="toggleConfirm()">
            <i class="bi bi-eye"></i>
        </button>
    </div>
</div>
        <button type="submit" name="submit" class="register-button">DAFTAR</button>
        </form>
        <p class="login-link">
            Sudah punya akun?
            <a href="/../action/login/login.php">Masuk di sini</a>
        </p>
    </div>
    <div class="popup" id="popup">
        <div class="popup-content">
            <h3 id="popup-title"></h3>
            <p id="popup-message"></p>
            <?php
            if(isset($_GET['status']) && $_GET['status'] === 'success') {
            ?> 
                <button onclick="window.location.href='/../action/login/login.php'">
            <?php    
            echo 'Login Sekarang';
            } else { 
            ?>
                <button onclick="window.location.href='/../action/register/register.php'">
            <?php 
            echo 'Tutup';
            }
            ?>
        </button>
        </div>
    </div>
    <script src="register.js"></script>
    <?php
    if (isset($_GET['status']) && isset($_GET['msg'])) {
        $type = htmlspecialchars($_GET['status']);
        $msg = urldecode(htmlspecialchars($_GET['msg']));
        if($type === 'success'|| $type === 'error') {
            echo "<script>showPopup('$type', '$msg');</script>";
        }
    }?>

    <footer class="footer"> <!-- FOOTER -->
         <p>© 2025 Jeda Sebentar</p>
    </footer>
</body>
</html>