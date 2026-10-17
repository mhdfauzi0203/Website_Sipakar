<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';
include '../forward_chaining/inference.php';

// Mulai identifikasi baru -> reset seluruh jawaban sebelumnya
if (isset($_GET['mulai'])) {
    unset($_SESSION['fc_jawaban']);
    unset($_SESSION['hasil_identifikasi']);
    header("Location: identifikasi.php");
    exit;
}

if (!isset($_SESSION['fc_jawaban'])) {
    $_SESSION['fc_jawaban'] = []; // ['G01' => 'ya'|'tidak', ...]
}
$jawaban = $_SESSION['fc_jawaban'];

// 1) Jalankan forward chaining dari gejala yang sudah dijawab "ya"
//    untuk melihat apakah rule sudah ada yang terpenuhi sampai
//    menyimpulkan sebuah penyakit.
$gejala_ya = gejalaYangDipilih($jawaban);
$hasil = jalankanForwardChaining($koneksi, $gejala_ya);

if ($hasil['penyakit'] !== null) {
    simpanRiwayatIdentifikasi($koneksi, $_SESSION['id_user'], $gejala_ya, $hasil);
    $_SESSION['hasil_identifikasi'] = $hasil;
    unset($_SESSION['fc_jawaban']);
    header("Location: hasil.php");
    exit;
}

// 2) Belum ada penyakit yang tersimpulkan -> cari pertanyaan berikutnya
//    (hanya gejala dari rule yang masih mungkin tercapai)
$kode_berikutnya = gejalaBerikutnya($koneksi, $jawaban);

if ($kode_berikutnya === null) {
    // Tidak ada lagi pertanyaan relevan & tidak ada penyakit yang cocok
    simpanRiwayatIdentifikasi($koneksi, $_SESSION['id_user'], $gejala_ya, $hasil);
    $_SESSION['hasil_identifikasi'] = $hasil;
    unset($_SESSION['fc_jawaban']);
    header("Location: hasil.php");
    exit;
}

$q = mysqli_query($koneksi, "SELECT * FROM gejala WHERE kode_gejala='" . mysqli_real_escape_string($koneksi, $kode_berikutnya) . "'");
$gejala_sekarang = mysqli_fetch_assoc($q);

$total_gejala   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM gejala"))['n'];
$sudah_dijawab  = count($jawaban);

$title = 'Identifikasi Gejala';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="max-width:700px;">
  <div class="card shadow p-4 p-md-5 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div>
        <h4 class="mb-0"><i class="bi bi-clipboard2-pulse me-2" style="color:var(--sp-primary);"></i>Identifikasi Gejala</h4>
        <p class="text-muted mb-0" style="font-size:0.9rem;">Pertanyaan ke-<?= $sudah_dijawab + 1 ?></p>
      </div>
      <a href="identifikasi.php?mulai=1" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i>Mulai Ulang</a>
    </div>

    <div class="progress mb-4" style="height: 10px;">
      <div class="progress-bar" role="progressbar"
           style="width: <?= min(95, round(($sudah_dijawab / max(1,$total_gejala)) * 100)) ?>%;"></div>
    </div>

    <div class="sp-question-card p-4 p-md-5 text-center">
      <span class="sp-badge-kode mb-3 d-inline-block"><?= htmlspecialchars($gejala_sekarang['kode_gejala']) ?></span>
      <h5 class="mb-0 position-relative">Apakah anak mengalami<br><b><?= htmlspecialchars($gejala_sekarang['nama_gejala']) ?></b>?</h5>

      <form action="proses_identifikasi.php" method="POST" class="mt-4 d-flex gap-3 position-relative">
        <input type="hidden" name="kode_gejala" value="<?= htmlspecialchars($gejala_sekarang['kode_gejala']) ?>">
        <button type="submit" name="jawaban" value="ya" class="btn btn-success sp-yn-btn">
          <i class="bi bi-check-circle-fill me-1"></i>Ya
        </button>
        <button type="submit" name="jawaban" value="tidak" class="btn btn-outline-danger sp-yn-btn">
          <i class="bi bi-x-circle me-1"></i>Tidak
        </button>
      </form>
    </div>

    <?php if ($sudah_dijawab > 0): ?>
      <details class="mt-4">
        <summary class="text-muted" style="cursor:pointer; font-size:0.9rem;">
          <i class="bi bi-chevron-down me-1"></i>Lihat jawaban sejauh ini (<?= $sudah_dijawab ?>)
        </summary>
        <ul class="sp-answer-log mt-2">
          <?php foreach (array_reverse($jawaban, true) as $kode => $nilai): ?>
            <li>
              <?php if ($nilai === 'ya'): ?>
                <i class="bi bi-check-circle-fill sp-tag-ya"></i>
              <?php else: ?>
                <i class="bi bi-x-circle sp-tag-tidak"></i>
              <?php endif; ?>
              <b><?= htmlspecialchars($kode) ?></b>
              <span class="<?= $nilai === 'ya' ? 'sp-tag-ya' : 'sp-tag-tidak' ?> ms-auto"><?= $nilai === 'ya' ? 'Ya' : 'Tidak' ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </details>
    <?php endif; ?>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
