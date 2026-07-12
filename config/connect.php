<?php
require_once __DIR__ . '/../env.php';

$host     = $_ENV['DB_HOST'] ?? 'localhost';
$user     = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';
$db       = $_ENV['DB_DATABASE'] ?? 'HMS';
$port     = (int)($_ENV['DB_PORT'] ?? 3306);

$conn = new mysqli($host, $user, $password, null, $port);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$db`");
$conn->select_db($db);
?>
