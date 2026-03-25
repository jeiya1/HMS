CREATE DATABASE IF NOT EXISTS HMS;
USE HMS;

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
CREATE TABLE IF NOT EXISTS Guests (
    GuestID INT AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(150) NOT NULL UNIQUE,
    FirstName VARCHAR(100) NOT NULL,
    LastName VARCHAR(100) NOT NULL,
    PhoneContact VARCHAR(30),
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

CREATE TABLE IF NOT EXISTS PasswordResets (
    ResetID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Token VARCHAR(255) NOT NULL,
    ExpiresAt DATETIME NOT NULL,
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
    BookingToken CHAR(36) NOT NULL UNIQUE, -- UUID token for guest access
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
-- CARTS
-- =========================

CREATE TABLE IF NOT EXISTS ReservationCarts (
    CartID INT AUTO_INCREMENT PRIMARY KEY,
    GuestID INT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ExpiresAt DATETIME,
    FOREIGN KEY (GuestID) REFERENCES Guests(GuestID)
);

CREATE TABLE IF NOT EXISTS CartRooms (
    CartRoomID INT AUTO_INCREMENT PRIMARY KEY,
    CartID INT NOT NULL,
    RoomID INT NOT NULL UNIQUE,
    CheckInDate DATE NOT NULL,
    CheckOutDate DATE NOT NULL,
    FOREIGN KEY (CartID) REFERENCES ReservationCarts(CartID),
    FOREIGN KEY (RoomID) REFERENCES Rooms(RoomID)
);

-- =========================
-- INDEXES
-- =========================
CREATE INDEX idx_reservation_dates
ON Reservations(CheckInDate, CheckOutDate);

CREATE INDEX idx_room_type
ON Rooms(RoomTypeID);