<?php
// Wajib set $base sebelum include file ini. Jalankan setelah cek_login.php
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . ($base ?? '') . "login.php");
    exit;
}
