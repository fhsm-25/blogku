@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=DM+Sans:wght@300;400;500;600&display=swap');

:root {
  --primary: #1a1a2e;
  --accent: #e94560;
  --accent2: #f5a623;
  --bg: #fafaf8;
  --card: #ffffff;
  --text: #2d2d2d;
  --text-muted: #888;
  --border: #ebebeb;
  --shadow: 0 4px 24px rgba(0,0,0,0.07);
  --shadow-hover: 0 12px 40px rgba(0,0,0,0.13);
  --radius: 16px;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'DM Sans', sans-serif;
  background: var(--bg);
  color: var(--text);
  line-height: 1.7;
}

a { text-decoration: none; color: inherit; }

/* ===== NAVBAR ===== */
.navbar {
  background: var(--primary);
  padding: 0 5%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
  position: sticky;
  top: 0;
  z-index: 999;
  box-shadow: 0 2px 20px rgba(0,0,0,0.3);
}

.navbar-brand {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  font-weight: 900;
  color: #fff;
  letter-spacing: -1px;
}

.navbar-brand span { color: var(--accent); }

.navbar-nav { display: flex; gap: 2rem; list-style: none; }

.navbar-nav a {
  color: rgba(255,255,255,0.75);
  font-size: 0.9rem;
  font-weight: 500;
  letter-spacing: 0.5px;
  transition: color 0.2s;
}

.navbar-nav a:hover { color: var(--accent); }

.btn-login {
  background: var(--accent);
  color: #fff !important;
  padding: 8px 20px;
  border-radius: 50px;
  font-weight: 600 !important;
  transition: background 0.2s, transform 0.2s !important;
}
.btn-login:hover { background: #c73652 !important; transform: translateY(-1px); }

/* ===== HERO ===== */
.hero {
  background: var(--primary);
  padding: 80px 5% 100px;
  position: relative;
  overflow: hidden;
}

.hero::before {
  content: '';
  position: absolute;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(233,69,96,0.15) 0%, transparent 70%);
  top: -100px; right: -100px;
  border-radius: 50%;
}

.hero::after {
  content: '';
  position: absolute;
  width: 400px; height: 400px;
  background: radial-gradient(circle, rgba(245,166,35,0.1) 0%, transparent 70%);
  bottom: -50px; left: 10%;
  border-radius: 50%;
}

.hero-content { position: relative; z-index: 1; max-width: 700px; }

.hero-badge {
  display: inline-block;
  background: rgba(233,69,96,0.2);
  color: var(--accent);
  border: 1px solid rgba(233,69,96,0.3);
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 20px;
}

.hero h1 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 900;
  color: #fff;
  line-height: 1.15;
  margin-bottom: 20px;
}

.hero h1 em { color: var(--accent); font-style: italic; }

.hero p {
  color: rgba(255,255,255,0.65);
  font-size: 1.1rem;
  max-width: 500px;
  margin-bottom: 30px;
}

.hero-search {
  display: flex;
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 50px;
  overflow: hidden;
  max-width: 480px;
  backdrop-filter: blur(10px);
}

.hero-search input {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  padding: 14px 20px;
  color: #fff;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
}

.hero-search input::placeholder { color: rgba(255,255,255,0.4); }

.hero-search button {
  background: var(--accent);
  border: none;
  padding: 14px 24px;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: background 0.2s;
}
.hero-search button:hover { background: #c73652; }

/* ===== MAIN LAYOUT ===== */
.main-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 60px 5%;
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 50px;
}

@media (max-width: 900px) {
  .main-container { grid-template-columns: 1fr; }
}

/* ===== SECTION TITLE ===== */
.section-title {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 30px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.section-title::after {
  content: '';
  flex: 1;
  height: 2px;
  background: linear-gradient(90deg, var(--accent), transparent);
}

/* ===== ARTICLE CARD ===== */
.articles-grid { display: flex; flex-direction: column; gap: 30px; }

.article-card {
  background: var(--card);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  display: grid;
  grid-template-columns: 280px 1fr;
  transition: transform 0.3s, box-shadow 0.3s;
  border: 1px solid var(--border);
}

.article-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-hover);
}

