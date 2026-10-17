<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';
include '../includes/cek_admin.php';

$data = mysqli_query($koneksi, "SELECT r.*, u.nama AS nama_user, p.nama_penyakit
                                 FROM riwayat_identifikasi r
                                 LEFT JOIN users u ON r.id_user = u.id_user
                                 LEFT JOIN penyakit p ON r.hasil_penyakit = p.kode_penyakit
                                 ORDER BY r.tanggal DESC");

$title = 'Riwayat Identifikasi';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container">
  <h4>Riwayat Identifikasi Seluruh User</h4>
  <table class="table table-bordered bg-white">
    <tr><th>Tanggal</th><th>User</th><th>Gejala Dipilih</th><th>Hasil</th></tr>
    <?php if (mysqli_num_rows($data) === 0): ?>
      <tr><td colspan="4" class="text-center">Belum ada data.</td></tr>
    <?php else: while ($d = mysqli_fetch_assoc($data)): ?>
      <tr>
        <td><?= $d['tanggal'] ?></td>
        <td><?= htmlspecialchars($d['nama_user']) ?></td>
        <td><?= htmlspecialchars($d['gejala_dipilih']) ?></td>
        <td><?= $d['nama_penyakit'] ?? '<i>Tidak teridentifikasi</i>' ?></td>
      </tr>
    <?php endwhile; endif; ?>
  </table>
</div>
<?php include '../includes/footer.php'; ?>
