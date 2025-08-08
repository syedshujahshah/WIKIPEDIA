<?php
session_start();
require 'db.php';

$article_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT a.*, c.name as category, u.username as author FROM articles a JOIN categories c ON a.category_id = c.id JOIN users u ON a.author_id = u.id WHERE a.id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - Wikipedia Clone</title>
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
        .article-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        button {
            padding: 0.75rem;
            background-color: #003087;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 1rem;
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
        <h1><?php echo htmlspecialchars($article['title']); ?></h1>
    </header>
    <div class="container">
        <div class="article-meta">
            <p><strong>Category:</strong> <?php echo htmlspecialchars($article['category']); ?></p>
            <p><strong>Author:</strong> <?php echo htmlspecialchars($article['author']); ?></p>
            <p><strong>Last Updated:</strong> <?php echo $article['updated_at']; ?></p>
        </div>
        <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <button onclick="redirectTo('edit_article.php?id=<?php echo $article_id; ?>')">Edit Article</button>
            <button onclick="redirectTo('history.php?id=<?php echo $article_id; ?>')">View History</button>
        <?php endif; ?>
        <button onclick="redirectTo('index.php')">Back to Home</button>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
