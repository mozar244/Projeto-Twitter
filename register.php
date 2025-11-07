<?php
include '../session_config.php';
include 'db_connect.php';  // ESSA LINHA É OBRIGATÓRIA!

header('Content-Type: application/json');

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$email = $_POST['email'] ?? '';
$is_admin = isset($_POST['is_admin']) && $_POST['is_admin'] === '1';

if (!$username || !$password) {
    echo json_encode(['success' => false, 'error' => 'Preencha usuário e senha']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Usuário já existe']);
    exit;
}

$privilege = $is_admin ? 'admin' : 'basic';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (username, email, password_hash, privilege_level) 
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$username, $email, $hash, $privilege]);

echo json_encode(['success' => true]);
?>