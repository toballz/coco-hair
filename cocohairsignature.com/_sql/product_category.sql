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
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `id_ai` bigint NOT NULL,
  `category_name` varchar(150) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`id_ai`, `category_name`, `date_created`) VALUES
(1, 'Miracle Knots', '2026-03-04 23:42:16'),
(2, 'Boho Knotless Braids', '2026-03-05 04:43:23'),
(3, 'Bora Braids', '2026-03-05 04:43:23'),
(4, 'Knotless Braids', '2026-03-05 04:43:37'),
(5, 'Goddess Knotless Braids', '2026-03-05 04:43:37'),
(6, 'BoxBraids', '2026-03-04 23:42:16'),
(7, 'Goddess/Boho BoxBraids', '2026-03-05 04:43:23'),
(8, 'Locs', '2026-03-05 04:43:37'),
(9, 'Lemonade Braids', '2026-03-05 04:43:37'),
(10, 'Stitch Braids', '2026-03-04 23:42:16'),
(11, 'Crotchet Braids', '2026-03-05 04:43:23'),
(12, 'Feedin Braids', '2026-03-05 04:43:23'),
(13, 'Passion Twists (not taking appts for these)', '2026-03-05 04:43:37'),
(14, 'Kids Braids (5 - 9yrs)', '2026-03-05 04:43:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`id_ai`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `id_ai` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
