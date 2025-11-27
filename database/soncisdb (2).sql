-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 05:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `soncisdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminusers`
--

CREATE TABLE `adminusers` (
  `Id` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `LastLoginAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminusers`
--

INSERT INTO `adminusers` (`Id`, `Username`, `PasswordHash`, `Email`, `CreatedAt`, `LastLoginAt`) VALUES
(1, 'admin', '$2a$11$KIXx8qXqZqZqZqZqZqZqZ.qZqZqZqZqZqZqZqZqZqZqZqZqZqZqZ', 'admin@soncis.com', '2025-11-23 06:54:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `Id` int(11) NOT NULL,
  `OrderId` int(11) NOT NULL,
  `ProductId` int(11) NOT NULL,
  `ProductName` varchar(200) NOT NULL,
  `Price` decimal(18,2) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Subtotal` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`Id`, `OrderId`, `ProductId`, `ProductName`, `Price`, `Quantity`, `Subtotal`) VALUES
(1, 1, 2, 'Executive Briefcase', 5325.00, 9, 47925.00);

-- --------------------------------------------------------

--
-- Table structure for table `ordermeta`
--

CREATE TABLE `ordermeta` (
  `Id` int(11) NOT NULL,
  `OrderId` int(11) NOT NULL,
  `MetaKey` varchar(255) NOT NULL,
  `MetaValue` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ordermeta`
--

INSERT INTO `ordermeta` (`Id`, `OrderId`, `MetaKey`, `MetaValue`) VALUES
(1, 1, 'mobile_money_number', '0533431086');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `Id` int(11) NOT NULL,
  `OrderNumber` varchar(50) NOT NULL,
  `CustomerName` varchar(200) NOT NULL,
  `CustomerEmail` varchar(200) NOT NULL,
  `CustomerPhone` varchar(50) DEFAULT NULL,
  `ShippingAddress` text DEFAULT NULL,
  `BillingAddress` text DEFAULT NULL,
  `Total` decimal(18,2) NOT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'pending',
  `PaymentMethod` varchar(50) NOT NULL,
  `PaymentTransactionId` varchar(100) DEFAULT NULL,
  `PaymentStatus` varchar(50) NOT NULL DEFAULT 'pending',
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`Id`, `OrderNumber`, `CustomerName`, `CustomerEmail`, `CustomerPhone`, `ShippingAddress`, `BillingAddress`, `Total`, `Status`, `PaymentMethod`, `PaymentTransactionId`, `PaymentStatus`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'SONCIS-20251124-ADA024', 'Abraham Caiquo', 'caiquoabraham@gmail.com', '0592961835', '5195 Tydman way, Burlington, Florida, 2023, Ghana', '5195 Tydman way, Burlington, Florida, 2023, Ghana', 47925.00, 'Pending', 'mobile-money', NULL, 'Awaiting Mobile Money Payment', '2025-11-24 19:46:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `Id` int(11) NOT NULL,
  `Name` varchar(200) NOT NULL,
  `Price` decimal(18,2) NOT NULL,
  `Description` text DEFAULT NULL,
  `ImageUrl` varchar(500) NOT NULL,
  `Category` varchar(100) DEFAULT NULL,
  `InStock` tinyint(1) NOT NULL DEFAULT 1,
  `StockQuantity` int(11) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT NULL,
  `Tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`Tags`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`Id`, `Name`, `Price`, `Description`, `ImageUrl`, `Category`, `InStock`, `StockQuantity`, `CreatedAt`, `UpdatedAt`, `Tags`) VALUES
(1, 'Vintage Leather Tote', 4425.00, 'Handcrafted vintage leather tote bag', '/images/product-thumb-1.jpg', 'Bags', 1, 10, '2025-11-23 06:54:24', NULL, '[\"newarrivals\",\"bestsellers\"]'),
(2, 'Executive Briefcase', 5325.00, 'Professional executive briefcase', '/images/product-thumb-2.jpg', 'Bags', 1, 8, '2025-11-23 06:54:24', NULL, '[\"bestsellers\"]');

-- --------------------------------------------------------

--
-- Table structure for table `sitecontents`
--

CREATE TABLE `sitecontents` (
  `Id` int(11) NOT NULL,
  `Key` varchar(100) NOT NULL,
  `Value` text NOT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `UpdatedAt` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sitecontents`
--

INSERT INTO `sitecontents` (`Id`, `Key`, `Value`, `Type`, `UpdatedAt`) VALUES
(1, 'hero_title', 'Timeless Elegance', 'text', '2025-11-23 06:54:24'),
(2, 'hero_description', 'Sed condimentum ipsum, ultrices in aliquam ac hendrerit diam praesent.', 'text', '2025-11-23 06:54:24'),
(3, 'contact_email', 'contact@yourcompany.com', 'text', '2025-11-23 06:54:24'),
(4, 'contact_phone', '0533431086', 'text', '2025-11-23 06:54:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminusers`
--
ALTER TABLE `adminusers`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `IX_OrderItems_OrderId` (`OrderId`);

--
-- Indexes for table `ordermeta`
--
ALTER TABLE `ordermeta`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `OrderId` (`OrderId`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `sitecontents`
--
ALTER TABLE `sitecontents`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminusers`
--
ALTER TABLE `adminusers`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ordermeta`
--
ALTER TABLE `ordermeta`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sitecontents`
--
ALTER TABLE `sitecontents`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `FK_OrderItems_Orders_OrderId` FOREIGN KEY (`OrderId`) REFERENCES `orders` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `ordermeta`
--
ALTER TABLE `ordermeta`
  ADD CONSTRAINT `ordermeta_ibfk_1` FOREIGN KEY (`OrderId`) REFERENCES `orders` (`Id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
