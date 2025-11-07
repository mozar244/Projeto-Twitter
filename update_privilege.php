<?php
// api/update_privilege.php

include '../session_config.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['privilege_level'] ?? '') !== 'admin') {
    http_response_code(403);
    exit(json_encode(['success' => false]));
}

$user_id = $_POST['user_id'] ?? 0;
$action = $_POST['action'] ?? '';

if (!$user_id || !in_array($action, ['promote', 'ban'])) {
    exit(json_encode(['success' => false]));
}

if ($action === 'promote') {
    $stmt = $pdo->prepare("UPDATE users SET privilege_level = 'moderator' WHERE id = ? AND privilege_level NOT IN ('admin', 'banned')");
} else {
    $stmt = $pdo->prepare("UPDATE users SET privilege_level = 'banned' WHERE id = ? AND privilege_level != 'admin'");
}

$stmt->execute([$user_id]);

echo json_encode(['success' => true]);
?>