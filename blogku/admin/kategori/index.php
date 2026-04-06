<?php
require '../../includes/auth_check.php';
require '../../config/db.php';
require '../../includes/functions.php';

if (!$is_admin) { header("Location: ../dashboard.php"); exit; }

$error = ''; $success = '';

// Tambah
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    if (!$nama) { $error = "Nama kategori wajib diisi!"; }
    else {
        $slug = makeSlug($nama);
        try {
            $pdo->prepare("INSERT INTO kategori (nama, slug, deskripsi) VALUES (?,?,?)")->execute([$nama, $slug, $deskripsi]);
            $success = "Kategori berhasil ditambahkan!";
        } catch (Exception $e) { $error = "Kategori sudah ada!"; }
    }
}

// Hapus
if (isset($_GET['hapus'])) {
    $pdo->prepare("DELETE FROM kategori WHERE id=?")->execute([(int)$_GET['hapus']]);
    $success = "Kategori berhasil dihapus!";
}

// Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM kategori WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_data = $stmt->fetch();
}

if (isset($_POST['update'])) {
    $id  = (int)$_POST['id'];
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    if (!$nama) { $error = "Nama kategori wajib diisi!"; }
    else {
        $slug = makeSlug($nama);
        $pdo->prepare("UPDATE kategori SET nama=?, slug=?, deskripsi=? WHERE id=?")->execute([$nama, $slug, $deskripsi, $id]);
        $success = "Kategori berhasil diupdate!";
        $edit_data = null;
    }
}

$kategoris = $pdo->query("SELECT k.*, COUNT(a.id) as jml FROM kategori k LEFT JOIN artikel a ON k.id=a.id_kategori GROUP BY k.id ORDER BY k.nama")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Kategori - Blog Ku</title>
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
      <a href="index.php" class="active"><span class="icon">📂</span> Kategori</a>
      <a href="../tag/index.php"><span class="icon">🏷️</span> Tag</a>
      <a href="../komentar/index.php"><span class="icon">💬</span> Komentar</a>
      <div class="admin-nav-label">Pengaturan</div>
      <a href="../user/index.php"><span class="icon">👥</span> Pengguna</a>
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
    <div class="admin-topbar"><h1>📂 Kelola Kategori</h1></div>
    <div class="admin-content">
      <?php if ($error): ?><div class="alert alert-danger" style="margin-bottom:20px;">❌ <?= $error ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success" style="margin-bottom:20px;">✅ <?= $success ?></div><?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:24px;">
        <!-- Form -->
        <div class="admin-card">
          <div class="admin-card-header"><h3><?= $edit_data ? '✏️ Edit Kategori' : '➕ Tambah Kategori' ?></h3></div>
          <form class="form-admin" method="POST">
            <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id'] ?>"><?php endif; ?>
            <div class="form-group">
              <label>Nama Kategori *</label>
              <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>" placeholder="Contoh: Teknologi">
            </div>
            <div class="form-group">
              <label>Deskripsi</label>
              <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat..."><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea>
            </div>
            <div style="display:flex;gap:8px;">
              <button type="submit" name="<?= $edit_data ? 'update' : 'tambah' ?>" class="btn-primary">
                <?= $edit_data ? '💾 Update' : '➕ Tambah' ?>
              </button>
              <?php if ($edit_data): ?><a href="index.php" class="btn-secondary">Batal</a><?php endif; ?>
            </div>
          </form>
        </div>

        <!-- List -->
        <div class="admin-card">
          <div class="admin-card-header"><h3>Daftar Kategori (<?= count($kategoris) ?>)</h3></div>
          <table class="table">
            <thead><tr><th>#</th><th>Nama</th><th>Slug</th><th>Artikel</th><th>Aksi</th></tr></thead>
            <tbody>
              <?php foreach ($kategoris as $i => $k): ?>
              <tr>
                <td><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
                <td><code><?= $k['slug'] ?></code></td>
                <td><?= $k['jml'] ?></td>
                <td>
                  <a href="?edit=<?= $k['id'] ?>" class="btn-sm btn-edit">Edit</a>
                  <a href="?hapus=<?= $k['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Hapus kategori ini?')">Hapus</a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($kategoris)): ?><tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Belum ada kategori.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
