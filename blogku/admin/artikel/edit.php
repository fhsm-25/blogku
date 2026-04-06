<?php
require '../../includes/auth_check.php';
require '../../config/db.php';
require '../../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM artikel WHERE id=?" . (!$is_admin ? " AND id_user={$current_user['id']}" : ""));
$stmt->execute([$id]);
$art = $stmt->fetch();
if (!$art) { header("Location: index.php"); exit; }

// Tag yang sudah dipilih
$selected_tags_stmt = $pdo->prepare("SELECT id_tag FROM artikel_tag WHERE id_artikel=?");
$selected_tags_stmt->execute([$id]);
$selected_tags = array_column($selected_tags_stmt->fetchAll(), 'id_tag');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul'] ?? '');
    $konten   = $_POST['konten'] ?? '';
    $ringkasan = trim($_POST['ringkasan'] ?? '');
    $id_kat   = (int)($_POST['id_kategori'] ?? 0);
    $status   = $_POST['status'] ?? 'draft';
    $tags     = $_POST['tags'] ?? [];

    if (!$judul || !$konten) {
        $error = "Judul dan konten wajib diisi!";
    } else {
        $slug = makeSlug($judul);
        $check = $pdo->prepare("SELECT COUNT(*) FROM artikel WHERE slug=? AND id!=?");
        $check->execute([$slug, $id]);
        if ($check->fetchColumn() > 0) $slug = makeSlug($judul) . '-' . $id;

        $gambar = $art['gambar'];
        if (!empty($_FILES['gambar']['name'])) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array(strtolower($ext), $allowed)) {
                // Hapus gambar lama
                if ($gambar && file_exists("../../assets/img/$gambar")) unlink("../../assets/img/$gambar");
                $gambar = time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['gambar']['tmp_name'], "../../assets/img/$gambar");
            }
        }

        $pdo->prepare("UPDATE artikel SET judul=?, slug=?, konten=?, ringkasan=?, gambar=?, id_kategori=?, status=?, updated_at=NOW() WHERE id=?")
            ->execute([$judul, $slug, $konten, $ringkasan, $gambar, $id_kat ?: null, $status, $id]);

        // Update tag
        $pdo->prepare("DELETE FROM artikel_tag WHERE id_artikel=?")->execute([$id]);
        if (!empty($tags)) {
            $stmt_tag = $pdo->prepare("INSERT IGNORE INTO artikel_tag VALUES (?,?)");
            foreach ($tags as $id_tag) $stmt_tag->execute([$id, (int)$id_tag]);
        }

        header("Location: index.php?success=2");
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
<title>Edit Artikel - Blog Ku</title>
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
      <h1>✏️ Edit Artikel</h1>
      <div style="display:flex;gap:8px;">
        <a href="../../public/artikel.php?slug=<?= $art['slug'] ?>" target="_blank" class="btn-secondary btn-sm">👁️ Lihat</a>
        <a href="index.php" class="btn-secondary btn-sm">← Kembali</a>
      </div>
    </div>
    <div class="admin-content">
      <?php if ($error): ?><div class="alert alert-danger" style="margin-bottom:20px;">❌ <?= $error ?></div><?php endif; ?>

      <div class="admin-card">
        <form class="form-admin" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label>Judul Artikel *</label>
            <input type="text" name="judul" class="form-control" required value="<?= htmlspecialchars($_POST['judul'] ?? $art['judul']) ?>">
          </div>
          <div class="form-group">
            <label>Ringkasan</label>
            <textarea name="ringkasan" class="form-control" rows="2"><?= htmlspecialchars($_POST['ringkasan'] ?? $art['ringkasan']) ?></textarea>
          </div>
          <div class="form-group">
            <label>Konten Artikel *</label>
            <textarea name="konten" class="form-control" rows="12" required><?= htmlspecialchars($_POST['konten'] ?? $art['konten']) ?></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Kategori</label>
              <select name="id_kategori" class="form-control">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategoris as $k): ?>
                <option value="<?= $k['id'] ?>" <?= (($_POST['id_kategori'] ?? $art['id_kategori']) == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="draft" <?= (($_POST['status'] ?? $art['status']) == 'draft') ? 'selected' : '' ?>>📄 Draft</option>
                <option value="publish" <?= (($_POST['status'] ?? $art['status']) == 'publish') ? 'selected' : '' ?>>🌐 Publish</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Gambar Utama</label>
            <?php if ($art['gambar']): ?>
            <div style="margin-bottom:10px;">
              <img src="../../assets/img/<?= $art['gambar'] ?>" style="height:80px;border-radius:8px;object-fit:cover;">
              <small style="display:block;color:var(--text-muted);margin-top:4px;">Gambar saat ini. Upload baru untuk mengganti.</small>
            </div>
            <?php endif; ?>
            <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewImg(this)">
            <img id="img-preview" class="img-preview">
          </div>
          <div class="form-group">
            <label>Tag Artikel</label>
            <div class="tag-checkboxes">
              <?php foreach ($semua_tag as $t): ?>
              <?php $checked = in_array($t['id'], isset($_POST['tags']) ? array_map('intval', $_POST['tags']) : $selected_tags); ?>
              <input type="checkbox" name="tags[]" value="<?= $t['id'] ?>" id="tag_<?= $t['id'] ?>" class="tag-check" <?= $checked ? 'checked' : '' ?>>
              <label for="tag_<?= $t['id'] ?>">#<?= htmlspecialchars($t['nama']) ?></label>
              <?php endforeach; ?>
            </div>
          </div>
          <div style="display:flex;gap:12px;">
            <button type="submit" class="btn-primary">💾 Update Artikel</button>
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
