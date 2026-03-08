<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Debugger</title>
</head>
<body>
    <h1>Database Debugger</h1>

    <?php 
    if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
        die("Access denied");
    }
    require_once "../../config/connect.php";

    if (isset($_POST['ResetDB'])) {
        $conn->query("DROP DATABASE IF EXISTS HMS");
        $conn->query("CREATE DATABASE HMS");
        $conn->select_db("HMS");
        require_once "debug_db.php";
        echo "Database Reset";
    }
    ?>

    <form method="post">
        <input type="submit" name="ResetDB" value="ResetDB">
    </form>
</body>
</html>