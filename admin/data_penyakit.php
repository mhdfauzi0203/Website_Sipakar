<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';
include '../includes/cek_admin.php';

$pesan = '';

// TAMBAH
if (isset($_POST['tambah'])) {
    $kode      = mysqli_real_escape_string($koneksi, trim($_POST['kode_penyakit']));
    $nama      = mysqli_real_escape_string($koneksi, trim($_POST['nama_penyakit']));
    $deskripsi = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi']));
    $solusi    = mysqli_real_escape_string($koneksi, trim($_POST['solusi']));

    $cek = mysqli_query($koneksi, "SELECT * FROM penyakit WHERE kode_penyakit='$kode'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Kode penyakit sudah ada.";
    } else {
        mysqli_query($koneksi, "INSERT INTO penyakit (kode_penyakit, nama_penyakit, deskripsi, solusi)
                                 VALUES ('$kode','$nama','$deskripsi','$solusi')");
        header("Location: data_penyakit.php");
        exit;
    }
}

// EDIT
if (isset($_POST['edit'])) {
    $kode      = mysqli_real_escape_string($koneksi, $_POST['kode_penyakit_lama']);
    $nama      = mysqli_real_escape_string($koneksi, trim($_POST['nama_penyakit']));
    $deskripsi = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi']));
    $solusi    = mysqli_real_escape_string($koneksi, trim($_POST['solusi']));

    mysqli_query($koneksi, "UPDATE penyakit SET nama_penyakit='$nama', deskripsi='$deskripsi', solusi='$solusi'
                             WHERE kode_penyakit='$kode'");
    header("Location: data_penyakit.php");
    exit;
}

// HAPUS
if (isset($_GET['hapus'])) {
    $kode = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM penyakit WHERE kode_penyakit='$kode'");
    header("Location: data_penyakit.php");
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM penyakit ORDER BY kode_penyakit");

$title = 'Data Penyakit';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container">
  <h4>Kelola Data Penyakit</h4>
  <?php if ($pesan): ?><div class="alert alert-danger"><?= htmlspecialchars($pesan) ?></div><?php endif; ?>

  <div class="card shadow p-3 mb-4">
    <h6>Tambah Data Penyakit</h6>
    <form method="POST" class="row g-2">
      <div class="col-md-2"><input type="text" name="kode_penyakit" class="form-control" placeholder="Kode (P006)" required></div>
      <div class="col-md-3"><input type="text" name="nama_penyakit" class="form-control" placeholder="Nama Penyakit" required></div>
      <div class="col-md-3"><textarea name="deskripsi" class="form-control" placeholder="Deskripsi" required></textarea></div>
      <div class="col-md-3"><textarea name="solusi" class="form-control" placeholder="Solusi" required></textarea></div>
      <div class="col-md-1"><button type="submit" name="tambah" class="btn btn-primary w-100">Simpan</button></div>
    </form>
  </div>

  <table class="table table-bordered bg-white">
    <tr><th>Kode</th><th>Nama</th><th>Deskripsi</th><th>Solusi</th><th>Aksi</th></tr>
    <?php while ($d = mysqli_fetch_assoc($data)): ?>
    <tr>
      <form method="POST">
        <input type="hidden" name="kode_penyakit_lama" value="<?= $d['kode_penyakit'] ?>">
        <td><?= $d['kode_penyakit'] ?></td>
        <td><input type="text" name="nama_penyakit" class="form-control" value="<?= htmlspecialchars($d['nama_penyakit']) ?>"></td>
        <td><textarea name="deskripsi" class="form-control"><?= htmlspecialchars($d['deskripsi']) ?></textarea></td>
        <td><textarea name="solusi" class="form-control"><?= htmlspecialchars($d['solusi']) ?></textarea></td>
        <td>
          <button type="submit" name="edit" class="btn btn-sm btn-warning mb-1">Update</button>
          <a href="?hapus=<?= $d['kode_penyakit'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
        </td>
      </form>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php include '../includes/footer.php'; ?>
