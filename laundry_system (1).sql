-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2025 at 12:53 PM
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
-- Database: `laundry_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `addon_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addons`
--

INSERT INTO `addons` (`addon_id`, `name`, `price`) VALUES
(1, 'Towels', 30.00),
(2, 'Bedsheets/Pillowcases', 50.00),
(3, 'Comforters', 75.00);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `contact_number`, `created_at`) VALUES
(4, 'Johann Yap', '0994244424', '2025-05-22 18:49:03'),
(5, 'Johann Yap', '0994244424', '2025-05-22 18:50:00'),
(6, 'Renz de arroz', '0994244424', '2025-05-22 19:08:15'),
(7, 'Johann Yap', '0994244424', '2025-05-22 19:08:30'),
(8, 'rey', '123123123123123', '2025-05-22 19:27:49'),
(9, 'Johann Yap', '0994244424', '2025-05-22 22:00:01'),
(10, 'Renz de arroz', '0994244424', '2025-05-22 22:10:11'),
(11, 'Divine', '21442145215', '2025-05-22 22:35:30'),
(12, 'Johann Yap', '0994244424', '2025-05-22 22:48:15'),
(13, 'Johann Yap', '0994244424', '2025-05-22 22:58:44'),
(14, 'Johann Yap', '0994244424', '2025-05-22 23:05:02'),
(15, 'Renz de arroz', '21442145215', '2025-05-22 23:05:13'),
(16, 'rey', '123123123123123', '2025-05-22 23:05:24'),
(17, 'asdfasf', '11111111111', '2025-05-22 23:05:46'),
(18, 'Divine', '2222123244', '2025-05-22 23:06:29'),
(19, 'Johann Yap', '0994244424', '2025-05-22 23:27:31'),
(20, 'nikko', '0994244424', '2025-05-23 04:48:11');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `order_reference` varchar(20) NOT NULL,
  `order_weight` decimal(5,2) NOT NULL,
  `delivery_type` enum('Pickup','Delivery') NOT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 50.00,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('Not yet Paid','Paid') NOT NULL DEFAULT 'Not yet Paid',
  `order_status` enum('Not Started','In Progress','Done') NOT NULL DEFAULT 'Not Started',
  `retrieval_status` enum('To be Claimed','To be Delivered','Claimed','Delivered') NOT NULL DEFAULT 'To be Claimed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_historical` tinyint(1) DEFAULT 0,
  `is_completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `order_reference`, `order_weight`, `delivery_type`, `delivery_location`, `subtotal`, `delivery_fee`, `total_amount`, `payment_status`, `order_status`, `retrieval_status`, `created_at`, `is_historical`, `is_completed`, `completed_at`) VALUES
(12, 15, '2025-05-23', '#771', 9.00, 'Delivery', 'adsadasdasd', 450.00, 50.00, 500.00, 'Paid', 'Done', 'Delivered', '2025-05-22 23:05:13', 0, 1, '2025-05-22 23:06:46'),
(13, 16, '2025-05-23', '#166', 9.00, 'Pickup', NULL, 820.00, 0.00, 820.00, 'Paid', 'Done', 'Claimed', '2025-05-22 23:05:24', 0, 1, '2025-05-22 23:06:46'),
(14, 17, '2025-05-23', '#792', 11.00, 'Delivery', 'adsadasdasdd', 595.00, 50.00, 645.00, 'Not yet Paid', 'Done', 'Claimed', '2025-05-22 23:05:46', 0, 0, NULL),
(15, 18, '2025-05-23', '#794', 21.00, 'Pickup', NULL, 560.00, 0.00, 560.00, 'Paid', 'In Progress', 'To be Claimed', '2025-05-22 23:06:29', 0, 0, NULL),
(16, 19, '2025-05-23', '#988', 8.00, 'Pickup', NULL, 150.00, 0.00, 150.00, 'Not yet Paid', 'Not Started', 'To be Claimed', '2025-05-22 23:27:31', 0, 0, NULL),
(17, 20, '2025-05-23', '#259', 9.00, 'Delivery', 'dsfgsgsdg', 325.00, 50.00, 375.00, 'Paid', 'Not Started', 'To be Delivered', '2025-05-23 04:48:11', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_addons`
--

CREATE TABLE `order_addons` (
  `order_id` int(11) NOT NULL,
  `addon_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_addons`
--

INSERT INTO `order_addons` (`order_id`, `addon_id`, `quantity`) VALUES
(12, 1, 1),
(12, 2, 2),
(12, 3, 2),
(13, 2, 4),
(13, 3, 6),
(14, 1, 2),
(14, 2, 2),
(14, 3, 3),
(15, 1, 5),
(17, 1, 1),
(17, 2, 1),
(17, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-05-22 15:53:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`addon_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_status` (`order_status`,`retrieval_status`);

--
-- Indexes for table `order_addons`
--
ALTER TABLE `order_addons`
  ADD PRIMARY KEY (`order_id`,`addon_id`),
  ADD KEY `addon_id` (`addon_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addons`
--
ALTER TABLE `addons`
  MODIFY `addon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `order_addons`
--
ALTER TABLE `order_addons`
  ADD CONSTRAINT `order_addons_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_addons_ibfk_2` FOREIGN KEY (`addon_id`) REFERENCES `addons` (`addon_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
