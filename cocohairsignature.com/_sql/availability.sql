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
-- Table structure for table `availability`
--

CREATE TABLE `availability` (
  `id_ai` int NOT NULL,
  `namer` varchar(100) NOT NULL,
  `description` json NOT NULL,
  `extra1` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `availability`
--

INSERT INTO `availability` (`id_ai`, `namer`, `description`, `extra1`) VALUES
(6, 'weekly', '{\"friday\": \"0830, 1130, 1630, 1730\", \"monday\": \"0830, 1130, 1630, 1730\", \"sunday\": \"\", \"tuesday\": \"0830, 1130, 1630, 1730\", \"saturday\": \"0700, 1030, 1330\", \"thursday\": \"0830, 1130, 1630, 1730\", \"wednesday\": \"0830, 1130, 1630, 1730\"}', ''),
(7, 'override', '[{\"date\": 20260308, \"time\": \"1300\"}, {\"date\": 20260315, \"time\": \"0800, 1130, 1430\"}]', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `availability`
--
ALTER TABLE `availability`
  ADD PRIMARY KEY (`id_ai`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `availability`
--
ALTER TABLE `availability`
  MODIFY `id_ai` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
