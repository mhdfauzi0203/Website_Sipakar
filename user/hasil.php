<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';

$hasil = $_SESSION['hasil_identifikasi'] ?? null;
if (!$hasil) {
    header("Location: identifikasi.php");
    exit;
}

$data_penyakit = null;
if ($hasil['penyakit']) {
    $kode = mysqli_real_escape_string($koneksi, $hasil['penyakit']);
    $q = mysqli_query($koneksi, "SELECT * FROM penyakit WHERE kode_penyakit='$kode'");
    $data_penyakit = mysqli_fetch_assoc($q);
}

$title = 'Hasil Identifikasi';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="max-width:760px;">
  <div class="card shadow p-4 p-md-5 fade-in">
    <h4 class="mb-4"><i class="bi bi-file-earmark-medical me-2" style="color:var(--sp-primary);"></i>Hasil Identifikasi</h4>

    <?php if ($data_penyakit): ?>
      <div class="sp-result-hero d-flex gap-3 align-items-start">
        <div class="sp-result-icon"><i class="bi bi-clipboard2-check"></i></div>
        <div>
          <div class="text-muted" style="font-size:0.85rem;">Penyakit Terindikasi</div>
          <h5 class="mb-0" style="color:var(--sp-primary-dark);"><?= htmlspecialchars($data_penyakit['nama_penyakit']) ?></h5>
        </div>
      </div>

      <div class="mt-4">
        <h6><i class="bi bi-info-circle me-1"></i>Deskripsi</h6>
        <p class="text-muted"><?= nl2br(htmlspecialchars($data_penyakit['deskripsi'])) ?></p>
      </div>
      <div class="mt-3">
        <h6><i class="bi bi-bandaid me-1"></i>Saran Penanganan Awal</h6>
        <p class="text-muted"><?= nl2br(htmlspecialchars($data_penyakit['solusi'])) ?></p>
      </div>

      <div class="alert alert-warning mt-4 mb-0">
        <i class="bi bi-exclamation-triangle me-1"></i>
        Hasil ini adalah identifikasi awal berdasarkan gejala yang dipilih, <b>bukan diagnosis medis</b>.
        Tetap disarankan untuk memeriksakan anak ke dokter/dermatolog agar mendapatkan
        diagnosis dan penanganan yang tepat.
      </div>
    <?php else: ?>
      <div class="sp-result-hero d-flex gap-3 align-items-start">
        <div class="sp-result-icon sp-result-none"><i class="bi bi-question-lg"></i></div>
        <div>
          <div class="text-muted" style="font-size:0.85rem;">Hasil</div>
          <h5 class="mb-0">Belum Dapat Diidentifikasi</h5>
        </div>
      </div>
      <div class="alert alert-secondary mt-4 mb-0">
        <i class="bi bi-info-circle me-1"></i>
        Gejala yang dipilih belum cukup/tidak sesuai dengan salah satu pola penyakit yang dikenali sistem.
        Silakan konsultasikan langsung kondisi anak dengan tenaga medis.
      </div>
    <?php endif; ?>

    <details class="mt-4">
      <summary class="text-muted" style="cursor:pointer; font-size:0.9rem;">
        <i class="bi bi-chevron-down me-1"></i>Lihat proses inferensi (debug)
      </summary>
      <pre class="bg-light p-3 mt-2 rounded" style="font-size:13px;"><?php
        foreach ($hasil['log'] as $i => $l) {
            echo "Iterasi " . ($i+1) . ": Rule {$l['rule']} => premis [" . implode(',', $l['premis']) . "] => hasil {$l['hasil']}\n";
        }
      ?></pre>
    </details>

    <div class="mt-4 d-flex gap-2 flex-wrap">
      <a href="identifikasi.php?mulai=1" class="btn btn-primary"><i class="bi bi-arrow-repeat me-1"></i>Identifikasi Lagi</a>
      <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-house me-1"></i>Kembali ke Dashboard</a>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
