-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: global_mysql:3306
-- Generation Time: Mar 17, 2026 at 01:54 AM
-- Server version: 8.0.45
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cocohairsignature_com`
--

-- --------------------------------------------------------

--
-- Table structure for table `product_variant`
--

CREATE TABLE `product_variant` (
  `id_ai` bigint NOT NULL,
  `product_list_id_ref` bigint NOT NULL,
  `price` decimal(11,2) NOT NULL COMMENT 'USD',
  `name` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_variant`
--

INSERT INTO `product_variant` (`id_ai`, `product_list_id_ref`, `price`, `name`, `description`) VALUES
(1, 1, 250.00, '(Hair not provided)', ''),
(2, 2, 320.00, 'bob/shoulder length', ''),
(3, 4, 350.00, 'mid-back', ''),
(4, 4, 450.00, 'lower back', ''),
(5, 4, 650.00, 'waist length', ''),
(6, 5, 310.00, 'mid-back', ''),
(7, 5, 400.00, 'lower back', ''),
(8, 5, 460.00, 'waist length', ''),
(9, 6, 550.00, 'mid back', ''),
(10, 6, 850.00, 'lower back', ''),
(11, 6, 1200.00, 'waist length', ''),
(12, 7, 550.00, 'mid back', ''),
(13, 7, 850.00, 'lower back', ''),
(14, 7, 1200.00, 'waist length', ''),
(15, 8, 300.00, 'mid-back', ''),
(16, 8, 400.00, 'lower back', ''),
(17, 8, 600.00, 'waist length', ''),
(18, 9, 270.00, 'mid-back', ''),
(19, 9, 370.00, 'lower back', ''),
(20, 9, 450.00, 'waist length', ''),
(21, 11, 250.00, 'mid-back', ''),
(22, 11, 350.00, 'lower back', ''),
(23, 11, 400.00, 'waist length', ''),
(24, 12, 240.00, 'mid-back', ''),
(25, 12, 300.00, 'lower back', ''),
(26, 12, 350.00, 'waist', ''),
(27, 24, 235.00, 'mid back', ''),
(28, 24, 300.00, 'waist length', ''),
(29, 24, 320.00, 'butt', ''),
(34, 10, 280.00, 'bob/shoulder length', ''),
(35, 13, 260.00, 'mid-back', ''),
(36, 13, 300.00, 'lower back', ''),
(37, 13, 370.00, 'waist length', ''),
(38, 14, 320.00, 'mid-back', ''),
(39, 14, 450.00, 'lower back', ''),
(40, 14, 600.00, 'waist length', ''),
(41, 15, 300.00, 'mid-back', ''),
(42, 15, 380.00, 'lower back', ''),
(43, 15, 450.00, 'waist length', ''),
(44, 16, 350.00, 'mid-back', ''),
(45, 16, 450.00, 'lower back', ''),
(46, 16, 620.00, 'waist length', ''),
(47, 17, 280.00, 'mid-back', ''),
(48, 17, 350.00, 'lower back', ''),
(49, 17, 450.00, 'waist length', ''),
(50, 18, 370.00, 'mid-back', ''),
(51, 18, 470.00, 'lower back', ''),
(52, 18, 620.00, 'waist length', ''),
(53, 19, 300.00, 'mid-back', ''),
(54, 19, 370.00, 'lower back', ''),
(55, 19, 450.00, 'waist length', ''),
(56, 20, 240.00, '(with or without curls)', ''),
(57, 21, 300.00, 'mid back', ''),
(58, 21, 400.00, 'waist length', ''),
(59, 21, 450.00, 'butt length', ''),
(60, 22, 250.00, 'mid back', ''),
(61, 22, 350.00, 'waist length', ''),
(62, 22, 380.00, 'butt length', ''),
(63, 23, 250.00, 'mid back', ''),
(64, 23, 350.00, 'waist length', ''),
(65, 23, 380.00, 'butt length', ''),
(66, 23, 0.00, 'Ombre', ''),
(67, 24, 0.00, 'Ombre', ''),
(71, 25, 250.00, 'mid back', ''),
(72, 25, 350.00, 'waist', ''),
(73, 25, 380.00, 'butt', ''),
(74, 26, 250.00, 'mid back', ''),
(75, 26, 350.00, 'waist', ''),
(76, 26, 380.00, 'butt', ''),
(77, 27, 300.00, 'mid back', ''),
(78, 27, 400.00, 'waist', ''),
(79, 27, 450.00, 'butt', ''),
(80, 28, 350.00, 'mid back', ''),
(81, 28, 435.00, 'waist', ''),
(82, 28, 450.00, 'butt', ''),
(83, 29, 285.00, 'mid back', ''),
(84, 29, 330.00, 'waist', ''),
(85, 29, 350.00, 'butt', ''),
(86, 30, 350.00, 'mid back', ''),
(87, 30, 450.00, 'lower back', ''),
(88, 30, 500.00, 'waist length', ''),
(89, 31, 250.00, 'mid back', ''),
(90, 31, 280.00, 'waist', '(Hair Not Included)'),
(91, 32, 270.00, 'mid back', ''),
(92, 32, 300.00, 'lower back', ''),
(93, 33, 270.00, 'mid back', ''),
(94, 33, 300.00, 'waist', '(Hair Not Included)'),
(95, 34, 220.00, 'shoulder length', ''),
(96, 34, 260.00, 'mid back', ''),
(97, 34, 320.00, 'waist ', '(Hair Not Included)'),
(98, 35, 220.00, 'shoulder length', ''),
(99, 35, 260.00, 'mid back', ''),
(100, 35, 320.00, 'waist ', '(Hair Not Included)'),
(101, 36, 250.00, 'mid back', ''),
(102, 36, 300.00, 'waist', ''),
(103, 37, 135.00, '', 'for 6 straight back stitch braids (Additional braid $15 each)'),
(104, 38, 200.00, 'Regular', ''),
(105, 39, 200.00, 'Regular', ''),
(106, 40, 200.00, 'Regular', ''),
(107, 41, 280.00, 'mid back', ''),
(108, 41, 350.00, 'waist', ''),
(109, 42, 280.00, 'mid back', ''),
(110, 42, 360.00, 'waist', ''),
(111, 43, 250.00, 'mid back', ''),
(112, 43, 300.00, 'waist', ''),
(113, 44, 350.00, 'mid back', ''),
(114, 44, 400.00, 'waist', ''),
(115, 45, 230.00, 'shoulder length', ''),
(116, 45, 260.00, 'mid back', ''),
(117, 45, 300.00, 'waist', '(Hair Not Included)'),
(118, 46, 220.00, 'shoulder length', ''),
(119, 46, 280.00, 'mid back', ''),
(120, 46, 350.00, 'waist', '(Hair Not Included)'),
(121, 47, 220.00, '', ''),
(122, 48, 230.00, 'mid back', ''),
(123, 48, 260.00, 'waist', ''),
(124, 49, 250.00, 'mid back', ''),
(125, 49, 300.00, 'waist', ''),
(126, 50, 175.00, '', ''),
(127, 51, 250.00, '', 'with or without curls'),
(128, 52, 250.00, '', ''),
(129, 53, 250.00, '', ''),
(130, 54, 180.00, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product_variant`
--
ALTER TABLE `product_variant`
  ADD PRIMARY KEY (`id_ai`),
  ADD KEY `product_list_id_fk` (`product_list_id_ref`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product_variant`
--
ALTER TABLE `product_variant`
  MODIFY `id_ai` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_variant`
--
ALTER TABLE `product_variant`
  ADD CONSTRAINT `product_list_id_fk` FOREIGN KEY (`product_list_id_ref`) REFERENCES `product_lists` (`id_ai`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
