<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("DELETE FROM pending_works WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
header('Location: index.php');
exit;
?>
