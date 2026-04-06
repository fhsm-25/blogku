<?php
require '../../includes/auth_check.php';
require '../../config/db.php';

if (!$is_admin) { header("Location: ../dashboard.php"); exit; }

$error = ''; $success = '';

// Tambah user
if (isset($_POST['tambah'])) {
    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'author';

    if (!$nama || !$username || !$password) {
        $error = "Nama, username, dan password wajib diisi!";
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (nama, username, email, password, role) VALUES (?,?,?,?,?)")
                ->execute([$nama, $username, $email, $hash, $role]);
            $success = "Pengguna berhasil ditambahkan!";
        } catch (Exception $e) {
            $error = "Username atau email sudah digunakan!";
        }
    }
}

// Hapus user
if (isset($_GET['hapus'])) {
    $hid = (int)$_GET['hapus'];
    if ($hid != $current_user['id']) {
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$hid]);
        $success = "Pengguna berhasil dihapus!";
    } else {
        $error = "Tidak bisa menghapus akun sendiri!";
    }
}

// Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_data = $stmt->fetch();
}

if (isset($_POST['update'])) {
    $uid      = (int)$_POST['id'];
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $role     = $_POST['role'] ?? 'author';
    $password = $_POST['password'] ?? '';

    if (!$nama) { $error = "Nama wajib diisi!"; }
    else {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET nama=?, email=?, role=?, password=? WHERE id=?")->execute([$nama, $email, $role, $hash, $uid]);
        } else {
            $pdo->prepare("UPDATE users SET nama=?, email=?, role=? WHERE id=?")->execute([$nama, $email, $role, $uid]);
        }
        $success = "Pengguna berhasil diupdate!";
        $edit_data = null;
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Pengguna - Blog Ku</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-brand"><h2>Blog<span>Ku</span></h2><p>Admin Panel</p></div>
    <nav class="admin-nav">
      <div class="admin-nav-label">Menu Utama</div>
      <a href="../dashboard.php"><span class="icon">🏠</span> Dashboard</a>
      <a href="../artikel/index.php"><span class="icon">📝</span> Artikel</a>
      <a href="../kategori/index.php"><span class="icon">📂</span> Kategori</a>
      <a href="../tag/index.php"><span class="icon">🏷️</span> Tag</a>
      <a href="../komentar/index.php"><span class="icon">💬</span> Komentar</a>
      <div class="admin-nav-label">Pengaturan</div>
      <a href="index.php" class="active"><span class="icon">👥</span> Pengguna</a>
      <div class="admin-nav-label">Blog</div>
      <a href="../../public/index.php" target="_blank"><span class="icon">🌐</span> Lihat Blog</a>
    </nav>
    <div class="admin-user">
      <div class="admin-user-avatar"><?= strtoupper(substr($current_user['nama'],0,1)) ?></div>
      <div class="admin-user-info"><div class="name"><?= htmlspecialchars($current_user['nama']) ?></div><div class="role"><?= ucfirst($current_user['role']) ?></div></div>
      <a href="../../auth/logout.php" title="Logout">🚪</a>
    </div>
  </aside>
  <main class="admin-main">
    <div class="admin-topbar"><h1>👥 Kelola Pengguna</h1></div>
    <div class="admin-content">
      <?php if ($error): ?><div class="alert alert-danger" style="margin-bottom:20px;">❌ <?= $error ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success" style="margin-bottom:20px;">✅ <?= $success ?></div><?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:24px;">
        <!-- Form -->
        <div class="admin-card">
          <div class="admin-card-header"><h3><?= $edit_data ? '✏️ Edit Pengguna' : '➕ Tambah Pengguna' ?></h3></div>
          <form class="form-admin" method="POST">
            <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id'] ?>"><?php endif; ?>
            <div class="form-group">
              <label>Nama Lengkap *</label>
              <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>" placeholder="Nama lengkap">
            </div>
            <?php if (!$edit_data): ?>
            <div class="form-group">
              <label>Username *</label>
              <input type="text" name="username" class="form-control" required placeholder="username unik">
            </div>
            <?php endif; ?>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($edit_data['email'] ?? '') ?>" placeholder="email@contoh.com">
            </div>
            <div class="form-group">
              <label>Password <?= $edit_data ? '(kosongkan jika tidak diubah)' : '*' ?></label>
              <input type="password" name="password" class="form-control" <?= !$edit_data ? 'required' : '' ?> placeholder="<?= $edit_data ? 'Password baru (opsional)' : 'Minimal 6 karakter' ?>">
            </div>
            <div class="form-group">
              <label>Role</label>
              <select name="role" class="form-control">
                <option value="author" <?= (($edit_data['role'] ?? 'author') == 'author') ? 'selected' : '' ?>>✍️ Author</option>
                <option value="admin" <?= (($edit_data['role'] ?? '') == 'admin') ? 'selected' : '' ?>>👑 Admin</option>
              </select>
            </div>
            <div style="display:flex;gap:8px;">
              <button type="submit" name="<?= $edit_data ? 'update' : 'tambah' ?>" class="btn-primary"><?= $edit_data ? '💾 Update' : '➕ Tambah' ?></button>
              <?php if ($edit_data): ?><a href="index.php" class="btn-secondary">Batal</a><?php endif; ?>
            </div>
          </form>
        </div>

        <!-- List -->
        <div class="admin-card">
          <div class="admin-card-header"><h3>Daftar Pengguna (<?= count($users) ?>)</h3></div>
          <table class="table">
            <thead><tr><th>#</th><th>Nama</th><th>Username</th><th>Role</th><th>Bergabung</th><th>Aksi</th></tr></thead>
            <tbody>
              <?php foreach ($users as $i => $u): ?>
              <tr>
                <td><?= $i+1 ?></td>
                <td>
                  <div style="display:flex;align-items:center;gap:8px;">
                    <div class="author-avatar" style="width:32px;height:32px;font-size:0.8rem;"><?= strtoupper(substr($u['nama'],0,1)) ?></div>
                    <strong><?= htmlspecialchars($u['nama']) ?></strong>
                  </div>
                </td>
                <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                <td><span class="badge badge-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                <td>
                  <a href="?edit=<?= $u['id'] ?>" class="btn-sm btn-edit">Edit</a>
                  <?php if ($u['id'] != $current_user['id']): ?>
                  <a href="?hapus=<?= $u['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
