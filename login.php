<?php
include 'config/koneksi.php';
$base = '';
$pesan = '';

if (isset($_GET['daftar']) && $_GET['daftar'] === 'sukses') {
    $pesan_sukses = "Registrasi berhasil! Silakan login.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = $_POST['password'];

    $result = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $row = mysqli_fetch_assoc($result);

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['nama']    = $row['nama'];
        $_SESSION['role']    = $row['role'];

        if ($row['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        $pesan = "Username atau password salah.";
    }
}

$title = 'Login';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="container" style="max-width:420px;">
  <div class="card shadow p-4 fade-in">
    <div class="sp-auth-icon"><i class="bi bi-shield-lock"></i></div>
    <h4 class="text-center mb-1">Selamat Datang Kembali</h4>
    <p class="text-center text-muted mb-3" style="font-size:0.9rem;">Masuk untuk mulai identifikasi gejala kulit anak</p>
    <?php if (!empty($pesan_sukses)): ?>
      <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($pesan_sukses) ?></div>
    <?php endif; ?>
    <?php if ($pesan): ?>
      <div class="alert alert-danger"><i class="bi bi-x-circle me-1"></i><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-1"></i>Login</button>
      <p class="text-center mt-3 mb-0">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
