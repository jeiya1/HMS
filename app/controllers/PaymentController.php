<?php

require_once '../app/models/Payment.php';

class PaymentController {

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
        // Validate missing payment info
        if (empty($reservationID) || empty($methodID)) {
            echo json_encode([
                "success" => false,
                "error" => "Missing payment information."
            ]);
            return;
        }

        // Validate amount is a positive number
        if ($amount <= 0) {
            echo json_encode([
                "success" => false,
                "error" => "Invalid payment amount."
            ]);
            return;
        }

        // ---------- PROCESS PAYMENT ----------
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
}
?>