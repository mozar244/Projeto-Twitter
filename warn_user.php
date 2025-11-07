<?php
// api/warn_user.php

include '../session_config.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['privilege_level'] ?? '') !== 'admin') {
    http_response_code(403);
    exit(json_encode(['success' => false]));
}

$tweet_id = $_POST['tweet_id'] ?? 0;
$reason = $_POST['reason'] ?? 'Violação das diretrizes';

if (!$tweet_id) {
    exit(json_encode(['success' => false]));
}

// Busca o dono do tweet
$stmt = $pdo->prepare("SELECT user_id FROM tweets WHERE id = ?");
$stmt->execute([$tweet_id]);
$tweet = $stmt->fetch();

if (!$tweet) {
    exit(json_encode(['success' => false]));
}

// Insere advertência
$stmt = $pdo->prepare("
    INSERT INTO warnings (tweet_id, admin_id, reason, message, created_at)
    VALUES (?, ?, ?, ?, NOW())
");
$message = "O tweet foi sinalizado por: " . $reason;
$stmt->execute([$tweet_id, $_SESSION['user_id'], $reason, $message]);
echo json_encode(['success' => true]);
?>