.article-card-img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  background: linear-gradient(135deg, #1a1a2e, #e94560);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 4rem;
}

.article-card-body { padding: 28px; }

.article-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}

.badge-kategori {
  background: rgba(233,69,96,0.1);
  color: var(--accent);
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.article-date {
  color: var(--text-muted);
  font-size: 0.8rem;
}

.article-card h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.35rem;
  font-weight: 700;
  margin-bottom: 10px;
  line-height: 1.35;
}

.article-card h2 a { transition: color 0.2s; }
.article-card h2 a:hover { color: var(--accent); }

.article-card p {
  color: var(--text-muted);
  font-size: 0.9rem;
  margin-bottom: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.article-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.author-info { display: flex; align-items: center; gap: 8px; }

.author-avatar {
  width: 32px; height: 32px;
  border-radius: 50%;
  background: var(--accent);
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 0.8rem;
}

.author-name { font-size: 0.85rem; font-weight: 500; }

.read-more {
  color: var(--accent);
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
  transition: gap 0.2s;
}
.read-more:hover { gap: 8px; }

@media (max-width: 600px) {
  .article-card { grid-template-columns: 1fr; }
  .article-card-img { height: 180px; }
}

/* ===== SIDEBAR ===== */
.sidebar { display: flex; flex-direction: column; gap: 30px; }

.sidebar-widget {
  background: var(--card);
  border-radius: var(--radius);
  padding: 28px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border);
}

.widget-title {
  font-family: 'Playfair Display', serif;
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 18px;
  padding-bottom: 12px;
  border-bottom: 2px solid var(--accent);
  display: inline-block;
}

.kategori-list { list-style: none; }
.kategori-list li {
  border-bottom: 1px solid var(--border);
  padding: 10px 0;
}
.kategori-list li:last-child { border-bottom: none; }
.kategori-list a {
  display: flex;
  justify-content: space-between;
  color: var(--text);
  font-size: 0.9rem;
  transition: color 0.2s;
}
.kategori-list a:hover { color: var(--accent); }
.kategori-list span {
  background: var(--bg);
  padding: 2px 8px;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-muted);
}

.tag-cloud { display: flex; flex-wrap: wrap; gap: 8px; }
.tag-item {
  background: var(--bg);
  border: 1px solid var(--border);
  color: var(--text);
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 0.8rem;
  transition: all 0.2s;
}
.tag-item:hover {
  background: var(--accent);
  color: #fff;
  border-color: var(--accent);
}

.artikel-populer-item {
  display: flex;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid var(--border);
}
.artikel-populer-item:last-child { border-bottom: none; }
.artikel-populer-img {
  width: 60px; height: 60px;
  border-radius: 8px;
  object-fit: cover;
  background: linear-gradient(135deg, var(--primary), var(--accent));
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}
.artikel-populer-info h4 {
  font-size: 0.85rem;
  font-weight: 600;
  line-height: 1.3;
  margin-bottom: 4px;
}
.artikel-populer-info h4 a:hover { color: var(--accent); }
.artikel-populer-info span { font-size: 0.75rem; color: var(--text-muted); }

