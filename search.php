<?php
session_start();
require 'db.php';

$query = isset($_GET['q']) ? $_GET['q'] : '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

$where = "a.status = 'published'";
$params = [];
if ($query) {
    $where .= " AND (a.title LIKE ? OR a.content LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
}
if ($category_id) {
    $where .= " AND a.category_id = ?";
    $params[] = $category_id;
}

$stmt = $pdo->prepare("SELECT a.id, a.title, a.content, c.name as category FROM articles a JOIN categories c ON a.category_id = c.id WHERE $where ORDER BY a.updated_at DESC");
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Wikipedia Clone</title>
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
        .search-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .search-bar input, .search-bar select {
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
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
        @media (max-width: 768px) {
            .search-bar {
                flex-direction: column;
            }
            .search-bar input, .search-bar select {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Search Articles</h1>
    </header>
    <div class="container">
        <div class="search-bar">
            <input type="text" id="searchInput" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search articles...">
            <select id="categoryFilter" onchange="filterByCategory()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php if ($category_id == $category['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button onclick="search()">Search</button>
        </div>
        <h2>Results</h2>
        <?php foreach ($articles as $article): ?>
            <div class="article-card">
                <h3><a href="#" onclick="redirectTo('view_article.php?id=<?php echo $article['id']; ?>')"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                <p><?php echo substr(htmlspecialchars($article['content']), 0, 200); ?>...</p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($article['category']); ?></p>
            </div>
        <?php endforeach; ?>
        <button onclick="redirectTo('index.php')">Back to Home</button>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
        function search() {
            const query = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            let url = 'search.php?';
            if (query) url += 'q=' + encodeURIComponent(query);
            if (category) url += (query ? '&' : '') + 'category_id=' + category;
            redirectTo(url);
        }
        function filterByCategory() {
            search();
        }
    </script>
</body>
</html>
