USE HMS;

-- =========================
-- USERS
-- =========================

DELIMITER $$

CREATE PROCEDURE CreateGuestUser(
    IN pEmailUser VARCHAR(150),
    IN pEmailGuest VARCHAR(150),
    IN pPasswordHash VARCHAR(255),
    IN pFirstName VARCHAR(100),
    IN pLastName VARCHAR(100),
    IN pPhone VARCHAR(20)
)
BEGIN

    DECLARE newGuestID INT;

    START TRANSACTION;

    -- Step 1: Create Guest FIRST
    INSERT INTO Guests
    (Email, FirstName, LastName, PhoneContact)
    VALUES
    (pEmailGuest, pFirstName, pLastName, pPhone);

    SET newGuestID = LAST_INSERT_ID();

    -- Step 2: Create User linked to Guest
    INSERT INTO Users
    (GuestID, RoleID, Email, PasswordHash)
    VALUES
    (newGuestID, 2, pEmailUser, pPasswordHash);

    COMMIT;

END$$

DELIMITER ;

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CreateUser(
    IN pGuestID INT,
    IN pRoleID INT,
    IN pEmail VARCHAR(150),
    IN pPasswordHash VARCHAR(255)
)
BEGIN

    INSERT INTO Users
    (GuestID, RoleID, Email, PasswordHash)
    VALUES
    (pGuestID, pRoleID, pEmail, pPasswordHash);

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CreateGuest(
    IN pEmail VARCHAR(150),
    IN pFirstName VARCHAR(100),
    IN pLastName VARCHAR(100),
    IN pPhone VARCHAR(20),
    OUT newGuestID INT
)
BEGIN
    INSERT INTO Guests (Email, FirstName, LastName, PhoneContact)
    VALUES (pEmail, pFirstName, pLastName, pPhone);

    SET newGuestID = LAST_INSERT_ID();
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
-- CARTS
-- =========================
DELIMITER $$

