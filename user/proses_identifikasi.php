<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: identifikasi.php");
    exit;
}

$kode_gejala = $_POST['kode_gejala'] ?? '';
$jawaban     = $_POST['jawaban'] ?? '';

// Validasi: kode gejala harus berformat G## dan jawaban hanya ya/tidak
$kode_valid    = preg_match('/^G\d{2}$/', $kode_gejala);
$jawaban_valid = in_array($jawaban, ['ya', 'tidak'], true);

if ($kode_valid && $jawaban_valid) {
    if (!isset($_SESSION['fc_jawaban'])) {
        $_SESSION['fc_jawaban'] = [];
    }
    // Simpan jawaban user untuk gejala ini.
    // Jika "ya" -> nanti akan dimasukkan ke working memory dan
    // dicocokkan dengan rule yang sesuai oleh identifikasi.php.
    // Jika "tidak" -> rule-rule yang butuh gejala ini otomatis gugur.
    $_SESSION['fc_jawaban'][$kode_gejala] = $jawaban;
}

header("Location: identifikasi.php");
exit;