/* ===== PAGINATION ===== */
.pagination {
  display: flex;
  gap: 8px;
  justify-content: center;
  margin-top: 40px;
}
.pagination a, .pagination span {
  width: 40px; height: 40px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 50%;
  border: 1px solid var(--border);
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.2s;
}
.pagination a:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
.pagination .active { background: var(--accent); color: #fff; border-color: var(--accent); }

/* ===== FOOTER ===== */
.footer {
  background: var(--primary);
  color: rgba(255,255,255,0.6);
  padding: 50px 5% 30px;
  margin-top: 60px;
}
.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 40px;
  max-width: 1200px;
  margin: 0 auto 40px;
}
.footer-brand {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  font-weight: 900;
  color: #fff;
  margin-bottom: 12px;
}
.footer-brand span { color: var(--accent); }
.footer h4 { color: #fff; font-size: 0.9rem; font-weight: 600; margin-bottom: 16px; letter-spacing: 1px; text-transform: uppercase; }
.footer ul { list-style: none; }
.footer ul li { margin-bottom: 10px; }
.footer ul a { color: rgba(255,255,255,0.5); font-size: 0.9rem; transition: color 0.2s; }
.footer ul a:hover { color: var(--accent); }
.footer-bottom {
  border-top: 1px solid rgba(255,255,255,0.1);
  padding-top: 24px;
  text-align: center;
  font-size: 0.85rem;
  max-width: 1200px;
  margin: 0 auto;
}

/* ===== DETAIL ARTIKEL ===== */
.detail-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 60px 5%;
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 50px;
}

.article-full { }
.article-full-header { margin-bottom: 30px; }
.article-full-img {
  width: 100%;
  height: 420px;
  object-fit: cover;
  border-radius: var(--radius);
  margin-bottom: 30px;
  background: linear-gradient(135deg, var(--primary), var(--accent));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 6rem;
}
.article-full h1 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(1.8rem, 4vw, 2.8rem);
  font-weight: 900;
  line-height: 1.2;
  margin-bottom: 16px;
}
.article-stats {
  display: flex;
  gap: 20px;
  color: var(--text-muted);
  font-size: 0.85rem;
  flex-wrap: wrap;
}
.article-stats span { display: flex; align-items: center; gap: 5px; }
.article-content {
  background: var(--card);
  border-radius: var(--radius);
  padding: 40px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border);
  font-size: 1rem;
  line-height: 1.9;
  margin-bottom: 30px;
}
.article-content h2, .article-content h3 {
  font-family: 'Playfair Display', serif;
  margin: 24px 0 12px;
}
.article-content p { margin-bottom: 16px; }
.article-content ul, .article-content ol { padding-left: 20px; margin-bottom: 16px; }
.article-content img { max-width: 100%; border-radius: 8px; margin: 16px 0; }

.article-tags { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 40px; align-items: center; }
.article-tags span { font-size: 0.85rem; color: var(--text-muted); }

/* ===== KOMENTAR ===== */
.komentar-section {
  background: var(--card);
  border-radius: var(--radius);
  padding: 36px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border);
}
.komentar-section h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.4rem;
  margin-bottom: 24px;
}
.komentar-item {
  border-bottom: 1px solid var(--border);
  padding: 20px 0;
}
.komentar-item:last-child { border-bottom: none; }
.komentar-header { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
.komentar-avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary), var(--accent));
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 0.9rem;
  flex-shrink: 0;
}
.komentar-name { font-weight: 600; font-size: 0.9rem; }
.komentar-date { font-size: 0.78rem; color: var(--text-muted); }
.komentar-isi { font-size: 0.92rem; color: var(--text); line-height: 1.6; }

