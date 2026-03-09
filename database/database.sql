CREATE DATABASE IF NOT EXISTS HMS;
USE HMS;

-- =========================
-- ROLES
-- =========================
CREATE TABLE IF NOT EXISTS Roles (
    RoleID INT AUTO_INCREMENT PRIMARY KEY,
    RoleName VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO Roles (RoleName) VALUES
('admin'),
('guest');

-- =========================
-- USERS
-- =========================
CREATE TABLE IF NOT EXISTS Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    RoleID INT NOT NULL,
    Email VARCHAR(150) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (RoleID) REFERENCES Roles(RoleID)
);

CREATE TABLE Guests (
    GuestID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NULL,
    FirstName VARCHAR(100) NOT NULL,
    LastName VARCHAR(100) NOT NULL,
    PhoneContact VARCHAR(20),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

-- =========================
-- BED TYPES
-- =========================
CREATE TABLE IF NOT EXISTS BedTypes (
    BedTypeID INT AUTO_INCREMENT PRIMARY KEY,
    BedName VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO BedTypes (BedName) VALUES
('Single'),
('Double'),
('Queen'),
('King');

-- =========================
-- ROOM TYPES
-- =========================
CREATE TABLE IF NOT EXISTS RoomTypes (
    RoomTypeID INT AUTO_INCREMENT PRIMARY KEY,
    RoomTypeName VARCHAR(100) NOT NULL,
    BasePrice DECIMAL(10,2) NOT NULL,
    BedTypeID INT,
    BedCount INT DEFAULT 1,
    MaxOccupancy INT NOT NULL,

    FOREIGN KEY (BedTypeID) REFERENCES BedTypes(BedTypeID)
);

-- =========================
-- FLOORS
-- =========================
CREATE TABLE IF NOT EXISTS Floors (
    FloorID INT AUTO_INCREMENT PRIMARY KEY,
    FloorNumber INT NOT NULL UNIQUE
);

-- =========================
-- ROOMS
-- =========================
CREATE TABLE IF NOT EXISTS Rooms (
    RoomID INT AUTO_INCREMENT PRIMARY KEY,
    RoomNumber VARCHAR(10) NOT NULL UNIQUE,
    FloorID INT NOT NULL,
    RoomTypeID INT NOT NULL,
    Status ENUM('available','occupied','maintenance') DEFAULT 'available',

    FOREIGN KEY (FloorID) REFERENCES Floors(FloorID),
    FOREIGN KEY (RoomTypeID) REFERENCES RoomTypes(RoomTypeID)
);

-- =========================
-- RESERVATION STATUS
-- =========================
CREATE TABLE IF NOT EXISTS ReservationStatus (
    StatusID INT AUTO_INCREMENT PRIMARY KEY,
    StatusName VARCHAR(50) UNIQUE
);

INSERT INTO ReservationStatus (StatusName) VALUES
('pending'),
('confirmed'),
('checked_in'),
('checked_out'),
('cancelled');

-- =========================
-- RESERVATIONS
-- =========================
CREATE TABLE IF NOT EXISTS Reservations (
    ReservationID INT AUTO_INCREMENT PRIMARY KEY,
    GuestID INT NOT NULL,
    StatusID INT NOT NULL,
    CheckInDate DATE NOT NULL,
    CheckOutDate DATE NOT NULL,
    NumAdults INT DEFAULT 1,
    NumChildren INT DEFAULT 0,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (GuestID) REFERENCES Guests(GuestID),
    FOREIGN KEY (StatusID) REFERENCES ReservationStatus(StatusID)
);

-- =========================
-- RESERVED ROOMS
-- (supports multi-room bookings)
-- =========================
CREATE TABLE IF NOT EXISTS ReservationRooms (
    ReservationRoomID INT AUTO_INCREMENT PRIMARY KEY,
    ReservationID INT NOT NULL,
    RoomID INT NOT NULL,

    FOREIGN KEY (ReservationID) REFERENCES Reservations(ReservationID),
    FOREIGN KEY (RoomID) REFERENCES Rooms(RoomID)
);

-- =========================
-- PAYMENT METHODS
-- =========================
CREATE TABLE IF NOT EXISTS PaymentMethods (
    MethodID INT AUTO_INCREMENT PRIMARY KEY,
    MethodName VARCHAR(50) UNIQUE
);

INSERT INTO PaymentMethods (MethodName) VALUES
('cash'),
('credit_card'),
('debit_card'),
('online_payment');

-- =========================
-- PAYMENTS
-- =========================
CREATE TABLE IF NOT EXISTS Payments (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    ReservationID INT NOT NULL,
    MethodID INT NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    PaymentStatus ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
    PaymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    TransactionReference VARCHAR(255),

    FOREIGN KEY (ReservationID) REFERENCES Reservations(ReservationID),
    FOREIGN KEY (MethodID) REFERENCES PaymentMethods(MethodID)
);

-- =========================
-- INDEXES
-- =========================
CREATE INDEX idx_reservation_dates
ON Reservations(CheckInDate, CheckOutDate);

CREATE INDEX idx_room_type
ON Rooms(RoomTypeID);

-- =========================
-- Admin User
-- =========================
INSERT INTO Users (RoleID, Email, PasswordHash) VALUES
(1, 'admin@hotel.com', 'password');

-- =========================
-- Hotel Layout
-- =========================
INSERT INTO Floors (FloorNumber) VALUES
(1),
(2),
(3);

INSERT INTO RoomTypes (RoomTypeName, BasePrice, BedTypeID, BedCount, MaxOccupancy) VALUES
('Standard Single', 1800.00, 2, 1, 2),
('Deluxe Single', 2300.00, 3, 1, 4),
('Suite Single', 3000.00, 4, 1, 6),
('Standard Double', 2700.00, 2, 2, 2),
('Deluxe Double', 3200.00, 3, 2, 4),
('Suite Double', 4000.00, 4, 2, 6);

INSERT INTO Rooms (RoomNumber, FloorID, RoomTypeID) VALUES
('101',1,1),('102',1,1),('103',1,2),('104',1,2),('105',1,3),
('106',1,1),('107',1,2),('108',1,3),('109',1,1),('110',1,2),

('201',2,1),('202',2,1),('203',2,2),('204',2,2),('205',2,3),
('206',2,1),('207',2,2),('208',2,3),('209',2,1),('210',2,2),

('301',3,1),('302',3,1),('303',3,2),('304',3,2),('305',3,3),
('306',3,1),('307',3,2),('308',3,3),('309',3,1),('310',3,2);

-- =========================
-- Test Reservation
-- =========================
INSERT INTO Users (RoleID, Email, PasswordHash) VALUES
(2, 'aniagjoseph593@gmail.com', 'password');

INSERT INTO Guests (UserID, FirstName, LastName, PhoneContact) VALUES
(2, 'Aniag', 'Joseph', '092584577102');

INSERT INTO Reservations (GuestID, StatusID, CheckInDate, CheckOutDate, NumAdults, NumChildren) VALUES
(1, 2, '2026-04-08', '2026-04-10', 1, 0);

INSERT INTO ReservationRooms (ReservationID, RoomID) VALUES
(1, 1);

INSERT INTO Payments (ReservationID, MethodID, Amount, PaymentStatus) VALUES
(1, 4, 5000.00, 'completed');