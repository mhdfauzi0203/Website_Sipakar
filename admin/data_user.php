<?php
include '../config/koneksi.php';
$base = '../';
include '../includes/cek_login.php';
include '../includes/cek_admin.php';

// HAPUS (tidak boleh hapus akun sendiri)
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    if ($id !== (int) $_SESSION['id_user']) {
        mysqli_query($koneksi, "DELETE FROM users WHERE id_user=$id");
    }
    header("Location: data_user.php");
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id_user");

$title = 'Data User';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container">
  <h4>Kelola Data User</h4>
  <table class="table table-bordered bg-white">
    <tr><th>ID</th><th>Nama</th><th>Username</th><th>Role</th><th>Terdaftar</th><th>Aksi</th></tr>
    <?php while ($d = mysqli_fetch_assoc($data)): ?>
    <tr>
      <td><?= $d['id_user'] ?></td>
      <td><?= htmlspecialchars($d['nama']) ?></td>
      <td><?= htmlspecialchars($d['username']) ?></td>
      <td><span class="badge bg-<?= $d['role']=='admin'?'danger':'secondary' ?>"><?= $d['role'] ?></span></td>
      <td><?= $d['created_at'] ?></td>
      <td>
        <?php if ($d['id_user'] != $_SESSION['id_user']): ?>
          <a href="?hapus=<?= $d['id_user'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
        <?php else: ?>
          <i class="text-muted">Akun Anda</i>
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php include '../includes/footer.php'; ?>
