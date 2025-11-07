<?php
// api/suspend_user.php

include '../session_config.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) exit(json_encode(['success' => false]));

$actor_id = $_SESSION['user_id'];
$actor_level = $_SESSION['privilege_level'] ?? 'basic';
$target_id = $_POST['target_id'] ?? 0;
$target_level = $_POST['level'] ?? '';

$allowed = false;
if ($actor_level === 'intermediate' && $target_level === 'basic') $allowed = true;
if ($actor_level === 'advanced' && in_array($target_level, ['basic', 'intermediate'])) $allowed = true;

if (!$allowed || !$target_id) {
    http_response_code(403);
    exit(json_encode(['success' => false]));
}

$stmt = $pdo->prepare("UPDATE users SET privilege_level = 'suspended' WHERE id = ?");
$stmt->execute([$target_id]);

echo json_encode(['success' => true]);
?>