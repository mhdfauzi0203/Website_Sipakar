<?php
// Wajib set $base sebelum include file ini.
// $base = '' untuk file di folder utama, $base = '../' untuk file di dalam admin/ atau user/
if (!isset($_SESSION['id_user'])) {
    header("Location: " . ($base ?? '') . "login.php");
    exit;
}
