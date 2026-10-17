<?php
include 'config/koneksi.php';
$base = '';
$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = $_POST['password'];

    if ($nama === '' || $username === '' || $password === '') {
        $pesan = "Semua field wajib diisi.";
    } else {
        $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $pesan = "Username sudah digunakan, silakan pilih username lain.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "INSERT INTO users (nama, username, password, role)
                                     VALUES ('$nama', '$username', '$hash', 'user')");
            header("Location: login.php?daftar=sukses");
            exit;
        }
    }
}

$title = 'Daftar Akun';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="container" style="max-width:420px;">
  <div class="card shadow p-4 fade-in">
    <div class="sp-auth-icon"><i class="bi bi-person-plus"></i></div>
    <h4 class="text-center mb-1">Buat Akun Baru</h4>
    <p class="text-center text-muted mb-3" style="font-size:0.9rem;">Daftar untuk mulai identifikasi gejala kulit anak</p>
    <?php if ($pesan): ?>
      <div class="alert alert-danger"><i class="bi bi-x-circle me-1"></i><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100"><i class="bi bi-person-check me-1"></i>Daftar</button>
      <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
