<?php
session_start();
require '../config/db.php';
require '../includes/functions.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if (!$slug) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT a.*, k.nama as kategori_nama, k.slug as kategori_slug, u.nama as author_nama
    FROM artikel a LEFT JOIN kategori k ON a.id_kategori=k.id LEFT JOIN users u ON a.id_user=u.id
    WHERE a.slug=? AND a.status='publish'");
$stmt->execute([$slug]);
$art = $stmt->fetch();
if (!$art) { header("Location: index.php"); exit; }

// Update views
$pdo->prepare("UPDATE artikel SET views=views+1 WHERE id=?")->execute([$art['id']]);

// Tags artikel ini
$tags = $pdo->prepare("SELECT t.* FROM tag t JOIN artikel_tag at2 ON t.id=at2.id_tag WHERE at2.id_artikel=?");
$tags->execute([$art['id']]);
$art_tags = $tags->fetchAll();

// Komentar
$koms = $pdo->prepare("SELECT * FROM komentar WHERE id_artikel=? AND status='approved' ORDER BY created_at ASC");
$koms->execute([$art['id']]);
$komentar = $koms->fetchAll();
$jml_komentar = count($komentar);

// Proses kirim komentar
$kom_success = false; $kom_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_komentar'])) {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    if (!$nama || !$isi) { $kom_error = "Nama dan isi komentar wajib diisi!"; }
    else {
        $pdo->prepare("INSERT INTO komentar (id_artikel, nama, email, isi) VALUES (?,?,?,?)")
            ->execute([$art['id'], $nama, $email, $isi]);
        $kom_success = true;
    }
}

// Sidebar
$kategoris = $pdo->query("SELECT k.*, COUNT(a.id) as jml FROM kategori k LEFT JOIN artikel a ON k.id=a.id_kategori AND a.status='publish' GROUP BY k.id")->fetchAll();
$all_tags = $pdo->query("SELECT * FROM tag")->fetchAll();
$populer = $pdo->query("SELECT a.* FROM artikel a WHERE a.status='publish' ORDER BY a.views DESC LIMIT 5")->fetchAll();

// Artikel terkait
$terkait = $pdo->prepare("SELECT a.* FROM artikel a WHERE a.id_kategori=? AND a.id!=? AND a.status='publish' LIMIT 3");
$terkait->execute([$art['id_kategori'], $art['id']]);
$related = $terkait->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($art['judul']) ?> - Blog Ku</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="navbar-brand">Blog<span>Ku</span></a>
  <ul class="navbar-nav">
    <li><a href="index.php">Beranda</a></li>
    <?php foreach ($kategoris as $k): ?>
    <li><a href="index.php?kategori=<?= $k['slug'] ?>"><?= htmlspecialchars($k['nama']) ?></a></li>
    <?php endforeach; ?>
  </ul>
  <a href="../auth/login.php" class="btn-login">Login Admin</a>
</nav>

