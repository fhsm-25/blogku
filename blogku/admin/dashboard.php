<?php
require '../includes/auth_check.php';
require '../config/db.php';

$total_artikel = $pdo->query("SELECT COUNT(*) FROM artikel")->fetchColumn();
$total_publish = $pdo->query("SELECT COUNT(*) FROM artikel WHERE status='publish'")->fetchColumn();
$total_komentar = $pdo->query("SELECT COUNT(*) FROM komentar WHERE status='pending'")->fetchColumn();
$total_user = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$artikel_terbaru = $pdo->query("SELECT a.*, u.nama as author_nama, k.nama as kat_nama FROM artikel a LEFT JOIN users u ON a.id_user=u.id LEFT JOIN kategori k ON a.id_kategori=k.id ORDER BY a.created_at DESC LIMIT 6")->fetchAll();
$komentar_terbaru = $pdo->query("SELECT km.*, a.judul FROM komentar km LEFT JOIN artikel a ON km.id_artikel=a.id WHERE km.status='pending' ORDER BY km.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Blog Ku Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-layout">

  <!-- SIDEBAR -->
  <aside class="admin-sidebar">
    <div class="admin-brand">
      <h2>Blog<span>Ku</span></h2>
      <p>Admin Panel</p>
    </div>
    <nav class="admin-nav">
      <div class="admin-nav-label">Menu Utama</div>
      <a href="dashboard.php" class="active"><span class="icon">🏠</span> Dashboard</a>
      <a href="artikel/index.php"><span class="icon">📝</span> Artikel</a>
      <a href="kategori/index.php"><span class="icon">📂</span> Kategori</a>
      <a href="tag/index.php"><span class="icon">🏷️</span> Tag</a>
      <a href="komentar/index.php"><span class="icon">💬</span> Komentar</a>
      <?php if ($is_admin): ?>
      <div class="admin-nav-label">Pengaturan</div>
      <a href="user/index.php"><span class="icon">👥</span> Pengguna</a>
      <?php endif; ?>
      <div class="admin-nav-label">Blog</div>
      <a href="../public/index.php" target="_blank"><span class="icon">🌐</span> Lihat Blog</a>
    </nav>
    <div class="admin-user">
      <div class="admin-user-avatar"><?= strtoupper(substr($current_user['nama'],0,1)) ?></div>
      <div class="admin-user-info">
        <div class="name"><?= htmlspecialchars($current_user['nama']) ?></div>
        <div class="role"><?= ucfirst($current_user['role']) ?></div>
      </div>
      <a href="../auth/logout.php" title="Logout">🚪</a>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="admin-main">
    <div class="admin-topbar">
      <h1>Dashboard</h1>
      <div class="admin-topbar-actions">
        <a href="artikel/tambah.php" class="btn-primary btn-sm" style="padding:8px 18px;">+ Artikel Baru</a>
      </div>
    </div>

    <div class="admin-content">
      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon red">📝</div>
          <div class="stat-info">
            <div class="value"><?= $total_artikel ?></div>
            <div class="label">Total Artikel</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">✅</div>
          <div class="stat-info">
            <div class="value"><?= $total_publish ?></div>
            <div class="label">Artikel Publish</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow">💬</div>
          <div class="stat-info">
            <div class="value"><?= $total_komentar ?></div>
            <div class="label">Komentar Pending</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue">👥</div>
          <div class="stat-info">
            <div class="value"><?= $total_user ?></div>
            <div class="label">Pengguna</div>
          </div>
        </div>
      </div>

      <!-- Artikel Terbaru -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h3>📝 Artikel Terbaru</h3>
          <a href="artikel/index.php" class="btn-sm btn-edit">Lihat Semua</a>
        </div>
        <table class="table">
          <thead>
            <tr><th>Judul</th><th>Kategori</th><th>Author</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <?php foreach ($artikel_terbaru as $a): ?>
            <tr>
              <td><strong><?= htmlspecialchars(substr($a['judul'],0,45)) ?><?= strlen($a['judul'])>45?'...':'' ?></strong></td>
              <td><?= htmlspecialchars($a['kat_nama'] ?? '-') ?></td>
              <td><?= htmlspecialchars($a['author_nama']) ?></td>
              <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
              <td><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
              <td>
                <a href="artikel/edit.php?id=<?= $a['id'] ?>" class="btn-sm btn-edit">Edit</a>
                <a href="artikel/hapus.php?id=<?= $a['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Hapus artikel ini?')">Hapus</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Komentar Pending -->
      <?php if (!empty($komentar_terbaru)): ?>
      <div class="admin-card">
        <div class="admin-card-header">
          <h3>💬 Komentar Menunggu Persetujuan</h3>
          <a href="komentar/index.php" class="btn-sm btn-edit">Kelola</a>
        </div>
        <table class="table">
          <thead>
            <tr><th>Nama</th><th>Artikel</th><th>Komentar</th><th>Waktu</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <?php foreach ($komentar_terbaru as $k): ?>
            <tr>
              <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
              <td><?= htmlspecialchars(substr($k['judul'],0,30)) ?>...</td>
              <td><?= htmlspecialchars(substr($k['isi'],0,50)) ?>...</td>
              <td><?= date('d/m H:i', strtotime($k['created_at'])) ?></td>
              <td>
                <a href="komentar/aksi.php?id=<?= $k['id'] ?>&aksi=approve" class="btn-sm btn-approve">✓ Approve</a>
                <a href="komentar/aksi.php?id=<?= $k['id'] ?>&aksi=delete" class="btn-sm btn-delete" onclick="return confirm('Hapus?')">Hapus</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>
