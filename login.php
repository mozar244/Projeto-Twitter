<?php
include '../session_config.php';
include 'db_connect.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, username, password_hash, privilege_level FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash']))

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['privilege_level'] = $user['privilege_level'];

        echo json_encode([
            'success' => true,
            'message' => 'Login realizado!',
            'redirect' => 'dashboard.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário ou senha incorretos.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no servidor.']);
}
exit;
?>