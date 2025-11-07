<?php
// api/alert_admin.php

include '../session_config.php';
include 'db_connect.php';

if ($_SESSION['privilege_level'] !== 'advanced') exit;

$tweet_id = $_POST['tweet_id'] ?? 0;
$reason = $_POST['reason'] ?? 'Comportamento inadequado';

$stmt = $pdo->prepare("INSERT INTO admin_alerts (tweet_id, reporter_id, reason, created_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$tweet_id, $_SESSION['user_id'], $reason]);

echo json_encode(['success' => true]);
?>