<div class="detail-container">
  <main>
    <!-- ARTIKEL -->
    <article class="article-full">
      <header class="article-full-header">
        <div class="article-meta" style="margin-bottom:16px;">
          <?php if ($art['kategori_nama']): ?>
          <a href="index.php?kategori=<?= $art['kategori_slug'] ?>" class="badge-kategori"><?= htmlspecialchars($art['kategori_nama']) ?></a>
          <?php endif; ?>
        </div>
        <h1><?= htmlspecialchars($art['judul']) ?></h1>
        <div class="article-stats" style="margin-top:12px;">
          <span>✍️ <?= htmlspecialchars($art['author_nama']) ?></span>
          <span>📅 <?= date('d M Y', strtotime($art['created_at'])) ?></span>
          <span>👁️ <?= number_format($art['views']) ?> views</span>
          <span>💬 <?= $jml_komentar ?> komentar</span>
        </div>
      </header>

      <div class="article-full-img">
        <?php if ($art['gambar'] && file_exists("../assets/img/".$art['gambar'])): ?>
          <img src="../assets/img/<?= $art['gambar'] ?>" alt="<?= htmlspecialchars($art['judul']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius);">
        <?php else: ?>📝<?php endif; ?>
      </div>

      <div class="article-content">
        <?= $art['konten'] ?>
      </div>

      <!-- Tags -->
      <?php if (!empty($art_tags)): ?>
      <div class="article-tags">
        <span>🏷️ Tag:</span>
        <?php foreach ($art_tags as $t): ?>
        <a href="index.php?tag=<?= $t['slug'] ?>" class="tag-item">#<?= htmlspecialchars($t['nama']) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </article>

    <!-- ARTIKEL TERKAIT -->
    <?php if (!empty($related)): ?>
    <div style="margin-bottom:40px;">
      <h3 class="section-title" style="font-size:1.3rem;">Artikel Terkait</h3>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;">
        <?php foreach ($related as $r): ?>
        <a href="artikel.php?slug=<?= $r['slug'] ?>" style="background:#fff;border-radius:12px;padding:20px;border:1px solid var(--border);display:block;transition:all 0.2s;" onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
          <h4 style="font-family:'Playfair Display',serif;font-size:0.95rem;line-height:1.4;margin-bottom:8px;"><?= htmlspecialchars($r['judul']) ?></h4>
          <span style="font-size:0.78rem;color:var(--text-muted);">👁️ <?= number_format($r['views']) ?> views</span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- KOMENTAR -->
    <div class="komentar-section">
      <h3>💬 Komentar (<?= $jml_komentar ?>)</h3>

      <?php if ($kom_success): ?>
        <div class="alert alert-success">✅ Komentar berhasil dikirim! Menunggu persetujuan admin.</div>
      <?php endif; ?>
      <?php if ($kom_error): ?>
        <div class="alert alert-danger">❌ <?= $kom_error ?></div>
      <?php endif; ?>

      <?php if (empty($komentar)): ?>
        <p style="color:var(--text-muted);text-align:center;padding:30px;">Belum ada komentar. Jadilah yang pertama! 💭</p>
      <?php endif; ?>

      <?php foreach ($komentar as $k): ?>
      <div class="komentar-item">
        <div class="komentar-header">
          <div class="komentar-avatar"><?= strtoupper(substr($k['nama'],0,1)) ?></div>
          <div>
            <div class="komentar-name"><?= htmlspecialchars($k['nama']) ?></div>
            <div class="komentar-date"><?= timeAgo($k['created_at']) ?></div>
          </div>
        </div>
        <p class="komentar-isi"><?= nl2br(htmlspecialchars($k['isi'])) ?></p>
      </div>
      <?php endforeach; ?>

      <!-- Form Komentar -->
      <div class="form-komentar">
        <h4>Tulis Komentar</h4>
        <form method="POST">
          <div class="form-row">
            <div class="form-group">
              <label>Nama *</label>
              <input type="text" name="nama" class="form-control" placeholder="Nama kamu" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" class="form-control" placeholder="email@contoh.com">
            </div>
          </div>
          <div class="form-group">
            <label>Komentar *</label>
            <textarea name="isi" class="form-control" rows="4" placeholder="Tulis komentar kamu di sini..." required></textarea>
          </div>
          <button type="submit" name="kirim_komentar" class="btn-primary">Kirim Komentar ✉️</button>
        </form>
      </div>
    </div>
  </main>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-widget">
      <div class="widget-title">Kategori</div>
      <ul class="kategori-list">
        <?php foreach ($kategoris as $k): ?>
        <li><a href="index.php?kategori=<?= $k['slug'] ?>"><?= htmlspecialchars($k['nama']) ?><span><?= $k['jml'] ?></span></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="sidebar-widget">
      <div class="widget-title">Artikel Populer</div>
      <?php foreach ($populer as $p): ?>
      <div class="artikel-populer-item">
        <div class="artikel-populer-img">📄</div>
        <div class="artikel-populer-info">
          <h4><a href="artikel.php?slug=<?= $p['slug'] ?>"><?= htmlspecialchars(substr($p['judul'],0,45)) ?>...</a></h4>
          <span>👁️ <?= number_format($p['views']) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="sidebar-widget">
      <div class="widget-title">Tag</div>
      <div class="tag-cloud">
        <?php foreach ($all_tags as $t): ?>
        <a href="index.php?tag=<?= $t['slug'] ?>" class="tag-item">#<?= htmlspecialchars($t['nama']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
  </aside>
</div>

<footer class="footer">
  <div class="footer-grid">
    <div>
      <div class="footer-brand">Blog<span>Ku</span></div>
      <p style="font-size:0.9rem;max-width:280px;line-height:1.7;">Platform blog personal yang menyajikan konten berkualitas.</p>
    </div>
    <div>
      <h4>Navigasi</h4>
      <ul><li><a href="index.php">Beranda</a></li></ul>
    </div>
    <div>
      <h4>Admin</h4>
      <ul><li><a href="../auth/login.php">Login Admin</a></li></ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© <?= date('Y') ?> <strong>Blog Ku</strong>. Dibuat dengan ❤️ menggunakan PHP & MySQL</p>
  </div>
</footer>
</body>
</html>
