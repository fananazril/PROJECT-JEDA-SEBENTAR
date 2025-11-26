<?php
date_default_timezone_set('Asia/Jakarta');

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'jedasebentar';

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
} else {
    mysqli_query($conn, "SET time_zone = '+07:00'");
}
?>