CREATE PROCEDURE AddRoomToCart(
    IN pCartID INT,
    IN pRoomID INT,
    IN pCheckIn DATE,
    IN pCheckOut DATE
)
BEGIN
    DECLARE isAvailable BOOLEAN;

    -- Validate dates
    IF pCheckIn >= pCheckOut THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Check-out must be after check-in';
    END IF;

    -- Check if room is already booked
    CALL CheckRoomAvailability(pRoomID, pCheckIn, pCheckOut, isAvailable);

    IF isAvailable = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Room not available for these dates';
    END IF;

    -- Add room to cart
    INSERT INTO CartRooms (CartID, RoomID, CheckInDate, CheckOutDate)
    VALUES (pCartID, pRoomID, pCheckIn, pCheckOut);
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE CheckoutCart(
    IN pCartID INT,
    IN pPaymentMethodID INT,
    IN pNumAdults INT
)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE vroomID INT;
    DECLARE checkIn DATE;
    DECLARE checkOut DATE;
    DECLARE vguestID INT;
    DECLARE reservationID INT;
    DECLARE isAvailable BOOLEAN;
    DECLARE errMsg VARCHAR(255);
    DECLARE token CHAR(36);

    -- Cursor for rooms in cart
    DECLARE cartCursor CURSOR FOR
        SELECT RoomID, CheckInDate, CheckOutDate
        FROM CartRooms
        WHERE CartID = pCartID;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Start transaction
    START TRANSACTION;

    -- Get GuestID into local variable (not @guestID)
    SELECT GuestID INTO vguestID
    FROM ReservationCarts
    WHERE CartID = pCartID
    FOR UPDATE;

    IF vguestID IS NULL THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid CartID: GuestID not found';
    END IF;

    OPEN cartCursor;
    read_loop: LOOP
        FETCH cartCursor INTO vroomID, checkIn, checkOut;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Check availability
        CALL CheckRoomAvailability(vroomID, checkIn, checkOut, isAvailable);
        IF isAvailable = 0 THEN
            SET errMsg = CONCAT('Room ', vroomID, ' no longer available');
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = errMsg;
        END IF;
    END LOOP;
    CLOSE cartCursor;

    -- before the insert
    SET token = UUID();

    -- insert with token
    INSERT INTO Reservations (GuestID, StatusID, CheckInDate, CheckOutDate, NumAdults, BookingToken)
    SELECT vguestID, 1, MIN(CheckInDate), MAX(CheckOutDate), pNumAdults, token
    FROM CartRooms
    WHERE CartID = pCartID;

    SET reservationID = LAST_INSERT_ID();

    -- Add rooms to ReservationRooms
    INSERT INTO ReservationRooms (ReservationID, RoomID)
    SELECT reservationID, RoomID
    FROM CartRooms
    WHERE CartID = pCartID;

    -- Create Payment
    INSERT INTO Payments (ReservationID, MethodID, Amount, PaymentStatus)
    SELECT reservationID, pPaymentMethodID, SUM(rt.BasePrice), 'pending'
    FROM CartRooms cr
    JOIN Rooms r ON cr.RoomID = r.RoomID
    JOIN RoomTypes rt ON r.RoomTypeID = rt.RoomTypeID
    WHERE cr.CartID = pCartID;

    -- Remove cart
    DELETE FROM CartRooms WHERE CartID = pCartID;
    DELETE FROM ReservationCarts WHERE CartID = pCartID;

    COMMIT;

    SELECT reservationID AS ReservationID;
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
    IN pRoomID INT,
    IN pPaymentMethodID INT,
    IN pAmount DECIMAL(10,2),
    OUT pReservationID INT,
    OUT pBookingToken CHAR(36)
)
BEGIN
    DECLARE isAvailable BOOLEAN;
    DECLARE token CHAR(36);

    IF pCheckIn < CURDATE() OR pCheckOut < pCheckIn THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Check-in date must be before or on check-out date';
    END IF;

    START TRANSACTION;

    -- Lock room
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
        -- Generate token
        SET token = UUID();

        INSERT INTO Reservations
        (GuestID, StatusID, CheckInDate, CheckOutDate, NumAdults, BookingToken)
        VALUES
        (pGuestID, 1, pCheckIn, pCheckOut, pNumAdults, token);

        SET pReservationID = LAST_INSERT_ID();
        SET pBookingToken = token;

        INSERT INTO ReservationRooms (ReservationID, RoomID)
        VALUES (pReservationID, pRoomID);

        INSERT INTO Payments (ReservationID, MethodID, Amount, PaymentStatus)
        VALUES (pReservationID, pPaymentMethodID, pAmount, 'pending');

        COMMIT;

        SELECT pReservationID AS ReservationID, pBookingToken AS BookingToken;
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

    INSERT INTO ReservationRooms (ReservationID, RoomID)
    VALUES (pReservationID, pRoomID);
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

CREATE PROCEDURE CancelReservationGuest(
    IN pBookingToken CHAR(36)
)
BEGIN
    DECLARE reservationID INT;
    DECLARE roomID INT;
    DECLARE done INT DEFAULT 0;

    -- Cursor to free rooms
    DECLARE roomCursor CURSOR FOR
        SELECT RoomID FROM ReservationRooms WHERE ReservationID = reservationID;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Find reservation by token
    SELECT ReservationID INTO reservationID
    FROM Reservations
    WHERE BookingToken = pBookingToken
      AND TokenExpiresAt IS NULL OR TokenExpiresAt > NOW()
    FOR UPDATE;

    IF reservationID IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid or expired booking token';
    END IF;


    START TRANSACTION;

    OPEN roomCursor;
    read_loop: LOOP
        FETCH roomCursor INTO roomID;
        IF done THEN LEAVE read_loop; END IF;

        UPDATE Rooms
        SET Status = 'available'
        WHERE RoomID = roomID;
    END LOOP;
    CLOSE roomCursor;

    UPDATE Reservations
    SET StatusID = 5
    WHERE ReservationID = reservationID;

    UPDATE Payments
    SET PaymentStatus = 'refunded'
    WHERE ReservationID = reservationID;

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