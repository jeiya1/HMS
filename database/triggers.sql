DELIMITER $$

CREATE TRIGGER trg_Guests_Insert
AFTER INSERT ON Guests
FOR EACH ROW
BEGIN
    INSERT INTO Logs (TableName, OperationType, RecordID, NewData)
    VALUES ('Guests', 'INSERT', NEW.GuestID, JSON_OBJECT(
        'Email', NEW.Email,
        'FirstName', NEW.FirstName,
        'LastName', NEW.LastName,
        'PhoneContact', NEW.PhoneContact,
        'BirthDate', NEW.BirthDate
    ));
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_Guests_Update
AFTER UPDATE ON Guests
FOR EACH ROW
BEGIN
    INSERT INTO Logs (TableName, OperationType, RecordID, OldData, NewData)
    VALUES ('Guests', 'UPDATE', OLD.GuestID,
        JSON_OBJECT(
            'Email', OLD.Email,
            'FirstName', OLD.FirstName,
            'LastName', OLD.LastName,
            'PhoneContact', OLD.PhoneContact,
            'BirthDate', OLD.BirthDate
        ),
        JSON_OBJECT(
            'Email', NEW.Email,
            'FirstName', NEW.FirstName,
            'LastName', NEW.LastName,
            'PhoneContact', NEW.PhoneContact,
            'BirthDate', NEW.BirthDate
        )
    );
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_Guests_Delete
AFTER DELETE ON Guests
FOR EACH ROW
BEGIN
    INSERT INTO Logs (TableName, OperationType, RecordID, OldData)
    VALUES ('Guests', 'DELETE', OLD.GuestID, JSON_OBJECT(
        'Email', OLD.Email,
        'FirstName', OLD.FirstName,
        'LastName', OLD.LastName,
        'PhoneContact', OLD.PhoneContact,
        'BirthDate', OLD.BirthDate
    ));
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_Users_Insert
AFTER INSERT ON Users
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, NewData)
    VALUES('Users','INSERT', NEW.UserID,
        JSON_OBJECT('GuestID', NEW.GuestID, 'RoleID', NEW.RoleID, 'Email', NEW.Email)
    );
END$$

CREATE TRIGGER trg_Users_Update
AFTER UPDATE ON Users
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData, NewData)
    VALUES('Users','UPDATE', OLD.UserID,
        JSON_OBJECT('GuestID', OLD.GuestID, 'RoleID', OLD.RoleID, 'Email', OLD.Email),
        JSON_OBJECT('GuestID', NEW.GuestID, 'RoleID', NEW.RoleID, 'Email', NEW.Email)
    );
END$$

CREATE TRIGGER trg_Users_Delete
AFTER DELETE ON Users
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData)
    VALUES('Users','DELETE', OLD.UserID,
        JSON_OBJECT('GuestID', OLD.GuestID, 'RoleID', OLD.RoleID, 'Email', OLD.Email)
    );
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_Roles_Insert
AFTER INSERT ON Roles
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, NewData)
    VALUES('Roles','INSERT', NEW.RoleID,
        JSON_OBJECT('RoleName', NEW.RoleName)
    );
END$$

CREATE TRIGGER trg_Roles_Update
AFTER UPDATE ON Roles
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData, NewData)
    VALUES('Roles','UPDATE', OLD.RoleID,
        JSON_OBJECT('RoleName', OLD.RoleName),
        JSON_OBJECT('RoleName', NEW.RoleName)
    );
END$$

CREATE TRIGGER trg_Roles_Delete
AFTER DELETE ON Roles
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData)
    VALUES('Roles','DELETE', OLD.RoleID,
        JSON_OBJECT('RoleName', OLD.RoleName)
    );
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_Floors_Insert
AFTER INSERT ON Floors
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, NewData)
    VALUES('Floors','INSERT', NEW.FloorID,
        JSON_OBJECT('FloorNumber', NEW.FloorNumber)
    );
END$$

CREATE TRIGGER trg_Floors_Update
AFTER UPDATE ON Floors
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData, NewData)
    VALUES('Floors','UPDATE', OLD.FloorID,
        JSON_OBJECT('FloorNumber', OLD.FloorNumber),
        JSON_OBJECT('FloorNumber', NEW.FloorNumber)
    );
END$$

CREATE TRIGGER trg_Floors_Delete
AFTER DELETE ON Floors
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData)
    VALUES('Floors','DELETE', OLD.FloorID,
        JSON_OBJECT('FloorNumber', OLD.FloorNumber)
    );
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_RoomTypes_Insert
AFTER INSERT ON RoomTypes
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, NewData)
    VALUES('RoomTypes','INSERT', NEW.RoomTypeID,
        JSON_OBJECT('RoomTypeName', NEW.RoomTypeName,'BasePrice', NEW.BasePrice,'BedTypeID', NEW.BedTypeID,'BedCount', NEW.BedCount,'MaxOccupancy', NEW.MaxOccupancy)
    );
