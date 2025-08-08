<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

if (isset($_GET['revision_id']) && isset($_GET['article_id'])) {
    $revision_id = $_GET['revision_id'];
    $article_id = $_GET['article_id'];
    $stmt = $pdo->prepare("SELECT content FROM revisions WHERE id = ?");
    $stmt->execute([$revision_id]);
    $revision = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("UPDATE articles SET content = ?, status = 'published' WHERE id = ?");
    $stmt->execute([$revision['content'], $article_id]);
    echo "<script>window.location.href='view_article.php?id=$article_id';</script>";
    exit;
}

$stmt = $pdo->query("SELECT a.id, a.title, a.status, c.name as category FROM articles a JOIN categories c ON a.category_id = c.id WHERE a.status = 'pending'");
$pending_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = $_POST['article_id'];
    $action = $_POST['action'];
    $status = $action === 'approve' ? 'published' : 'draft';
    $stmt = $pdo->prepare("UPDATE articles SET status = ? WHERE id = ?");
    $stmt->execute([$status, $article_id]);
    echo "<script>window.location.href='moderate.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderation - Wikipedia Clone</title>
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
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
        }
        .article-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .article-card h3 {
            margin: 0;
            color: #003087;
        }
        button {
            padding: 0.5rem 1rem;
            background-color: #003087;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 0.5rem;
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
        <h1>Moderation Panel</h1>
    </header>
    <div class="container">
        <h2>Pending Articles</h2>
        <?php foreach ($pending_articles as $article): ?>
            <div class="article-card">
                <h3><a href="#" onclick="redirectTo('view_article.php?id=<?php echo $article['id']; ?>')"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($article['category']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($article['status']); ?></p>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                    <button type="submit" name="action" value="approve">Approve</button>
                    <button type="submit" name="action" value="reject">Reject</button>
                </form>
                <button onclick="redirectTo('history.php?id=<?php echo $article['id']; ?>')">View History</button>
            </div>
        <?php endforeach; ?>
        <button onclick="redirectTo('index.php')">Back to Home</button>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
