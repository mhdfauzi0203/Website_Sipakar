<nav class="navbar navbar-expand-lg navbar-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?= $base ?>index.php">Sistem Pakar Kulit Anak</a>
    <div class="d-flex align-items-center gap-2">
      <?php if (isset($_SESSION['nama'])): ?>
        <span class="text-white me-2 d-none d-sm-inline"><i class="bi bi-person-circle me-1"></i>Halo, <?= htmlspecialchars($_SESSION['nama']) ?></span>
        <?php if ($_SESSION['role'] === 'admin'): ?>
          <a href="<?= $base ?>admin/dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-speedometer2 me-1"></i>Dashboard Admin</a>
        <?php else: ?>
          <a href="<?= $base ?>user/dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
        <?php endif; ?>
        <a href="<?= $base ?>logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
      <?php else: ?>
        <a href="<?= $base ?>login.php" class="btn btn-light btn-sm"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
        <a href="<?= $base ?>register.php" class="btn btn-outline-light btn-sm"><i class="bi bi-person-plus me-1"></i>Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
