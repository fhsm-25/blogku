<?php
require '../../includes/auth_check.php';
require '../../config/db.php';

$filter = $_GET['filter'] ?? 'pending';
$where = in_array($filter, ['pending','approved','rejected']) ? "WHERE km.status='$filter'" : "";

$komentar = $pdo->query("SELECT km.*, a.judul as judul_artikel, a.slug as artikel_slug FROM komentar km LEFT JOIN artikel a ON km.id_artikel=a.id $where ORDER BY km.created_at DESC")->fetchAll();

$counts = $pdo->query("SELECT status, COUNT(*) as jml FROM komentar GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Komentar - Blog Ku</title>
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
      <a href="index.php" class="active"><span class="icon">💬</span> Komentar</a>
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
    <div class="admin-topbar"><h1>💬 Kelola Komentar</h1></div>
    <div class="admin-content">
      <!-- Filter Tabs -->
      <div style="display:flex;gap:10px;margin-bottom:20px;">
        <a href="?filter=pending" class="btn-sm <?= $filter=='pending'?'btn-primary':'' ?>" style="<?= $filter!='pending'?'background:#fff;border:1px solid var(--border);color:var(--text);':'' ?>">
          ⏳ Pending <span style="background:rgba(255,255,255,0.2);padding:2px 6px;border-radius:50px;margin-left:4px;"><?= $counts['pending'] ?? 0 ?></span>
        </a>
        <a href="?filter=approved" class="btn-sm <?= $filter=='approved'?'btn-approve':'' ?>" style="<?= $filter!='approved'?'background:#fff;border:1px solid var(--border);color:var(--text);':'' ?>">
          ✅ Approved <span style="background:rgba(255,255,255,0.2);padding:2px 6px;border-radius:50px;margin-left:4px;"><?= $counts['approved'] ?? 0 ?></span>
        </a>
        <a href="?filter=all" class="btn-sm" style="background:#fff;border:1px solid var(--border);color:var(--text);">Semua</a>
      </div>

      <div class="admin-card">
        <div class="admin-card-header"><h3>Komentar <?= ucfirst($filter) ?> (<?= count($komentar) ?>)</h3></div>
        <table class="table">
          <thead><tr><th>Nama</th><th>Email</th><th>Artikel</th><th>Komentar</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr></thead>
          <tbody>
            <?php foreach ($komentar as $k): ?>
            <tr>
              <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
              <td><?= htmlspecialchars($k['email'] ?? '-') ?></td>
              <td><a href="../../public/artikel.php?slug=<?= $k['artikel_slug'] ?>" target="_blank" style="color:var(--accent);font-size:0.82rem;"><?= htmlspecialchars(substr($k['judul_artikel'],0,30)) ?>...</a></td>
              <td style="max-width:200px;"><?= htmlspecialchars(substr($k['isi'],0,60)) ?>...</td>
              <td><span class="badge badge-<?= $k['status'] ?>"><?= ucfirst($k['status']) ?></span></td>
              <td><?= date('d/m H:i', strtotime($k['created_at'])) ?></td>
              <td>
                <?php if ($k['status'] == 'pending'): ?>
                <a href="aksi.php?id=<?= $k['id'] ?>&aksi=approve&back=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-sm btn-approve">✓</a>
                <?php endif; ?>
                <a href="aksi.php?id=<?= $k['id'] ?>&aksi=delete&back=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-sm btn-delete" onclick="return confirm('Hapus komentar ini?')">✕</a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($komentar)): ?><tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">Tidak ada komentar.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>
