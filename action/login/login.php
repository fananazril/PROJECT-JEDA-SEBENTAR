<?php
session_start();

// fungsi cek sesi per user 
if (isset($_SESSION['username'])) {
    header("Location: /../beranda/home.php"); // syntax path
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeda Sebentar - Masuk</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo"> <img src="/../assets/Logo2.png" alt="logo" class="logo-img"></div>
        <h2>Masuk</h2>

        <?php if (isset($_GET['error'])): ?>
            <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <form action="/../useraction/proclogin.php]" method="POST"> <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-button">Masuk</button>
        </form>
        <p class="register-link">
            Belum punya akun? <a href="/../action/register/register.php"> Daftar di sini </a>
        </p>
    </div>

    <footer class="footer"> <!-- FOOTER -->
         <p>© 2025 Jeda Sebentar</p>
    </footer>
</body>
</html>