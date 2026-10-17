-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2026 at 02:09 PM
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
-- Database: `hospitality_booking_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`id`, `name`, `icon`) VALUES
(1, 'Free Wi-Fi', 'wifi'),
(2, 'Air conditioning', 'snowflake'),
(3, 'Swimming pool', 'waves'),
(4, 'Breakfast included', 'coffee'),
(5, 'On-site restaurant', 'utensils'),
(6, 'Free parking', 'car'),
(7, '24-hour front desk', 'clock'),
(8, 'Airport shuttle', 'bus'),
(9, 'Beachfront access', 'umbrella'),
(10, 'Fitness center', 'dumbbell'),
(11, 'Spa and wellness', 'flower'),
(12, 'Bar / lounge', 'wine'),
(13, 'Family rooms', 'users'),
(14, 'Meeting rooms', 'presentation'),
(15, 'Garden and outdoor area', 'trees'),
(16, 'Laundry service', 'shirt'),
(17, 'Tour desk', 'map'),
(18, 'Water sports', 'sailboat');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(40) NOT NULL,
  `guest_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `room_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `guest_first_name` varchar(100) NOT NULL,
  `guest_last_name` varchar(100) NOT NULL,
  `guest_email` varchar(254) NOT NULL,
  `guest_phone` varchar(32) DEFAULT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `guest_count` tinyint(3) UNSIGNED NOT NULL,
  `room_count` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `nightly_rate` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_fee_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PHP',
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `special_requests` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `reference`, `guest_user_id`, `property_id`, `room_type_id`, `guest_first_name`, `guest_last_name`, `guest_email`, `guest_phone`, `check_in`, `check_out`, `guest_count`, `room_count`, `nightly_rate`, `subtotal`, `discount_amount`, `tax_amount`, `service_fee_amount`, `total_amount`, `currency`, `status`, `special_requests`, `created_at`, `updated_at`) VALUES
(1, 'XYV-BKG-2025-00001', 33, 15, 53, 'Adrian', 'Estrada', 'adrian.estrada@example.com', '+639182911028', '2026-01-16', '2026-01-19', 2, 1, 3400.00, 10200.00, 0.00, 1224.00, 1020.00, 12444.00, 'PHP', 'completed', 'Early check-in if the room is available, please.', '2025-11-15 13:23:00', '2026-01-19 12:00:00'),
(2, 'XYV-BKG-2025-00002', 38, 6, 20, 'Mariel', 'Guevarra', 'mariel.guevarra@example.com', '+639962653845', '2026-02-07', '2026-02-11', 3, 1, 4950.00, 19800.00, 0.00, 2376.00, 1980.00, 24156.00, 'PHP', 'completed', 'Please arrange an airport pick-up. Flight details to follow.', '2025-11-28 16:30:00', '2026-02-11 12:00:00'),
(3, 'XYV-BKG-2025-00003', 19, 2, 6, 'Gabriel', 'Pascual', 'gabriel.pascual@example.com', '+639959922983', '2026-01-06', '2026-01-09', 1, 1, 6250.00, 18750.00, 0.00, 2250.00, 1875.00, 22875.00, 'PHP', 'completed', 'Requesting a room with a king bed if available.', '2025-12-07 11:38:00', '2026-01-09 12:00:00'),
(4, 'XYV-BKG-2025-00004', 22, 2, 6, 'Jasmine', 'Mercado', 'jasmine.mercado@example.com', '+639666861642', '2026-01-10', '2026-01-12', 3, 2, 6250.00, 25000.00, 0.00, 3000.00, 2500.00, 30500.00, 'PHP', 'completed', 'Early check-in if the room is available, please.', '2025-12-10 21:13:00', '2026-01-12 12:00:00'),
(5, 'XYV-BKG-2025-00005', 38, 18, 64, 'Mariel', 'Guevarra', 'mariel.guevarra@example.com', '+639962653845', '2026-02-03', '2026-02-04', 1, 1, 3200.00, 3200.00, 0.00, 384.00, 320.00, 3904.00, 'PHP', 'cancelled', 'Please prepare a vegetarian breakfast option.', '2025-12-16 21:06:00', '2026-01-25 10:24:11'),
(6, 'XYV-BKG-2025-00006', 35, 2, 5, 'Jerome', 'Legaspi', 'jerome.legaspi@example.com', '+639272808864', '2026-02-11', '2026-02-12', 2, 1, 4800.00, 4800.00, 0.00, 576.00, 480.00, 5856.00, 'PHP', 'completed', 'Celebrating an anniversary; a quiet room would be appreciated.', '2025-12-26 22:58:00', '2026-02-12 12:00:00'),
(7, 'XYV-BKG-2025-00007', 22, 10, 33, 'Jasmine', 'Mercado', 'jasmine.mercado@example.com', '+639666861642', '2026-02-10', '2026-02-17', 1, 1, 5200.00, 36400.00, 0.00, 4368.00, 3640.00, 44408.00, 'PHP', 'completed', 'Please prepare a vegetarian breakfast option.', '2025-12-27 11:19:00', '2026-02-17 12:00:00'),
(8, 'XYV-BKG-2025-00008', NULL, 1, 1, 'Arman', 'Dumlao', 'arman.dumlao@example.com', '+639176018898', '2026-01-15', '2026-01-16', 1, 1, 3200.00, 3200.00, 0.00, 384.00, 320.00, 3904.00, 'PHP', 'completed', 'Please arrange an airport pick-up. Flight details to follow.', '2025-12-30 21:49:00', '2026-01-16 12:00:00'),
(9, 'XYV-BKG-2026-00009', 27, 17, 60, 'Dominic', 'Flores', 'dominic.flores@example.com', '+639661621052', '2026-02-26', '2026-03-01', 1, 1, 6200.00, 18600.00, 0.00, 2232.00, 1860.00, 22692.00, 'PHP', 'completed', NULL, '2026-01-01 09:30:00', '2026-03-01 12:00:00'),
(10, 'XYV-BKG-2026-00010', 20, 13, 47, 'Nicole', 'Tan', 'nicole.tan@example.com', '+639551860320', '2026-01-19', '2026-01-21', 2, 1, 15000.00, 30000.00, 0.00, 3600.00, 3000.00, 36600.00, 'PHP', 'completed', NULL, '2026-01-05 20:42:00', '2026-01-21 12:00:00'),
(11, 'XYV-BKG-2026-00011', 34, 3, 12, 'Hannah', 'Cabrera', 'hannah.cabrera@example.com', '+639949256663', '2026-01-29', '2026-02-03', 3, 2, 13450.00, 134500.00, 0.00, 16140.00, 13450.00, 164090.00, 'PHP', 'completed', NULL, '2026-01-15 13:41:00', '2026-02-03 12:00:00'),
(12, 'XYV-BKG-2026-00012', 39, 9, 31, 'Raymond', 'Abad', 'raymond.abad@example.com', '+639958794326', '2026-02-26', '2026-03-03', 1, 1, 13000.00, 65000.00, 0.00, 7800.00, 6500.00, 79300.00, 'PHP', 'completed', NULL, '2026-01-17 19:14:00', '2026-03-03 12:00:00'),
(13, 'XYV-BKG-2026-00013', 31, 6, 19, 'Vincent', 'Dizon', 'vincent.dizon@example.com', '+639768464506', '2026-01-26', '2026-01-30', 3, 2, 3800.00, 30400.00, 0.00, 3648.00, 3040.00, 37088.00, 'PHP', 'completed', 'Early check-in if the room is available, please.', '2026-01-18 15:53:00', '2026-01-30 12:00:00'),
(14, 'XYV-BKG-2026-00014', 24, 9, 29, 'Camille', 'Soriano', 'camille.soriano@example.com', '+639964133017', '2026-02-12', '2026-02-15', 1, 1, 6500.00, 19500.00, 0.00, 2340.00, 1950.00, 23790.00, 'PHP', 'completed', 'Please prepare a vegetarian breakfast option.', '2026-01-29 15:35:00', '2026-02-15 12:00:00'),
(15, 'XYV-BKG-2026-00015', 31, 14, 51, 'Vincent', 'Dizon', 'vincent.dizon@example.com', '+639768464506', '2026-04-07', '2026-04-09', 3, 1, 10800.00, 21600.00, 0.00, 2592.00, 2160.00, 26352.00, 'PHP', 'completed', 'Arriving after 10:00 PM.', '2026-02-07 21:54:00', '2026-04-09 12:00:00'),
(16, 'XYV-BKG-2026-00016', NULL, 14, 50, 'Liza', 'Evangelista', 'liza.evangelista@example.com', '+639661297447', '2026-02-20', '2026-02-25', 2, 1, 7300.00, 36500.00, 0.00, 4380.00, 3650.00, 44530.00, 'PHP', 'completed', 'Celebrating an anniversary; a quiet room would be appreciated.', '2026-02-09 21:31:00', '2026-02-25 12:00:00'),
(17, 'XYV-BKG-2026-00017', 25, 6, 21, 'Enrico', 'Valdez', 'enrico.valdez@example.com', '+639270525898', '2026-03-11', '2026-03-12', 6, 1, 8000.00, 8000.00, 0.00, 960.00, 800.00, 9760.00, 'PHP', 'completed', 'Please arrange an airport pick-up. Flight details to follow.', '2026-02-10 16:58:00', '2026-03-12 12:00:00'),
(18, 'XYV-BKG-2026-00018', 31, 10, 34, 'Vincent', 'Dizon', 'vincent.dizon@example.com', '+639768464506', '2026-03-01', '2026-03-03', 1, 1, 7000.00, 14000.00, 0.00, 1680.00, 1400.00, 17080.00, 'PHP', 'completed', 'Traveling with a young child; a baby cot would be helpful.', '2026-02-18 10:50:00', '2026-03-03 12:00:00'),
(19, 'XYV-BKG-2026-00019', 35, 12, 41, 'Jerome', 'Legaspi', 'jerome.legaspi@example.com', '+639272808864', '2026-05-10', '2026-05-13', 3, 2, 3600.00, 21600.00, 0.00, 2592.00, 2160.00, 26352.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-02-28 07:01:00', '2026-05-13 12:00:00'),
(20, 'XYV-BKG-2026-00020', 33, 13, 46, 'Adrian', 'Estrada', 'adrian.estrada@example.com', '+639182911028', '2026-03-20', '2026-03-26', 1, 1, 10100.00, 60600.00, 0.00, 7272.00, 6060.00, 73932.00, 'PHP', 'completed', NULL, '2026-03-10 10:22:00', '2026-03-26 12:00:00'),
(21, 'XYV-BKG-2026-00021', 22, 10, 34, 'Jasmine', 'Mercado', 'jasmine.mercado@example.com', '+639666861642', '2026-05-23', '2026-05-27', 3, 1, 7000.00, 28000.00, 0.00, 3360.00, 2800.00, 34160.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-03-10 17:23:00', '2026-05-27 12:00:00'),
(22, 'XYV-BKG-2026-00022', 11, 16, 56, 'Marco', 'Reyes', 'marco.reyes@example.com', '+639967534778', '2026-05-20', '2026-05-24', 2, 1, 6800.00, 27200.00, 0.00, 3264.00, 2720.00, 33184.00, 'PHP', 'completed', NULL, '2026-03-11 12:33:00', '2026-05-24 12:00:00'),
(23, 'XYV-BKG-2026-00023', 34, 4, 13, 'Hannah', 'Cabrera', 'hannah.cabrera@example.com', '+639949256663', '2026-04-25', '2026-04-29', 2, 1, 2900.00, 11600.00, 0.00, 1392.00, 1160.00, 14152.00, 'PHP', 'completed', NULL, '2026-03-13 14:46:00', '2026-04-29 12:00:00'),
(24, 'XYV-BKG-2026-00024', 39, 16, 56, 'Raymond', 'Abad', 'raymond.abad@example.com', '+639958794326', '2026-04-14', '2026-04-16', 1, 1, 6800.00, 13600.00, 0.00, 1632.00, 1360.00, 16592.00, 'PHP', 'cancelled', 'Early check-in if the room is available, please.', '2026-03-20 14:46:00', '2026-03-22 20:09:45'),
(25, 'XYV-BKG-2026-00025', 34, 14, 52, 'Hannah', 'Cabrera', 'hannah.cabrera@example.com', '+639949256663', '2026-05-21', '2026-05-22', 12, 2, 15100.00, 30200.00, 0.00, 3624.00, 3020.00, 36844.00, 'PHP', 'completed', NULL, '2026-03-21 12:11:00', '2026-05-22 12:00:00'),
(26, 'XYV-BKG-2026-00026', 22, 3, 9, 'Jasmine', 'Mercado', 'jasmine.mercado@example.com', '+639666861642', '2026-04-11', '2026-04-14', 4, 2, 5600.00, 33600.00, 0.00, 4032.00, 3360.00, 40992.00, 'PHP', 'cancelled', 'Traveling with a young child; a baby cot would be helpful.', '2026-03-21 15:18:00', '2026-03-23 09:32:49'),
(27, 'XYV-BKG-2026-00027', 13, 1, 2, 'Joseph', 'Garcia', 'joseph.garcia@example.com', '+639951475136', '2026-05-14', '2026-05-15', 2, 1, 4150.00, 4150.00, 0.00, 498.00, 415.00, 5063.00, 'PHP', 'completed', 'Requesting a room with a king bed if available.', '2026-03-24 07:28:00', '2026-05-15 12:00:00'),
(28, 'XYV-BKG-2026-00028', 30, 18, 64, 'Michelle', 'Chua', 'michelle.chua@example.com', '+639959906593', '2026-05-11', '2026-05-15', 1, 1, 3200.00, 12800.00, 0.00, 1536.00, 1280.00, 15616.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-03-26 17:46:00', '2026-05-15 12:00:00'),
(29, 'XYV-BKG-2026-00029', 20, 2, 5, 'Nicole', 'Tan', 'nicole.tan@example.com', '+639551860320', '2026-05-21', '2026-05-22', 2, 1, 4800.00, 4800.00, 0.00, 576.00, 480.00, 5856.00, 'PHP', 'completed', NULL, '2026-03-26 22:54:00', '2026-05-22 12:00:00'),
(30, 'XYV-BKG-2026-00030', 31, 13, 45, 'Vincent', 'Dizon', 'vincent.dizon@example.com', '+639768464506', '2026-05-09', '2026-05-15', 2, 1, 7500.00, 45000.00, 0.00, 5400.00, 4500.00, 54900.00, 'PHP', 'cancelled', NULL, '2026-04-11 16:10:00', '2026-04-23 15:27:05'),
(31, 'XYV-BKG-2026-00031', 37, 13, 46, 'Francis', 'Tolentino', 'francis.tolentino@example.com', '+639964585003', '2026-06-03', '2026-06-05', 2, 1, 10100.00, 20200.00, 0.00, 2424.00, 2020.00, 24644.00, 'PHP', 'completed', NULL, '2026-04-12 13:47:00', '2026-06-05 12:00:00'),
(32, 'XYV-BKG-2026-00032', 20, 12, 41, 'Nicole', 'Tan', 'nicole.tan@example.com', '+639551860320', '2026-06-22', '2026-06-25', 2, 1, 3600.00, 10800.00, 0.00, 1296.00, 1080.00, 13176.00, 'PHP', 'completed', 'Traveling with a young child; a baby cot would be helpful.', '2026-04-17 13:27:00', '2026-06-25 12:00:00'),
(33, 'XYV-BKG-2026-00033', 25, 14, 50, 'Enrico', 'Valdez', 'enrico.valdez@example.com', '+639270525898', '2026-04-27', '2026-04-29', 2, 1, 7300.00, 14600.00, 0.00, 1752.00, 1460.00, 17812.00, 'PHP', 'cancelled', NULL, '2026-04-20 16:33:00', '2026-04-23 17:16:46'),
(34, 'XYV-BKG-2026-00034', 24, 2, 5, 'Camille', 'Soriano', 'camille.soriano@example.com', '+639964133017', '2026-05-19', '2026-05-23', 2, 1, 4800.00, 19200.00, 0.00, 2304.00, 1920.00, 23424.00, 'PHP', 'completed', NULL, '2026-04-28 10:17:00', '2026-05-23 12:00:00'),
(35, 'XYV-BKG-2026-00035', 11, 6, 19, 'Marco', 'Reyes', 'marco.reyes@example.com', '+639967534778', '2026-07-06', '2026-07-07', 2, 1, 3800.00, 3800.00, 0.00, 456.00, 380.00, 4636.00, 'PHP', 'completed', 'Please arrange an airport pick-up. Flight details to follow.', '2026-05-03 07:23:00', '2026-07-07 12:00:00'),
(36, 'XYV-BKG-2026-00036', 11, 4, 14, 'Marco', 'Reyes', 'marco.reyes@example.com', '+639967534778', '2026-06-03', '2026-06-05', 3, 1, 3750.00, 7500.00, 0.00, 900.00, 750.00, 9150.00, 'PHP', 'cancelled', 'Early check-in if the room is available, please.', '2026-05-05 07:18:00', '2026-05-23 09:19:01'),
(37, 'XYV-BKG-2026-00037', 19, 2, 5, 'Gabriel', 'Pascual', 'gabriel.pascual@example.com', '+639959922983', '2026-05-08', '2026-05-10', 3, 2, 4800.00, 19200.00, 0.00, 2304.00, 1920.00, 23424.00, 'PHP', 'completed', NULL, '2026-05-05 13:00:00', '2026-05-10 12:00:00'),
(38, 'XYV-BKG-2026-00038', 23, 3, 11, 'Lorenzo', 'Alvarez', 'lorenzo.alvarez@example.com', '+639956472587', '2026-06-11', '2026-06-14', 4, 1, 9500.00, 28500.00, 0.00, 3420.00, 2850.00, 34770.00, 'PHP', 'completed', NULL, '2026-05-11 18:59:00', '2026-06-14 12:00:00'),
(39, 'XYV-BKG-2026-00039', 19, 5, 17, 'Gabriel', 'Pascual', 'gabriel.pascual@example.com', '+639959922983', '2026-07-14', '2026-07-17', 4, 1, 7550.00, 22650.00, 0.00, 2718.00, 2265.00, 27633.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-05-14 18:45:00', '2026-07-17 12:00:00'),
(40, 'XYV-BKG-2026-00040', 22, 3, 12, 'Jasmine', 'Mercado', 'jasmine.mercado@example.com', '+639666861642', '2026-05-27', '2026-05-30', 3, 1, 13450.00, 40350.00, 0.00, 4842.00, 4035.00, 49227.00, 'PHP', 'completed', 'Please arrange an airport pick-up. Flight details to follow.', '2026-05-15 11:17:00', '2026-05-30 12:00:00'),
(41, 'XYV-BKG-2026-00041', 39, 13, 46, 'Raymond', 'Abad', 'raymond.abad@example.com', '+639958794326', '2026-06-20', '2026-06-22', 2, 1, 10100.00, 20200.00, 0.00, 2424.00, 2020.00, 24644.00, 'PHP', 'completed', NULL, '2026-05-15 15:00:00', '2026-06-22 12:00:00'),
(42, 'XYV-BKG-2026-00042', 35, 14, 51, 'Jerome', 'Legaspi', 'jerome.legaspi@example.com', '+639272808864', '2026-05-26', '2026-05-31', 1, 1, 10800.00, 54000.00, 0.00, 6480.00, 5400.00, 65880.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-05-15 21:36:00', '2026-05-31 12:00:00'),
(43, 'XYV-BKG-2026-00043', 19, 10, 36, 'Gabriel', 'Pascual', 'gabriel.pascual@example.com', '+639959922983', '2026-07-27', '2026-07-29', 5, 1, 14550.00, 29100.00, 0.00, 3492.00, 2910.00, 35502.00, 'PHP', 'completed', NULL, '2026-05-19 10:26:00', '2026-07-29 12:00:00'),
(44, 'XYV-BKG-2026-00044', 40, 9, 31, 'Charmaine', 'De Guzman', 'charmaine.deguzman@example.com', '+639966021458', '2026-08-01', '2026-08-08', 1, 1, 13000.00, 91000.00, 0.00, 10920.00, 9100.00, 111020.00, 'PHP', 'completed', 'Arriving after 10:00 PM.', '2026-05-21 14:27:00', '2026-08-08 12:00:00'),
(45, 'XYV-BKG-2026-00045', 16, 6, 21, 'Danica', 'Ocampo', 'danica.ocampo@example.com', '+639959298416', '2026-07-28', '2026-07-31', 3, 1, 8000.00, 24000.00, 0.00, 2880.00, 2400.00, 29280.00, 'PHP', 'completed', 'Prefer a room on a higher floor.', '2026-05-24 14:38:00', '2026-07-31 12:00:00'),
(46, 'XYV-BKG-2026-00046', 11, 15, 53, 'Marco', 'Reyes', 'marco.reyes@example.com', '+639967534778', '2026-08-05', '2026-08-11', 2, 1, 3400.00, 20400.00, 0.00, 2448.00, 2040.00, 24888.00, 'PHP', 'completed', 'Celebrating an anniversary; a quiet room would be appreciated.', '2026-05-26 12:38:00', '2026-08-11 12:00:00'),
(47, 'XYV-BKG-2026-00047', 36, 13, 45, 'Katherine', 'Sy', 'katherine.sy@example.com', '+639668764529', '2026-08-02', '2026-08-06', 2, 1, 7500.00, 30000.00, 0.00, 3600.00, 3000.00, 36600.00, 'PHP', 'completed', 'Early check-in if the room is available, please.', '2026-06-04 11:27:00', '2026-08-06 12:00:00'),
(48, 'XYV-BKG-2026-00048', 19, 10, 33, 'Gabriel', 'Pascual', 'gabriel.pascual@example.com', '+639959922983', '2026-08-04', '2026-08-07', 2, 1, 5200.00, 15600.00, 0.00, 1872.00, 1560.00, 19032.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-06-06 12:22:00', '2026-08-07 12:00:00'),
(49, 'XYV-BKG-2026-00049', 21, 6, 21, 'Rafael', 'Fernandez', 'rafael.fernandez@example.com', '+639557028382', '2026-08-14', '2026-08-16', 5, 1, 8000.00, 16000.00, 0.00, 1920.00, 1600.00, 19520.00, 'PHP', 'cancelled', 'Please prepare a vegetarian breakfast option.', '2026-06-06 12:33:00', '2026-06-06 19:59:38'),
(50, 'XYV-BKG-2026-00050', 23, 4, 13, 'Lorenzo', 'Alvarez', 'lorenzo.alvarez@example.com', '+639956472587', '2026-07-21', '2026-07-25', 4, 2, 2900.00, 23200.00, 0.00, 2784.00, 2320.00, 28304.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-06-10 15:28:00', '2026-07-25 12:00:00'),
(51, 'XYV-BKG-2026-00051', 37, 16, 56, 'Francis', 'Tolentino', 'francis.tolentino@example.com', '+639964585003', '2026-06-28', '2026-06-29', 2, 1, 6800.00, 6800.00, 0.00, 816.00, 680.00, 8296.00, 'PHP', 'completed', NULL, '2026-06-13 10:52:00', '2026-06-29 12:00:00'),
(52, 'XYV-BKG-2026-00052', 29, 1, 2, 'Kenneth', 'Go', 'kenneth.go@example.com', '+639170130411', '2026-07-05', '2026-07-09', 1, 1, 4150.00, 16600.00, 0.00, 1992.00, 1660.00, 20252.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-06-14 14:22:00', '2026-07-09 12:00:00'),
(53, 'XYV-BKG-2026-00053', 11, 15, 54, 'Marco', 'Reyes', 'marco.reyes@example.com', '+639967534778', '2026-08-27', '2026-08-30', 1, 1, 4600.00, 13800.00, 0.00, 1656.00, 1380.00, 16836.00, 'PHP', 'completed', NULL, '2026-07-02 19:00:00', '2026-08-30 12:00:00'),
(54, 'XYV-BKG-2026-00054', 24, 4, 13, 'Camille', 'Soriano', 'camille.soriano@example.com', '+639964133017', '2026-09-12', '2026-09-15', 3, 2, 2900.00, 17400.00, 0.00, 2088.00, 1740.00, 21228.00, 'PHP', 'completed', NULL, '2026-07-06 18:56:00', '2026-09-15 12:00:00'),
(55, 'XYV-BKG-2026-00055', 35, 5, 18, 'Jerome', 'Legaspi', 'jerome.legaspi@example.com', '+639272808864', '2026-07-15', '2026-07-18', 2, 1, 6300.00, 18900.00, 0.00, 2268.00, 1890.00, 23058.00, 'PHP', 'completed', 'Early check-in if the room is available, please.', '2026-07-08 13:45:00', '2026-07-18 12:00:00'),
(56, 'XYV-BKG-2026-00056', 37, 8, 25, 'Francis', 'Tolentino', 'francis.tolentino@example.com', '+639964585003', '2026-08-12', '2026-08-14', 2, 1, 3000.00, 6000.00, 0.00, 720.00, 600.00, 7320.00, 'PHP', 'completed', 'Please arrange an airport pick-up. Flight details to follow.', '2026-07-09 19:22:00', '2026-08-14 12:00:00'),
(57, 'XYV-BKG-2026-00057', 32, 1, 1, 'Sofia', 'Padilla', 'sofia.padilla@example.com', '+639184659085', '2026-07-13', '2026-07-14', 1, 1, 3200.00, 3200.00, 0.00, 384.00, 320.00, 3904.00, 'PHP', 'completed', NULL, '2026-07-10 09:03:00', '2026-07-14 12:00:00'),
(58, 'XYV-BKG-2026-00058', 31, 16, 58, 'Vincent', 'Dizon', 'vincent.dizon@example.com', '+639768464506', '2026-08-18', '2026-08-19', 3, 2, 13600.00, 27200.00, 0.00, 3264.00, 2720.00, 33184.00, 'PHP', 'completed', 'Please prepare a vegetarian breakfast option.', '2026-07-10 20:59:00', '2026-08-19 12:00:00'),
(59, 'XYV-BKG-2026-00059', 34, 4, 14, 'Hannah', 'Cabrera', 'hannah.cabrera@example.com', '+639949256663', '2026-09-23', '2026-09-27', 3, 1, 3750.00, 15000.00, 0.00, 1800.00, 1500.00, 18300.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-07-18 18:53:00', '2026-09-27 12:00:00'),
(60, 'XYV-BKG-2026-00060', 14, 14, 50, 'Bianca', 'Lim', 'bianca.lim@example.com', '+639177981933', '2026-09-09', '2026-09-12', 2, 1, 7300.00, 21900.00, 0.00, 2628.00, 2190.00, 26718.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-07-19 08:33:00', '2026-09-12 12:00:00'),
(61, 'XYV-BKG-2026-00061', 14, 16, 58, 'Bianca', 'Lim', 'bianca.lim@example.com', '+639177981933', '2026-09-04', '2026-09-09', 2, 1, 13600.00, 68000.00, 0.00, 8160.00, 6800.00, 82960.00, 'PHP', 'completed', 'Celebrating an anniversary; a quiet room would be appreciated.', '2026-07-22 12:31:00', '2026-09-09 12:00:00'),
(62, 'XYV-BKG-2026-00062', 20, 10, 33, 'Nicole', 'Tan', 'nicole.tan@example.com', '+639551860320', '2026-09-03', '2026-09-09', 2, 1, 5200.00, 31200.00, 0.00, 3744.00, 3120.00, 38064.00, 'PHP', 'completed', 'Traveling with a young child; a baby cot would be helpful.', '2026-07-22 22:16:00', '2026-09-09 12:00:00'),
(63, 'XYV-BKG-2026-00063', 12, 9, 30, 'Patricia', 'Cruz', 'patricia.cruz@example.com', '+639174571784', '2026-08-08', '2026-08-12', 4, 2, 8800.00, 70400.00, 0.00, 8448.00, 7040.00, 85888.00, 'PHP', 'completed', 'Traveling with a young child; a baby cot would be helpful.', '2026-07-23 16:25:00', '2026-08-12 12:00:00'),
(64, 'XYV-BKG-2026-00064', 11, 17, 61, 'Marco', 'Reyes', 'marco.reyes@example.com', '+639967534778', '2026-07-30', '2026-08-02', 1, 1, 8350.00, 25050.00, 0.00, 3006.00, 2505.00, 30561.00, 'PHP', 'completed', 'Late check-out requested if possible.', '2026-07-25 20:29:00', '2026-08-02 12:00:00'),
(65, 'XYV-BKG-2026-00065', 10, 1, 1, 'Andrea', 'Santos', 'andrea.santos@example.com', '+639956634400', '2026-09-16', '2026-09-22', 2, 1, 3200.00, 19200.00, 0.00, 2304.00, 1920.00, 23424.00, 'PHP', 'completed', NULL, '2026-07-30 09:47:00', '2026-09-22 12:00:00'),
(66, 'XYV-BKG-2026-00066', 16, 3, 9, 'Danica', 'Ocampo', 'danica.ocampo@example.com', '+639959298416', '2026-08-29', '2026-09-04', 1, 1, 5600.00, 33600.00, 0.00, 4032.00, 3360.00, 40992.00, 'PHP', 'completed', 'Traveling with a young child; a baby cot would be helpful.', '2026-08-06 16:01:00', '2026-09-04 12:00:00'),
(67, 'XYV-BKG-2026-00067', 34, 10, 35, 'Hannah', 'Cabrera', 'hannah.cabrera@example.com', '+639949256663', '2026-08-26', '2026-08-30', 6, 2, 10400.00, 83200.00, 0.00, 9984.00, 8320.00, 101504.00, 'PHP', 'cancelled', 'Prefer a room on a higher floor.', '2026-08-12 22:49:00', '2026-08-19 22:12:51'),
(68, 'XYV-BKG-2026-00068', 37, 11, 39, 'Francis', 'Tolentino', 'francis.tolentino@example.com', '+639964585003', '2026-10-10', '2026-10-12', 1, 1, 19600.00, 39200.00, 0.00, 4704.00, 3920.00, 47824.00, 'PHP', 'completed', NULL, '2026-08-13 15:24:00', '2026-10-12 12:00:00'),
(69, 'XYV-BKG-2026-00069', 15, 12, 42, 'Carlo', 'Aquino', 'carlo.aquino@example.com', '+639964520599', '2026-09-07', '2026-09-10', 1, 1, 4850.00, 14550.00, 0.00, 1746.00, 1455.00, 17751.00, 'PHP', 'completed', 'Requesting a room with a king bed if available.', '2026-08-18 07:06:00', '2026-09-10 12:00:00'),
(70, 'XYV-BKG-2026-00070', 10, 16, 56, 'Andrea', 'Santos', 'andrea.santos@example.com', '+639956634400', '2026-09-13', '2026-09-15', 2, 1, 6800.00, 13600.00, 0.00, 1632.00, 1360.00, 16592.00, 'PHP', 'completed', NULL, '2026-08-21 20:32:00', '2026-09-15 12:00:00'),
(71, 'XYV-BKG-2026-00071', 33, 13, 45, 'Adrian', 'Estrada', 'adrian.estrada@example.com', '+639182911028', '2026-09-29', '2026-10-02', 1, 1, 7500.00, 22500.00, 0.00, 2700.00, 2250.00, 27450.00, 'PHP', 'completed', 'Celebrating an anniversary; a quiet room would be appreciated.', '2026-08-23 20:02:00', '2026-10-02 12:00:00'),
(72, 'XYV-BKG-2026-00072', 27, 5, 16, 'Dominic', 'Flores', 'dominic.flores@example.com', '+639661621052', '2026-10-10', '2026-10-13', 1, 1, 4200.00, 12600.00, 0.00, 1512.00, 1260.00, 15372.00, 'PHP', 'completed', 'Early check-in if the room is available, please.', '2026-08-24 11:10:00', '2026-10-13 12:00:00'),
(73, 'XYV-BKG-2026-00073', NULL, 11, 38, 'Benedict', 'Javier', 'benedict.javier@example.com', '+639278250701', '2026-10-07', '2026-10-10', 3, 1, 13250.00, 39750.00, 0.00, 4770.00, 3975.00, 48495.00, 'PHP', 'cancelled', 'Celebrating an anniversary; a quiet room would be appreciated.', '2026-08-25 11:10:00', '2026-09-06 16:28:12'),
(74, 'XYV-BKG-2026-00074', 35, 2, 8, 'Jerome', 'Legaspi', 'jerome.legaspi@example.com', '+639272808864', '2026-09-06', '2026-09-08', 3, 1, 11500.00, 23000.00, 0.00, 2760.00, 2300.00, 28060.00, 'PHP', 'completed', 'Please arrange an airport pick-up. Flight details to follow.', '2026-09-03 16:49:00', '2026-09-08 12:00:00'),
(75, 'XYV-BKG-2026-00075', 11, 3, 12, 'Marco', 'Reyes', 'marco.reyes@example.com', '+639967534778', '2026-10-05', '2026-10-09', 3, 1, 13450.00, 53800.00, 0.00, 6456.00, 5380.00, 65636.00, 'PHP', 'completed', NULL, '2026-09-04 15:30:00', '2026-10-09 12:00:00'),
(76, 'XYV-BKG-2026-00076', 23, 11, 38, 'Lorenzo', 'Alvarez', 'lorenzo.alvarez@example.com', '+639956472587', '2026-09-16', '2026-09-19', 3, 1, 13250.00, 39750.00, 0.00, 4770.00, 3975.00, 48495.00, 'PHP', 'completed', 'Traveling with a young child; a baby cot would be helpful.', '2026-09-09 17:35:00', '2026-09-19 12:00:00'),
(77, 'XYV-BKG-2026-00077', 33, 1, 4, 'Adrian', 'Estrada', 'adrian.estrada@example.com', '+639182911028', '2026-11-21', '2026-11-22', 1, 1, 7700.00, 7700.00, 0.00, 924.00, 770.00, 9394.00, 'PHP', 'confirmed', NULL, '2026-09-13 20:43:00', '2026-09-13 20:53:00'),
(78, 'XYV-BKG-2026-00078', 13, 7, 24, 'Joseph', 'Garcia', 'joseph.garcia@example.com', '+639951475136', '2026-09-30', '2026-10-04', 6, 1, 9200.00, 36800.00, 0.00, 4416.00, 3680.00, 44896.00, 'PHP', 'completed', 'Please prepare a vegetarian breakfast option.', '2026-09-16 10:44:00', '2026-10-04 12:00:00'),
(79, 'XYV-BKG-2026-00079', 26, 2, 5, 'Trisha', 'Manalo', 'trisha.manalo@example.com', '+639668939494', '2026-10-16', '2026-10-18', 1, 1, 4800.00, 9600.00, 0.00, 1152.00, 960.00, 11712.00, 'PHP', 'confirmed', NULL, '2026-09-22 14:10:00', '2026-09-22 14:21:00'),
(80, 'XYV-BKG-2026-00080', 37, 12, 41, 'Francis', 'Tolentino', 'francis.tolentino@example.com', '+639964585003', '2026-10-19', '2026-10-22', 2, 1, 3600.00, 10800.00, 0.00, 1296.00, 1080.00, 13176.00, 'PHP', 'confirmed', NULL, '2026-09-24 13:51:00', '2026-09-24 13:57:00'),
(81, 'XYV-BKG-2026-00081', 40, 18, 65, 'Charmaine', 'De Guzman', 'charmaine.deguzman@example.com', '+639966021458', '2026-12-09', '2026-12-11', 1, 1, 4300.00, 8600.00, 0.00, 1032.00, 860.00, 10492.00, 'PHP', 'cancelled', 'Requesting a room with a king bed if available.', '2026-09-26 12:48:00', '2026-10-03 15:43:23'),
(82, 'XYV-BKG-2026-00082', 23, 15, 54, 'Lorenzo', 'Alvarez', 'lorenzo.alvarez@example.com', '+639956472587', '2026-11-09', '2026-11-14', 2, 1, 4600.00, 23000.00, 0.00, 2760.00, 2300.00, 28060.00, 'PHP', 'confirmed', NULL, '2026-10-01 20:52:00', '2026-10-01 21:08:00'),
(83, 'XYV-BKG-2026-00083', 24, 9, 29, 'Camille', 'Soriano', 'camille.soriano@example.com', '+639964133017', '2026-10-11', '2026-10-16', 1, 1, 6500.00, 32500.00, 0.00, 3900.00, 3250.00, 39650.00, 'PHP', 'completed', 'Arriving after 10:00 PM.', '2026-10-02 18:14:00', '2026-10-16 12:00:00'),
(84, 'XYV-BKG-2026-00084', 40, 13, 45, 'Charmaine', 'De Guzman', 'charmaine.deguzman@example.com', '+639966021458', '2026-12-12', '2026-12-15', 2, 1, 7500.00, 22500.00, 0.00, 2700.00, 2250.00, 27450.00, 'PHP', 'confirmed', NULL, '2026-10-05 08:59:00', '2026-10-05 09:23:00'),
(85, 'XYV-BKG-2026-00085', 36, 12, 41, 'Katherine', 'Sy', 'katherine.sy@example.com', '+639668764529', '2026-10-18', '2026-10-22', 2, 1, 3600.00, 14400.00, 0.00, 1728.00, 1440.00, 17568.00, 'PHP', 'confirmed', NULL, '2026-10-05 21:29:00', '2026-10-05 21:51:00'),
(86, 'XYV-BKG-2026-00086', 38, 12, 41, 'Mariel', 'Guevarra', 'mariel.guevarra@example.com', '+639962653845', '2026-12-21', '2026-12-23', 1, 1, 3600.00, 7200.00, 0.00, 864.00, 720.00, 8784.00, 'PHP', 'confirmed', 'Prefer a room on a higher floor.', '2026-10-12 22:22:00', '2026-10-12 22:45:00'),
(87, 'XYV-BKG-2026-00087', 32, 12, 42, 'Sofia', 'Padilla', 'sofia.padilla@example.com', '+639184659085', '2026-11-11', '2026-11-17', 1, 1, 4850.00, 29100.00, 0.00, 3492.00, 2910.00, 35502.00, 'PHP', 'confirmed', NULL, '2026-10-15 12:00:00', '2026-10-15 12:21:00'),
(88, 'XYV-BKG-2026-00088', 11, 10, 36, 'Marco', 'Reyes', 'marco.reyes@example.com', '+639967534778', '2026-12-09', '2026-12-14', 5, 1, 14550.00, 72750.00, 0.00, 8730.00, 7275.00, 88755.00, 'PHP', 'confirmed', NULL, '2026-10-15 22:00:00', '2026-10-15 22:10:00'),
(89, 'XYV-BKG-2026-00089', 18, 10, 35, 'Isabella', 'Domingo', 'isabella.domingo@example.com', '+639765008754', '2026-11-11', '2026-11-13', 2, 1, 10400.00, 20800.00, 0.00, 2496.00, 2080.00, 25376.00, 'PHP', 'pending', 'Early check-in if the room is available, please.', '2026-10-16 03:00:00', '2026-10-16 03:25:00'),
(90, 'XYV-BKG-2026-00090', NULL, 3, 10, 'Liza', 'Evangelista', 'liza.evangelista@example.com', '+639990363454', '2026-12-11', '2026-12-12', 1, 1, 7300.00, 7300.00, 0.00, 876.00, 730.00, 8906.00, 'PHP', 'pending', 'Requesting a room with a king bed if available.', '2026-10-16 07:00:00', '2026-10-16 07:15:00'),
(91, 'XYV-BKG-2026-00091', 21, 1, 3, 'Rafael', 'Fernandez', 'rafael.fernandez@example.com', '+639557028382', '2026-12-09', '2026-12-12', 3, 1, 5450.00, 16350.00, 0.00, 1962.00, 1635.00, 19947.00, 'PHP', 'confirmed', 'Late check-out requested if possible.', '2026-10-16 16:00:00', '2026-10-16 16:15:00'),
(92, 'XYV-BKG-2026-00092', 27, 11, 37, 'Dominic', 'Flores', 'dominic.flores@example.com', '+639661621052', '2026-10-26', '2026-10-31', 2, 1, 9800.00, 49000.00, 0.00, 5880.00, 4900.00, 59780.00, 'PHP', 'confirmed', 'Please prepare a vegetarian breakfast option.', '2026-10-16 17:55:00', '2026-10-16 18:07:00'),
(93, 'XYV-BKG-2026-00093', 17, 12, 41, 'Paolo', 'Castillo', 'paolo.castillo@example.com', '+639966628360', '2026-12-08', '2026-12-10', 1, 1, 3600.00, 7200.00, 0.00, 864.00, 720.00, 8784.00, 'PHP', 'confirmed', 'Please arrange an airport pick-up. Flight details to follow.', '2026-10-17 02:00:00', '2026-10-17 02:24:00'),
(94, 'XYV-BKG-2026-00094', 28, 3, 9, 'Alyssa', 'Rivera', 'alyssa.rivera@example.com', '+639991452441', '2026-11-14', '2026-11-17', 2, 1, 5600.00, 16800.00, 0.00, 2016.00, 1680.00, 20496.00, 'PHP', 'pending', NULL, '2026-10-17 04:00:00', '2026-10-17 04:03:00'),
(95, 'XYV-BKG-2026-00095', 37, 18, 65, 'Francis', 'Tolentino', 'francis.tolentino@example.com', '+639964585003', '2026-11-23', '2026-11-27', 3, 1, 4300.00, 17200.00, 0.00, 2064.00, 1720.00, 20984.00, 'PHP', 'pending', 'Celebrating an anniversary; a quiet room would be appreciated.', '2026-10-17 05:00:00', '2026-10-17 05:22:00'),
(96, 'XYV-BKG-2026-00096', 29, 5, 16, 'Kenneth', 'Go', 'kenneth.go@example.com', '+639170130411', '2026-12-02', '2026-12-04', 1, 1, 4200.00, 8400.00, 0.00, 1008.00, 840.00, 10248.00, 'PHP', 'confirmed', 'Prefer a room on a higher floor.', '2026-10-17 05:00:00', '2026-10-17 05:21:00');

-- --------------------------------------------------------

--
-- Table structure for table `content_pages`
--

CREATE TABLE `content_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(40) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content_type` varchar(80) NOT NULL,
  `body` longtext NOT NULL,
  `status` enum('draft','in_review','scheduled','published','archived') NOT NULL DEFAULT 'draft',
  `publish_at` datetime DEFAULT NULL,
  `author_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(160) NOT NULL,
  `name` varchar(160) NOT NULL,
  `country` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(2048) DEFAULT NULL,
  `status` enum('draft','in_review','published','archived') NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`id`, `slug`, `name`, `country`, `description`, `image_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'manila-philippines', 'Metro Manila', 'Philippines', 'The Philippine capital region blends Spanish-colonial Intramuros, national museums, Makati and Bonifacio Global City business districts, and a diverse food scene.', 'https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&w=900&q=80', 'published', '2025-04-01 09:00:00', '2025-04-01 09:00:00'),
(2, 'baguio-philippines', 'Baguio City', 'Philippines', 'The Summer Capital of the Philippines is known for cool mountain weather, pine-lined roads, Burnham Park, the public market, and Cordillera arts and culture.', 'https://images.unsplash.com/photo-1510798831971-661eb04b3739?auto=format&fit=crop&w=900&q=80', 'published', '2025-04-01 09:00:00', '2025-04-01 09:00:00'),
(3, 'tagaytay-philippines', 'Tagaytay', 'Philippines', 'A highland getaway south of Manila, popular for Taal Volcano views, garden cafés, farm-to-table dining, and cool year-round breezes.', 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80', 'published', '2025-04-01 09:00:00', '2025-04-01 09:00:00'),
(4, 'cebu-philippines', 'Cebu', 'Philippines', 'The gateway to Central Visayas, offering heritage landmarks, Mactan Island beaches, canyoneering, whale shark encounters, and day trips across the province.', 'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?auto=format&fit=crop&w=900&q=80', 'published', '2025-04-01 09:00:00', '2025-04-01 09:00:00'),
(5, 'boracay-philippines', 'Boracay', 'Philippines', 'A world-famous island in Aklan, celebrated for the powder-white sand of White Beach, sailing at sunset, water sports, and lively dining along the shore.', 'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=900&q=80', 'published', '2025-04-01 09:00:00', '2025-04-01 09:00:00'),
(6, 'palawan-philippines', 'Palawan', 'Philippines', 'Home to the Puerto Princesa Underground River, El Nido\'s Bacuit Bay and Coron\'s wreck-diving waters, with limestone cliffs, lagoons, and island-hopping tours.', 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=900&q=80', 'published', '2025-04-01 09:00:00', '2025-04-01 09:00:00'),
(7, 'siargao-philippines', 'Siargao', 'Philippines', 'The surfing capital of the Philippines, with palm-fringed island roads, Cloud 9 breaks, tidal pools, and relaxed island living around General Luna.', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=80', 'published', '2025-04-01 09:00:00', '2025-04-01 09:00:00'),
(8, 'bohol-philippines', 'Bohol', 'Philippines', 'Known for the Chocolate Hills, Loboc River cruises, tarsier sanctuaries, and the white-sand beaches and dive sites of Panglao Island.', 'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=900&q=80', 'published', '2025-04-01 09:00:00', '2025-04-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(60) NOT NULL,
  `payment_type` enum('charge','refund') NOT NULL DEFAULT 'charge',
  `provider` varchar(80) DEFAULT NULL,
  `provider_transaction_id` varchar(190) DEFAULT NULL,
  `provider_checkout_url` varchar(500) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PHP',
  `status` enum('pending','authorized','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `reference`, `payment_type`, `provider`, `provider_transaction_id`, `provider_checkout_url`, `amount`, `currency`, `status`, `processed_at`, `created_at`) VALUES
(1, 1, 'XYV-PAY-2025-00001', 'charge', 'Pay at Property', NULL, NULL, 12444.00, 'PHP', 'completed', '2026-01-16 14:00:00', '2025-11-15 13:23:00'),
(2, 2, 'XYV-PAY-2025-00002', 'charge', 'GCash', 'GC016848761715', NULL, 24156.00, 'PHP', 'completed', '2025-11-28 16:42:00', '2025-11-28 16:30:00'),
(3, 3, 'XYV-PAY-2025-00003', 'charge', 'GCash', 'GC614381585999', NULL, 22875.00, 'PHP', 'completed', '2025-12-07 11:46:00', '2025-12-07 11:38:00'),
(4, 4, 'XYV-PAY-2025-00004', 'charge', 'GCash', 'GC923348193427', NULL, 30500.00, 'PHP', 'completed', '2025-12-10 21:20:00', '2025-12-10 21:13:00'),
(5, 5, 'XYV-PAY-2025-00005', 'charge', 'Pay at Property', NULL, NULL, 3904.00, 'PHP', 'cancelled', NULL, '2025-12-16 21:06:00'),
(6, 6, 'XYV-PAY-2025-00006', 'charge', 'Credit/Debit Card', 'ch_4u953ft68ykwakpt', NULL, 5856.00, 'PHP', 'completed', '2025-12-26 23:07:00', '2025-12-26 22:58:00'),
(7, 7, 'XYV-PAY-2025-00007', 'charge', 'Pay at Property', NULL, NULL, 44408.00, 'PHP', 'completed', '2026-02-10 14:00:00', '2025-12-27 11:19:00'),
(8, 8, 'XYV-PAY-2025-00008', 'charge', 'GCash', 'GC345827183377', NULL, 3904.00, 'PHP', 'completed', '2025-12-30 21:51:00', '2025-12-30 21:49:00'),
(9, 9, 'XYV-PAY-2026-00009', 'charge', 'Pay at Property', NULL, NULL, 22692.00, 'PHP', 'completed', '2026-02-26 14:00:00', '2026-01-01 09:30:00'),
(10, 10, 'XYV-PAY-2026-00010', 'charge', 'BDO Online Banking', 'BDO08410530012', NULL, 36600.00, 'PHP', 'completed', '2026-01-05 20:45:00', '2026-01-05 20:42:00'),
(11, 11, 'XYV-PAY-2026-00011', 'charge', 'GCash', 'GC788029307848', NULL, 164090.00, 'PHP', 'completed', '2026-01-15 13:46:00', '2026-01-15 13:41:00'),
(12, 12, 'XYV-PAY-2026-00012', 'charge', 'Credit/Debit Card', 'ch_cwf7jhnpub5x9adh', NULL, 79300.00, 'PHP', 'completed', '2026-01-17 19:22:00', '2026-01-17 19:14:00'),
(13, 13, 'XYV-PAY-2026-00013', 'charge', 'GCash', 'GC313705168892', NULL, 37088.00, 'PHP', 'completed', '2026-01-18 15:55:00', '2026-01-18 15:53:00'),
(14, 14, 'XYV-PAY-2026-00014', 'charge', 'BDO Online Banking', 'BDO31171978862', NULL, 23790.00, 'PHP', 'completed', '2026-01-29 15:39:00', '2026-01-29 15:35:00'),
(15, 15, 'XYV-PAY-2026-00015', 'charge', 'Maya', 'MYA64042D85A4', NULL, 26352.00, 'PHP', 'completed', '2026-02-07 22:02:00', '2026-02-07 21:54:00'),
(16, 16, 'XYV-PAY-2026-00016', 'charge', 'Maya', 'MYADE733BA546', NULL, 44530.00, 'PHP', 'completed', '2026-02-09 21:36:00', '2026-02-09 21:31:00'),
(17, 17, 'XYV-PAY-2026-00017', 'charge', 'GCash', 'GC046259863840', NULL, 9760.00, 'PHP', 'completed', '2026-02-10 17:05:00', '2026-02-10 16:58:00'),
(18, 18, 'XYV-PAY-2026-00018', 'charge', 'Maya', 'MYA4D4DE7063D', NULL, 17080.00, 'PHP', 'completed', '2026-02-18 10:55:00', '2026-02-18 10:50:00'),
(19, 19, 'XYV-PAY-2026-00019', 'charge', 'BDO Online Banking', 'BDO34305168511', NULL, 26352.00, 'PHP', 'completed', '2026-02-28 07:08:00', '2026-02-28 07:01:00'),
(20, 20, 'XYV-PAY-2026-00020', 'charge', 'Credit/Debit Card', 'ch_wca3t3pafsd55ac7', NULL, 73932.00, 'PHP', 'completed', '2026-03-10 10:34:00', '2026-03-10 10:22:00'),
(21, 21, 'XYV-PAY-2026-00021', 'charge', 'GCash', 'GC391828729326', NULL, 34160.00, 'PHP', 'completed', '2026-03-10 17:35:00', '2026-03-10 17:23:00'),
(22, 22, 'XYV-PAY-2026-00022', 'charge', 'GCash', 'GC398724793131', NULL, 33184.00, 'PHP', 'completed', '2026-03-11 12:45:00', '2026-03-11 12:33:00'),
(23, 23, 'XYV-PAY-2026-00023', 'charge', 'GCash', 'GC759184059182', NULL, 14152.00, 'PHP', 'completed', '2026-03-13 14:53:00', '2026-03-13 14:46:00'),
(24, 24, 'XYV-PAY-2026-00024', 'charge', 'Credit/Debit Card', 'ch_d2wefsfnkufdygmq', NULL, 16592.00, 'PHP', 'completed', '2026-03-20 14:57:00', '2026-03-20 14:46:00'),
(25, 24, 'XYV-PAY-2026-00025', 'refund', 'Credit/Debit Card', 'ch_y6ja5cn8jb7qnwjz', NULL, 16592.00, 'PHP', 'completed', '2026-03-23 20:09:45', '2026-03-23 20:09:45'),
(26, 25, 'XYV-PAY-2026-00026', 'charge', 'GCash', 'GC251210227307', NULL, 36844.00, 'PHP', 'completed', '2026-03-21 12:22:00', '2026-03-21 12:11:00'),
(27, 26, 'XYV-PAY-2026-00027', 'charge', 'GCash', 'GC794900598799', NULL, 40992.00, 'PHP', 'completed', '2026-03-21 15:29:00', '2026-03-21 15:18:00'),
(28, 26, 'XYV-PAY-2026-00028', 'refund', 'GCash', 'GC339936667012', NULL, 40992.00, 'PHP', 'completed', '2026-03-26 09:32:49', '2026-03-26 09:32:49'),
(29, 27, 'XYV-PAY-2026-00029', 'charge', 'Credit/Debit Card', 'ch_v485us4zjvhckgwg', NULL, 5063.00, 'PHP', 'completed', '2026-03-24 07:39:00', '2026-03-24 07:28:00'),
(30, 28, 'XYV-PAY-2026-00030', 'charge', 'GCash', 'GC142705957353', NULL, 15616.00, 'PHP', 'completed', '2026-03-26 17:51:00', '2026-03-26 17:46:00'),
(31, 29, 'XYV-PAY-2026-00031', 'charge', 'GCash', 'GC059726324347', NULL, 5856.00, 'PHP', 'completed', '2026-03-26 23:01:00', '2026-03-26 22:54:00'),
(32, 30, 'XYV-PAY-2026-00032', 'charge', 'Maya', 'MYAE30476AC86', NULL, 54900.00, 'PHP', 'completed', '2026-04-11 16:12:00', '2026-04-11 16:10:00'),
(33, 30, 'XYV-PAY-2026-00033', 'refund', 'Maya', 'MYA84CB161DAA', NULL, 54900.00, 'PHP', 'completed', '2026-04-24 15:27:05', '2026-04-24 15:27:05'),
(34, 31, 'XYV-PAY-2026-00034', 'charge', 'Pay at Property', NULL, NULL, 24644.00, 'PHP', 'completed', '2026-06-03 14:00:00', '2026-04-12 13:47:00'),
(35, 32, 'XYV-PAY-2026-00035', 'charge', 'BDO Online Banking', 'BDO91286897910', NULL, 13176.00, 'PHP', 'completed', '2026-04-17 13:34:00', '2026-04-17 13:27:00'),
(36, 33, 'XYV-PAY-2026-00036', 'charge', 'Pay at Property', NULL, NULL, 17812.00, 'PHP', 'cancelled', NULL, '2026-04-20 16:33:00'),
(37, 34, 'XYV-PAY-2026-00037', 'charge', 'Credit/Debit Card', 'ch_kfe98f4td94aenw2', NULL, 23424.00, 'PHP', 'completed', '2026-04-28 10:24:00', '2026-04-28 10:17:00'),
(38, 35, 'XYV-PAY-2026-00038', 'charge', 'Maya', 'MYA688996A77D', NULL, 4636.00, 'PHP', 'completed', '2026-05-03 07:31:00', '2026-05-03 07:23:00'),
(39, 36, 'XYV-PAY-2026-00039', 'charge', 'Pay at Property', NULL, NULL, 9150.00, 'PHP', 'cancelled', NULL, '2026-05-05 07:18:00'),
(40, 37, 'XYV-PAY-2026-00040', 'charge', 'BDO Online Banking', 'BDO97667740543', NULL, 23424.00, 'PHP', 'completed', '2026-05-05 13:04:00', '2026-05-05 13:00:00'),
(41, 38, 'XYV-PAY-2026-00041', 'charge', 'Maya', 'MYA8E64D21122', NULL, 34770.00, 'PHP', 'completed', '2026-05-11 19:04:00', '2026-05-11 18:59:00'),
(42, 39, 'XYV-PAY-2026-00042', 'charge', 'GCash', 'GC497635098559', NULL, 27633.00, 'PHP', 'completed', '2026-05-14 18:53:00', '2026-05-14 18:45:00'),
(43, 40, 'XYV-PAY-2026-00043', 'charge', 'Credit/Debit Card', 'ch_kdzg5dussfp239rk', NULL, 49227.00, 'PHP', 'completed', '2026-05-15 11:22:00', '2026-05-15 11:17:00'),
(44, 41, 'XYV-PAY-2026-00044', 'charge', 'Pay at Property', NULL, NULL, 24644.00, 'PHP', 'completed', '2026-06-20 14:00:00', '2026-05-15 15:00:00'),
(45, 42, 'XYV-PAY-2026-00045', 'charge', 'Credit/Debit Card', 'ch_rhzj63k4upfe96gd', NULL, 65880.00, 'PHP', 'completed', '2026-05-15 21:44:00', '2026-05-15 21:36:00'),
(46, 43, 'XYV-PAY-2026-00046', 'charge', 'Pay at Property', NULL, NULL, 35502.00, 'PHP', 'completed', '2026-07-27 14:00:00', '2026-05-19 10:26:00'),
(47, 44, 'XYV-PAY-2026-00047', 'charge', 'Pay at Property', NULL, NULL, 111020.00, 'PHP', 'completed', '2026-08-01 14:00:00', '2026-05-21 14:27:00'),
(48, 45, 'XYV-PAY-2026-00048', 'charge', 'Maya', 'MYA5F0B4251DA', NULL, 29280.00, 'PHP', 'completed', '2026-05-24 14:41:00', '2026-05-24 14:38:00'),
(49, 46, 'XYV-PAY-2026-00049', 'charge', 'GCash', 'GC932879180718', NULL, 24888.00, 'PHP', 'completed', '2026-05-26 12:48:00', '2026-05-26 12:38:00'),
(50, 47, 'XYV-PAY-2026-00050', 'charge', 'GCash', 'GC509643389411', NULL, 36600.00, 'PHP', 'completed', '2026-06-04 11:38:00', '2026-06-04 11:27:00'),
(51, 48, 'XYV-PAY-2026-00051', 'charge', 'Maya', 'MYA0C2B4C8838', NULL, 19032.00, 'PHP', 'completed', '2026-06-06 12:26:00', '2026-06-06 12:22:00'),
(52, 49, 'XYV-PAY-2026-00052', 'charge', 'Maya', 'MYA2C53583474', NULL, 19520.00, 'PHP', 'completed', '2026-06-06 12:43:00', '2026-06-06 12:33:00'),
(53, 49, 'XYV-PAY-2026-00053', 'refund', 'Maya', 'MYA1F9ECD9392', NULL, 19520.00, 'PHP', 'completed', '2026-06-09 19:59:38', '2026-06-09 19:59:38'),
(54, 50, 'XYV-PAY-2026-00054', 'charge', 'GCash', 'GC714664023840', NULL, 28304.00, 'PHP', 'completed', '2026-06-10 15:29:00', '2026-06-10 15:28:00'),
(55, 51, 'XYV-PAY-2026-00055', 'charge', 'Pay at Property', NULL, NULL, 8296.00, 'PHP', 'completed', '2026-06-28 14:00:00', '2026-06-13 10:52:00'),
(56, 52, 'XYV-PAY-2026-00056', 'charge', 'GCash', 'GC841355913320', NULL, 20252.00, 'PHP', 'completed', '2026-06-14 14:34:00', '2026-06-14 14:22:00'),
(57, 53, 'XYV-PAY-2026-00057', 'charge', 'GCash', 'GC559424834945', NULL, 16836.00, 'PHP', 'completed', '2026-07-02 19:01:00', '2026-07-02 19:00:00'),
(58, 54, 'XYV-PAY-2026-00058', 'charge', 'Maya', 'MYA1A25FF21F5', NULL, 21228.00, 'PHP', 'completed', '2026-07-06 19:03:00', '2026-07-06 18:56:00'),
(59, 55, 'XYV-PAY-2026-00059', 'charge', 'BDO Online Banking', 'BDO09755121509', NULL, 23058.00, 'PHP', 'completed', '2026-07-08 13:57:00', '2026-07-08 13:45:00'),
(60, 56, 'XYV-PAY-2026-00060', 'charge', 'GCash', 'GC033439959245', NULL, 7320.00, 'PHP', 'completed', '2026-07-09 19:29:00', '2026-07-09 19:22:00'),
(61, 57, 'XYV-PAY-2026-00061', 'charge', 'Maya', 'MYABB3426F9F6', NULL, 3904.00, 'PHP', 'completed', '2026-07-10 09:11:00', '2026-07-10 09:03:00'),
(62, 58, 'XYV-PAY-2026-00062', 'charge', 'Maya', 'MYA5473A7CC6A', NULL, 33184.00, 'PHP', 'completed', '2026-07-10 21:01:00', '2026-07-10 20:59:00'),
(63, 59, 'XYV-PAY-2026-00063', 'charge', 'Pay at Property', NULL, NULL, 18300.00, 'PHP', 'completed', '2026-09-23 14:00:00', '2026-07-18 18:53:00'),
(64, 60, 'XYV-PAY-2026-00064', 'charge', 'GCash', 'GC307653145057', NULL, 26718.00, 'PHP', 'completed', '2026-07-19 08:34:00', '2026-07-19 08:33:00'),
(65, 61, 'XYV-PAY-2026-00065', 'charge', 'Credit/Debit Card', 'ch_cf2semm9qdksz4bc', NULL, 82960.00, 'PHP', 'completed', '2026-07-22 12:37:00', '2026-07-22 12:31:00'),
(66, 62, 'XYV-PAY-2026-00066', 'charge', 'Maya', 'MYA76E916AC14', NULL, 38064.00, 'PHP', 'completed', '2026-07-22 22:23:00', '2026-07-22 22:16:00'),
(67, 63, 'XYV-PAY-2026-00067', 'charge', 'Credit/Debit Card', 'ch_47ry7rv6enwahd3k', NULL, 85888.00, 'PHP', 'completed', '2026-07-23 16:29:00', '2026-07-23 16:25:00'),
(68, 64, 'XYV-PAY-2026-00068', 'charge', 'GCash', 'GC176993589829', NULL, 30561.00, 'PHP', 'completed', '2026-07-25 20:38:00', '2026-07-25 20:29:00'),
(69, 65, 'XYV-PAY-2026-00069', 'charge', 'Maya', 'MYA55BE40C761', NULL, 23424.00, 'PHP', 'completed', '2026-07-30 09:54:00', '2026-07-30 09:47:00'),
(70, 66, 'XYV-PAY-2026-00070', 'charge', 'GCash', 'GC913151464872', NULL, 40992.00, 'PHP', 'completed', '2026-08-06 16:04:00', '2026-08-06 16:01:00'),
(71, 67, 'XYV-PAY-2026-00071', 'charge', 'Credit/Debit Card', 'ch_xw66ydfx8w92c6q4', NULL, 101504.00, 'PHP', 'completed', '2026-08-12 22:58:00', '2026-08-12 22:49:00'),
(72, 67, 'XYV-PAY-2026-00072', 'refund', 'Credit/Debit Card', 'ch_ggcq3ptv3n7dyqf7', NULL, 101504.00, 'PHP', 'completed', '2026-08-22 22:12:51', '2026-08-22 22:12:51'),
(73, 68, 'XYV-PAY-2026-00073', 'charge', 'Maya', 'MYA35277300D9', NULL, 47824.00, 'PHP', 'completed', '2026-08-13 15:25:00', '2026-08-13 15:24:00'),
(74, 69, 'XYV-PAY-2026-00074', 'charge', 'Credit/Debit Card', 'ch_qsscyadega8gq39s', NULL, 17751.00, 'PHP', 'completed', '2026-08-18 07:12:00', '2026-08-18 07:06:00'),
(75, 70, 'XYV-PAY-2026-00075', 'charge', 'GCash', 'GC448978916382', NULL, 16592.00, 'PHP', 'completed', '2026-08-21 20:36:00', '2026-08-21 20:32:00'),
(76, 71, 'XYV-PAY-2026-00076', 'charge', 'GCash', 'GC073148099090', NULL, 27450.00, 'PHP', 'completed', '2026-08-23 20:11:00', '2026-08-23 20:02:00'),
(77, 72, 'XYV-PAY-2026-00077', 'charge', 'Pay at Property', NULL, NULL, 15372.00, 'PHP', 'completed', '2026-10-10 14:00:00', '2026-08-24 11:10:00'),
(78, 73, 'XYV-PAY-2026-00078', 'charge', 'Maya', 'MYA1F6B41A81B', NULL, 48495.00, 'PHP', 'completed', '2026-08-25 11:17:00', '2026-08-25 11:10:00'),
(79, 73, 'XYV-PAY-2026-00079', 'refund', 'Maya', 'MYAAD23C5A88A', NULL, 48495.00, 'PHP', 'completed', '2026-09-10 16:28:12', '2026-09-10 16:28:12'),
(80, 74, 'XYV-PAY-2026-00080', 'charge', 'GCash', 'GC825519126939', NULL, 28060.00, 'PHP', 'completed', '2026-09-03 17:01:00', '2026-09-03 16:49:00'),
(81, 75, 'XYV-PAY-2026-00081', 'charge', 'GCash', 'GC344195887477', NULL, 65636.00, 'PHP', 'completed', '2026-09-04 15:34:00', '2026-09-04 15:30:00'),
(82, 76, 'XYV-PAY-2026-00082', 'charge', 'Credit/Debit Card', 'ch_edx23psewjdkpf65', NULL, 48495.00, 'PHP', 'completed', '2026-09-09 17:41:00', '2026-09-09 17:35:00'),
(83, 77, 'XYV-PAY-2026-00083', 'charge', 'Maya', 'MYAAB4150C408', NULL, 9394.00, 'PHP', 'completed', '2026-09-13 20:52:00', '2026-09-13 20:43:00'),
(84, 78, 'XYV-PAY-2026-00084', 'charge', 'Pay at Property', NULL, NULL, 44896.00, 'PHP', 'completed', '2026-09-30 14:00:00', '2026-09-16 10:44:00'),
(85, 79, 'XYV-PAY-2026-00085', 'charge', 'BDO Online Banking', 'BDO36895402555', NULL, 11712.00, 'PHP', 'completed', '2026-09-22 14:20:00', '2026-09-22 14:10:00'),
(86, 80, 'XYV-PAY-2026-00086', 'charge', 'Pay at Property', NULL, NULL, 13176.00, 'PHP', 'pending', NULL, '2026-09-24 13:51:00'),
(87, 81, 'XYV-PAY-2026-00087', 'charge', 'GCash', 'GC759062805873', NULL, 10492.00, 'PHP', 'completed', '2026-09-26 12:53:00', '2026-09-26 12:48:00'),
(88, 81, 'XYV-PAY-2026-00088', 'refund', 'GCash', 'GC063202042324', NULL, 10492.00, 'PHP', 'completed', '2026-10-04 15:43:23', '2026-10-04 15:43:23'),
(89, 82, 'XYV-PAY-2026-00089', 'charge', 'GCash', 'GC339701054909', NULL, 28060.00, 'PHP', 'completed', '2026-10-01 20:56:00', '2026-10-01 20:52:00'),
(90, 83, 'XYV-PAY-2026-00090', 'charge', 'Credit/Debit Card', 'ch_zxmgv9dcer2fkkvu', NULL, 39650.00, 'PHP', 'completed', '2026-10-02 18:15:00', '2026-10-02 18:14:00'),
(91, 84, 'XYV-PAY-2026-00091', 'charge', 'BDO Online Banking', 'BDO05067470556', NULL, 27450.00, 'PHP', 'completed', '2026-10-05 09:03:00', '2026-10-05 08:59:00'),
(92, 85, 'XYV-PAY-2026-00092', 'charge', 'Maya', 'MYADC124BCAED', NULL, 17568.00, 'PHP', 'completed', '2026-10-05 21:36:00', '2026-10-05 21:29:00'),
(93, 86, 'XYV-PAY-2026-00093', 'charge', 'Credit/Debit Card', 'ch_vwrjk7db49d44mny', NULL, 8784.00, 'PHP', 'completed', '2026-10-12 22:32:00', '2026-10-12 22:22:00'),
(94, 87, 'XYV-PAY-2026-00094', 'charge', 'GCash', 'GC292221843449', NULL, 35502.00, 'PHP', 'completed', '2026-10-15 12:09:00', '2026-10-15 12:00:00'),
(95, 88, 'XYV-PAY-2026-00095', 'charge', 'Credit/Debit Card', 'ch_dzsmp8zbz9bmf6ua', NULL, 88755.00, 'PHP', 'completed', '2026-10-15 22:06:00', '2026-10-15 22:00:00'),
(96, 89, 'XYV-PAY-2026-00096', 'charge', 'Pay at Property', NULL, NULL, 25376.00, 'PHP', 'pending', NULL, '2026-10-16 03:00:00'),
(97, 90, 'XYV-PAY-2026-00097', 'charge', 'GCash', NULL, NULL, 8906.00, 'PHP', 'pending', NULL, '2026-10-16 07:00:00'),
(98, 91, 'XYV-PAY-2026-00098', 'charge', 'Pay at Property', NULL, NULL, 19947.00, 'PHP', 'pending', NULL, '2026-10-16 16:00:00'),
(99, 92, 'XYV-PAY-2026-00099', 'charge', 'Pay at Property', NULL, NULL, 59780.00, 'PHP', 'pending', NULL, '2026-10-16 17:55:00'),
(100, 93, 'XYV-PAY-2026-00100', 'charge', 'GCash', 'GC017614807497', NULL, 8784.00, 'PHP', 'completed', '2026-10-17 02:04:00', '2026-10-17 02:00:00'),
(101, 94, 'XYV-PAY-2026-00101', 'charge', 'GCash', NULL, NULL, 20496.00, 'PHP', 'pending', NULL, '2026-10-17 04:00:00'),
(102, 95, 'XYV-PAY-2026-00102', 'charge', 'BDO Online Banking', NULL, NULL, 20984.00, 'PHP', 'pending', NULL, '2026-10-17 05:00:00'),
(103, 96, 'XYV-PAY-2026-00103', 'charge', 'Maya', 'MYA8427658D42', NULL, 10248.00, 'PHP', 'completed', '2026-10-17 05:04:00', '2026-10-17 05:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `external_code` varchar(40) DEFAULT NULL,
  `owner_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `destination_id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(180) NOT NULL,
  `name` varchar(180) NOT NULL,
  `city` varchar(120) NOT NULL,
  `country` varchar(100) NOT NULL,
  `property_type` varchar(80) NOT NULL,
  `description` text NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `base_nightly_rate` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PHP',
  `status` enum('draft','in_review','published','rejected','archived') NOT NULL DEFAULT 'draft',
  `is_sample` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `external_code`, `owner_user_id`, `destination_id`, `slug`, `name`, `city`, `country`, `property_type`, `description`, `latitude`, `longitude`, `base_nightly_rate`, `currency`, `status`, `is_sample`, `created_at`, `updated_at`) VALUES
(1, 'PRP-MNL-001', 4, 1, 'intramuros-city-hotel-manila', 'Intramuros City Hotel', 'Manila', 'Philippines', 'Heritage hotel', 'A comfortable city hotel a short walk from Fort Santiago, San Agustin Church, and the museums of historic Intramuros. Well suited to cultural travelers and short Manila stays.', 14.5917000, 120.9726000, 3200.00, 'PHP', 'published', 1, '2025-05-04 10:00:00', '2025-05-04 10:00:00'),
(2, 'PRP-MNL-002', 4, 1, 'makati-central-suites', 'Makati Central Suites', 'Makati', 'Philippines', 'Business hotel', 'A contemporary business hotel in the Makati central business district, close to Ayala Avenue offices, shopping centers, and fine dining. Offers meeting facilities and a fitness center.', 14.5547000, 121.0244000, 4800.00, 'PHP', 'published', 1, '2025-05-07 10:00:00', '2025-05-07 10:00:00'),
(3, 'PRP-MNL-003', 4, 1, 'bonifacio-garden-boutique-hotel', 'Bonifacio Garden Boutique Hotel', 'Taguig', 'Philippines', 'Boutique hotel', 'A design-led boutique hotel in Bonifacio Global City with landscaped courtyards, a rooftop pool, and easy access to High Street dining, galleries, and nightlife.', 14.5507000, 121.0460000, 5600.00, 'PHP', 'published', 1, '2025-05-10 10:00:00', '2025-05-10 10:00:00'),
(4, 'PRP-BAG-001', 5, 2, 'pine-breeze-lodge-baguio', 'Pine Breeze Lodge', 'Baguio City', 'Philippines', 'Mountain lodge', 'A relaxed mountain lodge with warm, cozy rooms and convenient access to Burnham Park, Session Road, and the Baguio public market.', 16.4107000, 120.5937000, 2900.00, 'PHP', 'published', 1, '2025-05-13 10:00:00', '2025-05-13 10:00:00'),
(5, 'PRP-BAG-002', 5, 2, 'pinewood-ridge-cabins-baguio', 'Pinewood Ridge Cabins', 'Baguio City', 'Philippines', 'Cabin resort', 'Private pine-forest cabins on a quiet ridge outside the city center, designed for families and groups who want fireplaces, outdoor dining areas, and cool mountain air.', 16.3902000, 120.5801000, 4200.00, 'PHP', 'published', 1, '2025-05-16 10:00:00', '2025-05-16 10:00:00'),
(6, 'PRP-TAG-001', 5, 3, 'taal-view-retreat-tagaytay', 'Taal View Retreat', 'Tagaytay', 'Philippines', 'Boutique hotel', 'A quiet highland retreat with inviting common areas, garden terraces, and easy access to Taal-view cafés and restaurants along the Tagaytay ridge.', 14.1070000, 120.9610000, 3800.00, 'PHP', 'published', 1, '2025-05-19 10:00:00', '2025-05-19 10:00:00'),
(7, 'PRP-TAG-002', 5, 3, 'highland-orchard-villas-tagaytay', 'Highland Orchard Villas', 'Tagaytay', 'Philippines', 'Villa resort', 'Standalone garden villas set among fruit trees, ideal for family gatherings and weekend escapes from Metro Manila. Each villa has a private terrace and kitchenette.', 14.1013000, 120.9402000, 4600.00, 'PHP', 'published', 1, '2025-05-22 10:00:00', '2025-05-22 10:00:00'),
(8, 'PRP-CEB-001', 6, 4, 'harborlight-stay-cebu', 'Harborlight Stay Cebu', 'Cebu City', 'Philippines', 'Business hotel', 'A practical base for city sightseeing, business trips, and day tours around Cebu, close to Colon Street, Fuente Osmeña Circle, and the Basilica del Santo Niño.', 10.3157000, 123.8854000, 3000.00, 'PHP', 'published', 1, '2025-05-25 10:00:00', '2025-05-25 10:00:00'),
(9, 'PRP-CEB-002', 6, 4, 'mactan-coral-beach-resort', 'Mactan Coral Beach Resort', 'Lapu-Lapu City', 'Philippines', 'Beach resort', 'A beachfront resort on Mactan Island with a lagoon-style pool, dive and island-hopping arrangements, and close proximity to Mactan-Cebu International Airport.', 10.3095000, 124.0016000, 6500.00, 'PHP', 'published', 1, '2025-05-28 10:00:00', '2025-05-28 10:00:00'),
(10, 'PRP-BOR-001', 7, 5, 'white-sand-inn-boracay', 'White Sand Inn', 'Boracay', 'Philippines', 'Beach hotel', 'A laid-back island stay within walking distance of White Beach Station 2, D\'Mall, restaurants, and water-sport operators.', 11.9674000, 121.9248000, 5200.00, 'PHP', 'published', 1, '2025-05-31 10:00:00', '2025-05-31 10:00:00'),
(11, 'PRP-BOR-002', 7, 5, 'sunset-cove-beach-resort-boracay', 'Sunset Cove Beach Resort', 'Boracay', 'Philippines', 'Beach resort', 'A beachfront resort on the quieter Station 1 side of White Beach, known for sunset views, an infinity pool, and direct sand access.', 11.9835000, 121.9183000, 9800.00, 'PHP', 'published', 1, '2025-06-03 10:00:00', '2025-06-03 10:00:00'),
(12, 'PRP-PAL-001', 8, 6, 'lagoon-garden-escape-palawan', 'Lagoon Garden Escape', 'Puerto Princesa', 'Philippines', 'Resort', 'A nature-inspired resort for guests planning Underground River visits, Honda Bay island trips, and outdoor activities, set within tropical gardens.', 9.7640000, 118.7480000, 3600.00, 'PHP', 'published', 1, '2025-06-06 10:00:00', '2025-06-06 10:00:00'),
(13, 'PRP-PAL-002', 8, 6, 'bacuit-bay-beach-resort-el-nido', 'Bacuit Bay Beach Resort', 'El Nido', 'Philippines', 'Beach resort', 'A beachfront resort in El Nido with views of the Bacuit Bay limestone cliffs and ready access to Island Hopping Tours A, B, C, and D.', 11.1784000, 119.3930000, 7500.00, 'PHP', 'published', 1, '2025-06-09 10:00:00', '2025-06-09 10:00:00'),
(14, 'PRP-PAL-003', 8, 6, 'coron-bayview-hotel-palawan', 'Coron Bayview Hotel', 'Coron', 'Philippines', 'Seaview hotel', 'A hillside hotel overlooking Coron Bay, a short ride from the town proper, with a view deck and easy booking for Kayangan Lake and wreck-diving tours.', 11.9986000, 120.2043000, 5400.00, 'PHP', 'published', 1, '2025-06-12 10:00:00', '2025-06-12 10:00:00'),
(15, 'PRP-SIA-001', 9, 7, 'siargao-palm-surf-lodge', 'Siargao Palm Surf Lodge', 'General Luna', 'Philippines', 'Surf lodge', 'A relaxed surf lodge among coconut palms in General Luna, minutes from the Cloud 9 boardwalk, with board storage and arranged surf lessons.', 9.7869000, 126.1581000, 3400.00, 'PHP', 'published', 1, '2025-06-15 10:00:00', '2025-06-15 10:00:00'),
(16, 'PRP-SIA-002', 9, 7, 'island-breeze-boutique-resort-siargao', 'Island Breeze Boutique Resort', 'General Luna', 'Philippines', 'Boutique resort', 'A small boutique resort with an open-air restaurant, a freshwater pool, and tropical-modern bungalows, close to the island\'s main surf breaks and cafés.', 9.7715000, 126.1623000, 6800.00, 'PHP', 'published', 1, '2025-06-18 10:00:00', '2025-06-18 10:00:00'),
(17, 'PRP-BOH-001', 6, 8, 'panglao-sunrise-beach-resort', 'Panglao Sunrise Beach Resort', 'Panglao', 'Philippines', 'Beach resort', 'A beachfront resort near Alona Beach with a large pool, dive-shop partnerships, and arranged Balicasag island-hopping and dolphin-watching trips.', 9.5500000, 123.7700000, 6200.00, 'PHP', 'published', 1, '2025-06-21 10:00:00', '2025-06-21 10:00:00'),
(18, 'PRP-BOH-002', 6, 8, 'loboc-riverside-lodge-bohol', 'Loboc Riverside Lodge', 'Loboc', 'Philippines', 'Riverside lodge', 'A tranquil lodge on the Loboc River, convenient to the river cruise, the Chocolate Hills, and Bohol\'s tarsier sanctuary, with a riverside deck and garden.', 9.6393000, 124.0296000, 3200.00, 'PHP', 'published', 1, '2025-06-24 10:00:00', '2025-06-24 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `property_amenities`
--

CREATE TABLE `property_amenities` (
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `amenity_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_amenities`
--

INSERT INTO `property_amenities` (`property_id`, `amenity_id`) VALUES
(1, 1),
(1, 2),
(1, 5),
(1, 7),
(1, 13),
(1, 16),
(1, 17),
(2, 1),
(2, 2),
(2, 4),
(2, 5),
(2, 7),
(2, 8),
(2, 10),
(2, 14),
(2, 16),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 7),
(3, 10),
(3, 11),
(3, 12),
(3, 15),
(4, 1),
(4, 4),
(4, 5),
(4, 6),
(4, 13),
(4, 15),
(4, 16),
(5, 1),
(5, 4),
(5, 5),
(5, 6),
(5, 13),
(5, 15),
(6, 1),
(6, 2),
(6, 4),
(6, 5),
(6, 6),
(6, 11),
(6, 15),
(7, 1),
(7, 2),
(7, 3),
(7, 6),
(7, 12),
(7, 13),
(7, 15),
(8, 1),
(8, 2),
(8, 4),
(8, 5),
(8, 7),
(8, 8),
(8, 14),
(8, 17),
(9, 1),
(9, 2),
(9, 3),
(9, 5),
(9, 8),
(9, 9),
(9, 11),
(9, 12),
(9, 13),
(9, 17),
(9, 18),
(10, 1),
(10, 2),
(10, 3),
(10, 4),
(10, 12),
(10, 17),
(10, 18),
(11, 1),
(11, 2),
(11, 3),
(11, 4),
(11, 5),
(11, 9),
(11, 11),
(11, 12),
(11, 13),
(11, 18),
(12, 1),
(12, 2),
(12, 3),
(12, 5),
(12, 6),
(12, 8),
(12, 15),
(12, 17),
(13, 1),
(13, 2),
(13, 4),
(13, 5),
(13, 9),
(13, 12),
(13, 17),
(13, 18),
(14, 1),
(14, 2),
(14, 3),
(14, 4),
(14, 5),
(14, 7),
(14, 17),
(15, 1),
(15, 2),
(15, 5),
(15, 15),
(15, 16),
(15, 17),
(15, 18),
(16, 1),
(16, 2),
(16, 3),
(16, 4),
(16, 5),
(16, 11),
(16, 12),
(16, 15),
(17, 1),
(17, 2),
(17, 3),
(17, 5),
(17, 8),
(17, 9),
(17, 12),
(17, 13),
(17, 17),
(17, 18),
(18, 1),
(18, 2),
(18, 4),
(18, 5),
(18, 6),
(18, 15),
(18, 17);

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

CREATE TABLE `property_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `image_url` varchar(2048) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_images`
--

INSERT INTO `property_images` (`id`, `property_id`, `image_url`, `alt_text`, `sort_order`, `is_primary`, `created_at`) VALUES
(1, 1, 'https://images.unsplash.com/photo-551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Intramuros City Hotel in Manila', 0, 1, '2025-05-04 10:00:00'),
(2, 1, 'https://images.unsplash.com/photo-590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80', 'Guest room at Intramuros City Hotel', 1, 0, '2025-05-04 10:00:00'),
(3, 1, 'https://images.unsplash.com/photo-518509562904-e7ef99cdcc86?auto=format&fit=crop&w=1200&q=80', 'Scenery near Intramuros City Hotel, Manila', 2, 0, '2025-05-04 10:00:00'),
(4, 2, 'https://images.unsplash.com/photo-564501049412-61c2a3083791?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Makati Central Suites in Makati', 0, 1, '2025-05-07 10:00:00'),
(5, 2, 'https://images.unsplash.com/photo-618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80', 'Guest room at Makati Central Suites', 1, 0, '2025-05-07 10:00:00'),
(6, 2, 'https://images.unsplash.com/photo-518509562904-e7ef99cdcc86?auto=format&fit=crop&w=1200&q=80', 'Scenery near Makati Central Suites, Makati', 2, 0, '2025-05-07 10:00:00'),
(7, 3, 'https://images.unsplash.com/photo-455587734955-081b22074882?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Bonifacio Garden Boutique Hotel in Taguig', 0, 1, '2025-05-10 10:00:00'),
(8, 3, 'https://images.unsplash.com/photo-611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=80', 'Guest room at Bonifacio Garden Boutique Hotel', 1, 0, '2025-05-10 10:00:00'),
(9, 3, 'https://images.unsplash.com/photo-518509562904-e7ef99cdcc86?auto=format&fit=crop&w=1200&q=80', 'Scenery near Bonifacio Garden Boutique Hotel, Taguig', 2, 0, '2025-05-10 10:00:00'),
(10, 4, 'https://images.unsplash.com/photo-535827841776-24afc1e255ac?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Pine Breeze Lodge in Baguio City', 0, 1, '2025-05-13 10:00:00'),
(11, 4, 'https://images.unsplash.com/photo-578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=80', 'Guest room at Pine Breeze Lodge', 1, 0, '2025-05-13 10:00:00'),
(12, 4, 'https://images.unsplash.com/photo-510798831971-661eb04b3739?auto=format&fit=crop&w=1200&q=80', 'Scenery near Pine Breeze Lodge, Baguio City', 2, 0, '2025-05-13 10:00:00'),
(13, 5, 'https://images.unsplash.com/photo-566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Pinewood Ridge Cabins in Baguio City', 0, 1, '2025-05-16 10:00:00'),
(14, 5, 'https://images.unsplash.com/photo-596394516093-501ba68a0ba6?auto=format&fit=crop&w=1200&q=80', 'Guest room at Pinewood Ridge Cabins', 1, 0, '2025-05-16 10:00:00'),
(15, 5, 'https://images.unsplash.com/photo-500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80', 'Scenery near Pinewood Ridge Cabins, Baguio City', 2, 0, '2025-05-16 10:00:00'),
(16, 6, 'https://images.unsplash.com/photo-571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Taal View Retreat in Tagaytay', 0, 1, '2025-05-19 10:00:00'),
(17, 6, 'https://images.unsplash.com/photo-582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80', 'Guest room at Taal View Retreat', 1, 0, '2025-05-19 10:00:00'),
(18, 6, 'https://images.unsplash.com/photo-510798831971-661eb04b3739?auto=format&fit=crop&w=1200&q=80', 'Scenery near Taal View Retreat, Tagaytay', 2, 0, '2025-05-19 10:00:00'),
(19, 7, 'https://images.unsplash.com/photo-520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Highland Orchard Villas in Tagaytay', 0, 1, '2025-05-22 10:00:00'),
(20, 7, 'https://images.unsplash.com/photo-590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80', 'Guest room at Highland Orchard Villas', 1, 0, '2025-05-22 10:00:00'),
(21, 7, 'https://images.unsplash.com/photo-500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80', 'Scenery near Highland Orchard Villas, Tagaytay', 2, 0, '2025-05-22 10:00:00'),
(22, 8, 'https://images.unsplash.com/photo-542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Harborlight Stay Cebu in Cebu City', 0, 1, '2025-05-25 10:00:00'),
(23, 8, 'https://images.unsplash.com/photo-618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80', 'Guest room at Harborlight Stay Cebu', 1, 0, '2025-05-25 10:00:00'),
(24, 8, 'https://images.unsplash.com/photo-518509562904-e7ef99cdcc86?auto=format&fit=crop&w=1200&q=80', 'Scenery near Harborlight Stay Cebu, Cebu City', 2, 0, '2025-05-25 10:00:00'),
(25, 9, 'https://images.unsplash.com/photo-540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Mactan Coral Beach Resort in Lapu-Lapu City', 0, 1, '2025-05-28 10:00:00'),
(26, 9, 'https://images.unsplash.com/photo-611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=80', 'Guest room at Mactan Coral Beach Resort', 1, 0, '2025-05-28 10:00:00'),
(27, 9, 'https://images.unsplash.com/photo-573843981267-be1999ff37cd?auto=format&fit=crop&w=1200&q=80', 'Scenery near Mactan Coral Beach Resort, Lapu-Lapu City', 2, 0, '2025-05-28 10:00:00'),
(28, 10, 'https://images.unsplash.com/photo-535827841776-24afc1e255ac?auto=format&fit=crop&w=1200&q=80', 'Exterior view of White Sand Inn in Boracay', 0, 1, '2025-05-31 10:00:00'),
(29, 10, 'https://images.unsplash.com/photo-578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=80', 'Guest room at White Sand Inn', 1, 0, '2025-05-31 10:00:00'),
(30, 10, 'https://images.unsplash.com/photo-519046904884-53103b34b206?auto=format&fit=crop&w=1200&q=80', 'Scenery near White Sand Inn, Boracay', 2, 0, '2025-05-31 10:00:00'),
(31, 11, 'https://images.unsplash.com/photo-566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Sunset Cove Beach Resort in Boracay', 0, 1, '2025-06-03 10:00:00'),
(32, 11, 'https://images.unsplash.com/photo-596394516093-501ba68a0ba6?auto=format&fit=crop&w=1200&q=80', 'Guest room at Sunset Cove Beach Resort', 1, 0, '2025-06-03 10:00:00'),
(33, 11, 'https://images.unsplash.com/photo-500375592092-40eb2168fd21?auto=format&fit=crop&w=1200&q=80', 'Scenery near Sunset Cove Beach Resort, Boracay', 2, 0, '2025-06-03 10:00:00'),
(34, 12, 'https://images.unsplash.com/photo-571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Lagoon Garden Escape in Puerto Princesa', 0, 1, '2025-06-06 10:00:00'),
(35, 12, 'https://images.unsplash.com/photo-582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80', 'Guest room at Lagoon Garden Escape', 1, 0, '2025-06-06 10:00:00'),
(36, 12, 'https://images.unsplash.com/photo-507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80', 'Scenery near Lagoon Garden Escape, Puerto Princesa', 2, 0, '2025-06-06 10:00:00'),
(37, 13, 'https://images.unsplash.com/photo-520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Bacuit Bay Beach Resort in El Nido', 0, 1, '2025-06-09 10:00:00'),
(38, 13, 'https://images.unsplash.com/photo-590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80', 'Guest room at Bacuit Bay Beach Resort', 1, 0, '2025-06-09 10:00:00'),
(39, 13, 'https://images.unsplash.com/photo-573843981267-be1999ff37cd?auto=format&fit=crop&w=1200&q=80', 'Scenery near Bacuit Bay Beach Resort, El Nido', 2, 0, '2025-06-09 10:00:00'),
(40, 14, 'https://images.unsplash.com/photo-437719417032-8595fd9e9dc6?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Coron Bayview Hotel in Coron', 0, 1, '2025-06-12 10:00:00'),
(41, 14, 'https://images.unsplash.com/photo-618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80', 'Guest room at Coron Bayview Hotel', 1, 0, '2025-06-12 10:00:00'),
(42, 14, 'https://images.unsplash.com/photo-519046904884-53103b34b206?auto=format&fit=crop&w=1200&q=80', 'Scenery near Coron Bayview Hotel, Coron', 2, 0, '2025-06-12 10:00:00'),
(43, 15, 'https://images.unsplash.com/photo-540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Siargao Palm Surf Lodge in General Luna', 0, 1, '2025-06-15 10:00:00'),
(44, 15, 'https://images.unsplash.com/photo-611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=80', 'Guest room at Siargao Palm Surf Lodge', 1, 0, '2025-06-15 10:00:00'),
(45, 15, 'https://images.unsplash.com/photo-500375592092-40eb2168fd21?auto=format&fit=crop&w=1200&q=80', 'Scenery near Siargao Palm Surf Lodge, General Luna', 2, 0, '2025-06-15 10:00:00'),
(46, 16, 'https://images.unsplash.com/photo-535827841776-24afc1e255ac?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Island Breeze Boutique Resort in General Luna', 0, 1, '2025-06-18 10:00:00'),
(47, 16, 'https://images.unsplash.com/photo-578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=80', 'Guest room at Island Breeze Boutique Resort', 1, 0, '2025-06-18 10:00:00'),
(48, 16, 'https://images.unsplash.com/photo-507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80', 'Scenery near Island Breeze Boutique Resort, General Luna', 2, 0, '2025-06-18 10:00:00'),
(49, 17, 'https://images.unsplash.com/photo-566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Panglao Sunrise Beach Resort in Panglao', 0, 1, '2025-06-21 10:00:00'),
(50, 17, 'https://images.unsplash.com/photo-596394516093-501ba68a0ba6?auto=format&fit=crop&w=1200&q=80', 'Guest room at Panglao Sunrise Beach Resort', 1, 0, '2025-06-21 10:00:00'),
(51, 17, 'https://images.unsplash.com/photo-573843981267-be1999ff37cd?auto=format&fit=crop&w=1200&q=80', 'Scenery near Panglao Sunrise Beach Resort, Panglao', 2, 0, '2025-06-21 10:00:00'),
(52, 18, 'https://images.unsplash.com/photo-571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80', 'Exterior view of Loboc Riverside Lodge in Loboc', 0, 1, '2025-06-24 10:00:00'),
(53, 18, 'https://images.unsplash.com/photo-582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80', 'Guest room at Loboc Riverside Lodge', 1, 0, '2025-06-24 10:00:00'),
(54, 18, 'https://images.unsplash.com/photo-510798831971-661eb04b3739?auto=format&fit=crop&w=1200&q=80', 'Scenery near Loboc Riverside Lodge, Loboc', 2, 0, '2025-06-24 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `property_staff`
--

CREATE TABLE `property_staff` (
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `job_title` varchar(100) NOT NULL,
  `status` enum('pending','active','disabled') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'customer', 'Guest account for searching and booking accommodation in the Philippines'),
(2, 'partner', 'Philippine accommodation owner or property operator'),
(3, 'staff', 'Authorized staff member of a listed Philippine property'),
(4, 'support', 'Customer support representative for bookings and guest concerns'),
(5, 'admin', 'Administrator responsible for platform settings and listings');

-- --------------------------------------------------------

--
-- Table structure for table `room_inventory`
--

CREATE TABLE `room_inventory` (
  `room_type_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_date` date NOT NULL,
  `available_units` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `nightly_rate` decimal(10,2) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `max_guests` tinyint(3) UNSIGNED NOT NULL,
  `total_units` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `nightly_rate` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PHP',
  `status` enum('active','inactive','in_review') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `property_id`, `name`, `description`, `max_guests`, `total_units`, `nightly_rate`, `currency`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Standard Queen Room', 'Compact, well-lit room with a queen bed, work desk, and en-suite bathroom.', 2, 16, 3200.00, 'PHP', 'active', '2025-05-04 10:00:00', '2025-05-04 10:00:00'),
(2, 1, 'Deluxe King Room', 'Spacious room with a king bed, a seating area, and city views.', 2, 12, 4150.00, 'PHP', 'active', '2025-05-04 10:00:00', '2025-05-04 10:00:00'),
(3, 1, 'Family Room', 'Two double beds and extra floor space, suitable for families of up to four.', 4, 6, 5450.00, 'PHP', 'active', '2025-05-04 10:00:00', '2025-05-04 10:00:00'),
(4, 1, 'Executive Suite', 'Separate living area, king bed, and complimentary lounge access.', 3, 3, 7700.00, 'PHP', 'active', '2025-05-04 10:00:00', '2025-05-04 10:00:00'),
(5, 2, 'Standard Queen Room', 'Compact, well-lit room with a queen bed, work desk, and en-suite bathroom.', 2, 21, 4800.00, 'PHP', 'active', '2025-05-07 10:00:00', '2025-05-07 10:00:00'),
(6, 2, 'Deluxe King Room', 'Spacious room with a king bed, a seating area, and city views.', 2, 16, 6250.00, 'PHP', 'active', '2025-05-07 10:00:00', '2025-05-07 10:00:00'),
(7, 2, 'Family Room', 'Two double beds and extra floor space, suitable for families of up to four.', 4, 8, 8150.00, 'PHP', 'active', '2025-05-07 10:00:00', '2025-05-07 10:00:00'),
(8, 2, 'Executive Suite', 'Separate living area, king bed, and complimentary lounge access.', 3, 4, 11500.00, 'PHP', 'active', '2025-05-07 10:00:00', '2025-05-07 10:00:00'),
(9, 3, 'Standard Queen Room', 'Compact, well-lit room with a queen bed, work desk, and en-suite bathroom.', 2, 13, 5600.00, 'PHP', 'active', '2025-05-10 10:00:00', '2025-05-10 10:00:00'),
(10, 3, 'Deluxe King Room', 'Spacious room with a king bed, a seating area, and city views.', 2, 10, 7300.00, 'PHP', 'active', '2025-05-10 10:00:00', '2025-05-10 10:00:00'),
(11, 3, 'Family Room', 'Two double beds and extra floor space, suitable for families of up to four.', 4, 5, 9500.00, 'PHP', 'active', '2025-05-10 10:00:00', '2025-05-10 10:00:00'),
(12, 3, 'Executive Suite', 'Separate living area, king bed, and complimentary lounge access.', 3, 2, 13450.00, 'PHP', 'active', '2025-05-10 10:00:00', '2025-05-10 10:00:00'),
(13, 4, 'Cozy Queen Room', 'Warm room with a queen bed, thick blankets, and garden or pine-tree views.', 2, 8, 2900.00, 'PHP', 'active', '2025-05-13 10:00:00', '2025-05-13 10:00:00'),
(14, 4, 'Deluxe Mountain View Room', 'Larger room with a private balcony facing the surrounding hills.', 3, 6, 3750.00, 'PHP', 'active', '2025-05-13 10:00:00', '2025-05-13 10:00:00'),
(15, 4, 'Family Cabin', 'Multi-bed cabin with a living area and outdoor terrace for up to six guests.', 6, 4, 6100.00, 'PHP', 'active', '2025-05-13 10:00:00', '2025-05-13 10:00:00'),
(16, 5, 'Pine Cabin for Two', 'Private cabin with a fireplace, queen bed, and outdoor sitting area.', 2, 4, 4200.00, 'PHP', 'active', '2025-05-16 10:00:00', '2025-05-16 10:00:00'),
(17, 5, 'Forest Family Cabin', 'Two-bedroom cabin with a living area and barbecue deck, sleeping up to six.', 6, 2, 7550.00, 'PHP', 'active', '2025-05-16 10:00:00', '2025-05-16 10:00:00'),
(18, 5, 'Ridge View Cabin', 'Premium cabin with a wide deck facing the ridge, sleeping up to four.', 4, 2, 6300.00, 'PHP', 'active', '2025-05-16 10:00:00', '2025-05-16 10:00:00'),
(19, 6, 'Cozy Queen Room', 'Warm room with a queen bed, thick blankets, and garden or pine-tree views.', 2, 8, 3800.00, 'PHP', 'active', '2025-05-19 10:00:00', '2025-05-19 10:00:00'),
(20, 6, 'Deluxe Mountain View Room', 'Larger room with a private balcony facing the surrounding hills.', 3, 6, 4950.00, 'PHP', 'active', '2025-05-19 10:00:00', '2025-05-19 10:00:00'),
(21, 6, 'Family Cabin', 'Multi-bed cabin with a living area and outdoor terrace for up to six guests.', 6, 4, 8000.00, 'PHP', 'active', '2025-05-19 10:00:00', '2025-05-19 10:00:00'),
(22, 7, 'Garden Villa', 'One-bedroom villa with a private terrace surrounded by orchard gardens.', 2, 3, 4600.00, 'PHP', 'active', '2025-05-22 10:00:00', '2025-05-22 10:00:00'),
(23, 7, 'Taal View Villa', 'Elevated villa with a terrace overlooking Taal Lake and Volcano.', 4, 2, 6450.00, 'PHP', 'active', '2025-05-22 10:00:00', '2025-05-22 10:00:00'),
(24, 7, 'Two-Bedroom Family Villa', 'Two bedrooms, a living area, and a kitchenette for family groups of up to six.', 6, 2, 9200.00, 'PHP', 'active', '2025-05-22 10:00:00', '2025-05-22 10:00:00'),
(25, 8, 'Standard Queen Room', 'Compact, well-lit room with a queen bed, work desk, and en-suite bathroom.', 2, 19, 3000.00, 'PHP', 'active', '2025-05-25 10:00:00', '2025-05-25 10:00:00'),
(26, 8, 'Deluxe King Room', 'Spacious room with a king bed, a seating area, and city views.', 2, 14, 3900.00, 'PHP', 'active', '2025-05-25 10:00:00', '2025-05-25 10:00:00'),
(27, 8, 'Family Room', 'Two double beds and extra floor space, suitable for families of up to four.', 4, 7, 5100.00, 'PHP', 'active', '2025-05-25 10:00:00', '2025-05-25 10:00:00'),
(28, 8, 'Executive Suite', 'Separate living area, king bed, and complimentary lounge access.', 3, 4, 7200.00, 'PHP', 'active', '2025-05-25 10:00:00', '2025-05-25 10:00:00'),
(29, 9, 'Garden View Room', 'Comfortable room with a queen bed and views over the resort gardens.', 2, 14, 6500.00, 'PHP', 'active', '2025-05-28 10:00:00', '2025-05-28 10:00:00'),
(30, 9, 'Ocean View Deluxe Room', 'Room with a private balcony and partial sea views.', 3, 11, 8800.00, 'PHP', 'active', '2025-05-28 10:00:00', '2025-05-28 10:00:00'),
(31, 9, 'Beachfront Suite', 'Suite steps from the shoreline with a separate lounge and king bed.', 4, 6, 13000.00, 'PHP', 'active', '2025-05-28 10:00:00', '2025-05-28 10:00:00'),
(32, 9, 'Family Villa', 'Two-bedroom villa with a kitchenette and a private terrace, sleeping up to six.', 6, 4, 18200.00, 'PHP', 'active', '2025-05-28 10:00:00', '2025-05-28 10:00:00'),
(33, 10, 'Garden View Room', 'Comfortable room with a queen bed and views over the resort gardens.', 2, 10, 5200.00, 'PHP', 'active', '2025-05-31 10:00:00', '2025-05-31 10:00:00'),
(34, 10, 'Ocean View Deluxe Room', 'Room with a private balcony and partial sea views.', 3, 8, 7000.00, 'PHP', 'active', '2025-05-31 10:00:00', '2025-05-31 10:00:00'),
(35, 10, 'Beachfront Suite', 'Suite steps from the shoreline with a separate lounge and king bed.', 4, 4, 10400.00, 'PHP', 'active', '2025-05-31 10:00:00', '2025-05-31 10:00:00'),
(36, 10, 'Family Villa', 'Two-bedroom villa with a kitchenette and a private terrace, sleeping up to six.', 6, 3, 14550.00, 'PHP', 'active', '2025-05-31 10:00:00', '2025-05-31 10:00:00'),
(37, 11, 'Garden View Room', 'Comfortable room with a queen bed and views over the resort gardens.', 2, 10, 9800.00, 'PHP', 'active', '2025-06-03 10:00:00', '2025-06-03 10:00:00'),
(38, 11, 'Ocean View Deluxe Room', 'Room with a private balcony and partial sea views.', 3, 8, 13250.00, 'PHP', 'active', '2025-06-03 10:00:00', '2025-06-03 10:00:00'),
(39, 11, 'Beachfront Suite', 'Suite steps from the shoreline with a separate lounge and king bed.', 4, 4, 19600.00, 'PHP', 'active', '2025-06-03 10:00:00', '2025-06-03 10:00:00'),
(40, 11, 'Family Villa', 'Two-bedroom villa with a kitchenette and a private terrace, sleeping up to six.', 6, 3, 27450.00, 'PHP', 'active', '2025-06-03 10:00:00', '2025-06-03 10:00:00'),
(41, 12, 'Garden View Room', 'Comfortable room with a queen bed and views over the resort gardens.', 2, 10, 3600.00, 'PHP', 'active', '2025-06-06 10:00:00', '2025-06-06 10:00:00'),
(42, 12, 'Ocean View Deluxe Room', 'Room with a private balcony and partial sea views.', 3, 8, 4850.00, 'PHP', 'active', '2025-06-06 10:00:00', '2025-06-06 10:00:00'),
(43, 12, 'Beachfront Suite', 'Suite steps from the shoreline with a separate lounge and king bed.', 4, 4, 7200.00, 'PHP', 'active', '2025-06-06 10:00:00', '2025-06-06 10:00:00'),
(44, 12, 'Family Villa', 'Two-bedroom villa with a kitchenette and a private terrace, sleeping up to six.', 6, 3, 10100.00, 'PHP', 'active', '2025-06-06 10:00:00', '2025-06-06 10:00:00'),
(45, 13, 'Garden View Room', 'Comfortable room with a queen bed and views over the resort gardens.', 2, 9, 7500.00, 'PHP', 'active', '2025-06-09 10:00:00', '2025-06-09 10:00:00'),
(46, 13, 'Ocean View Deluxe Room', 'Room with a private balcony and partial sea views.', 3, 7, 10100.00, 'PHP', 'active', '2025-06-09 10:00:00', '2025-06-09 10:00:00'),
(47, 13, 'Beachfront Suite', 'Suite steps from the shoreline with a separate lounge and king bed.', 4, 4, 15000.00, 'PHP', 'active', '2025-06-09 10:00:00', '2025-06-09 10:00:00'),
(48, 13, 'Family Villa', 'Two-bedroom villa with a kitchenette and a private terrace, sleeping up to six.', 6, 3, 21000.00, 'PHP', 'active', '2025-06-09 10:00:00', '2025-06-09 10:00:00'),
(49, 14, 'Garden View Room', 'Comfortable room with a queen bed and views over the resort gardens.', 2, 9, 5400.00, 'PHP', 'active', '2025-06-12 10:00:00', '2025-06-12 10:00:00'),
(50, 14, 'Ocean View Deluxe Room', 'Room with a private balcony and partial sea views.', 3, 7, 7300.00, 'PHP', 'active', '2025-06-12 10:00:00', '2025-06-12 10:00:00'),
(51, 14, 'Beachfront Suite', 'Suite steps from the shoreline with a separate lounge and king bed.', 4, 4, 10800.00, 'PHP', 'active', '2025-06-12 10:00:00', '2025-06-12 10:00:00'),
(52, 14, 'Family Villa', 'Two-bedroom villa with a kitchenette and a private terrace, sleeping up to six.', 6, 3, 15100.00, 'PHP', 'active', '2025-06-12 10:00:00', '2025-06-12 10:00:00'),
(53, 15, 'Surf Lodge Twin Room', 'Twin-bed room with board storage access and a fan-and-AC setup.', 2, 10, 3400.00, 'PHP', 'active', '2025-06-15 10:00:00', '2025-06-15 10:00:00'),
(54, 15, 'Deluxe Queen Room', 'Queen room with an outdoor shower and a small veranda.', 2, 6, 4600.00, 'PHP', 'active', '2025-06-15 10:00:00', '2025-06-15 10:00:00'),
(55, 15, 'Family Bungalow', 'Standalone bungalow with two beds, suitable for up to five guests.', 5, 3, 7150.00, 'PHP', 'active', '2025-06-15 10:00:00', '2025-06-15 10:00:00'),
(56, 16, 'Garden View Room', 'Comfortable room with a queen bed and views over the resort gardens.', 2, 7, 6800.00, 'PHP', 'active', '2025-06-18 10:00:00', '2025-06-18 10:00:00'),
(57, 16, 'Ocean View Deluxe Room', 'Room with a private balcony and partial sea views.', 3, 6, 9200.00, 'PHP', 'active', '2025-06-18 10:00:00', '2025-06-18 10:00:00'),
(58, 16, 'Beachfront Suite', 'Suite steps from the shoreline with a separate lounge and king bed.', 4, 3, 13600.00, 'PHP', 'active', '2025-06-18 10:00:00', '2025-06-18 10:00:00'),
(59, 16, 'Family Villa', 'Two-bedroom villa with a kitchenette and a private terrace, sleeping up to six.', 6, 2, 19050.00, 'PHP', 'active', '2025-06-18 10:00:00', '2025-06-18 10:00:00'),
(60, 17, 'Garden View Room', 'Comfortable room with a queen bed and views over the resort gardens.', 2, 12, 6200.00, 'PHP', 'active', '2025-06-21 10:00:00', '2025-06-21 10:00:00'),
(61, 17, 'Ocean View Deluxe Room', 'Room with a private balcony and partial sea views.', 3, 10, 8350.00, 'PHP', 'active', '2025-06-21 10:00:00', '2025-06-21 10:00:00'),
(62, 17, 'Beachfront Suite', 'Suite steps from the shoreline with a separate lounge and king bed.', 4, 5, 12400.00, 'PHP', 'active', '2025-06-21 10:00:00', '2025-06-21 10:00:00'),
(63, 17, 'Family Villa', 'Two-bedroom villa with a kitchenette and a private terrace, sleeping up to six.', 6, 4, 17350.00, 'PHP', 'active', '2025-06-21 10:00:00', '2025-06-21 10:00:00'),
(64, 18, 'Riverside Standard Room', 'Simple, comfortable room with garden-facing windows.', 2, 6, 3200.00, 'PHP', 'active', '2025-06-24 10:00:00', '2025-06-24 10:00:00'),
(65, 18, 'Deluxe River View Room', 'Room with a balcony facing the Loboc River.', 3, 4, 4300.00, 'PHP', 'active', '2025-06-24 10:00:00', '2025-06-24 10:00:00'),
(66, 18, 'Family Bungalow', 'Bungalow with two beds and a porch for up to five guests.', 5, 2, 6100.00, 'PHP', 'active', '2025-06-24 10:00:00', '2025-06-24 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(40) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(180) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `status` enum('open','in_progress','closed') NOT NULL DEFAULT 'open',
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `description`, `updated_by`, `updated_at`) VALUES
('default_country', 'Philippines', 'Default country for destination and property listings', 1, '2026-10-17 15:55:31'),
('default_currency', 'PHP', 'Default currency displayed for accommodation prices', 1, '2026-10-17 15:55:31'),
('default_language', 'en-PH', 'Default language/locale for the Philippine market', 1, '2026-10-17 15:55:31'),
('default_timezone', 'Asia/Manila', 'Default timezone for booking dates and times', 1, '2026-10-17 15:55:31'),
('payment_methods_demo', 'GCash, Maya, BDO Online Banking, Credit/Debit Card, Pay at Property', 'Demo payment methods; no real payment processing is enabled', 1, '2026-10-17 15:55:31'),
('service_charge_percent', '10', 'Demo service charge applied to booking subtotals', 1, '2026-10-17 15:55:31'),
('tax_label', 'Applicable Philippine taxes and fees', 'Display label only; actual taxes and fees must be configured based on the property and applicable rules', 1, '2026-10-17 15:55:31'),
('vat_rate_percent', '12', 'Demo VAT rate applied to booking subtotals', 1, '2026-10-17 15:55:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` smallint(5) UNSIGNED NOT NULL,
  `email` varchar(254) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `status` enum('pending','active','suspended','disabled') NOT NULL DEFAULT 'pending',
  `email_verified_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `status`, `email_verified_at`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 5, 'admin@xyvora.example', 'adminpoako', 'Maria Lourdes', 'Dela Cruz', '+639185881399', 'active', '2025-02-03 18:34:00', '2025-12-28 01:49:51', '2025-02-03 09:00:00', '2026-10-17 15:59:40'),
(2, 4, 'support.manila@xyvora.example', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Angelica', 'Reyes', '+639998797309', 'active', '2025-02-10 10:57:00', '2025-05-08 09:46:43', '2025-02-10 09:30:00', '2025-05-08 09:46:43'),
(3, 4, 'support.visayas@xyvora.example', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Joshua', 'Bautista', '+639661707536', 'active', '2025-03-04 13:07:00', '2025-11-30 09:33:43', '2025-03-04 08:45:00', '2025-11-30 09:33:43'),
(4, 2, 'rafael.villanueva@heritagehospitality.example', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Rafael', 'Villanueva', '+639766815188', 'active', '2025-03-15 15:04:00', '2025-10-25 19:17:19', '2025-03-15 10:00:00', '2025-10-25 19:17:19'),
(5, 2, 'corazon.mendoza@highlandstays.example', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Corazon', 'Mendoza', '+639952940557', 'active', '2025-03-24 18:17:00', '2025-06-03 21:28:33', '2025-03-24 11:00:00', '2025-06-03 21:28:33'),
(6, 2, 'miguel.torres@visayasstays.example', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Miguel', 'Torres', '+639949871689', 'active', '2025-04-02 20:27:00', '2026-02-06 18:01:27', '2025-04-02 12:00:00', '2026-02-06 18:01:27'),
(7, 2, 'katrina.salazar@boracaycollection.example', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Katrina', 'Salazar', '+639964697880', 'active', '2025-04-11 22:54:00', '2025-10-03 22:26:48', '2025-04-11 13:00:00', '2025-10-03 22:26:48'),
(8, 2, 'eduardo.ramos@palawanescapes.example', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Eduardo', 'Ramos', '+639270819672', 'active', '2025-04-20 22:18:00', '2025-10-16 09:30:48', '2025-04-20 14:00:00', '2025-10-16 09:30:48'),
(9, 2, 'isabel.navarro@siargaoislandstays.example', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Isabel', 'Navarro', '+639187784864', 'active', '2025-04-30 00:26:00', '2026-09-15 01:24:03', '2025-04-29 15:00:00', '2026-09-15 01:24:03'),
(10, 1, 'andrea.santos@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Andrea', 'Santos', '+639956634400', 'active', '2026-01-12 21:38:00', '2026-09-25 10:01:58', '2026-01-12 19:00:00', '2026-09-25 10:01:58'),
(11, 1, 'marco.reyes@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Marco', 'Reyes', '+639967534778', 'active', '2026-03-02 23:34:00', '2026-08-19 06:18:43', '2026-03-02 15:00:00', '2026-08-19 06:18:43'),
(12, 1, 'patricia.cruz@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Patricia', 'Cruz', '+639174571784', 'active', '2026-02-11 01:48:00', '2026-04-23 20:08:19', '2026-02-10 18:00:00', '2026-04-23 20:08:19'),
(13, 1, 'joseph.garcia@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Joseph', 'Garcia', '+639951475136', 'active', '2025-12-04 21:34:00', '2026-05-29 07:39:47', '2025-12-04 19:00:00', '2026-05-29 07:39:47'),
(14, 1, 'bianca.lim@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Bianca', 'Lim', '+639177981933', 'active', '2025-05-08 11:08:00', '2026-09-03 18:40:15', '2025-05-08 09:00:00', '2026-09-03 18:40:15'),
(15, 1, 'carlo.aquino@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Carlo', 'Aquino', '+639964520599', 'active', '2025-05-24 04:28:00', '2025-09-06 14:03:29', '2025-05-23 20:00:00', '2025-09-06 14:03:29'),
(16, 1, 'danica.ocampo@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Danica', 'Ocampo', '+639959298416', 'active', '2026-02-04 13:48:00', '2026-03-30 00:31:02', '2026-02-04 11:00:00', '2026-03-30 00:31:02'),
(17, 1, 'paolo.castillo@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Paolo', 'Castillo', '+639966628360', 'active', '2026-02-25 13:53:00', '2026-07-27 16:59:10', '2026-02-25 12:00:00', '2026-07-27 16:59:10'),
(18, 1, 'isabella.domingo@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Isabella', 'Domingo', '+639765008754', 'active', '2025-06-07 15:45:00', '2026-02-16 13:52:38', '2025-06-07 08:00:00', '2026-02-16 13:52:38'),
(19, 1, 'gabriel.pascual@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Gabriel', 'Pascual', '+639959922983', 'active', '2025-07-02 22:29:00', '2026-03-21 22:18:07', '2025-07-02 15:00:00', '2026-03-21 22:18:07'),
(20, 1, 'nicole.tan@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Nicole', 'Tan', '+639551860320', 'active', '2025-12-22 02:22:00', '2026-01-10 10:08:22', '2025-12-21 17:00:00', '2026-01-10 10:08:22'),
(21, 1, 'rafael.fernandez@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Rafael', 'Fernandez', '+639557028382', 'active', '2025-12-18 22:49:00', '2026-09-02 15:02:39', '2025-12-18 20:00:00', '2026-09-02 15:02:39'),
(22, 1, 'jasmine.mercado@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Jasmine', 'Mercado', '+639666861642', 'active', '2025-08-04 19:57:00', '2025-12-19 06:32:59', '2025-08-04 14:00:00', '2025-12-19 06:32:59'),
(23, 1, 'lorenzo.alvarez@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Lorenzo', 'Alvarez', '+639956472587', 'active', '2025-09-12 22:40:00', '2025-12-15 21:55:51', '2025-09-12 15:00:00', '2025-12-15 21:55:51'),
(24, 1, 'camille.soriano@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Camille', 'Soriano', '+639964133017', 'active', '2026-01-26 01:43:00', '2026-07-04 06:51:30', '2026-01-25 20:00:00', '2026-07-04 06:51:30'),
(25, 1, 'enrico.valdez@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Enrico', 'Valdez', '+639270525898', 'active', '2026-02-04 23:46:00', '2026-03-20 10:25:03', '2026-02-04 20:00:00', '2026-03-20 10:25:03'),
(26, 1, 'trisha.manalo@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Trisha', 'Manalo', '+639668939494', 'active', '2026-03-15 04:34:00', '2026-05-06 07:05:27', '2026-03-14 19:00:00', '2026-05-06 07:05:27'),
(27, 1, 'dominic.flores@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Dominic', 'Flores', '+639661621052', 'active', '2025-06-21 21:53:00', '2026-07-21 08:12:22', '2025-06-21 20:00:00', '2026-07-21 08:12:22'),
(28, 1, 'alyssa.rivera@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Alyssa', 'Rivera', '+639991452441', 'active', '2025-10-08 12:19:00', '2026-03-18 08:47:04', '2025-10-08 12:00:00', '2026-03-18 08:47:04'),
(29, 1, 'kenneth.go@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Kenneth', 'Go', '+639170130411', 'active', '2025-09-09 23:39:00', '2026-06-09 16:11:32', '2025-09-09 14:00:00', '2026-06-09 16:11:32'),
(30, 1, 'michelle.chua@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Michelle', 'Chua', '+639959906593', 'active', '2025-10-10 03:58:00', '2026-03-25 07:45:47', '2025-10-09 20:00:00', '2026-03-25 07:45:47'),
(31, 1, 'vincent.dizon@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Vincent', 'Dizon', '+639768464506', 'active', '2025-07-01 18:29:00', '2026-02-06 08:15:10', '2025-07-01 18:00:00', '2026-02-06 08:15:10'),
(32, 1, 'sofia.padilla@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Sofia', 'Padilla', '+639184659085', 'active', '2025-10-17 15:27:00', '2026-05-24 07:37:14', '2025-10-17 12:00:00', '2026-05-24 07:37:14'),
(33, 1, 'adrian.estrada@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Adrian', 'Estrada', '+639182911028', 'active', '2025-05-30 01:07:00', '2026-09-06 11:00:12', '2025-05-29 19:00:00', '2026-09-06 11:00:12'),
(34, 1, 'hannah.cabrera@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Hannah', 'Cabrera', '+639949256663', 'active', '2025-10-02 15:16:00', '2026-06-17 15:48:52', '2025-10-02 08:00:00', '2026-06-17 15:48:52'),
(35, 1, 'jerome.legaspi@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Jerome', 'Legaspi', '+639272808864', 'active', '2025-08-21 22:36:00', '2026-02-16 02:59:47', '2025-08-21 20:00:00', '2026-02-16 02:59:47'),
(36, 1, 'katherine.sy@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Katherine', 'Sy', '+639668764529', 'active', '2025-05-12 09:36:00', '2025-09-29 14:26:09', '2025-05-12 08:00:00', '2025-09-29 14:26:09'),
(37, 1, 'francis.tolentino@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Francis', 'Tolentino', '+639964585003', 'active', '2026-02-08 16:06:00', '2026-03-03 23:21:59', '2026-02-08 08:00:00', '2026-03-03 23:21:59'),
(38, 1, 'mariel.guevarra@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Mariel', 'Guevarra', '+639962653845', 'active', '2025-08-22 21:30:00', '2026-06-10 19:24:31', '2025-08-22 18:00:00', '2026-06-10 19:24:31'),
(39, 1, 'raymond.abad@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Raymond', 'Abad', '+639958794326', 'active', '2025-10-31 17:42:00', '2026-05-08 22:11:31', '2025-10-31 15:00:00', '2026-05-08 22:11:31'),
(40, 1, 'charmaine.deguzman@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Charmaine', 'De Guzman', '+639966021458', 'active', '2025-05-03 17:36:00', '2025-08-19 16:46:18', '2025-05-03 13:00:00', '2025-08-19 16:46:18'),
(41, 1, 'louis.macaraeg@example.com', '$2b$10$Lj1PPc/ZGwI0SrIOS8jPTef2QgnYZ8usFA7Tp5I7uV6qbrCfAbP4O', 'Louis', 'Macaraeg', '+639963949066', 'pending', NULL, NULL, '2026-10-14 18:20:00', '2026-10-14 18:20:00'),
(45, 5, 'geraldmanongdo912@gmail.com', '$2y$10$8knRQyXaiF5WSoju1Y77VepuD5I0qDUuOQrwtz22IkBmUNBN9hj3.', 'Gerald', 'Manongdo', '09562184010', 'pending', NULL, '2026-10-17 16:20:36', '2026-10-17 16:02:58', '2026-10-17 16:20:36'),
(47, 1, 'alexanderbautista@gmail.com', '$2y$10$6tc2HOf5oyYdZ9OnH66nguCbeli9jsJmCpH05GYOa5PGH1R0uK5pG', 'Alexander', 'Bautista', '09562184010', 'pending', NULL, '2026-10-17 16:21:58', '2026-10-17 16:21:47', '2026-10-17 16:21:58');

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `booking_updates` tinyint(1) NOT NULL DEFAULT 1,
  `travel_inspiration` tinyint(1) NOT NULL DEFAULT 0,
  `product_announcements` tinyint(1) NOT NULL DEFAULT 0,
  `preferred_currency` char(3) NOT NULL DEFAULT 'PHP',
  `preferred_language` varchar(10) NOT NULL DEFAULT 'en-PH',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`user_id`, `property_id`, `created_at`) VALUES
(4, 17, '2026-10-17 15:56:17'),
(10, 1, '2026-08-30 00:25:02'),
(10, 11, '2026-10-07 07:54:51'),
(10, 12, '2026-04-04 09:21:23'),
(10, 13, '2026-04-22 05:38:58'),
(11, 2, '2026-05-01 12:20:03'),
(11, 6, '2026-07-30 18:06:57'),
(11, 7, '2026-04-01 20:19:34'),
(11, 8, '2026-06-03 03:20:50'),
(12, 16, '2026-09-08 05:29:50'),
(13, 2, '2026-03-15 20:07:01'),
(13, 3, '2026-02-09 02:51:17'),
(13, 16, '2026-03-17 19:53:54'),
(14, 18, '2026-06-12 05:32:19'),
(15, 12, '2025-10-04 12:50:18'),
(15, 16, '2026-02-10 19:56:39'),
(15, 17, '2026-10-07 23:10:26'),
(16, 11, '2026-09-28 08:19:56'),
(16, 17, '2026-04-19 10:29:06'),
(17, 6, '2026-05-20 20:37:37'),
(17, 7, '2026-07-22 23:44:58'),
(17, 17, '2026-04-22 02:06:26'),
(18, 2, '2026-09-28 20:29:40'),
(19, 3, '2025-07-12 10:04:27'),
(19, 4, '2026-09-28 21:36:40'),
(19, 15, '2025-10-19 10:02:02'),
(20, 2, '2026-04-06 15:02:48'),
(20, 5, '2026-01-26 14:28:36'),
(20, 12, '2026-02-23 08:38:20'),
(20, 16, '2026-05-31 10:07:47'),
(21, 4, '2026-03-14 17:28:18'),
(21, 8, '2026-09-16 13:19:06'),
(21, 14, '2026-04-07 22:32:34'),
(22, 9, '2025-08-30 06:29:37'),
(22, 11, '2025-12-01 12:36:19'),
(22, 13, '2026-06-27 05:11:53'),
(23, 9, '2026-08-03 23:20:33'),
(23, 11, '2026-10-08 16:38:54'),
(23, 18, '2026-04-08 22:47:58'),
(24, 5, '2026-09-30 04:38:43'),
(24, 8, '2026-08-31 06:00:29'),
(25, 4, '2026-08-28 03:52:17'),
(25, 6, '2026-07-03 09:39:03'),
(25, 14, '2026-09-16 03:52:49'),
(25, 16, '2026-07-04 00:18:50'),
(26, 1, '2026-06-03 08:05:04'),
(26, 11, '2026-04-14 11:53:04'),
(26, 15, '2026-08-02 22:45:12'),
(27, 8, '2026-08-21 13:24:15'),
(27, 10, '2026-04-20 08:13:45'),
(28, 8, '2026-09-24 16:45:20'),
(28, 12, '2025-10-28 07:15:56'),
(28, 13, '2026-05-08 19:15:43'),
(28, 15, '2026-09-09 04:55:57'),
(29, 14, '2026-05-10 03:03:17'),
(30, 15, '2026-01-11 19:40:57'),
(31, 10, '2026-05-01 22:18:41'),
(31, 12, '2026-08-16 22:33:01'),
(31, 14, '2025-10-25 15:48:03'),
(32, 17, '2026-05-20 10:11:05'),
(33, 5, '2025-06-23 13:07:56'),
(34, 2, '2025-10-08 07:03:36'),
(34, 3, '2025-11-12 23:01:18'),
(34, 9, '2026-07-31 18:05:58'),
(34, 16, '2025-10-13 06:03:30'),
(35, 1, '2026-05-02 23:22:16'),
(35, 5, '2025-09-25 06:20:50'),
(35, 9, '2026-08-21 15:55:13'),
(36, 9, '2025-09-10 23:24:15'),
(37, 1, '2026-05-31 00:24:17'),
(37, 6, '2026-03-15 17:09:11'),
(38, 18, '2025-09-02 08:36:22'),
(39, 2, '2026-08-21 23:50:54'),
(39, 11, '2026-02-19 12:08:56'),
(39, 16, '2026-02-24 06:43:36'),
(40, 1, '2026-08-01 07:56:34'),
(40, 13, '2025-07-04 21:58:27'),
(40, 17, '2026-05-16 18:52:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_amenities_name` (`name`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_bookings_reference` (`reference`),
  ADD KEY `idx_bookings_guest_created` (`guest_user_id`,`created_at`),
  ADD KEY `idx_bookings_property_dates` (`property_id`,`check_in`,`check_out`),
  ADD KEY `idx_bookings_status_created` (`status`,`created_at`),
  ADD KEY `fk_bookings_room_type` (`room_type_id`);

--
-- Indexes for table `content_pages`
--
ALTER TABLE `content_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_content_pages_reference` (`reference`),
  ADD UNIQUE KEY `uq_content_pages_slug` (`slug`),
  ADD KEY `idx_content_pages_status_publish` (`status`,`publish_at`),
  ADD KEY `fk_content_pages_author` (`author_user_id`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_destinations_slug` (`slug`),
  ADD KEY `idx_destinations_status_name` (`status`,`name`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_payments_reference` (`reference`),
  ADD UNIQUE KEY `uq_payments_provider_transaction` (`provider`,`provider_transaction_id`),
  ADD KEY `idx_payments_booking_status` (`booking_id`,`status`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_properties_slug` (`slug`),
  ADD UNIQUE KEY `uq_properties_external_code` (`external_code`),
  ADD KEY `idx_properties_destination_status` (`destination_id`,`status`),
  ADD KEY `idx_properties_owner_status` (`owner_user_id`,`status`);

--
-- Indexes for table `property_amenities`
--
ALTER TABLE `property_amenities`
  ADD PRIMARY KEY (`property_id`,`amenity_id`),
  ADD KEY `fk_property_amenities_amenity` (`amenity_id`);

--
-- Indexes for table `property_images`
--
ALTER TABLE `property_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_property_images_order` (`property_id`,`sort_order`);

--
-- Indexes for table `property_staff`
--
ALTER TABLE `property_staff`
  ADD PRIMARY KEY (`property_id`,`user_id`),
  ADD KEY `idx_property_staff_user_status` (`user_id`,`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_name` (`name`);

--
-- Indexes for table `room_inventory`
--
ALTER TABLE `room_inventory`
  ADD PRIMARY KEY (`room_type_id`,`inventory_date`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_room_types_property_name` (`property_id`,`name`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_support_tickets_reference` (`reference`),
  ADD KEY `idx_support_tickets_status_priority` (`status`,`priority`),
  ADD KEY `fk_support_tickets_user` (`user_id`),
  ADD KEY `fk_support_tickets_assignee` (`assigned_to`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`),
  ADD KEY `fk_system_settings_user` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_role_status` (`role_id`,`status`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`user_id`,`property_id`),
  ADD KEY `fk_wishlists_property` (`property_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_pages`
--
ALTER TABLE `content_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_guest` FOREIGN KEY (`guest_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `content_pages`
--
ALTER TABLE `content_pages`
  ADD CONSTRAINT `fk_content_pages_author` FOREIGN KEY (`author_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `fk_properties_destination` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_properties_owner` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `property_amenities`
--
ALTER TABLE `property_amenities`
  ADD CONSTRAINT `fk_property_amenities_amenity` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_property_amenities_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `property_images`
--
ALTER TABLE `property_images`
  ADD CONSTRAINT `fk_property_images_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `property_staff`
--
ALTER TABLE `property_staff`
  ADD CONSTRAINT `fk_property_staff_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_property_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_inventory`
--
ALTER TABLE `room_inventory`
  ADD CONSTRAINT `fk_room_inventory_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_types`
--
ALTER TABLE `room_types`
  ADD CONSTRAINT `fk_room_types_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `fk_support_tickets_assignee` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_support_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `fk_system_settings_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `fk_user_preferences_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `fk_wishlists_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wishlists_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
