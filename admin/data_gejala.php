<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';
include '../includes/cek_admin.php';

$pesan = '';

// TAMBAH
if (isset($_POST['tambah'])) {
    $kode     = mysqli_real_escape_string($koneksi, trim($_POST['kode_gejala']));
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama_gejala']));
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);

    $cek = mysqli_query($koneksi, "SELECT * FROM gejala WHERE kode_gejala='$kode'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Kode gejala sudah ada.";
    } else {
        mysqli_query($koneksi, "INSERT INTO gejala (kode_gejala, nama_gejala, kategori)
                                 VALUES ('$kode','$nama','$kategori')");
        header("Location: data_gejala.php");
        exit;
    }
}

// EDIT
if (isset($_POST['edit'])) {
    $kode     = mysqli_real_escape_string($koneksi, $_POST['kode_gejala_lama']);
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama_gejala']));
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);

    mysqli_query($koneksi, "UPDATE gejala SET nama_gejala='$nama', kategori='$kategori' WHERE kode_gejala='$kode'");
    header("Location: data_gejala.php");
    exit;
}

// HAPUS
if (isset($_GET['hapus'])) {
    $kode = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM gejala WHERE kode_gejala='$kode'");
    header("Location: data_gejala.php");
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM gejala ORDER BY kode_gejala");

$title = 'Data Gejala';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container">
  <h4>Kelola Data Gejala</h4>
  <?php if ($pesan): ?><div class="alert alert-danger"><?= htmlspecialchars($pesan) ?></div><?php endif; ?>

  <div class="card shadow p-3 mb-4">
    <h6>Tambah Data Gejala</h6>
    <form method="POST" class="row g-2">
      <div class="col-md-2"><input type="text" name="kode_gejala" class="form-control" placeholder="Kode (G31)" required></div>
      <div class="col-md-6"><input type="text" name="nama_gejala" class="form-control" placeholder="Nama Gejala" required></div>
      <div class="col-md-2">
        <select name="kategori" class="form-select">
          <option value="umum">Umum</option>
          <option value="khusus">Khusus</option>
        </select>
      </div>
      <div class="col-md-2"><button type="submit" name="tambah" class="btn btn-primary w-100">Simpan</button></div>
    </form>
  </div>

  <table class="table table-bordered bg-white">
    <tr><th>Kode</th><th>Nama Gejala</th><th>Kategori</th><th>Aksi</th></tr>
    <?php while ($d = mysqli_fetch_assoc($data)): ?>
    <tr>
      <form method="POST">
        <input type="hidden" name="kode_gejala_lama" value="<?= $d['kode_gejala'] ?>">
        <td><?= $d['kode_gejala'] ?></td>
        <td><input type="text" name="nama_gejala" class="form-control" value="<?= htmlspecialchars($d['nama_gejala']) ?>"></td>
        <td>
          <select name="kategori" class="form-select">
            <option value="umum" <?= $d['kategori']=='umum'?'selected':'' ?>>Umum</option>
            <option value="khusus" <?= $d['kategori']=='khusus'?'selected':'' ?>>Khusus</option>
          </select>
        </td>
        <td>
          <button type="submit" name="edit" class="btn btn-sm btn-warning mb-1">Update</button>
          <a href="?hapus=<?= $d['kode_gejala'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Yakin hapus data ini? Rule yang memakai gejala ini juga bisa terpengaruh.')">Hapus</a>
        </td>
      </form>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php include '../includes/footer.php'; ?>
