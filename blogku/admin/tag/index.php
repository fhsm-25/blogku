<?php
require '../../includes/auth_check.php';
require '../../config/db.php';
require '../../includes/functions.php';

$error = ''; $success = '';

if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama'] ?? '');
    if (!$nama) { $error = "Nama tag wajib diisi!"; }
    else {
        $slug = makeSlug($nama);
        try {
            $pdo->prepare("INSERT INTO tag (nama, slug) VALUES (?,?)")->execute([$nama, $slug]);
            $success = "Tag berhasil ditambahkan!";
        } catch (Exception $e) { $error = "Tag sudah ada!"; }
    }
}

if (isset($_GET['hapus'])) {
    $pdo->prepare("DELETE FROM tag WHERE id=?")->execute([(int)$_GET['hapus']]);
    $success = "Tag berhasil dihapus!";
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM tag WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_data = $stmt->fetch();
}

if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $nama = trim($_POST['nama'] ?? '');
    if (!$nama) { $error = "Nama tag wajib diisi!"; }
    else {
        $slug = makeSlug($nama);
        $pdo->prepare("UPDATE tag SET nama=?, slug=? WHERE id=?")->execute([$nama, $slug, $id]);
        $success = "Tag berhasil diupdate!";
        $edit_data = null;
    }
}

$tags = $pdo->query("SELECT t.*, COUNT(at2.id_artikel) as jml FROM tag t LEFT JOIN artikel_tag at2 ON t.id=at2.id_tag GROUP BY t.id ORDER BY t.nama")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Tag - Blog Ku</title>
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
      <a href="index.php" class="active"><span class="icon">🏷️</span> Tag</a>
      <a href="../komentar/index.php"><span class="icon">💬</span> Komentar</a>
      <?php if ($is_admin): ?><div class="admin-nav-label">Pengaturan</div><a href="../user/index.php"><span class="icon">👥</span> Pengguna</a><?php endif; ?>
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
    <div class="admin-topbar"><h1>🏷️ Kelola Tag</h1></div>
    <div class="admin-content">
      <?php if ($error): ?><div class="alert alert-danger" style="margin-bottom:20px;">❌ <?= $error ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success" style="margin-bottom:20px;">✅ <?= $success ?></div><?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:24px;">
        <div class="admin-card">
          <div class="admin-card-header"><h3><?= $edit_data ? '✏️ Edit Tag' : '➕ Tambah Tag' ?></h3></div>
          <form class="form-admin" method="POST">
            <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id'] ?>"><?php endif; ?>
            <div class="form-group">
              <label>Nama Tag *</label>
              <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>" placeholder="Contoh: PHP">
            </div>
            <div style="display:flex;gap:8px;">
              <button type="submit" name="<?= $edit_data ? 'update' : 'tambah' ?>" class="btn-primary"><?= $edit_data ? '💾 Update' : '➕ Tambah' ?></button>
              <?php if ($edit_data): ?><a href="index.php" class="btn-secondary">Batal</a><?php endif; ?>
            </div>
          </form>
        </div>
        <div class="admin-card">
          <div class="admin-card-header"><h3>Daftar Tag (<?= count($tags) ?>)</h3></div>
          <table class="table">
            <thead><tr><th>#</th><th>Nama</th><th>Slug</th><th>Artikel</th><th>Aksi</th></tr></thead>
            <tbody>
              <?php foreach ($tags as $i => $t): ?>
              <tr>
                <td><?= $i+1 ?></td>
                <td><span class="tag-item" style="font-size:0.85rem;">#<?= htmlspecialchars($t['nama']) ?></span></td>
                <td><code><?= $t['slug'] ?></code></td>
                <td><?= $t['jml'] ?></td>
                <td>
                  <a href="?edit=<?= $t['id'] ?>" class="btn-sm btn-edit">Edit</a>
                  <a href="?hapus=<?= $t['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Hapus tag ini?')">Hapus</a>
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
