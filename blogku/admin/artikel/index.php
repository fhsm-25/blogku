<?php
require '../../includes/auth_check.php';
require '../../config/db.php';

$success = isset($_GET['success']) ? $_GET['success'] : '';

$where = $is_admin ? "" : "WHERE a.id_user = {$current_user['id']}";
$artikels = $pdo->query("SELECT a.*, k.nama as kat_nama, u.nama as author_nama FROM artikel a LEFT JOIN kategori k ON a.id_kategori=k.id LEFT JOIN users u ON a.id_user=u.id $where ORDER BY a.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Artikel - Blog Ku</title>
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
      <h1>📝 Kelola Artikel</h1>
      <a href="tambah.php" class="btn-primary btn-sm" style="padding:8px 18px;">+ Tambah Artikel</a>
    </div>
    <div class="admin-content">
      <?php if ($success == 1): ?><div class="alert alert-success" style="margin-bottom:20px;">✅ Artikel berhasil disimpan!</div><?php endif; ?>
      <?php if ($success == 2): ?><div class="alert alert-success" style="margin-bottom:20px;">✅ Artikel berhasil diupdate!</div><?php endif; ?>
      <?php if ($success == 3): ?><div class="alert alert-success" style="margin-bottom:20px;">✅ Artikel berhasil dihapus!</div><?php endif; ?>

      <div class="admin-card">
        <div class="admin-card-header"><h3>Semua Artikel (<?= count($artikels) ?>)</h3></div>
        <table class="table">
          <thead><tr><th>#</th><th>Judul</th><th>Kategori</th><th>Author</th><th>Status</th><th>Views</th><th>Tanggal</th><th>Aksi</th></tr></thead>
          <tbody>
            <?php foreach ($artikels as $i => $a): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><strong><?= htmlspecialchars(substr($a['judul'],0,40)) ?><?= strlen($a['judul'])>40?'...':'' ?></strong></td>
              <td><?= htmlspecialchars($a['kat_nama'] ?? '-') ?></td>
              <td><?= htmlspecialchars($a['author_nama']) ?></td>
              <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
              <td><?= number_format($a['views']) ?></td>
              <td><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
              <td>
                <a href="../../public/artikel.php?slug=<?= $a['slug'] ?>" target="_blank" class="btn-sm" style="background:rgba(16,185,129,0.1);color:#059669;">👁️</a>
                <a href="edit.php?id=<?= $a['id'] ?>" class="btn-sm btn-edit">Edit</a>
                <a href="hapus.php?id=<?= $a['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Yakin hapus artikel ini?')">Hapus</a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($artikels)): ?><tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:40px;">Belum ada artikel.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>
