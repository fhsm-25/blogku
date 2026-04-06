<?php
session_start();
require '../config/db.php';
require '../includes/functions.php';

// Ambil artikel publish dengan pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 5;
$offset = ($page - 1) * $per_page;

$filter_kat = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$filter_tag = isset($_GET['tag']) ? $_GET['tag'] : '';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$where = "WHERE a.status = 'publish'";
$params = [];

if ($filter_kat) { $where .= " AND k.slug = ?"; $params[] = $filter_kat; }
if ($filter_tag) { $where .= " AND EXISTS (SELECT 1 FROM artikel_tag at2 JOIN tag t2 ON at2.id_tag=t2.id WHERE at2.id_artikel=a.id AND t2.slug=?)"; $params[] = $filter_tag; }
if ($search) { $where .= " AND (a.judul LIKE ? OR a.ringkasan LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

$total = $pdo->prepare("SELECT COUNT(*) FROM artikel a LEFT JOIN kategori k ON a.id_kategori=k.id $where");
$total->execute($params);
$total_rows = $total->fetchColumn();
$total_pages = ceil($total_rows / $per_page);

$stmt = $pdo->prepare("SELECT a.*, k.nama as kategori_nama, k.slug as kategori_slug, u.nama as author_nama
    FROM artikel a
    LEFT JOIN kategori k ON a.id_kategori = k.id
    LEFT JOIN users u ON a.id_user = u.id
    $where ORDER BY a.created_at DESC LIMIT $per_page OFFSET $offset");
$stmt->execute($params);
$artikels = $stmt->fetchAll();

// Sidebar: kategori
$kategoris = $pdo->query("SELECT k.*, COUNT(a.id) as jml FROM kategori k LEFT JOIN artikel a ON k.id=a.id_kategori AND a.status='publish' GROUP BY k.id")->fetchAll();

// Sidebar: tag
$tags = $pdo->query("SELECT * FROM tag")->fetchAll();

// Sidebar: artikel populer
$populer = $pdo->query("SELECT a.*, k.nama as kategori_nama FROM artikel a LEFT JOIN kategori k ON a.id_kategori=k.id WHERE a.status='publish' ORDER BY a.views DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $search ? "Hasil: $search - " : ($filter_kat ? ucfirst($filter_kat)." - " : "") ?>Blog Ku</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <a href="index.php" class="navbar-brand">Blog<span>Ku</span></a>
  <ul class="navbar-nav">
    <li><a href="index.php">Beranda</a></li>
    <?php foreach ($kategoris as $k): ?>
    <li><a href="?kategori=<?= $k['slug'] ?>"><?= htmlspecialchars($k['nama']) ?></a></li>
    <?php endforeach; ?>
  </ul>
  <a href="../auth/login.php" class="btn-login">Login Admin</a>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-content">
    <span class="hero-badge">✦ Blog Terkini</span>
    <h1>Selamat Datang di <em>Blog Ku</em></h1>
    <p>Temukan artikel menarik seputar teknologi, lifestyle, tutorial, dan banyak lagi.</p>
    <form class="hero-search" method="GET" action="index.php">
      <input type="text" name="q" placeholder="Cari artikel..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit">Cari</button>
    </form>
  </div>
</section>

<!-- MAIN -->
<div class="main-container">
  <!-- ARTIKEL -->
  <main>
    <h2 class="section-title">
      <?php if ($search): ?>Hasil Pencarian: "<?= htmlspecialchars($search) ?>"
      <?php elseif ($filter_kat): ?>Kategori: <?= htmlspecialchars(ucfirst($filter_kat)) ?>
      <?php elseif ($filter_tag): ?>Tag: #<?= htmlspecialchars($filter_tag) ?>
      <?php else: ?>Artikel Terbaru<?php endif; ?>
    </h2>

    <div class="articles-grid">
      <?php if (empty($artikels)): ?>
        <p style="color:var(--text-muted); padding: 40px; text-align:center;">Tidak ada artikel ditemukan.</p>
      <?php endif; ?>
      <?php foreach ($artikels as $art): ?>
      <article class="article-card">
        <div class="article-card-img">
          <?php if ($art['gambar'] && file_exists("../assets/img/".$art['gambar'])): ?>
            <img src="../assets/img/<?= $art['gambar'] ?>" alt="<?= htmlspecialchars($art['judul']) ?>" style="width:100%;height:100%;object-fit:cover;">
          <?php else: ?>
            📝
          <?php endif; ?>
        </div>
        <div class="article-card-body">
          <div class="article-meta">
            <?php if ($art['kategori_nama']): ?>
            <a href="?kategori=<?= $art['kategori_slug'] ?>" class="badge-kategori"><?= htmlspecialchars($art['kategori_nama']) ?></a>
            <?php endif; ?>
            <span class="article-date">📅 <?= timeAgo($art['created_at']) ?></span>
            <span class="article-date">👁️ <?= number_format($art['views']) ?> views</span>
          </div>
          <h2><a href="artikel.php?slug=<?= $art['slug'] ?>"><?= htmlspecialchars($art['judul']) ?></a></h2>
          <p><?= htmlspecialchars($art['ringkasan'] ?: strip_tags(substr($art['konten'], 0, 150)).'...') ?></p>
          <div class="article-footer">
            <div class="author-info">
              <div class="author-avatar"><?= strtoupper(substr($art['author_nama'], 0, 1)) ?></div>
              <span class="author-name"><?= htmlspecialchars($art['author_nama']) ?></span>
            </div>
            <a href="artikel.php?slug=<?= $art['slug'] ?>" class="read-more">Baca Selengkapnya →</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?><a href="?page=<?= $page-1 ?>&q=<?= urlencode($search) ?>&kategori=<?= $filter_kat ?>">‹</a><?php endif; ?>
      <?php for ($i=1; $i<=$total_pages; $i++): ?>
      <<?= $i==$page ? 'span class="active"' : 'a href="?page='.$i.'&q='.urlencode($search).'&kategori='.$filter_kat.'"' ?>><?= $i ?></<?= $i==$page ? 'span' : 'a' ?>>
      <?php endfor; ?>
      <?php if ($page < $total_pages): ?><a href="?page=<?= $page+1 ?>&q=<?= urlencode($search) ?>&kategori=<?= $filter_kat ?>">›</a><?php endif; ?>
    </div>
    <?php endif; ?>
  </main>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-widget">
      <div class="widget-title">Kategori</div>
      <ul class="kategori-list">
        <?php foreach ($kategoris as $k): ?>
        <li><a href="?kategori=<?= $k['slug'] ?>"><?= htmlspecialchars($k['nama']) ?><span><?= $k['jml'] ?></span></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="sidebar-widget">
      <div class="widget-title">Artikel Populer</div>
      <?php foreach ($populer as $p): ?>
      <div class="artikel-populer-item">
        <div class="artikel-populer-img">📄</div>
        <div class="artikel-populer-info">
          <h4><a href="artikel.php?slug=<?= $p['slug'] ?>"><?= htmlspecialchars(substr($p['judul'], 0, 45)) ?>...</a></h4>
          <span>👁️ <?= number_format($p['views']) ?> views</span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="sidebar-widget">
      <div class="widget-title">Tag</div>
      <div class="tag-cloud">
        <?php foreach ($tags as $t): ?>
        <a href="?tag=<?= $t['slug'] ?>" class="tag-item">#<?= htmlspecialchars($t['nama']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
  </aside>
</div>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-grid">
    <div>
      <div class="footer-brand">Blog<span>Ku</span></div>
      <p style="font-size:0.9rem;max-width:280px;line-height:1.7;">Platform blog personal yang menyajikan konten berkualitas seputar teknologi, lifestyle, dan tutorial.</p>
    </div>
    <div>
      <h4>Navigasi</h4>
      <ul>
        <li><a href="index.php">Beranda</a></li>
        <?php foreach ($kategoris as $k): ?><li><a href="?kategori=<?= $k['slug'] ?>"><?= htmlspecialchars($k['nama']) ?></a></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h4>Admin</h4>
      <ul>
        <li><a href="../auth/login.php">Login Admin</a></li>
        <li><a href="../admin/dashboard.php">Dashboard</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© <?= date('Y') ?> <strong>Blog Ku</strong>. Dibuat dengan ❤️ menggunakan PHP & MySQL</p>
  </div>
</footer>

</body>
</html>
