<?php
$host = 'localhost';
$dbname = 'dbs3csiqg7bepy';
$username = 'uac1gp3zeje8t';
$password = 'hk8ilpc7us2e';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
