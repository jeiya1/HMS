USE HMS;

DELIMITER $$

CREATE PROCEDURE RefundPaymentByToken(
    IN pBookingToken VARCHAR(255),
    IN pTransactionRef VARCHAR(255)
)
BEGIN
    DECLARE vReservationID INT;
    DECLARE existingRef VARCHAR(255);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Refund transaction failed';
    END;

    START TRANSACTION;

    -- Get ReservationID
    SELECT ReservationID
    INTO vReservationID
    FROM Reservations
    WHERE BookingToken = pBookingToken
    LIMIT 1;

    -- Validate reservation
    IF vReservationID IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Reservation not found';
    END IF;

    -- Validate payment exists
    IF NOT EXISTS (
        SELECT 1 FROM Payments WHERE ReservationID = vReservationID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Payment record not found';
    END IF;

    -- Get existing transaction reference
    SELECT TransactionReference
    INTO existingRef
    FROM Payments
    WHERE ReservationID = vReservationID
    LIMIT 1;

    -- Update payment to refunded
    UPDATE Payments
    SET PaymentStatus = 'refunded',
        TransactionReference = COALESCE(pTransactionRef, existingRef, UUID()),
        PaymentDate = CURRENT_TIMESTAMP
    WHERE ReservationID = vReservationID;

    COMMIT;
END$$

DELIMITER ;