<?php
require '../../includes/auth_check.php';
require '../../config/db.php';

$id   = (int)($_GET['id'] ?? 0);
$aksi = $_GET['aksi'] ?? '';
$back = $_GET['back'] ?? 'index.php';

if ($id) {
    if ($aksi === 'approve') {
        $pdo->prepare("UPDATE komentar SET status='approved' WHERE id=?")->execute([$id]);
    } elseif ($aksi === 'delete') {
        $pdo->prepare("DELETE FROM komentar WHERE id=?")->execute([$id]);
    }
}

header("Location: " . (strpos($back, 'dashboard') !== false ? $back : 'index.php'));
exit;
