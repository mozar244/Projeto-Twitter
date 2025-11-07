<?php
// admin_dashboard.php

include 'session_config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['privilege_level'] ?? '') !== 'admin') {
    header('Location: index.html');
    exit;
}

include 'api/db_connect.php';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin</title>
    <style>
        body { font-family: Arial; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        .btn { padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9em; margin-right: 5px; }
        .btn-warn { background: #ffc107; color: #212529; }
        .btn-promote { background: #28a745; color: white; }
        .btn-ban { background: #dc3545; color: white; }
        .tweet-list { margin-top: 30px; }
        .tweet-item { border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 8px; }
        .tweet-actions { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Painel Admin - <?php echo htmlspecialchars($admin['username']); ?></h1>
            <a href="dashboard.php">Voltar ao Feed</a>
        </div>

        <!-- USUÁRIOS -->
        <h2>Gerenciar Usuários</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Nível</th>
                <th>Pontos</th>
                <th>Ações</th>
            </tr>
            <?php
            $stmt = $pdo->query("SELECT id, username, privilege_level, points FROM users ORDER BY id");
            while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $isAdmin = $user['privilege_level'] === 'admin';
                $isBanned = $user['privilege_level'] === 'banned';
                echo "<tr>";
                echo "<td>{$user['id']}</td>";
                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td>" . ucfirst($user['privilege_level']) . "</td>";
                echo "<td>{$user['points']}</td>";
                echo "<td>";
                if (!$isAdmin && !$isBanned) {
                    echo "<button class='btn btn-promote' onclick='promoteUser({$user['id']})'>Promover</button>";
                    echo "<button class='btn btn-ban' onclick='banUser({$user['id']})'>Banir</button>";
                } elseif ($isBanned) {
                    echo "<span style='color:#dc3545;'>Banido</span>";
                } else {
                    echo "<span style='color:#28a745;'>Admin</span>";
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>

        <!-- TWEETS PARA MODERAÇÃO -->
        <div class="tweet-list">
            <h2>Tweets Recentes (Moderação)</h2>
            <?php
            $stmt = $pdo->query("
                SELECT t.id, t.content, t.created_at, u.username 
                FROM tweets t 
                JOIN users u ON t.user_id = u.id 
                WHERE u.privilege_level != 'admin'
                ORDER BY t.created_at DESC LIMIT 20
            ");
            while ($tweet = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='tweet-item'>";
                echo "<strong>@{$tweet['username']}</strong> ";
                echo "<small>" . date('d/m H:i', strtotime($tweet['created_at'])) . "</small><br>";
                echo "<p>" . nl2br(htmlspecialchars($tweet['content'])) . "</p>";
                echo "<div class='tweet-actions'>";
                echo "<button class='btn btn-warn' onclick='warnTweet({$tweet['id']})'>Advertir</button>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <script>
        async function promoteUser(userId) {
            if (!confirm('Promover este usuário para moderador?')) return;
            await actionUser(userId, 'promote');
        }

        async function banUser(userId) {
            if (!confirm('Banir este usuário permanentemente?')) return;
            await actionUser(userId, 'ban');
        }

        async function warnTweet(tweetId) {
                const reason = prompt('Motivo da advertência:');
                if (!reason) return;

                const res = await fetch('api/warn_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `tweet_id=${tweetId}&reason=${encodeURIComponent(reason)}`
                });

                if (res.ok) {
                    alert('Advertência enviada!');
                } else {
                    alert('Erro ao advertir.');
                }
        }

        async function actionUser(userId, action) {
            const res = await fetch('api/update_privilege.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `user_id=${userId}&action=${action}`
            });
            if (res.ok) {
                location.reload();
            } else {
                alert('Erro na ação.');
            }
        }
    </script>
</body>
</html>