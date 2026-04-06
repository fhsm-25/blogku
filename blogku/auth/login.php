<?php
session_start();
require '../config/db.php';
require '../includes/functions.php';

if (isset($_SESSION['user'])) { header("Location: ../admin/dashboard.php"); exit; }

// Generate captcha awal
if (!isset($_SESSION['captcha_answer'])) generateCaptcha();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha  = (int)($_POST['captcha'] ?? 0);

    if ($captcha !== (int)$_SESSION['captcha_answer']) {
        $error = "Jawaban CAPTCHA salah! Coba lagi.";
        generateCaptcha(); // Reset captcha
    } elseif (!$username || !$password) {
        $error = "Username dan password wajib diisi!";
        generateCaptcha();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            unset($_SESSION['captcha_answer'], $_SESSION['captcha_soal']);
            header("Location: ../admin/dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah!";
            generateCaptcha();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin - Blog Ku</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
  body { background: #1a1a2e; }
</style>
</head>
<body>

<div class="login-page">
  <div class="login-card">
    <div class="login-logo">
      <h1>Blog<span>Ku</span></h1>
      <p>Masuk ke panel admin</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
      </div>

      <!-- CAPTCHA -->
      <div class="form-group">
        <label>Verifikasi CAPTCHA</label>
        <div class="captcha-box">
          <span class="captcha-soal">🧮 <?= $_SESSION['captcha_soal'] ?> = ?</span>
          <input type="number" name="captcha" class="form-control" placeholder="Jawab" required>
        </div>
        <small style="color:rgba(255,255,255,0.3);font-size:0.78rem;">Jawab soal matematika di atas untuk membuktikan kamu bukan robot.</small>
      </div>

      <button type="submit" class="btn-primary btn-full">🔐 Masuk</button>
    </form>

    <div style="margin-top:24px;text-align:center;">
      <a href="../public/index.php" style="color:rgba(255,255,255,0.35);font-size:0.85rem;">← Kembali ke Blog</a>
    </div>

    <div style="margin-top:20px;padding:14px;background:rgba(255,255,255,0.04);border-radius:10px;border:1px solid rgba(255,255,255,0.08);">
      <p style="color:rgba(255,255,255,0.3);font-size:0.78rem;text-align:center;margin-bottom:6px;">Demo Login:</p>
      <p style="color:rgba(255,255,255,0.4);font-size:0.8rem;text-align:center;">Admin: <code style="color:var(--accent2);">admin</code> / <code style="color:var(--accent2);">password</code></p>
      <p style="color:rgba(255,255,255,0.4);font-size:0.8rem;text-align:center;">Author: <code style="color:var(--accent2);">author1</code> / <code style="color:var(--accent2);">password</code></p>
    </div>
  </div>
</div>

</body>
</html>