END$$

CREATE TRIGGER trg_RoomTypes_Update
AFTER UPDATE ON RoomTypes
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData, NewData)
    VALUES('RoomTypes','UPDATE', OLD.RoomTypeID,
        JSON_OBJECT('RoomTypeName', OLD.RoomTypeName,'BasePrice', OLD.BasePrice,'BedTypeID', OLD.BedTypeID,'BedCount', OLD.BedCount,'MaxOccupancy', OLD.MaxOccupancy),
        JSON_OBJECT('RoomTypeName', NEW.RoomTypeName,'BasePrice', NEW.BasePrice,'BedTypeID', NEW.BedTypeID,'BedCount', NEW.BedCount,'MaxOccupancy', NEW.MaxOccupancy)
    );
END$$

CREATE TRIGGER trg_RoomTypes_Delete
AFTER DELETE ON RoomTypes
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData)
    VALUES('RoomTypes','DELETE', OLD.RoomTypeID,
        JSON_OBJECT('RoomTypeName', OLD.RoomTypeName,'BasePrice', OLD.BasePrice,'BedTypeID', OLD.BedTypeID,'BedCount', OLD.BedCount,'MaxOccupancy', OLD.MaxOccupancy)
    );
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_Rooms_Insert
AFTER INSERT ON Rooms
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, NewData)
    VALUES('Rooms','INSERT', NEW.RoomID,
        JSON_OBJECT('RoomNumber', NEW.RoomNumber,'FloorID', NEW.FloorID,'RoomTypeID', NEW.RoomTypeID,'Status', NEW.Status)
    );
END$$

CREATE TRIGGER trg_Rooms_Update
AFTER UPDATE ON Rooms
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData, NewData)
    VALUES('Rooms','UPDATE', OLD.RoomID,
        JSON_OBJECT('RoomNumber', OLD.RoomNumber,'FloorID', OLD.FloorID,'RoomTypeID', OLD.RoomTypeID,'Status', OLD.Status),
        JSON_OBJECT('RoomNumber', NEW.RoomNumber,'FloorID', NEW.FloorID,'RoomTypeID', NEW.RoomTypeID,'Status', NEW.Status)
    );
END$$

CREATE TRIGGER trg_Rooms_Delete
AFTER DELETE ON Rooms
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData)
    VALUES('Rooms','DELETE', OLD.RoomID,
        JSON_OBJECT('RoomNumber', OLD.RoomNumber,'FloorID', OLD.FloorID,'RoomTypeID', OLD.RoomTypeID,'Status', OLD.Status)
    );
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_Reservations_Insert
AFTER INSERT ON Reservations
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, NewData)
    VALUES(
        'Reservations',
        'INSERT',
        NEW.ReservationID,
        JSON_OBJECT(
            'GuestID', NEW.GuestID,
            'Status', NEW.Status,
            'BookingToken', NEW.BookingToken,
            'CreatedAt', NEW.CreatedAt
        )
    );
END$$

CREATE TRIGGER trg_Reservations_Update
AFTER UPDATE ON Reservations
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData, NewData)
    VALUES(
        'Reservations',
        'UPDATE',
        OLD.ReservationID,
        JSON_OBJECT(
            'GuestID', OLD.GuestID,
            'Status', OLD.Status,
            'BookingToken', OLD.BookingToken,
            'CreatedAt', OLD.CreatedAt
        ),
        JSON_OBJECT(
            'GuestID', NEW.GuestID,
            'Status', NEW.Status,
            'BookingToken', NEW.BookingToken,
            'CreatedAt', NEW.CreatedAt
        )
    );
END$$

CREATE TRIGGER trg_Reservations_Update
AFTER UPDATE ON Reservations
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData, NewData)
    VALUES('Reservations','UPDATE', OLD.ReservationID,
        JSON_OBJECT('GuestID', OLD.GuestID,'StatusID', OLD.StatusID,'CheckInDate', OLD.CheckInDate,'CheckOutDate', OLD.CheckOutDate,'NumAdults', OLD.NumAdults,'BookingToken', OLD.BookingToken),
        JSON_OBJECT('GuestID', NEW.GuestID,'StatusID', NEW.StatusID,'CheckInDate', NEW.CheckInDate,'CheckOutDate', NEW.CheckOutDate,'NumAdults', NEW.NumAdults,'BookingToken', NEW.BookingToken)
    );
END$$

DELIMITER $$

CREATE TRIGGER trg_ReservationRooms_Insert
AFTER INSERT ON ReservationRooms
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, NewData)
    VALUES(
        'ReservationRooms',
        'INSERT',
        NEW.ReservationRoomID,
        JSON_OBJECT(
            'ReservationID', NEW.ReservationID,
            'RoomID', NEW.RoomID,
            'CheckInDate', NEW.CheckInDate,
            'CheckOutDate', NEW.CheckOutDate,
            'NumAdults', NEW.NumAdults,
            'NumChildren', NEW.NumChildren,
            'Status', NEW.Status
        )
    );
