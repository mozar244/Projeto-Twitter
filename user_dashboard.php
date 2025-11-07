<?php
// user_dashboard.php

include 'session_config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}

include 'api/db_connect.php';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username, privilege_level, points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT COALESCE(w.reason, 'Sem motivo informado') as reason, 
           w.created_at, 
           a.username as admin_name
    FROM warnings w
    JOIN users a ON w.admin_id = a.id
    WHERE w.tweet_id IN (SELECT id FROM tweets WHERE user_id = ?)
    ORDER BY w.created_at DESC
");$stmt->execute([$user_id]);
$warnings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Painel - @<?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .level-badge { background: #007bff; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.8em; }
        .warning-box { background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 20px 0; color: #856404; }
        .warning-header { font-weight: bold; color: #d39e00; margin-bottom: 8px; }
        .warning-item { background: white; padding: 10px; margin: 8px 0; border-radius: 5px; border-left: 4px solid #f39c12; }
        .no-warnings { color: #28a745; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>@<?php echo htmlspecialchars($user['username']); ?></h1>
            <a href="dashboard.php">Voltar ao Feed</a>
        </div>

        <div style="text-align:center; margin:20px 0;">
            <span class="level-badge"><?php echo ucfirst($user['privilege_level']); ?></span>
            <p style="margin:10px 0; font-size:1.2em;"><strong><?php echo $user['points']; ?> pontos</strong></p>
        </div>

        <div class="warning-box">
            <div class="warning-header">
                Advertências 
                <span style="background:red;color:white;padding:2px 6px;border-radius:50%;font-size:12px;"><?php echo count($warnings); ?></span>
            </div>
            <?php if (empty($warnings)): ?>
                <p class="no-warnings">Nenhuma advertência. Continue assim!</p>
            <?php else: ?>
                <?php foreach ($warnings as $w): ?>
                    <div class="warning-item">
                        <strong>De: @<?php echo htmlspecialchars($w['admin_name']); ?></strong> 
                        <small>— <?php echo date('d/m H:i', strtotime($w['created_at'])); ?></small><br>
                        <?php echo nl2br(htmlspecialchars($w['reason'])); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <a href="dashboard.php" class="btn" style="display:block; text-align:center; margin-top:20px;">Voltar ao Feed</a>
    </div>
</body>
</html>