.form-komentar { margin-top: 32px; }
.form-komentar h4 { font-family: 'Playfair Display', serif; font-size: 1.2rem; margin-bottom: 20px; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; }
.form-control {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid var(--border);
  border-radius: 10px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
  background: var(--bg);
  transition: border-color 0.2s, box-shadow 0.2s;
  color: var(--text);
}
.form-control:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(233,69,96,0.1);
}
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.btn-primary {
  background: var(--accent);
  color: #fff;
  border: none;
  padding: 12px 28px;
  border-radius: 50px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, transform 0.2s;
}
.btn-primary:hover { background: #c73652; transform: translateY(-1px); }

/* ===== LOGIN PAGE ===== */
.login-page {
  min-height: 100vh;
  background: var(--primary);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  position: relative;
  overflow: hidden;
}
.login-page::before {
  content: '';
  position: absolute;
  width: 700px; height: 700px;
  background: radial-gradient(circle, rgba(233,69,96,0.12) 0%, transparent 70%);
  top: -200px; right: -200px;
}
.login-card {
  background: rgba(255,255,255,0.04);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 24px;
  padding: 50px 44px;
  width: 100%;
  max-width: 440px;
  position: relative;
  z-index: 1;
}
.login-logo {
  text-align: center;
  margin-bottom: 36px;
}
.login-logo h1 {
  font-family: 'Playfair Display', serif;
  font-size: 2.5rem;
  font-weight: 900;
  color: #fff;
}
.login-logo h1 span { color: var(--accent); }
.login-logo p { color: rgba(255,255,255,0.45); font-size: 0.9rem; margin-top: 4px; }

.login-card .form-group label { color: rgba(255,255,255,0.7); }
.login-card .form-control {
  background: rgba(255,255,255,0.07);
  border-color: rgba(255,255,255,0.1);
  color: #fff;
}
.login-card .form-control::placeholder { color: rgba(255,255,255,0.3); }
.login-card .form-control:focus {
  border-color: var(--accent);
  background: rgba(255,255,255,0.1);
}

.captcha-box {
  background: rgba(245,166,35,0.1);
  border: 1px solid rgba(245,166,35,0.3);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}
.captcha-soal {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--accent2);
  flex: 1;
}
.captcha-box .form-control { max-width: 100px; text-align: center; }

