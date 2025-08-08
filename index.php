<?php
session_start();
require 'db.php';

// Fetch featured and recent articles
$stmt = $pdo->query("SELECT a.id, a.title, a.content, c.name as category FROM articles a JOIN categories c ON a.category_id = c.id WHERE a.status = 'published' ORDER BY a.updated_at DESC LIMIT 5");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wikipedia Clone - Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        header {
            background-color: #003087;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        nav {
            background-color: #e6e6e6;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a {
            color: #003087;
            text-decoration: none;
            margin: 0 1rem;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
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
        .search-bar {
            display: flex;
            gap: 1rem;
        }
        .search-bar input {
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 200px;
        }
        .search-bar button {
            padding: 0.5rem 1rem;
            background-color: #003087;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #005bb5;
        }
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 1rem;
            }
            .search-bar input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Wikipedia Clone</h1>
    </header>
    <nav>
        <div>
            <a href="index.php">Home</a>
            <a href="#" onclick="redirectTo('create_article.php')">Create Article</a>
            <a href="#" onclick="redirectTo('search.php')">Search</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" onclick="redirectTo('profile.php')">Profile</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="#" onclick="redirectTo('moderate.php')">Moderate</a>
                <?php endif; ?>
                <a href="#" onclick="redirectTo('logout.php')">Logout</a>
            <?php else: ?>
                <a href="#" onclick="redirectTo('login.php')">Login</a>
            <?php endif; ?>
        </div>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search articles...">
            <button onclick="search()">Search</button>
        </div>
    </nav>
    <div class="container">
        <h2>Featured Articles</h2>
        <?php foreach ($articles as $article): ?>
            <div class="article-card">
                <h3><a href="#" onclick="redirectTo('view_article.php?id=<?php echo $article['id']; ?>')"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                <p><?php echo substr(htmlspecialchars($article['content']), 0, 200); ?>...</p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($article['category']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
        function search() {
            const query = document.getElementById('searchInput').value;
            redirectTo('search.php?q=' + encodeURIComponent(query));
        }
    </script>
</body>
</html>
