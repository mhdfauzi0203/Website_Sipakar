<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';
include '../includes/cek_admin.php';

$pesan = '';

// TAMBAH RULE BARU (kode_rule + kesimpulan + banyak premis)
if (isset($_POST['tambah'])) {
    $kode_rule  = mysqli_real_escape_string($koneksi, trim($_POST['kode_rule']));
    $kesimpulan = mysqli_real_escape_string($koneksi, trim($_POST['kesimpulan']));
    $premis     = $_POST['premis'] ?? [];

    $cek = mysqli_query($koneksi, "SELECT * FROM rule WHERE kode_rule='$kode_rule'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Kode rule sudah ada.";
    } elseif (empty($premis)) {
        $pesan = "Pilih minimal satu premis (gejala/fakta).";
    } else {
        mysqli_query($koneksi, "INSERT INTO rule (kode_rule, kesimpulan) VALUES ('$kode_rule','$kesimpulan')");
        foreach ($premis as $item) {
            $item = mysqli_real_escape_string($koneksi, $item);
            mysqli_query($koneksi, "INSERT INTO rule_premis (kode_rule, kode_item) VALUES ('$kode_rule','$item')");
        }
        header("Location: data_rule.php");
        exit;
    }
}

// HAPUS RULE (premis ikut terhapus karena ON DELETE CASCADE)
if (isset($_GET['hapus'])) {
    $kode = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM rule WHERE kode_rule='$kode'");
    header("Location: data_rule.php");
    exit;
}

// Data untuk dropdown premis: semua gejala (G) + fakta turunan (F01-F11) + kode penyakit (P) untuk kesimpulan
$daftar_gejala = mysqli_query($koneksi, "SELECT kode_gejala FROM gejala ORDER BY kode_gejala");
$daftar_penyakit = mysqli_query($koneksi, "SELECT kode_penyakit FROM penyakit ORDER BY kode_penyakit");

// Ambil semua rule beserta premisnya untuk ditampilkan
$rules = mysqli_query($koneksi, "SELECT * FROM rule ORDER BY kode_rule");

$title = 'Data Rule';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container">
  <h4>Kelola Data Rule (Basis Aturan)</h4>
  <p class="text-muted">
    Premis bisa berupa kode gejala (G01-G30) atau kode fakta turunan (F01-F11) hasil rule lain.
    Kesimpulan bisa berupa kode fakta turunan (Fxx) atau kode penyakit (Pxxx) jika rule ini adalah rule final.
  </p>
  <?php if ($pesan): ?><div class="alert alert-danger"><?= htmlspecialchars($pesan) ?></div><?php endif; ?>

  <div class="card shadow p-3 mb-4">
    <h6>Tambah Rule Baru</h6>
    <form method="POST">
      <div class="row g-2 mb-2">
        <div class="col-md-3">
          <label class="form-label">Kode Rule</label>
          <input type="text" name="kode_rule" class="form-control" placeholder="R17" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Kesimpulan (THEN)</label>
          <input type="text" name="kesimpulan" class="form-control"
                 placeholder="F12 atau P006 (fakta turunan atau kode penyakit)" required>
        </div>
      </div>

      <label class="form-label">Premis (IF) — centang gejala/fakta yang jadi syarat:</label>
      <div class="row" style="max-height:250px; overflow-y:auto; border:1px solid #ddd; padding:10px; border-radius:8px;">
        <?php
        mysqli_data_seek($daftar_gejala, 0);
        while ($g = mysqli_fetch_assoc($daftar_gejala)): ?>
          <div class="col-md-3 form-check">
            <input class="form-check-input" type="checkbox" name="premis[]" value="<?= $g['kode_gejala'] ?>" id="p_<?= $g['kode_gejala'] ?>">
            <label class="form-check-label" for="p_<?= $g['kode_gejala'] ?>"><?= $g['kode_gejala'] ?></label>
          </div>
        <?php endwhile; ?>
        <?php for ($i = 1; $i <= 11; $i++):
          $fkode = 'F' . str_pad($i, 2, '0', STR_PAD_LEFT); ?>
          <div class="col-md-3 form-check">
            <input class="form-check-input" type="checkbox" name="premis[]" value="<?= $fkode ?>" id="p_<?= $fkode ?>">
            <label class="form-check-label" for="p_<?= $fkode ?>"><?= $fkode ?></label>
          </div>
        <?php endfor; ?>
      </div>

      <button type="submit" name="tambah" class="btn btn-primary mt-3">Simpan Rule</button>
    </form>
  </div>

  <table class="table table-bordered bg-white">
    <tr><th>Kode Rule</th><th>Premis (IF)</th><th>Kesimpulan (THEN)</th><th>Aksi</th></tr>
    <?php while ($r = mysqli_fetch_assoc($rules)):
      $premis_q = mysqli_query($koneksi, "SELECT kode_item FROM rule_premis WHERE kode_rule='{$r['kode_rule']}'");
      $premis_list = [];
      while ($p = mysqli_fetch_assoc($premis_q)) $premis_list[] = $p['kode_item'];
    ?>
      <tr>
        <td><b><?= $r['kode_rule'] ?></b></td>
        <td><?= implode(' AND ', $premis_list) ?></td>
        <td><b><?= $r['kesimpulan'] ?></b></td>
        <td>
          <a href="?hapus=<?= $r['kode_rule'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Yakin hapus rule ini?')">Hapus</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php include '../includes/footer.php'; ?>
