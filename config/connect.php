<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "HMS";

$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS $db");
$conn->select_db($db);
?>