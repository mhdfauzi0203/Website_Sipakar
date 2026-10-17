<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';
include '../includes/cek_admin.php';

$jml_user    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM users WHERE role='user'"))['c'];
$jml_penyakit= mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM penyakit"))['c'];
$jml_gejala  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM gejala"))['c'];
$jml_rule    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM rule"))['c'];
$jml_riwayat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM riwayat_identifikasi"))['c'];

$title = 'Dashboard Admin';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container">
  <h4 class="mb-4"><i class="bi bi-speedometer2 me-2" style="color:var(--sp-primary);"></i>Dashboard Admin</h4>
  <div class="row g-3 mb-4">
    <div class="col-md-2 col-6">
      <div class="sp-stat-card text-center fade-in"><i class="bi bi-people"></i><div class="sp-stat-number"><?= $jml_user ?></div><small>User Terdaftar</small></div>
    </div>
    <div class="col-md-2 col-6">
      <div class="sp-stat-card text-center fade-in" style="background:linear-gradient(120deg, var(--sp-accent) 0%, var(--sp-accent-dark) 100%);"><i class="bi bi-bandaid"></i><div class="sp-stat-number"><?= $jml_penyakit ?></div><small>Data Penyakit</small></div>
    </div>
    <div class="col-md-2 col-6">
      <div class="sp-stat-card text-center fade-in" style="background:linear-gradient(120deg, #6a7fdb 0%, #4a5fc1 100%);"><i class="bi bi-clipboard2-pulse"></i><div class="sp-stat-number"><?= $jml_gejala ?></div><small>Data Gejala</small></div>
    </div>
    <div class="col-md-2 col-6">
      <div class="sp-stat-card text-center fade-in" style="background:linear-gradient(120deg, #2fbf87 0%, #1e9e6c 100%);"><i class="bi bi-diagram-3"></i><div class="sp-stat-number"><?= $jml_rule ?></div><small>Rule</small></div>
    </div>
    <div class="col-md-2 col-6">
      <div class="sp-stat-card text-center fade-in" style="background:linear-gradient(120deg, #7a8a89 0%, #55605f 100%);"><i class="bi bi-clock-history"></i><div class="sp-stat-number"><?= $jml_riwayat ?></div><small>Riwayat</small></div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-3 col-6"><a href="data_penyakit.php" class="btn btn-outline-primary w-100 py-3"><i class="bi bi-bandaid d-block mb-1" style="font-size:1.3rem;"></i>Kelola Data Penyakit</a></div>
    <div class="col-md-3 col-6"><a href="data_gejala.php" class="btn btn-outline-primary w-100 py-3"><i class="bi bi-clipboard2-pulse d-block mb-1" style="font-size:1.3rem;"></i>Kelola Data Gejala</a></div>
    <div class="col-md-3 col-6"><a href="data_rule.php" class="btn btn-outline-primary w-100 py-3"><i class="bi bi-diagram-3 d-block mb-1" style="font-size:1.3rem;"></i>Kelola Data Rule</a></div>
    <div class="col-md-3 col-6"><a href="data_user.php" class="btn btn-outline-primary w-100 py-3"><i class="bi bi-people d-block mb-1" style="font-size:1.3rem;"></i>Kelola Data User</a></div>
    <div class="col-md-3 col-6"><a href="riwayat.php" class="btn btn-outline-primary w-100 py-3"><i class="bi bi-clock-history d-block mb-1" style="font-size:1.3rem;"></i>Riwayat Identifikasi</a></div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
