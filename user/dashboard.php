<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';

$id_user = $_SESSION['id_user'];
$q = mysqli_query($koneksi, "SELECT r.*, p.nama_penyakit
                              FROM riwayat_identifikasi r
                              LEFT JOIN penyakit p ON r.hasil_penyakit = p.kode_penyakit
                              WHERE r.id_user = $id_user
                              ORDER BY r.tanggal DESC LIMIT 10");

$total_riwayat = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) AS n FROM riwayat_identifikasi WHERE id_user = $id_user"))['n'];
$total_teridentifikasi = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) AS n FROM riwayat_identifikasi WHERE id_user = $id_user AND hasil_penyakit IS NOT NULL"))['n'];

$title = 'Dashboard';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 fade-in">
    <div>
      <h4 class="mb-1">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?> 👋</h4>
      <p class="text-muted mb-0">Berikut ringkasan aktivitas identifikasi Anda.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="info_penyakit.php" class="btn btn-outline-primary"><i class="bi bi-journal-medical me-1"></i>Info Penyakit</a>
      <a href="identifikasi.php?mulai=1" class="btn btn-primary"><i class="bi bi-clipboard2-pulse me-1"></i>Mulai Identifikasi Baru</a>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="sp-stat-card fade-in">
        <i class="bi bi-clock-history"></i>
        <div class="sp-stat-number"><?= (int)$total_riwayat ?></div>
        <div>Total Identifikasi</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="sp-stat-card fade-in" style="background: linear-gradient(120deg, var(--sp-accent) 0%, var(--sp-accent-dark) 100%);">
        <i class="bi bi-check2-circle"></i>
        <div class="sp-stat-number"><?= (int)$total_teridentifikasi ?></div>
        <div>Berhasil Teridentifikasi</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="sp-stat-card fade-in" style="background: linear-gradient(120deg, #6a7fdb 0%, #4a5fc1 100%);">
        <i class="bi bi-shield-check"></i>
        <div class="sp-stat-number"><?= (int)($total_riwayat - $total_teridentifikasi) ?></div>
        <div>Tidak Teridentifikasi</div>
      </div>
    </div>
  </div>

  <div class="card shadow p-3 p-md-4 fade-in">
    <h5 class="mb-3"><i class="bi bi-list-check me-2"></i>Riwayat Identifikasi Terakhir</h5>
    <div class="table-responsive">
      <table class="table mt-2 align-middle">
        <tr><th>Tanggal</th><th>Gejala Dipilih</th><th>Hasil</th></tr>
        <?php if (mysqli_num_rows($q) === 0): ?>
          <tr><td colspan="3" class="text-center text-muted py-4">
            <i class="bi bi-inbox" style="font-size:1.5rem;"></i><br>Belum ada riwayat identifikasi.
          </td></tr>
        <?php else: while ($r = mysqli_fetch_assoc($q)): ?>
          <tr>
            <td class="text-nowrap"><?= $r['tanggal'] ?></td>
            <td><?= htmlspecialchars($r['gejala_dipilih']) ?></td>
            <td>
              <?php if ($r['nama_penyakit']): ?>
                <span class="badge rounded-pill" style="background:var(--sp-primary);"><?= htmlspecialchars($r['nama_penyakit']) ?></span>
              <?php else: ?>
                <span class="badge rounded-pill bg-secondary">Tidak teridentifikasi</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; endif; ?>
      </table>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
