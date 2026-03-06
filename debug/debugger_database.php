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
    require_once "../connect.php";

    if (isset($_POST['CreateDB'])) {
        require_once "debug_db.php";
        echo "Database Created";
    }

    if (isset($_POST['ResetDB'])) {
        $conn->query("DROP DATABASE IF EXISTS HMS");
        require_once "debug_db.php";
        echo "Database Resetted";
    }

    if (isset($_POST['ClearDB'])) {
        $conn->execute_query("SET FOREIGN_KEY_CHECKS = 0");
        $tables = $conn->query("SHOW TABLES");
        while ($row = $tables->fetch_array()) {
            $conn->query("TRUNCATE TABLE " . $row[0]);
        }
        echo "All Tables Cleared";
    }
    ?>

    <form method="post">
        <input type="submit" name="CreateDB" value="CreateDB">
        <input type="submit" name="ResetDB" value="ResetDB">
        <input type="submit" name="ClearDB" value="ClearDB">
    </form>
</body>
</html>