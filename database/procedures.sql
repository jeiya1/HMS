USE HMS;

-- =========================
-- USERS
-- =========================

DELIMITER $$

CREATE PROCEDURE CreateGuestUser(
    IN pEmail VARCHAR(150),
    IN pPasswordHash VARCHAR(255),
    IN pFirstName VARCHAR(100),
    IN pLastName VARCHAR(100),
    IN pPhone VARCHAR(20)
)
BEGIN

    DECLARE newUserID INT;

    START TRANSACTION;

    INSERT INTO Users
    (RoleID,Email,PasswordHash)
    VALUES
    (2,pEmail,pPasswordHash);

    SET newUserID = LAST_INSERT_ID();

    INSERT INTO Guests
    (UserID,FirstName,LastName,PhoneContact)
    VALUES
    (newUserID,pFirstName,pLastName,pPhone);

    COMMIT;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CreateUser(
    IN pEmail VARCHAR(150),
    IN pPasswordHash VARCHAR(255)
)
BEGIN

    DECLARE newUserID INT;

    START TRANSACTION;

    INSERT INTO Users
    (RoleID,Email,PasswordHash)
    VALUES
    (2,pEmail,pPasswordHash);

    COMMIT;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CreateGuest(
    IN pFirstName VARCHAR(100),
    IN pLastName VARCHAR(100),
    IN pPhone VARCHAR(20)
)
BEGIN

    START TRANSACTION;

    INSERT INTO Guests
    (FirstName,LastName,PhoneContact)
    VALUES
    (pFirstName,pLastName,pPhone);

    COMMIT;

END$$

DELIMITER ;

-- =========================
-- ROOMS
-- =========================

DELIMITER $$

CREATE PROCEDURE GetAvailableRooms(
    IN pCheckIn DATE,
    IN pCheckOut DATE
)
BEGIN

    IF pCheckIn >= pCheckOut THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Check-out must be after check-in';
    END IF;

    SELECT r.RoomID, r.RoomNumber, rt.RoomTypeName, rt.BasePrice
    FROM Rooms r
    JOIN RoomTypes rt ON r.RoomTypeID = rt.RoomTypeID
    WHERE r.Status = 'available'
    AND r.RoomID NOT IN (

        SELECT rr.RoomID
        FROM Reservations res
        JOIN ReservationRooms rr 
        ON res.ReservationID = rr.ReservationID

        WHERE res.StatusID NOT IN (4,5)
        AND res.CheckInDate < pCheckOut
        AND res.CheckOutDate > pCheckIn

    );

END$$

DELIMITER ;

-- =========================
-- RESERVATION
-- =========================

DELIMITER $$

CREATE PROCEDURE CreateReservation(
    IN pGuestID INT,
    IN pCheckIn DATE,
    IN pCheckOut DATE,
    IN pNumAdults INT,
    IN pNumChildren INT,
    IN pRoomID INT,
    IN pPaymentMethodID INT,
    IN pAmount DECIMAL(10,2)
)
BEGIN

    DECLARE reservationID INT;
    DECLARE conflictCount INT;

    START TRANSACTION;

    SELECT RoomID
    FROM Rooms
    WHERE RoomID = pRoomID
    FOR UPDATE;

    SELECT COUNT(*)
    INTO conflictCount
    FROM Reservations r
    JOIN ReservationRooms rr ON r.ReservationID = rr.ReservationID
    WHERE rr.RoomID = pRoomID
    AND r.StatusID NOT IN (4,5)
    AND r.CheckInDate < pCheckOut
    AND r.CheckOutDate > pCheckIn;

    IF conflictCount > 0 THEN

        ROLLBACK;

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Room already booked for these dates';

    ELSE

        INSERT INTO Reservations
        (GuestID, StatusID, CheckInDate, CheckOutDate, NumAdults, NumChildren)
        VALUES
        (pGuestID, 1, pCheckIn, pCheckOut, pNumAdults, pNumChildren);

        SET reservationID = LAST_INSERT_ID();

        INSERT INTO ReservationRooms (ReservationID, RoomID)
        VALUES (reservationID, pRoomID);

        INSERT INTO Payments (ReservationID, MethodID, Amount, PaymentStatus)
        VALUES (reservationID, pPaymentMethodID, pAmount, 'pending');

        COMMIT;

    END IF;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CancelReservation(
    IN pReservationID INT
)
BEGIN
    DECLARE roomID INT;

    START TRANSACTION;

    SELECT RoomID 
    INTO roomID
    FROM ReservationRooms
    WHERE ReservationID = pReservationID
    FOR UPDATE;

    UPDATE Rooms
    SET Status = 'available'
    WHERE RoomID = roomID;

    UPDATE Reservations
    SET StatusID = 5
    WHERE ReservationID = pReservationID;

    UPDATE Payments
    SET PaymentStatus = 'refunded'
    WHERE ReservationID = pReservationID;

    COMMIT;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CheckInGuest(
    IN pReservationID INT
)
BEGIN

    DECLARE roomID INT;

    START TRANSACTION;

    UPDATE Reservations
    SET StatusID = 3
    WHERE ReservationID = pReservationID;

    SELECT RoomID
    INTO roomID
    FROM ReservationRooms
    WHERE ReservationID = pReservationID
    LIMIT 1;

    UPDATE Rooms
    SET Status = 'occupied'
    WHERE RoomID = roomID;

    COMMIT;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CheckOutGuest(
    IN pReservationID INT
)
BEGIN

    DECLARE roomID INT;

    START TRANSACTION;

    UPDATE Reservations
    SET StatusID = 4
    WHERE ReservationID = pReservationID;

    SELECT RoomID
    INTO roomID
    FROM ReservationRooms
    WHERE ReservationID = pReservationID
    LIMIT 1;

    UPDATE Rooms
    SET Status = 'available'
    WHERE RoomID = roomID;

    COMMIT;

END$$

DELIMITER ;

-- COMPLETE PAYMENT

DELIMITER $$

CREATE PROCEDURE CompletePayment(
    IN pReservationID INT,
    IN pTransactionRef VARCHAR(255)
)
BEGIN

    START TRANSACTION;

    UPDATE Payments
    SET PaymentStatus = 'completed',
        TransactionReference = pTransactionRef,
        PaymentDate = CURRENT_TIMESTAMP
    WHERE ReservationID = pReservationID;

    UPDATE Reservations
    SET StatusID = 2
    WHERE ReservationID = pReservationID;

    COMMIT;

END$$

DELIMITER ;

-- FAILED PAYMENT

DELIMITER $$

CREATE PROCEDURE FailPayment(
    IN pReservationID INT
)
BEGIN

    START TRANSACTION;

    UPDATE Payments
    SET PaymentStatus = 'failed'
    WHERE ReservationID = pReservationID;

    UPDATE Reservations
    SET StatusID = 5
    WHERE ReservationID = pReservationID;

    COMMIT;

END$$

DELIMITER ;