.alert {
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-size: 0.9rem;
}
.alert-danger { background: rgba(233,69,96,0.15); color: #ff8096; border: 1px solid rgba(233,69,96,0.2); }
.alert-success { background: rgba(0,200,100,0.15); color: #00c864; border: 1px solid rgba(0,200,100,0.2); }

.btn-full { width: 100%; padding: 14px; font-size: 1rem; }

/* ===== ADMIN DASHBOARD ===== */
.admin-layout { display: flex; min-height: 100vh; }

.admin-sidebar {
  width: 260px;
  background: var(--primary);
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  overflow-y: auto;
  z-index: 100;
}

.admin-brand {
  padding: 28px 24px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.admin-brand h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.6rem;
  font-weight: 900;
  color: #fff;
}
.admin-brand h2 span { color: var(--accent); }
.admin-brand p { color: rgba(255,255,255,0.35); font-size: 0.78rem; margin-top: 2px; }

.admin-nav { padding: 20px 0; flex: 1; }
.admin-nav-label {
  padding: 10px 24px 6px;
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: rgba(255,255,255,0.25);
}
.admin-nav a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 11px 24px;
  color: rgba(255,255,255,0.55);
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.2s;
  border-left: 3px solid transparent;
}
.admin-nav a:hover, .admin-nav a.active {
  color: #fff;
  background: rgba(255,255,255,0.06);
  border-left-color: var(--accent);
}
.admin-nav a .icon { font-size: 1.1rem; width: 20px; text-align: center; }

.admin-user {
  padding: 20px 24px;
  border-top: 1px solid rgba(255,255,255,0.08);
  display: flex;
  align-items: center;
  gap: 12px;
}
.admin-user-avatar {
  width: 38px; height: 38px;
  border-radius: 50%;
  background: var(--accent);
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 0.9rem;
  flex-shrink: 0;
}
.admin-user-info .name { color: #fff; font-size: 0.88rem; font-weight: 600; }
.admin-user-info .role { color: rgba(255,255,255,0.35); font-size: 0.75rem; }
.admin-user a { color: rgba(255,255,255,0.3); margin-left: auto; font-size: 1.2rem; }
.admin-user a:hover { color: var(--accent); }

.admin-main {
  margin-left: 260px;
  flex: 1;
  background: #f4f4f0;
  min-height: 100vh;
}

.admin-topbar {
  background: #fff;
  padding: 0 32px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--border);
  box-shadow: 0 1px 0 rgba(0,0,0,0.05);
}
.admin-topbar h1 { font-size: 1.25rem; font-weight: 700; }
.admin-topbar-actions { display: flex; gap: 12px; }

.admin-content { padding: 32px; }

/* Stats Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 32px;
}
.stat-card {
  background: #fff;
  border-radius: 14px;
  padding: 24px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
  display: flex;
  align-items: center;
  gap: 20px;
}
.stat-icon {
  width: 52px; height: 52px;
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.5rem;
}
.stat-icon.red { background: rgba(233,69,96,0.1); }
.stat-icon.blue { background: rgba(59,130,246,0.1); }
.stat-icon.green { background: rgba(16,185,129,0.1); }
.stat-icon.yellow { background: rgba(245,166,35,0.1); }
.stat-info .value {
  font-size: 1.8rem;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 4px;
}
.stat-info .label { font-size: 0.82rem; color: var(--text-muted); }

/* Admin Table */
.admin-card {
  background: #fff;
  border-radius: 14px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
  overflow: hidden;
  margin-bottom: 24px;
}
.admin-card-header {
  padding: 20px 24px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.admin-card-header h3 { font-size: 1rem; font-weight: 700; }

.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 14px 20px; text-align: left; border-bottom: 1px solid var(--border); font-size: 0.88rem; }
.table th { background: #fafaf8; font-weight: 600; color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; }
.table tr:last-child td { border-bottom: none; }
.table tbody tr:hover { background: #fafaf8; }

.badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 50px;
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
}
.badge-publish { background: rgba(16,185,129,0.12); color: #059669; }
.badge-draft { background: rgba(107,114,128,0.12); color: #6b7280; }
.badge-pending { background: rgba(245,166,35,0.12); color: #d97706; }
.badge-approved { background: rgba(16,185,129,0.12); color: #059669; }
.badge-admin { background: rgba(233,69,96,0.12); color: var(--accent); }
.badge-author { background: rgba(59,130,246,0.12); color: #3b82f6; }

.btn-sm {
  padding: 5px 12px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: all 0.2s;
  text-decoration: none;
  display: inline-block;
}
.btn-edit { background: rgba(59,130,246,0.1); color: #3b82f6; }
.btn-edit:hover { background: #3b82f6; color: #fff; }
.btn-delete { background: rgba(233,69,96,0.1); color: var(--accent); }
.btn-delete:hover { background: var(--accent); color: #fff; }
.btn-approve { background: rgba(16,185,129,0.1); color: #059669; }
.btn-approve:hover { background: #059669; color: #fff; }
.btn-secondary {
  background: #f4f4f0;
  color: var(--text);
  padding: 10px 20px;
  border-radius: 50px;
  font-weight: 600;
  border: 1px solid var(--border);
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: all 0.2s;
  text-decoration: none;
  display: inline-block;
}
.btn-secondary:hover { background: var(--border); }

/* Admin Form */
.form-admin { padding: 28px; }
.form-admin .form-group { margin-bottom: 20px; }
.form-admin label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
.form-admin .form-control { background: var(--bg); }
select.form-control { cursor: pointer; }
textarea.form-control { resize: vertical; min-height: 200px; }

.tag-checkboxes { display: flex; flex-wrap: wrap; gap: 8px; }
.tag-check { display: none; }
.tag-check + label {
  background: var(--bg);
  border: 1px solid var(--border);
  padding: 5px 14px;
  border-radius: 50px;
  font-size: 0.82rem;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 500;
}
.tag-check:checked + label {
  background: var(--accent);
  color: #fff;
  border-color: var(--accent);
}

.img-preview {
  width: 200px;
  height: 130px;
  border-radius: 10px;
  object-fit: cover;
  border: 2px solid var(--border);
  display: none;
  margin-top: 10px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .admin-sidebar { display: none; }
  .admin-main { margin-left: 0; }
  .form-row { grid-template-columns: 1fr; }
  .detail-container { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr; }
}
