<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';

$data = mysqli_query($koneksi, "SELECT * FROM penyakit ORDER BY kode_penyakit");

$title = 'Informasi Penyakit';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container">
  <h4 class="mb-1"><i class="bi bi-journal-medical me-2" style="color:var(--sp-primary);"></i>Informasi Penyakit Kulit pada Anak</h4>
  <p class="text-muted mb-4">Kenali gejala dan penanganan awal berbagai penyakit kulit yang umum dialami anak.</p>
  <div class="row g-3">
    <?php $i = 0; while ($d = mysqli_fetch_assoc($data)): $i++; ?>
      <div class="col-md-6">
        <div class="sp-feature h-100 fade-in">
          <div class="sp-feature-icon"><i class="bi bi-bandaid"></i></div>
          <h5><?= htmlspecialchars($d['nama_penyakit']) ?></h5>
          <p class="mb-2"><b class="text-muted" style="font-size:0.85rem;">DESKRIPSI</b><br><?= htmlspecialchars($d['deskripsi']) ?></p>
          <p class="mb-0"><b class="text-muted" style="font-size:0.85rem;">SOLUSI</b><br><?= htmlspecialchars($d['solusi']) ?></p>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
