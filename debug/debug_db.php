<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../connect.php";

$conn->query("CREATE DATABASE IF NOT EXISTS HMS");
$conn->query("USE HMS");

// TABLES

$conn->execute_query("
CREATE TABLE IF NOT EXISTS Roles (
    RoleID INT AUTO_INCREMENT PRIMARY KEY,
    RoleName VARCHAR(50) NOT NULL UNIQUE
)");

$conn->execute_query("
CREATE TABLE IF NOT EXISTS Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    RoleID INT NOT NULL,
    FirstName VARCHAR(100) NOT NULL,
    LastName VARCHAR(100) NOT NULL,
    Email VARCHAR(150) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    PhoneContact VARCHAR(20),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (RoleID) REFERENCES Roles(RoleID)
)");

$conn->execute_query("
CREATE TABLE IF NOT EXISTS RoomTypes (
    RoomTypeID INT AUTO_INCREMENT PRIMARY KEY
)");

$conn->execute_query("
CREATE TABLE IF NOT EXISTS Rooms (
    RoomID INT AUTO_INCREMENT PRIMARY KEY
)");

$conn->execute_query("
CREATE TABLE IF NOT EXISTS Reservations (
    ReservationID INT AUTO_INCREMENT PRIMARY KEY
)");

$conn->execute_query("
CREATE TABLE IF NOT EXISTS Payments (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY
)");

?>