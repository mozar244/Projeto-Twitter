<?php
header('Content-Type: application/json');
include 'db_connect.php';
include '../session_config.php';  // <-- Sobe um nível!
//session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$tweet_id = filter_input(INPUT_POST, 'tweet_id', FILTER_VALIDATE_INT);

if (!$tweet_id) {
    echo json_encode(['success' => false]);
    exit;
}

// Verifica se já curtiu
$stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND tweet_id = ?");
$stmt->execute([$user_id, $tweet_id]);
$like = $stmt->fetch();

if ($like) {
    // Remove curtida
    $pdo->prepare("DELETE FROM likes WHERE id = ?")->execute([$like['id']]);
    $liked = false;
} else {
    // Adiciona curtida
    $pdo->prepare("INSERT INTO likes (user_id, tweet_id) VALUES (?, ?)")
          ->execute([$user_id, $tweet_id]);
    $liked = true;
}

// Conta curtidas
$count = $pdo->query("SELECT COUNT(*) FROM likes WHERE tweet_id = $tweet_id")->fetchColumn();

echo json_encode(['success' => true, 'liked' => $liked, 'like_count' => $count]);
?>