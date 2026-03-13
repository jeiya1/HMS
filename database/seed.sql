INSERT INTO Roles (RoleName) VALUES
('admin'),
('guest');

INSERT INTO BedTypes (BedName) VALUES
('Single'),
('Double'),
('Queen'),
('King');

INSERT INTO ReservationStatus (StatusName) VALUES
('pending'),
('confirmed'),
('checked_in'),
('checked_out'),
('cancelled');

INSERT INTO PaymentMethods (MethodName) VALUES
('cash'),
('credit_card'),
('debit_card'),
('online_payment');

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

-- INSERT INTO Reservations (GuestID, StatusID, CheckInDate, CheckOutDate, NumAdults, NumChildren) VALUES
-- (1, 2, '2026-04-08', '2026-04-10', 1, 0);

-- INSERT INTO ReservationRooms (ReservationID, RoomID) VALUES
-- (1, 1);

-- INSERT INTO Payments (ReservationID, MethodID, Amount, PaymentStatus) VALUES
-- (1, 4, 5000.00, 'completed');

-- UPDATE Reservations
-- SET StatusID = 5
-- WHERE StatusID = 1
-- AND CreatedAt < NOW() - INTERVAL 30 MINUTE;