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
-- Table structure for table `product_lists`
--

CREATE TABLE `product_lists` (
  `id_ai` bigint NOT NULL,
  `hair_name` varchar(200) NOT NULL,
  `description` varchar(250) NOT NULL,
  `category` bigint NOT NULL,
  `hair_images` json NOT NULL COMMENT '[]',
  `time_range` varchar(50) NOT NULL DEFAULT '3hrs',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0,1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_lists`
--

INSERT INTO `product_lists` (`id_ai`, `hair_name`, `description`, `category`, `hair_images`, `time_range`, `date_created`, `date_modified`, `is_active`) VALUES
(1, '', '', 1, '[54]', '2 hours', '2026-03-05 04:57:14', '2026-03-05 06:03:25', 1),
(2, 'Bob Boho Knotless Braids (Smedium)', 'For curls only, you can bring 2packs of wavy human hair of your choice, I only provide synthetic.', 2, '[46]', '3hrs', '2026-03-05 05:05:03', '2026-03-05 06:03:52', 1),
(3, 'boho knotless braids', 'For curls only, you can bring 2packs of wavy human hair of your choice, I only provide synthetic.', 2, '[22]', '3hrs', '2026-03-05 05:07:03', '2026-03-05 06:04:25', 1),
(4, 'boho knotless braids (Small)', 'For curls only, you can bring 2packs of wavy human hair of your choice, I only provide synthetic.', 2, '[28]', '3hrs ($50 extra for fuller curls)', '2026-03-05 05:07:38', '2026-03-05 06:04:37', 1),
(5, 'boho knotless braids (Medium)', 'For curls only, you can bring 2packs of wavy human hair of your choice, I only provide synthetic.', 2, '[23]', '3hrs ($50 extra for fuller curls)', '2026-03-05 05:34:01', '2026-03-05 06:04:52', 1),
(6, 'Bora Braids (Small/Smedium/Medium)', 'Bring 3 packs of wavy human hair of your choice. (Note that this pic is Small.)', 3, '[52]', '4hrs', '2026-03-05 05:41:42', '2026-03-05 06:05:08', 1),
(7, 'Bora Braids (Smedium/Medium)', 'Bring 3 packs of wavy human hair of your choice', 3, '[50]', '4 hrs', '2026-03-05 05:46:31', '2026-03-05 06:05:18', 1),
(8, 'knotless (Small)', '', 4, '[10]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:16:30', 1),
(9, 'knotless (Smedium)', '', 4, '[7]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:16:33', 1),
(10, 'knotless (Smedium)', '', 4, '[47]', '3hrs', '2026-03-05 06:15:01', '2026-03-05 06:17:06', 1),
(11, 'knotless (Medium)', '', 4, '[9]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:17:03', 1),
(12, 'knotless (Large)', '', 4, '[5]', '1.5 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(13, 'knotless (Jumbo)', '', 4, '[8]', '2.5 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(14, 'Triangle Knotless Braids (Small)', '', 4, '[4]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(15, 'Triangle Knotless Braids (Medium)', '', 4, '[3]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(16, 'goddess knotless braids (Small)', 'For curls only, you can bring 2packs of wavy human hair of your choice, I only provide synthetic.', 5, '[2]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(17, 'goddess knotless braids (Medium)', 'For curls only, you can bring 2packs of wavy human hair of your choice, I only provide synthetic.', 5, '[21]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(18, 'Triangle Goddess Knotless Braids (Small)', 'For curls only, you can bring 2packs of wavy human hair of your choice, I only provide synthetic.', 5, '[32]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(19, 'French Curls Knotless/BoxBraids (Smedium)', 'Bring 3 packs of Fretress French Curl Braiding Hair', 5, '[49]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(20, 'XL Knotless Braids', 'For curls you can bring 1pack of wavy hair', 5, '[45]', '1.5 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(21, 'box braids (Small)', '', 6, '[18]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(22, 'box braids (Smedium)', '', 6, '[17]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(23, 'box braids (Medium)', '', 6, '[16]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(24, 'box braids (Jumbo)', '', 6, '[1]', '2.5 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(25, 'Triangle Braids (Small)', '', 6, '[31]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(26, 'Triangle Braids (Smedium)', '', 6, '[15]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(27, 'Goddess BoxBraids (Smedium)', 'For curls only, you can bring 2packs of wavy human hair of your choice', 7, '[29]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(28, 'boho goddess boxBraids (Smedium)', 'For curls only, you can bring 2packs of wavy human hair of your choice', 7, '[12]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(29, 'boho goddess boxBraids (Medium)', 'For curls only, you can bring 2packs of wavy human hair of your choice', 7, '[13]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(30, 'Indie / Distressed Locs', 'Hair not provided', 8, '[48]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(31, 'goddess locs (Medium)', 'Bring 7 bags Jamaican twist hair', 8, '[27]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(32, 'Triangle Boho Goddess Locs (Medium)', 'Bring Jamaican twist hair', 8, '[35]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(33, 'Boho goddess locs (Medium)', 'Bring Jamaican twist hair', 8, '[26]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(34, 'butterfly locs (Medium)', 'Bring water wave crotchet hair', 8, '[36]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 06:13:47', 1),
(35, 'Butterfly locs (Medium)', 'Bring 6 packs of water wave crotchet hair or 6 packs of Marley hair (depending on the look you want)', 8, '[24]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 15:19:52', 1),
(36, 'Lemonade Braids', '', 9, '[58]', '2.5 hours', '2026-03-05 05:54:22', '2026-03-05 15:19:52', 1),
(37, 'Stitch Braids', '6 straight back stitch braids', 10, '[55]', '2 hours', '2026-03-05 05:54:22', '2026-03-05 15:19:52', 1),
(38, 'Crotchet Braids', '(I do not provide hair for any crotchet styles!)', 11, '[30]', '1.5 hours', '2026-03-05 05:54:22', '2026-03-05 16:07:42', 1),
(39, 'Crotchet Braids', '(I do not provide hair for any crotchet styles!)', 11, '[33]', '1.5 hours', '2026-03-05 05:54:22', '2026-03-05 16:07:42', 1),
(40, 'Crotchet Braids', '(I do not provide hair for any crotchet styles!)', 11, '[34]', '1.5 hours', '2026-03-05 05:54:22', '2026-03-05 16:11:15', 1),
(41, 'Feedin Braids', '', 12, '[39]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(42, 'Feedin Braids With Knotless Braids', '', 12, '[37]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(43, 'Feedin Braids with Knotless Triangle', '', 12, '[19]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(44, 'Feedin Goddess Braids', '', 12, '[20]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(45, 'Spring Twists', 'Hair not included', 13, '[38]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(46, 'passion twists (Medium)', 'Hair not included', 13, '[25]', '3 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(47, 'Kids Knotless Braids with Curly ends (Smedium)', '', 14, '[56]', '2.5 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(48, 'Kids BoxBraids', '', 14, '[6]', '2.5 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(49, 'Kids Goddess BoxBraids', '', 14, '[14]', '2.5 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(50, 'Triangle Braids (Medium)', '', 14, '[42]', '2.5 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(51, 'Small Goddess Knotless Braids', '', 14, '[43]', '2.5 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(52, 'Goddess BoxBraids (Small)', '', 14, '[44]', '2.5 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(53, 'Goddess BoxBraids (Medium)', '', 14, '[40]', '2.5 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1),
(54, 'Kids Knotless Braids (Large)', 'Beads not included', 14, '[41]', '2 hours', '2026-03-05 05:54:22', '2026-03-05 16:10:16', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product_lists`
--
ALTER TABLE `product_lists`
  ADD PRIMARY KEY (`id_ai`),
  ADD KEY `p_category_fk` (`category`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product_lists`
--
ALTER TABLE `product_lists`
  MODIFY `id_ai` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_lists`
--
ALTER TABLE `product_lists`
  ADD CONSTRAINT `p_category_fk` FOREIGN KEY (`category`) REFERENCES `product_category` (`id_ai`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
