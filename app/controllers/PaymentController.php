<?php

require_once '../app/models/Payment.php';


class PaymentController {
    private $paymentModel;

    public function payReservation() {

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                "success" => false,
                "error" => "Invalid request method."
            ]);
            return;
        }

        $reservationID = $_POST['reservationID'] ?? null;
        $methodID = $_POST['methodID'] ?? null;
        $amount = $_POST['amount'] ?? null;

        // ---------- VALIDATION ----------
        if (empty($reservationID) || empty($methodID)) {
            echo json_encode([
                "success" => false,
                "error" => "Missing payment information."
            ]);
            return;
        }

        if ($amount <= 0) {
            echo json_encode([
                "success" => false,
                "error" => "Invalid payment amount."
            ]);
            return;
        }

        try {
            $paymentModel = new Payment($GLOBALS['conn']);

            $result = $paymentModel->createPayment(
                $reservationID,
                $methodID,
                $amount
            );

            if ($result) {
                echo json_encode([
                    "success" => true,
                    "message" => "Payment successful!"
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "error" => "Payment failed. Try again."
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => "Server error: " . $e->getMessage()
            ]);
        }
    }
    //admin functions
    public function refund()
    {

    $id = $_POST['id'];

    $this->paymentModel->refundPayment($id);

    header("Location: /payments");

    }
}
?>