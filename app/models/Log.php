<?php

class Log {

private $conn;

public function __construct()
{
$this->conn = new mysqli("localhost","root","","hotel");
}

public function getAdminLogs()
{

$sql = "SELECT date, action FROM admin_logs ORDER BY date DESC LIMIT 10";

$result = $this->conn->query($sql);

return $result->fetch_all(MYSQLI_ASSOC);

}

public function getUserLogs()
{

$sql = "SELECT date, action FROM user_logs ORDER BY date DESC LIMIT 10";

$result = $this->conn->query($sql);

return $result->fetch_all(MYSQLI_ASSOC);

}

public function addLog($action)
{

$stmt = $this->conn->prepare(
"INSERT INTO admin_logs(action,date) VALUES (?,NOW())"
);

$stmt->bind_param("s",$action);

$stmt->execute();

}

}