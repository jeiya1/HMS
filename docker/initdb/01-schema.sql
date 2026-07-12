CREATE DATABASE IF NOT EXISTS HMS;
USE HMS;

-- Example
CREATE USER IF NOT EXISTS 'hms_admin'@'localhost' IDENTIFIED BY 'password';
CREATE USER IF NOT EXISTS 'hms_app'@'localhost' IDENTIFIED BY 'password';

GRANT ALL PRIVILEGES ON HMS.* TO 'hms_admin'@'localhost';
GRANT SELECT, INSERT, UPDATE ON HMS.* TO 'hms_app'@'localhost';

FLUSH PRIVILEGES;

-- =========================
-- LOGS
-- =========================

CREATE TABLE IF NOT EXISTS Logs (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    TableName VARCHAR(100) NOT NULL,
    OperationType ENUM('INSERT','UPDATE','DELETE') NOT NULL,
    RecordID INT NULL,
    OldData JSON NULL,
    NewData JSON NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- ROLES
-- =========================
CREATE TABLE IF NOT EXISTS Roles (
    RoleID INT AUTO_INCREMENT PRIMARY KEY,
    RoleName VARCHAR(50) NOT NULL UNIQUE
);

-- =========================
-- USERS
-- =========================

-- TODO: Delete if Expired
CREATE TABLE IF NOT EXISTS SessionGuests (
    SessionGuestID INT AUTO_INCREMENT PRIMARY KEY,
    SessionToken VARCHAR(255) NOT NULL UNIQUE,
    ExpiresAt TIMESTAMP NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Guests (
    GuestID INT AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(150) NOT NULL,
    FirstName VARCHAR(100) NOT NULL,
    LastName VARCHAR(100) NOT NULL,
    PhoneContact VARCHAR(30),
    BirthDate DATE NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    GuestID INT NOT NULL UNIQUE,
    RoleID INT NOT NULL,
    Email VARCHAR(150) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (GuestID) REFERENCES Guests(GuestID),
    FOREIGN KEY (RoleID) REFERENCES Roles(RoleID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- TODO: Delete if expired
CREATE TABLE IF NOT EXISTS PasswordResets (
    ResetID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Token VARCHAR(255) NOT NULL,
    ExpiresAt DATETIME NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

CREATE TABLE IF NOT EXISTS DiscountTypes (
    DiscountTypeID INT AUTO_INCREMENT PRIMARY KEY,
    DiscountName VARCHAR(50) NOT NULL UNIQUE,
    DiscountPercentage DECIMAL(5,2) NOT NULL CHECK (DiscountPercentage >= 0 AND DiscountPercentage <= 100)
);

CREATE TABLE IF NOT EXISTS GuestDiscounts (
    GuestDiscountID INT AUTO_INCREMENT PRIMARY KEY,
    GuestID INT NOT NULL,
    DiscountTypeID INT NOT NULL,
    CardNumber VARCHAR(50) NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (GuestID) REFERENCES Guests(GuestID),
    FOREIGN KEY (DiscountTypeID) REFERENCES DiscountTypes(DiscountTypeID)
);
-- =========================
-- BED TYPES
-- =========================
CREATE TABLE IF NOT EXISTS BedTypes (
    BedTypeID INT AUTO_INCREMENT PRIMARY KEY,
    BedName VARCHAR(50) NOT NULL UNIQUE
);

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
-- RESERVATIONS
-- =========================

-- TODO: Add back tehe NumChildren
CREATE TABLE IF NOT EXISTS Reservations (
    ReservationID INT AUTO_INCREMENT PRIMARY KEY,
    GuestID INT NOT NULL,
    GuestUUID CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    Status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    BookingToken CHAR(36) NOT NULL UNIQUE, -- UUID token for guest access
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (GuestID) REFERENCES Guests(GuestID)
);

-- =========================
-- RESERVED ROOMS
-- (supports multi-room bookings)
-- =========================
CREATE TABLE IF NOT EXISTS ReservationRooms (
    ReservationRoomID INT AUTO_INCREMENT PRIMARY KEY,
    ReservationID INT NOT NULL,
    CheckInDate DATE NOT NULL,
    CheckOutDate DATE NOT NULL,
    NumAdults INT NOT NULL DEFAULT 1,
    NumChildren INT NOT NULL DEFAULT 0,
    RoomID INT NOT NULL,
    Status ENUM('pending','confirmed','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'pending',

    FOREIGN KEY (ReservationID) REFERENCES Reservations(ReservationID),
    FOREIGN KEY (RoomID) REFERENCES Rooms(RoomID),
    CHECK (NumAdults >= 0),
    CHECK (NumChildren >= 0)
);

CREATE TABLE IF NOT EXISTS UserReservations (
    UserReservationID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    ReservationID INT NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (ReservationID) REFERENCES Reservations(ReservationID)
);

-- =========================
-- PAYMENT METHODS
-- =========================
CREATE TABLE IF NOT EXISTS PaymentMethods (
    MethodID INT AUTO_INCREMENT PRIMARY KEY,
    MethodName VARCHAR(50) UNIQUE
);

-- =========================
-- PAYMENTS
-- =========================
CREATE TABLE IF NOT EXISTS ReservationDiscounts (
    ReservationDiscountID INT AUTO_INCREMENT PRIMARY KEY,
    ReservationID INT NOT NULL,
    DiscountType VARCHAR(50) NOT NULL,
    DiscountValue DECIMAL(10,2) NOT NULL,
    CardNumber VARCHAR(50),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ReservationID) REFERENCES Reservations(ReservationID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Payments (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    ReservationID INT NOT NULL,
    MethodID INT NOT NULL,
    TotalBeforeDiscount DECIMAL(10,2) NOT NULL DEFAULT 0.00, -- Original total
    DiscountAmount DECIMAL(10,2) NOT NULL DEFAULT 0.00,       -- Discount applied
    Amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,               -- Total charged
    PaymentStatus ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
    PaymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    TransactionReference VARCHAR(255),

    FOREIGN KEY (ReservationID) REFERENCES Reservations(ReservationID) ON DELETE CASCADE,
    FOREIGN KEY (MethodID) REFERENCES PaymentMethods(MethodID)
);
-- =========================
-- CARTS
-- =========================

-- TODO: Delete if Expired
CREATE TABLE IF NOT EXISTS ReservationCarts (
    CartID INT AUTO_INCREMENT PRIMARY KEY,
    SessionGuestID INT NOT NULL UNIQUE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ExpiresAt DATETIME,
    FOREIGN KEY (SessionGuestID) REFERENCES SessionGuests(SessionGuestID)
);

CREATE TABLE IF NOT EXISTS CartRooms (
    CartRoomID INT AUTO_INCREMENT PRIMARY KEY,
    CartID INT NOT NULL,
    RoomID INT NOT NULL,
    CheckInDate DATE NOT NULL,
    CheckOutDate DATE NOT NULL,

    NumAdults INT NOT NULL DEFAULT 1,
    NumChildren INT NOT NULL DEFAULT 0,

    FOREIGN KEY (CartID) REFERENCES ReservationCarts(CartID),
    FOREIGN KEY (RoomID) REFERENCES Rooms(RoomID)
);

-- =========================
-- INDEXES
-- =========================
CREATE INDEX idx_reservation_dates
ON ReservationRooms(CheckInDate, CheckOutDate);

CREATE INDEX idx_room_type
ON Rooms(RoomTypeID);

CREATE INDEX idx_session_guest 
ON ReservationCarts(SessionGuestID);