<?php

class Event {

private $conn;

public function __construct()
{
$this->conn = new mysqli("localhost","root","","hotel");
}

public function getAllEvents()
{

$sql = "SELECT
guest_name,
room_number,
check_in,
check_out
FROM reservations";

$result = $this->conn->query($sql);

return $result->fetch_all(MYSQLI_ASSOC);

}

}