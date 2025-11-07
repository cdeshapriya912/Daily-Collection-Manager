-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 07, 2025 at 07:24 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sahanalk`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer ID (e.g., C001)',
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'First name from registration',
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Last name from registration',
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto-generated: first_name + last_name',
  `full_name_with_surname` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Full name with surname (optional)',
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Permanent address',
  `gnd` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Grama Niladari Division',
  `lgi` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Local Government Institutions',
  `police_station` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Police station',
  `nic` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'NIC ID Number (12 digits or 9+V/X)',
  `occupation` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Permanent Occupation',
  `residence_period` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Period of residence at address',
  `nic_front_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to NIC front image',
  `nic_back_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to NIC back image',
  `customer_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to customer photo',
  `status` enum('active','inactive','blocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `total_purchased` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total amount purchased',
  `total_paid` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total amount paid',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customer registration table with extended fields for form data and documents';

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_code`, `first_name`, `last_name`, `full_name`, `full_name_with_surname`, `email`, `mobile`, `address`, `gnd`, `lgi`, `police_station`, `nic`, `occupation`, `residence_period`, `nic_front_path`, `nic_back_path`, `customer_photo_path`, `status`, `total_purchased`, `total_paid`, `created_at`, `updated_at`) VALUES
(1, 'C001', 'Chinthaka', 'Deshapriya', 'Chinthaka Deshapriya', 'Ranaweera Koralalage Don Chinthaka Deshapriya', 'chinthaka@example.com', '0778553032', '08/1,Kaluwelgoda,Makewita,Ja-Ela', 'Siyabalapitiya', 'MInuwangoda', 'Gampaha', '199144558896', 'Graphic Designer', '35', 'uploads/customers/C001_nic_front_1762108290.jpg', 'uploads/customers/C001_nic_back_1762108290.jpg', 'uploads/customers/C001_photo_1762108290.jpg', 'active', '0.00', '0.00', '2025-11-02 21:31:30', '2025-11-07 22:23:12');

-- --------------------------------------------------------

--
-- Table structure for table `installment_schedules`
--

CREATE TABLE `installment_schedules` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `schedule_date` date NOT NULL,
  `due_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT '0.00',
  `status` enum('pending','paid','missed','partial') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_id` int(10) UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daily installment payment schedule tracking';

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Order number (e.g., ORD-2024-001)',
  `customer_id` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `remaining_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `installment_period` int(10) UNSIGNED DEFAULT '30' COMMENT 'Payment period in days',
  `daily_payment` decimal(10,2) DEFAULT '0.00' COMMENT 'Daily payment amount',
  `status` enum('pending','active','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'Staff who created order',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `assignment_date` date DEFAULT NULL COMMENT 'Date when installment was assigned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL COMMENT 'Price at time of order',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'quantity * unit_price'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `order_items`
--
DELIMITER $$
CREATE TRIGGER `trg_order_items_update_stock` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    UPDATE products 
    SET quantity = quantity - NEW.quantity,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.product_id 
      AND quantity >= NEW.quantity; -- Prevent negative stock
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_code` (`customer_code`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD KEY `idx_customers_code` (`customer_code`),
  ADD KEY `idx_customers_first_name` (`first_name`),
  ADD KEY `idx_customers_last_name` (`last_name`),
  ADD KEY `idx_customers_mobile` (`mobile`),
  ADD KEY `idx_customers_nic` (`nic`),
  ADD KEY `idx_customers_status` (`status`),
  ADD KEY `idx_customers_email` (`email`);

--
-- Indexes for table `installment_schedules`
--
ALTER TABLE `installment_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_installment_schedules_payment` (`payment_id`),
  ADD KEY `idx_schedule_order_date` (`order_id`,`schedule_date`),
  ADD KEY `idx_schedule_status` (`status`),
  ADD KEY `idx_schedule_order` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `fk_orders_creator` (`created_by`),
  ADD KEY `idx_orders_number` (`order_number`),
  ADD KEY `idx_orders_customer` (`customer_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_date` (`order_date`),
  ADD KEY `idx_orders_customer_status` (`customer_id`,`status`),
  ADD KEY `idx_orders_assignment_date` (`assignment_date`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order` (`order_id`),
  ADD KEY `idx_order_items_product` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `installment_schedules`
--
ALTER TABLE `installment_schedules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `installment_schedules`
--
ALTER TABLE `installment_schedules`
  ADD CONSTRAINT `fk_installment_schedules_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_installment_schedules_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
