<?php
require '../../includes/auth_check.php';
require '../../config/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM artikel WHERE id=?" . (!$is_admin ? " AND id_user={$current_user['id']}" : ""));
$stmt->execute([$id]);
$art = $stmt->fetch();

if ($art) {
    // Hapus gambar
    if ($art['gambar'] && file_exists("../../assets/img/".$art['gambar'])) {
        unlink("../../assets/img/".$art['gambar']);
    }
    $pdo->prepare("DELETE FROM artikel WHERE id=?")->execute([$id]);
}

header("Location: index.php?success=3");
exit;
