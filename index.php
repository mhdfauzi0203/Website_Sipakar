<?php
include 'config/koneksi.php';
$base = '';
$title = 'Beranda - Sistem Pakar Kulit Anak';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="container">

  <div class="sp-hero text-center fade-in">
    <div class="sp-hero-emoji mb-2">🧴🩹</div>
    <h2>Identifikasi Awal Penyakit Kulit pada Anak</h2>
    <p class="mt-3 mb-0 mx-auto" style="max-width:640px;">
      Jawab beberapa pertanyaan sederhana seputar gejala yang dialami anak Anda.
      Sistem akan menganalisisnya menggunakan metode <b>Forward Chaining</b> untuk
      memberikan indikasi awal beserta saran penanganan.
    </p>
    <?php if (!isset($_SESSION['id_user'])): ?>
      <div class="mt-4 d-flex gap-2 justify-content-center flex-wrap">
        <a href="login.php" class="btn btn-light"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
        <a href="register.php" class="btn btn-outline-light"><i class="bi bi-person-plus me-1"></i>Daftar Akun</a>
      </div>
    <?php else: ?>
      <div class="mt-4">
        <?php if ($_SESSION['role'] === 'admin'): ?>
          <a href="admin/dashboard.php" class="btn btn-light"><i class="bi bi-speedometer2 me-1"></i>Ke Dashboard Admin</a>
        <?php else: ?>
          <a href="user/identifikasi.php?mulai=1" class="btn btn-light"><i class="bi bi-clipboard2-pulse me-1"></i>Mulai Identifikasi</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="row mt-4 g-3">
    <div class="col-md-4">
      <div class="sp-feature fade-in">
        <div class="sp-feature-icon"><i class="bi bi-chat-square-dots"></i></div>
        <h6>Tanya-Jawab Sederhana</h6>
        <p class="text-muted mb-0" style="font-size:0.92rem;">Cukup jawab <b>Ya</b> atau <b>Tidak</b> untuk tiap gejala, tanpa istilah medis yang rumit.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="sp-feature fade-in">
        <div class="sp-feature-icon"><i class="bi bi-diagram-3"></i></div>
        <h6>Metode Forward Chaining</h6>
        <p class="text-muted mb-0" style="font-size:0.92rem;">Setiap jawaban langsung dicocokkan dengan basis aturan (rule) yang disusun oleh pakar.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="sp-feature fade-in">
        <div class="sp-feature-icon"><i class="bi bi-journal-medical"></i></div>
        <h6>Saran Penanganan Awal</h6>
        <p class="text-muted mb-0" style="font-size:0.92rem;">Dapatkan deskripsi penyakit dan saran awal, lengkap dengan riwayat identifikasi.</p>
      </div>
    </div>
  </div>

  <div class="alert alert-warning mt-4 mb-4 fade-in">
    <i class="bi bi-exclamation-triangle me-1"></i>
    Hasil identifikasi bukan diagnosis medis. Tetap disarankan berkonsultasi dengan dokter/dermatolog.
  </div>

</div>
<?php include 'includes/footer.php'; ?>
