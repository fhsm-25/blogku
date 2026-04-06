<?php
require '../../includes/auth_check.php';
require '../../config/db.php';
require '../../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul'] ?? '');
    $konten   = $_POST['konten'] ?? '';
    $ringkasan = trim($_POST['ringkasan'] ?? '');
    $id_kat   = (int)($_POST['id_kategori'] ?? 0);
    $status   = $_POST['status'] ?? 'draft';
    $tags     = $_POST['tags'] ?? [];
    $id_user  = $current_user['id'];

    if (!$judul || !$konten) {
        $error = "Judul dan konten wajib diisi!";
    } else {
        $slug = makeSlug($judul);
        // Pastikan slug unik
        $check = $pdo->prepare("SELECT COUNT(*) FROM artikel WHERE slug=?");
        $check->execute([$slug]);
        if ($check->fetchColumn() > 0) $slug .= '-' . time();

        // Upload gambar
        $gambar = '';
        if (!empty($_FILES['gambar']['name'])) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array(strtolower($ext), $allowed)) {
                $gambar = time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['gambar']['tmp_name'], "../../assets/img/$gambar");
            }
        }

        $stmt = $pdo->prepare("INSERT INTO artikel (judul, slug, konten, ringkasan, gambar, id_kategori, id_user, status) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$judul, $slug, $konten, $ringkasan, $gambar, $id_kat ?: null, $id_user, $status]);
        $id_artikel = $pdo->lastInsertId();

        // Simpan tag
        if (!empty($tags)) {
            $stmt_tag = $pdo->prepare("INSERT IGNORE INTO artikel_tag VALUES (?,?)");
            foreach ($tags as $id_tag) $stmt_tag->execute([$id_artikel, (int)$id_tag]);
        }

        header("Location: index.php?success=1");
        exit;
    }
}

$kategoris = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();
$semua_tag = $pdo->query("SELECT * FROM tag ORDER BY nama")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Artikel - Blog Ku</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-brand"><h2>Blog<span>Ku</span></h2><p>Admin Panel</p></div>
    <nav class="admin-nav">
      <div class="admin-nav-label">Menu Utama</div>
      <a href="../dashboard.php"><span class="icon">🏠</span> Dashboard</a>
      <a href="index.php" class="active"><span class="icon">📝</span> Artikel</a>
      <a href="../kategori/index.php"><span class="icon">📂</span> Kategori</a>
      <a href="../tag/index.php"><span class="icon">🏷️</span> Tag</a>
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
    <div class="admin-topbar">
      <h1>✍️ Tambah Artikel Baru</h1>
      <a href="index.php" class="btn-secondary btn-sm">← Kembali</a>
    </div>
    <div class="admin-content">
      <?php if ($error): ?><div class="alert alert-danger" style="margin-bottom:20px;">❌ <?= $error ?></div><?php endif; ?>

      <div class="admin-card">
        <form class="form-admin" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label>Judul Artikel *</label>
            <input type="text" name="judul" class="form-control" placeholder="Masukkan judul artikel yang menarik..." required value="<?= htmlspecialchars($_POST['judul'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Ringkasan</label>
            <textarea name="ringkasan" class="form-control" rows="2" placeholder="Ringkasan singkat artikel (opsional)..."><?= htmlspecialchars($_POST['ringkasan'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>Konten Artikel *</label>
            <textarea name="konten" class="form-control" rows="12" placeholder="Tulis konten artikel di sini... (HTML diperbolehkan)" required><?= htmlspecialchars($_POST['konten'] ?? '') ?></textarea>
            <small style="color:var(--text-muted);">💡 Tips: Kamu bisa menggunakan tag HTML seperti &lt;p&gt;, &lt;h2&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;img&gt; dll.</small>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Kategori</label>
              <select name="id_kategori" class="form-control">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategoris as $k): ?>
                <option value="<?= $k['id'] ?>" <?= (($_POST['id_kategori'] ?? '') == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="draft" <?= (($_POST['status'] ?? 'draft') == 'draft') ? 'selected' : '' ?>>📄 Draft</option>
                <option value="publish" <?= (($_POST['status'] ?? '') == 'publish') ? 'selected' : '' ?>>🌐 Publish</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Gambar Utama</label>
            <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewImg(this)">
            <img id="img-preview" class="img-preview">
          </div>
          <div class="form-group">
            <label>Tag Artikel</label>
            <div class="tag-checkboxes">
              <?php foreach ($semua_tag as $t): ?>
              <input type="checkbox" name="tags[]" value="<?= $t['id'] ?>" id="tag_<?= $t['id'] ?>" class="tag-check" <?= in_array($t['id'], $_POST['tags'] ?? []) ? 'checked' : '' ?>>
              <label for="tag_<?= $t['id'] ?>">#<?= htmlspecialchars($t['nama']) ?></label>
              <?php endforeach; ?>
            </div>
          </div>
          <div style="display:flex;gap:12px;">
            <button type="submit" class="btn-primary">💾 Simpan Artikel</button>
            <a href="index.php" class="btn-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<script>
function previewImg(input) {
  const preview = document.getElementById('img-preview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
</body>
</html>
