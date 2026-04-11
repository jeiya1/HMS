USE HMS;

INSERT INTO Roles (RoleName) VALUES ('admin'), ('guest'), ('receptionist');

INSERT INTO BedTypes (BedName) VALUES
('Single'),
('Double'),
('Queen'),
('King');

INSERT INTO DiscountTypes (DiscountTypeID, DiscountName, DiscountPercentage) VALUES 
(1,'Senior',20),
(2,'PWD',15);

INSERT INTO PaymentMethods (MethodName) VALUES
('cash'),
('card'),
('online_payment');

-- =========================
-- Admin User
-- =========================
INSERT INTO Guests (Email, FirstName, LastName, PhoneContact, BirthDate) VALUES 
('admin@hotel.com', 'Admin', 'Admin', '+630000000000', '2005-11-28');

INSERT INTO Users (GuestID, RoleID, Email, PasswordHash) VALUES
(1, 1, 'admin@hotel.com', '$2y$10$zL5JmHmi0PALf5/1/sACZOgd4LU4PsQQkGePVDYOMXHSGwOP4s8KK');

-- =========================
-- Receptionist User  (password: same as admin)
-- =========================
INSERT INTO Guests (Email, FirstName, LastName, PhoneContact, BirthDate) VALUES
('receptionist@hotel.com', 'Front', 'Desk', '+630000000001', '1995-06-15');
INSERT INTO Users (GuestID, RoleID, Email, PasswordHash) VALUES
(2, 3, 'receptionist@hotel.com', '$2y$10$zL5JmHmi0PALf5/1/sACZOgd4LU4PsQQkGePVDYOMXHSGwOP4s8KK');

-- =========================
-- Hotel Layout
-- =========================
INSERT INTO Floors (FloorNumber) VALUES
(1),
(2),
(3),
(4);

INSERT INTO RoomTypes (RoomTypeName, BasePrice, BedTypeID, BedCount, MaxOccupancy) VALUES
('Standard Single', 1800.00, 2, 1, 2),
('Deluxe Single', 2300.00, 3, 1, 4),
('Suite Single', 3000.00, 4, 1, 6),
('Standard Double', 2700.00, 2, 2, 3),
('Deluxe Double', 3200.00, 3, 2, 6),
('Suite Double', 4000.00, 4, 2, 10);

INSERT INTO Rooms (RoomNumber, FloorID, RoomTypeID) VALUES
('101',1,1),('102',1,1),('103',1,2),('104',1,2),('105',1,3),
('106',1,1),('107',1,2),('108',1,3),('109',1,1),('110',1,2),

('201',2,1),('202',2,1),('203',2,2),('204',2,2),('205',2,3),
('206',2,1),('207',2,2),('208',2,3),('209',2,1),('210',2,2),

('301',3,1),('302',3,1),('303',3,2),('304',3,2),('305',3,3),
('306',3,1),('307',3,2),('308',3,3),('309',3,1),('310',3,2),

('401',4,4),('402',4,4),('403',4,5),('404',4,5),('405',4,6),
('406',4,4),('407',4,5),('408',4,6),('409',4,4),('410',4,5);