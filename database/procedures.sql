USE HMS;

-- =========================
-- USERS
-- =========================

DELIMITER $$

CREATE PROCEDURE CreateGuestUser(
    IN pEmail VARCHAR(150),
    IN pEmailGuest VARCHAR(150),
    IN pPasswordHash VARCHAR(255),
    IN pFirstName VARCHAR(100),
    IN pLastName VARCHAR(100),
    IN pPhone VARCHAR(20)
)
BEGIN

    DECLARE newUserID INT;

    START TRANSACTION;

    CALL CreateUser (pEmail, pPasswordHash, 2);

    SET newUserID = LAST_INSERT_ID();

    CALL CreateGuest (newUserID, pEmailGuest, pFirstName, pLastName, pPhone);

    COMMIT;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CreateUser(
    IN pRoleID INT,
    IN pEmail VARCHAR(150),
    IN pPasswordHash VARCHAR(255)
)
BEGIN

    START TRANSACTION;

    INSERT INTO Users
    (RoleID,Email,PasswordHash)
    VALUES
    (pRoleID,pEmail,pPasswordHash);

    COMMIT;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CreateGuest(
    IN pUserID INT,
    IN pEmail VARCHAR(150),
    IN pFirstName VARCHAR(100),
    IN pLastName VARCHAR(100),
    IN pPhone VARCHAR(20)
)
BEGIN

    START TRANSACTION;

    INSERT INTO Guests
    (UserID, Email, FirstName,LastName,PhoneContact)
    VALUES
    (pUserID, pEmail, pFirstName,pLastName,pPhone);

    COMMIT;

END$$

DELIMITER ;

-- =========================
-- ROOMS
-- =========================

DELIMITER $$

CREATE PROCEDURE GetRoomPrice(
    IN pRoomID INT,
    OUT pBasePrice DECIMAL(10,2)
)
BEGIN
    SELECT rt.BasePrice
    INTO pBasePrice
    FROM Rooms r
    JOIN RoomTypes rt ON r.RoomTypeID = rt.RoomTypeID
    WHERE r.RoomID = pRoomID;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE GetRoomName(
    IN pRoomID INT,
    OUT pRoomTypeName VARCHAR(100)
)
BEGIN
    SELECT rt.RoomTypeName
    INTO pRoomTypeName
    FROM Rooms r
    JOIN RoomTypes rt ON r.RoomTypeID = rt.RoomTypeID
    WHERE r.RoomID = pRoomID;
END$$

DELIMITER ;

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

DELIMITER $$

CREATE PROCEDURE CheckRoomAvailability(
    IN pRoomID INT,
    IN pCheckIn DATE,
    IN pCheckOut DATE,
    OUT isAvailable BOOLEAN
)
BEGIN
    DECLARE overlapCount INT;

    SELECT COUNT(*) INTO overlapCount
    FROM ReservationRooms rr
    JOIN Reservations r ON rr.ReservationID = r.ReservationID
    WHERE rr.RoomID = pRoomID
      AND r.StatusID IN (1,2,3)
      AND NOT (r.CheckOutDate <= pCheckIn OR r.CheckInDate >= pCheckOut);

    IF overlapCount = 0 THEN
        SET isAvailable = TRUE;
    ELSE
        SET isAvailable = FALSE;
    END IF;
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
    DECLARE isAvailable BOOLEAN;

    IF pCheckIn < CURDATE() OR pCheckOut < pCheckIn THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Check-in date must be before or on check-out date';
    END IF;

    START TRANSACTION;

    SELECT RoomID
    FROM Rooms
    WHERE RoomID = pRoomID
    FOR UPDATE;

    CALL CheckRoomAvailability(pRoomID, pCheckIn, pCheckOut, isAvailable);

    IF isAvailable = 0 THEN
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
        SELECT reservationID AS ReservationID;

    END IF;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE AddRoomToReservation(
    IN pReservationID INT,
    IN pRoomID INT
)
BEGIN
    DECLARE isAvailable BOOLEAN;
    DECLARE checkIn DATE;
    DECLARE checkOut DATE;

    SELECT CheckInDate, CheckOutDate
    INTO checkIn, checkOut
    FROM Reservations
    WHERE ReservationID = pReservationID
    FOR UPDATE;

    IF checkIn IS NULL OR checkOut IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reservation not found';
    END IF;

    CALL CheckRoomAvailability(pRoomID, checkIn, checkOut, isAvailable);

    IF isAvailable = FALSE THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Room not available for these dates';
    ELSE
        INSERT INTO ReservationRooms (ReservationID, RoomID)
        VALUES (pReservationID, pRoomID);
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CancelReservation(
    IN pReservationID INT
)
BEGIN
    DECLARE roomID INT;
    DECLARE done INT DEFAULT 0;

    DECLARE roomCursor CURSOR FOR
        SELECT RoomID
        FROM ReservationRooms 
        WHERE ReservationID = pReservationID;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    START TRANSACTION;

    OPEN roomCursor;
    read_loop: LOOP
        FETCH roomCursor INTO roomID;
        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE Rooms
        SET Status = 'available'
        WHERE RoomID = roomID;
    END LOOP;
    CLOSE roomCursor;

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
    DECLARE done INT DEFAULT 0;
    DECLARE currentStatus INT;

    DECLARE roomCursor CURSOR FOR
        SELECT RoomID 
        FROM ReservationRooms 
        WHERE ReservationID = pReservationID;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    SELECT StatusID INTO currentStatus
    FROM Reservations
    WHERE ReservationID = pReservationID;

    IF currentStatus != 2 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = "Only confirmed reservations can be checked in";
    ELSE

    START TRANSACTION;

    UPDATE Reservations
    SET StatusID = 3
    WHERE ReservationID = pReservationID;

    OPEN roomCursor;
    read_loop: LOOP
        FETCH roomCursor INTO roomID;
        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE Rooms
        SET Status = 'occupied'
        WHERE RoomID = roomID;
    END LOOP;
    CLOSE roomCursor;

    COMMIT;
    END IF;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CheckOutGuest(
    IN pReservationID INT
)
BEGIN

    DECLARE roomID INT;
    DECLARE done INT DEFAULT 0;
    DECLARE currentStatus INT;

    DECLARE roomCursor CURSOR FOR
        SELECT RoomID FROM ReservationRooms WHERE ReservationID = pReservationID;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    SELECT StatusID INTO currentStatus
    FROM Reservations
    WHERE ReservationID = pReservationID;

    IF currentStatus != 3 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = "Only checked-in reservations can be checked out";
    ELSE

    START TRANSACTION;

    UPDATE Reservations
    SET StatusID = 4
    WHERE ReservationID = pReservationID;

    OPEN roomCursor;
    read_loop: LOOP
        FETCH roomCursor INTO roomID;
        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE Rooms
        SET Status = 'available'
        WHERE RoomID = roomID;
    END LOOP;
    CLOSE roomCursor;

    COMMIT;
    END IF;

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