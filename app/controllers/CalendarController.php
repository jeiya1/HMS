<?php

require_once "../app/models/Event.php";

class CalendarController {

private $eventModel;

public function __construct()
{
$this->eventModel = new Event();
}

public function index()
{

$events = $this->eventModel->getAllEvents();

require "../app/views/calendar/index.php";

}

}