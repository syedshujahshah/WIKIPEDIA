<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $author_id = $_SESSION['user_id'];
    $status = $_SESSION['role'] === 'admin' ? 'published' : 'pending';

    $stmt = $pdo->prepare("INSERT INTO articles (title, content, category_id, author_id, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $category_id, $author_id, $status]);
    $article_id = $pdo->lastInsertId();

    if ($status === 'published') {
        $stmt = $pdo->prepare("INSERT INTO revisions (article_id, content, editor_id, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$article_id, $content, $author_id, 'Initial version']);
    }

    echo "<script>window.location.href='view_article.php?id=$article_id';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Article - Wikipedia Clone</title>
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
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        textarea {
            height: 300px;
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
        <h1>Create Article</h1>
    </header>
    <div class="container">
        <form method="POST">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" required></textarea>
            </div>
            <button type="submit">Create Article</button>
        </form>
        <button onclick="redirectTo('index.php')">Back to Home</button>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
