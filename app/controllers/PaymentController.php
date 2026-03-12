<?php

require_once '../app/models/Payment.php';

class PaymentController {

    public function payReservation() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $reservationID = $_POST['reservationID'];
            $methodID = $_POST['methodID'];
            $amount = $_POST['amount'];

            if(empty($reservationID) || empty($methodID)){//check missing input
                echo "Missing payment information.";
                return;
            }

            $paymentModel = new Payment($GLOBALS['conn']);

            $paymentModel->createPayment(
                $reservationID,
                $methodID,
                $amount
            );

        } else {

            echo "Invalid request method.";

        }

    }

}
?>