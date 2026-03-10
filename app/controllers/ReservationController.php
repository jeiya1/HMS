<?php

require_once '../app/models/Reservation.php';
require_once '../app/models/Room.php';

class ReservationController {

    public function createReservation() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $guestID = $_POST['guestID'];
            $roomID = $_POST['roomID'];
            $checkin = $_POST['checkin'];
            $checkout = $_POST['checkout'];
            $adults = $_POST['adults'];
            $children = $_POST['children'];

            if(empty($checkin) || empty($checkout)){
                echo "Please select check-in and check-out dates.";
                return;
            }

            if($adults < 1){
                echo "At least 1 adult is required.";
                return;
            }

            $roomModel = new Room($GLOBALS['conn']);

            $available = $roomModel->checkRoomAvailability($roomID,$checkin,$checkout);

            if(!$available){
                echo "Room is already booked for selected dates.";
                return;
            }//Prevents double booking by checking if a room is already reserved during the selected dates

            $reservationModel = new Reservation($GLOBALS['conn']);

            $reservationID = $reservationModel->createReservation(
                $guestID,
                1, // pending status
                $checkin,
                $checkout,
                $adults,
                $children
            );

            if($reservationID){

                $reservationModel->assignRoom($reservationID,$roomID);

                echo "Booking completed!";

            }

        } else {

            echo "Invalid request method.";

        }

    }

}
?>