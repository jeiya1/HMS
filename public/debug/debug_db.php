<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../../config/connect.php";

$sql = file_get_contents('../../database/database.sql');

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
} else {
    echo "SQL Error: " . $conn->error;
}
?>