<?php
include 'session_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}

include 'api/db_connect.php';
$user_id = $_SESSION['user_id'];

// BUSCA USUÁRIO
$stmt = $pdo->prepare("SELECT username, privilege_level, points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: index.html');
    exit;
}

// BUSCA TODOS OS TWEETS
$stmt = $pdo->query("
    SELECT t.id, t.content, t.created_at, u.username, u.privilege_level,
           (SELECT COUNT(*) FROM likes l WHERE l.tweet_id = t.id) AS like_count
    FROM tweets t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");
$tweets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed - Minha Rede</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 1.5em; }
        .tweet-box { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 10px; resize: none; font-size: 1em; }
        .btn { background: #1da1f2; color: white; border: none; padding: 10px 20px; border-radius: 30px; cursor: pointer; font-weight: bold; }
        .tweet { border-bottom: 1px solid #eee; padding: 15px 0; }
        .tweet-header { display: flex; justify-content: space-between; font-size: 0.9em; color: #555; }
        .like-btn { background: none; border: none; font-size: 1.4em; cursor: pointer; }
        .like-btn:hover { transform: scale(1.2); }
    </style>
</head>
<body>
    <div class="container">
       <div class="header">
            <h1>Bem-vindo, <?php echo htmlspecialchars($user['username'] ?? 'Usuário'); ?>!</h1>
                    <div>
                        <strong>Nível: <?php echo ucfirst($user['privilege_level'] ?? 'basic'); ?></strong> 
                        | Pontos: <?php echo $user['points'] ?? 0; ?>
                        
                        <!-- BOTÃO PARA USER DASHBOARD -->
                        <a href="user_dashboard.php" class="btn" style="font-size:0.8em; padding:8px 12px; margin-left:10px;">Meu Painel</a>

                        <?php if (($user['privilege_level'] ?? '') === 'admin'): ?>
                            <a href="admin_dashboard.php" class="btn" style="font-size:0.8em; padding:8px 12px; margin-left:10px;">Painel Admin</a>
                        <?php endif; ?>
                        <a href="index.html?logout=1" style="margin-left:10px; color:#666;">Sair</a>
                    </div>
                </div>
        <div style="margin-bottom:30px;">
            <textarea id="tweetContent" class="tweet-box" placeholder="No que você está pensando?" maxlength="280"></textarea>
            <button onclick="postTweet()" class="btn" style="margin-top:10px;">Tweetar</button>
        </div>

        <div id="feed">
            <?php if (empty($tweets)): ?>
                <p style="text-align:center; color:#666;">Nenhum tweet ainda. Seja o primeiro!</p>
            <?php else: ?>
                <?php foreach ($tweets as $tweet): ?>
                    <div class="tweet">
                        <strong>@<?php echo htmlspecialchars($tweet['username']); ?></strong>
                        <?php if ($tweet['privilege_level'] === 'admin'): ?>
                            <span style="color:#e0245e; font-size:0.8em;"> [ADMIN]</span>
                        <?php endif; ?>
                        <p><?php echo nl2br(htmlspecialchars($tweet['content'])); ?></p>
                        <div class="tweet-header">
                            <small><?php echo date('d/m H:i', strtotime($tweet['created_at'])); ?></small>
                            <button class="like-btn" onclick="likeTweet(<?php echo $tweet['id']; ?>, this)">❤️ <?php echo $tweet['like_count']; ?></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        async function postTweet() {
            const content = document.getElementById('tweetContent').value.trim();
            if (!content) return alert('Escreva algo!');

            const res = await fetch('api/post_tweet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `content=${encodeURIComponent(content)}&user_id=<?php echo $user_id; ?>`
            });

            if (res.ok) {
                document.getElementById('tweetContent').value = '';
                location.reload();
            } else {
                alert('Erro ao tweetar.');
            }
        }

        async function likeTweet(tweetId, btn) {
            const res = await fetch('api/like_tweet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `tweet_id=${tweetId}&user_id=<?php echo $user_id; ?>`
            });

            if (res.ok) {
                const data = await res.json();
                btn.innerHTML = `❤️ ${data.like_count}`;
            }
        }
    </script>
</body>
</html>