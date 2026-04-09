<?php

require_once "../app/models/Room.php";

class RoomsController {

    private $roomModel;

    public function __construct()
    {
        $this->roomModel = new Room();
    }

    // SHOW ROOMS PAGE
    public function index()
    {
        $rooms = $this->roomModel->getAllRooms();

        require "../app/views/rooms/index.php";
    }

    // ADD ROOM
    public function store()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $type = $_POST['type'];
            $occupancy = $_POST['room_occupancy'];
            $price = $_POST['price'];
            $description = $_POST['description'];

            $imageName = $_FILES['image']['name'];
            $tmp = $_FILES['image']['tmp_name'];

            $uploadPath = "../public/images/" . $imageName;

            move_uploaded_file($tmp,$uploadPath);

            $this->roomModel->addRoom(
                $type,
                $occupancy,
                $price,
                $description,
                $imageName
            );

            header("Location: /rooms");
            exit;
        }
    }

    // UPDATE ROOM
    public function update()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id = $_POST['id'];
            $type = $_POST['type'];
            $occupancy = $_POST['occupancy'];
            $price = $_POST['price'];
            $description = $_POST['description'];

            $this->roomModel->updateRoom(
                $id,
                $type,
                $occupancy,
                $price,
                $description
            );

            header("Location: /rooms");
            exit;
        }
    }

    // DELETE ROOM
    public function delete()
    {
        if(isset($_POST['id']))
        {
            $id = $_POST['id'];

            $this->roomModel->deleteRoom($id);

            header("Location: /rooms");
            exit;
        }
    }

}