END$$

CREATE TRIGGER trg_ReservationRooms_Update
AFTER UPDATE ON ReservationRooms
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData, NewData)
    VALUES(
        'ReservationRooms',
        'UPDATE',
        OLD.ReservationRoomID,
        JSON_OBJECT(
            'ReservationID', OLD.ReservationID,
            'RoomID', OLD.RoomID,
            'CheckInDate', OLD.CheckInDate,
            'CheckOutDate', OLD.CheckOutDate,
            'NumAdults', OLD.NumAdults,
            'NumChildren', OLD.NumChildren,
            'Status', OLD.Status
        ),
        JSON_OBJECT(
            'ReservationID', NEW.ReservationID,
            'RoomID', NEW.RoomID,
            'CheckInDate', NEW.CheckInDate,
            'CheckOutDate', NEW.CheckOutDate,
            'NumAdults', NEW.NumAdults,
            'NumChildren', NEW.NumChildren,
            'Status', NEW.Status
        )
    );
END$$

CREATE TRIGGER trg_ReservationRooms_Delete
AFTER DELETE ON ReservationRooms
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData)
    VALUES(
        'ReservationRooms',
        'DELETE',
        OLD.ReservationRoomID,
        JSON_OBJECT(
            'ReservationID', OLD.ReservationID,
            'RoomID', OLD.RoomID,
            'CheckInDate', OLD.CheckInDate,
            'CheckOutDate', OLD.CheckOutDate,
            'NumAdults', OLD.NumAdults,
            'NumChildren', OLD.NumChildren,
            'Status', OLD.Status
        )
    );
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_Payments_Insert
AFTER INSERT ON Payments
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, NewData)
    VALUES('Payments','INSERT', NEW.PaymentID,
        JSON_OBJECT('ReservationID', NEW.ReservationID,'MethodID', NEW.MethodID,'Amount', NEW.Amount,'PaymentStatus', NEW.PaymentStatus,'TransactionReference', NEW.TransactionReference)
    );
END$$

CREATE TRIGGER trg_Payments_Update
AFTER UPDATE ON Payments
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData, NewData)
    VALUES('Payments','UPDATE', OLD.PaymentID,
        JSON_OBJECT('ReservationID', OLD.ReservationID,'MethodID', OLD.MethodID,'Amount', OLD.Amount,'PaymentStatus', OLD.PaymentStatus,'TransactionReference', OLD.TransactionReference),
        JSON_OBJECT('ReservationID', NEW.ReservationID,'MethodID', NEW.MethodID,'Amount', NEW.Amount,'PaymentStatus', NEW.PaymentStatus,'TransactionReference', NEW.TransactionReference)
    );
END$$

CREATE TRIGGER trg_Payments_Delete
AFTER DELETE ON Payments
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData)
    VALUES('Payments','DELETE', OLD.PaymentID,
        JSON_OBJECT('ReservationID', OLD.ReservationID,'MethodID', OLD.MethodID,'Amount', OLD.Amount,'PaymentStatus', OLD.PaymentStatus,'TransactionReference', OLD.TransactionReference)
    );
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_CartRooms_Insert
AFTER INSERT ON CartRooms
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, NewData)
    VALUES('CartRooms','INSERT', NEW.CartRoomID,
        JSON_OBJECT('CartID', NEW.CartID,'RoomID', NEW.RoomID,'CheckInDate', NEW.CheckInDate,'CheckOutDate', NEW.CheckOutDate,'NumAdults', NEW.NumAdults)
    );
END$$

CREATE TRIGGER trg_CartRooms_Update
AFTER UPDATE ON CartRooms
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData, NewData)
    VALUES('CartRooms','UPDATE', OLD.CartRoomID,
        JSON_OBJECT('CartID', OLD.CartID,'RoomID', OLD.RoomID,'CheckInDate', OLD.CheckInDate,'CheckOutDate', OLD.CheckOutDate,'NumAdults', OLD.NumAdults),
        JSON_OBJECT('CartID', NEW.CartID,'RoomID', NEW.RoomID,'CheckInDate', NEW.CheckInDate,'CheckOutDate', NEW.CheckOutDate,'NumAdults', NEW.NumAdults)
    );
END$$

CREATE TRIGGER trg_CartRooms_Delete
AFTER DELETE ON CartRooms
FOR EACH ROW
BEGIN
    INSERT INTO Logs(TableName, OperationType, RecordID, OldData)
    VALUES('CartRooms','DELETE', OLD.CartRoomID,
        JSON_OBJECT('CartID', OLD.CartID,'RoomID', OLD.RoomID,'CheckInDate', OLD.CheckInDate,'CheckOutDate', OLD.CheckOutDate,'NumAdults', OLD.NumAdults)
    );
END$$

DELIMITER ;