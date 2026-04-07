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
-- Table structure for table `product_purchased`
--

CREATE TABLE `product_purchased` (
  `id_gen` varchar(30) NOT NULL,
  `customername` varchar(100) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phonenumber` json NOT NULL COMMENT '{"cc":"","number":""}',
  `date_scheduled` varchar(10) NOT NULL,
  `time_scheduled` int NOT NULL COMMENT '24hrs',
  `haspaid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0,1',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `product_variant_id_ref` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product_purchased`
--
ALTER TABLE `product_purchased`
  ADD PRIMARY KEY (`id_gen`),
  ADD KEY `purchased_item_ref_fk` (`product_variant_id_ref`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_purchased`
--
ALTER TABLE `product_purchased`
  ADD CONSTRAINT `purchased_item_ref_fk` FOREIGN KEY (`product_variant_id_ref`) REFERENCES `product_variant` (`id_ai`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
