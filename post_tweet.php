<?php
header('Content-Type: application/json');
include 'db_connect.php';
include '../session_config.php';  // <-- Sobe um nível!
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

    if ($content && $user_id) {
        $stmt = $pdo->prepare("INSERT INTO tweets (user_id, content, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $content]);
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Dados inválidos']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
}
?>