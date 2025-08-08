<?php
session_start();
require 'db.php';

$article_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT r.*, u.username as editor FROM revisions r JOIN users u ON r.editor_id = u.id WHERE r.article_id = ? ORDER BY r.edited_at DESC");
$stmt->execute([$article_id]);
$revisions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT title FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revision History - <?php echo htmlspecialchars($article['title']); ?> - Wikipedia Clone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        header {
            background-color: #003087;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .revision {
            border-bottom: 1px solid #ccc;
            padding: 1rem 0;
        }
        button {
            padding: 0.75rem;
            background-color: #003087;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #005bb5;
        }
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Revision History - <?php echo htmlspecialchars($article['title']); ?></h1>
    </header>
    <div class="container">
        <?php foreach ($revisions as $revision): ?>
            <div class="revision">
                <p><strong>Edited by:</strong> <?php echo htmlspecialchars($revision['editor']); ?></p>
                <p><strong>Edited at:</strong> <?php echo $revision['edited_at']; ?></p>
                <p><strong>Comment:</strong> <?php echo htmlspecialchars($revision['comment']); ?></p>
                <p><?php echo substr(htmlspecialchars($revision['content']), 0, 200); ?>...</p>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <button onclick="redirectTo('moderate.php?revision_id=<?php echo $revision['id']; ?>&article_id=<?php echo $article_id; ?>')">Revert to this version</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <button onclick="redirectTo('view_article.php?id=<?php echo $article_id; ?>')">Back to Article</button>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
