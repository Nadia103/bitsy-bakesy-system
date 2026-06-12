-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2026 at 03:39 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bitsy`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customerID` int(11) NOT NULL,
  `customerName` varchar(100) NOT NULL,
  `customerEmail` varchar(100) NOT NULL,
  `customerPhoneNo` varchar(15) NOT NULL,
  `customerPassword` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customerID`, `customerName`, `customerEmail`, `customerPhoneNo`, `customerPassword`, `created_at`) VALUES
(0, 'Walk-in', 'walkin@bitsy.com', '0000000000', '', '2025-12-29 01:08:34'),
(1, 'Aisyah', 'aisyah@gmail.com', '0179209442', '$2y$10$I/GjZzKIwayAhQ9cxWt8u.HrsRNxq4dczMLHQVkdtWN1nscbhmOxy', '2025-11-13 05:13:03'),
(2, 'Mimi ', 'mimi@gmail.com', '0139809221', 'Mimi123', '2025-11-21 02:08:37'),
(3, 'Nur Natasya', 'tasya@gmail.com', '0199209441', '$2y$10$xwRgiUOyXx4X497UZx/zw.QmUnFSvxzdz8wlMSuYAvlPlxXaeudQe', '2025-11-21 03:20:47'),
(5, 'Nur Alisha', 'alisha@gmail.com', '0139266821', '$2y$10$W6610AbUzT9.qw5yFGU.xOSt7V7aONbbs9Dna5AgnScHOxDlGzCJy', '2026-01-03 02:09:50'),
(6, 'Nur Ana', 'ana@gmail.com', '0193909441', '$2y$10$Vsq16p5UkXu22u.ANQ7oC.r/ZsyUjdQYn/rExdKlNVirn9GmY0SLW', '2026-01-12 02:48:59'),
(7, 'Laila', 'laila@gmail.com', '0139209441', '$2y$10$sI.UkPMaU/gOfWqgkj3gA.R1XKTYve/d5jPcIncJ3xHQHQCom8/yu', '2026-01-12 02:51:30'),
(8, 'Nur Maisarah', 'maisarah@gmail.com', '0134509441', '$2y$10$OYCXzlrFs8SK4g.0jCEq5.jT5L9bzUmYYMhtdR04/59bfjUXGwjAm', '2026-01-12 15:25:23'),
(9, 'siti', 'siti@bitsy.com', '0199209441', '$2y$10$5LfqoRfcwHZM7qmHJfnQROiAtdpBRYJ05YDIAzFUq9JCW1N3kDFBi', '2026-01-26 04:46:42');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menuID` int(11) NOT NULL,
  `menuName` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `menuType` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menuID`, `menuName`, `description`, `category`, `menuType`, `image`, `created_at`) VALUES
(1, 'Carrot Cake', 'Moist carrot cake with cream cheese frosting', 'Cake', 'Product', 'carrot.jpg', '2025-11-13 08:28:19'),
(2, 'Ovomaltine Cake', 'Chocolate cake with Ovomaltine crunch', 'Cake', 'Product', 'ovomaltine.jpg', '2025-11-13 08:28:19'),
(3, 'Medovik Honey Cake', 'Russian layered honey cake', 'Cake', 'Product', 'medovik.jpg', '2025-11-13 08:28:19'),
(4, 'Indulgence Cake', 'Rich chocolate indulgence cake', 'Cake', 'Product', 'indulgence.jpg', '2025-11-13 08:28:19'),
(6, 'Burnt Cheesecake', 'Creamy Basque burnt cheesecake', 'Cake', 'Product', 'burnt.jpg', '2025-11-13 08:28:19'),
(7, 'Tiramisu In Box', 'Classic tiramisu served in a box', 'Cake', 'Product', 'tiramisu_box.jpg', '2025-11-13 08:28:19'),
(8, 'Dubai Choco Box', 'Premium chocolate dessert box', 'What’s New', 'Product', 'dubai_choco_box.png', '2025-11-13 08:28:19'),
(9, 'Creme Brulee', 'Caramelized custard dessert', 'What’s New', 'Product', 'creme_brulee.jpg', '2025-11-13 08:28:19'),
(10, 'Mochi Burnt Cheesecake', 'Soft mochi layered with burnt cheesecake', 'What’s New', 'Product', 'mochi.jpg', '2025-11-13 08:28:19'),
(11, 'Creamhorn', 'Pastry horn filled with sweet cream', 'Pastry', 'Product', 'creamhorn.jpg', '2025-11-13 08:28:19'),
(12, 'Sardine Puff', 'Flaky pastry filled with sardine mix', 'Pastry', 'Product', 'sardine.jpg', '2025-11-13 08:28:19'),
(13, 'Chicken Pie', 'Savory pie with creamy chicken filling', 'Pastry', 'Product', 'chicken.jpg', '2025-11-13 08:28:19'),
(14, 'Lasagna', 'Layered pasta with beef and cheese', 'Pastry', 'Product', 'lasagna.jpg', '2025-11-13 08:28:19'),
(15, 'Japanese Creampuff (1 pc)', 'Soft creampuff filled with custard cream', 'Dessert', 'Product', 'creampuff.jpg', '2025-11-13 08:28:19'),
(16, 'Japanese Creampuff (3 pcs)', 'Set of 3 soft creampuffs filled with custard cream', 'Dessert', 'Product', 'creampuff.jpg', '2025-11-13 08:28:19'),
(17, 'Ferrero Japanese Creampuff (1 pc)', 'Creampuff with Ferrero chocolate filling', 'Dessert', 'Product', 'ferrero.jpg', '2025-11-13 08:28:19'),
(18, 'Ferrero Japanese Creampuff (3 pcs)', 'Set of 3 Ferrero-flavored creampuffs', 'Dessert', 'Product', 'ferrero.jpg', '2025-11-13 08:28:19'),
(19, 'Mini Pavlova', 'Mini pavlova with fruits and cream', 'Dessert', 'Product', 'babylova.jpg', '2025-11-13 08:28:19'),
(20, 'Caramel Cinnamon Roll', 'Cinnamon roll topped with caramel glaze', 'Dessert', 'Product', 'c_caramel.jpg', '2025-11-13 08:28:19'),
(21, 'Creamcheese Cinnamon Roll', 'Cinnamon roll with cream cheese frosting', 'Dessert', 'Product', 'c_cheese.jpg', '2025-11-13 08:28:19'),
(22, 'Pistachio Cinnamon Roll', 'Cinnamon roll with pistachio topping', 'Dessert', 'Product', 'pistachio_cinnamon_roll.jpg', '2025-11-13 08:28:19'),
(23, 'Biscoff Cinnamon Roll', 'Cinnamon roll with Biscoff spread', 'Dessert', 'Product', 'c_biscoff.jpg', '2025-11-13 08:28:19'),
(24, 'Choc Hazel Soft Cookie', 'Soft baked chocolate hazelnut cookie', 'Dessert', 'Product', 'csc.jpg', '2025-11-13 08:28:19'),
(25, 'Biscoff Soft Cookie', 'Soft baked cookie with Biscoff flavor', 'Dessert', 'Product', 'bsc.jpg', '2025-11-13 08:28:19'),
(26, 'Chocolate Tart', 'Rich chocolate tart slice', 'Dessert', 'Product', 'choctart.jpg', '2025-11-13 08:28:19'),
(27, 'Mix Tartlets (1 pc)', 'Mini tartlet with assorted flavors', 'Dessert', 'Product', 'desserts.jpg', '2025-11-13 08:28:19'),
(28, 'Mix Tartlets (4 pcs)', 'Set of 4 mini tartlets with assorted flavors', 'Dessert', 'Product', 'desserts.jpg', '2025-11-13 08:28:19');

-- --------------------------------------------------------

--
-- Table structure for table `menu_new`
--

CREATE TABLE `menu_new` (
  `menuID` int(11) NOT NULL,
  `menuName` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `menuType` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stockQuantity` int(11) DEFAULT 0,
  `unitType` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `minLevel` int(11) DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_new`
--

INSERT INTO `menu_new` (`menuID`, `menuName`, `price`, `description`, `category`, `menuType`, `image`, `stockQuantity`, `unitType`, `created_at`, `minLevel`) VALUES
(1, 'Carrot Cake', '0.00', 'Moist carrot cake with cream cheese frosting', 'Cake', 'Product', 'carrot.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(2, 'Ovomaltine Cake', '0.00', 'Chocolate cake with Ovomaltine crunch', 'Cake', 'Product', 'ovomaltine.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(3, 'Medovik Honey Cake', '0.00', 'Russian layered honey cake', 'Cake', 'Product', 'medovik.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(4, 'Indulgence Cake', '0.00', 'Rich chocolate indulgence cake', 'Cake', 'Product', 'indulgence.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(5, 'Pistachio Cake', '0.00', 'Nutty pistachio cake with creamy layers', 'Cake', 'Product', 'pistachio_cake.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(6, 'Burnt Cheesecake', '0.00', 'Creamy Basque burnt cheesecake', 'Cake', 'Product', 'burnt.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(7, 'Tiramisu In Box', '0.00', 'Classic tiramisu served in a box', 'Cake', 'Product', 'tiramisu_box.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(8, 'Dubai Choco Box', '0.00', 'Premium chocolate dessert box', 'What’s New', 'Product', 'dubai_choco_box.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(9, 'Creme Brulee', '0.00', 'Caramelized custard dessert', 'What’s New', 'Product', 'creme_brulee.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(10, 'Mochi Burnt Cheesecake', '0.00', 'Soft mochi layered with burnt cheesecake', 'What’s New', 'Product', 'mochi.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(11, 'Creamhorn', '0.00', 'Pastry horn filled with sweet cream', 'Pastry', 'Product', 'creamhorn.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(12, 'Sardine Puff', '0.00', 'Flaky pastry filled with sardine mix', 'Pastry', 'Product', 'sardine.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(13, 'Chicken Pie', '0.00', 'Savory pie with creamy chicken filling', 'Pastry', 'Product', 'chicken.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(14, 'Lasagna', '0.00', 'Layered pasta with beef and cheese', 'Pastry', 'Product', 'lasagna.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(15, 'Japanese Creampuff (1 pc)', '0.00', 'Soft creampuff filled with custard cream', 'Dessert', 'Product', 'creampuff.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(16, 'Japanese Creampuff (3 pcs)', '0.00', 'Set of 3 soft creampuffs filled with custard cream', 'Dessert', 'Product', 'creampuff.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(17, 'Ferrero Japanese Creampuff (1 pc)', '0.00', 'Creampuff with Ferrero chocolate filling', 'Dessert', 'Product', 'ferrero.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(18, 'Ferrero Japanese Creampuff (3 pcs)', '0.00', 'Set of 3 Ferrero-flavored creampuffs', 'Dessert', 'Product', 'ferrero.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(19, 'Mini Pavlova', '0.00', 'Mini pavlova with fruits and cream', 'Dessert', 'Product', 'babylova.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(20, 'Caramel Cinnamon Roll', '0.00', 'Cinnamon roll topped with caramel glaze', 'Dessert', 'Product', 'c_caramel.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(21, 'Creamcheese Cinnamon Roll', '0.00', 'Cinnamon roll with cream cheese frosting', 'Dessert', 'Product', 'c_cheese.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(22, 'Pistachio Cinnamon Roll', '0.00', 'Cinnamon roll with pistachio topping', 'Dessert', 'Product', 'pistachio_cinnamon_roll.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(23, 'Biscoff Cinnamon Roll', '0.00', 'Cinnamon roll with Biscoff spread', 'Dessert', 'Product', 'c_biscoff.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(24, 'Choc Hazel Soft Cookie', '0.00', 'Soft baked chocolate hazelnut cookie', 'Dessert', 'Product', 'csc.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(25, 'Biscoff Soft Cookie', '0.00', 'Soft baked cookie with Biscoff flavor', 'Dessert', 'Product', 'bsc.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(26, 'Chocolate Tart', '0.00', 'Rich chocolate tart slice', 'Dessert', 'Product', 'choctart.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(27, 'Mix Tartlets (1 pc)', '0.00', 'Mini tartlet with assorted flavors', 'Dessert', 'Product', 'desserts.jpg', 0, NULL, '2025-11-13 08:28:19', 3),
(28, 'Mix Tartlets (4 pcs)', '0.00', 'Set of 4 mini tartlets with assorted flavors', 'Dessert', 'Product', 'desserts.jpg', 0, NULL, '2025-11-13 08:28:19', 3);

-- --------------------------------------------------------

--
-- Table structure for table `menu_old`
--

CREATE TABLE `menu_old` (
  `menuID` int(11) NOT NULL,
  `menuName` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `menuType` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_old`
--

INSERT INTO `menu_old` (`menuID`, `menuName`, `description`, `category`, `menuType`, `image`, `created_at`) VALUES
(1, 'Carrot Cake', 'Moist carrot cake with cream cheese frosting', 'Cake', 'Product', 'carrot.jpg', '2025-11-13 08:28:19'),
(2, 'Ovomaltine Cake', 'Chocolate cake with Ovomaltine crunch', 'Cake', 'Product', 'ovomaltine.jpg', '2025-11-13 08:28:19'),
(3, 'Medovik Honey Cake', 'Russian layered honey cake', 'Cake', 'Product', 'medovik.jpg', '2025-11-13 08:28:19'),
(4, 'Indulgence Cake', 'Rich chocolate indulgence cake', 'Cake', 'Product', 'indulgence.jpg', '2025-11-13 08:28:19'),
(5, 'Pistachio Cake', 'Nutty pistachio cake with creamy layers', 'Cake', 'Product', 'pistachio_cake.jpg', '2025-11-13 08:28:19'),
(6, 'Burnt Cheesecake', 'Creamy Basque burnt cheesecake', 'Cake', 'Product', 'burnt.jpg', '2025-11-13 08:28:19'),
(7, 'Tiramisu In Box', 'Classic tiramisu served in a box', 'Cake', 'Product', 'tiramisu_box.jpg', '2025-11-13 08:28:19'),
(8, 'Dubai Choco Box', 'Premium chocolate dessert box', 'What’s New', 'Product', 'dubai_choco_box.jpg', '2025-11-13 08:28:19'),
(9, 'Creme Brulee', 'Caramelized custard dessert', 'What’s New', 'Product', 'creme_brulee.jpg', '2025-11-13 08:28:19'),
(10, 'Mochi Burnt Cheesecake', 'Soft mochi layered with burnt cheesecake', 'What’s New', 'Product', 'mochi.jpg', '2025-11-13 08:28:19'),
(11, 'Creamhorn', 'Pastry horn filled with sweet cream', 'Pastry', 'Product', 'creamhorn.jpg', '2025-11-13 08:28:19'),
(12, 'Sardine Puff', 'Flaky pastry filled with sardine mix', 'Pastry', 'Product', 'sardine.jpg', '2025-11-13 08:28:19'),
(13, 'Chicken Pie', 'Savory pie with creamy chicken filling', 'Pastry', 'Product', 'chicken.jpg', '2025-11-13 08:28:19'),
(14, 'Lasagna', 'Layered pasta with beef and cheese', 'Pastry', 'Product', 'lasagna.jpg', '2025-11-13 08:28:19'),
(15, 'Japanese Creampuff (1 pc)', 'Soft creampuff filled with custard cream', 'Dessert', 'Product', 'creampuff.jpg', '2025-11-13 08:28:19'),
(16, 'Japanese Creampuff (3 pcs)', 'Set of 3 soft creampuffs filled with custard cream', 'Dessert', 'Product', 'creampuff.jpg', '2025-11-13 08:28:19'),
(17, 'Ferrero Japanese Creampuff (1 pc)', 'Creampuff with Ferrero chocolate filling', 'Dessert', 'Product', 'ferrero.jpg', '2025-11-13 08:28:19'),
(18, 'Ferrero Japanese Creampuff (3 pcs)', 'Set of 3 Ferrero-flavored creampuffs', 'Dessert', 'Product', 'ferrero.jpg', '2025-11-13 08:28:19'),
(19, 'Mini Pavlova', 'Mini pavlova with fruits and cream', 'Dessert', 'Product', 'babylova.jpg', '2025-11-13 08:28:19'),
(20, 'Caramel Cinnamon Roll', 'Cinnamon roll topped with caramel glaze', 'Dessert', 'Product', 'c_caramel.jpg', '2025-11-13 08:28:19'),
(21, 'Creamcheese Cinnamon Roll', 'Cinnamon roll with cream cheese frosting', 'Dessert', 'Product', 'c_cheese.jpg', '2025-11-13 08:28:19'),
(22, 'Pistachio Cinnamon Roll', 'Cinnamon roll with pistachio topping', 'Dessert', 'Product', 'pistachio_cinnamon_roll.jpg', '2025-11-13 08:28:19'),
(23, 'Biscoff Cinnamon Roll', 'Cinnamon roll with Biscoff spread', 'Dessert', 'Product', 'c_biscoff.jpg', '2025-11-13 08:28:19'),
(24, 'Choc Hazel Soft Cookie', 'Soft baked chocolate hazelnut cookie', 'Dessert', 'Product', 'csc.jpg', '2025-11-13 08:28:19'),
(25, 'Biscoff Soft Cookie', 'Soft baked cookie with Biscoff flavor', 'Dessert', 'Product', 'bsc.jpg', '2025-11-13 08:28:19'),
(26, 'Chocolate Tart', 'Rich chocolate tart slice', 'Dessert', 'Product', 'choctart.jpg', '2025-11-13 08:28:19'),
(27, 'Mix Tartlets (1 pc)', 'Mini tartlet with assorted flavors', 'Dessert', 'Product', 'desserts.jpg', '2025-11-13 08:28:19'),
(28, 'Mix Tartlets (4 pcs)', 'Set of 4 mini tartlets with assorted flavors', 'Dessert', 'Product', 'desserts.jpg', '2025-11-13 08:28:19');

-- --------------------------------------------------------

--
-- Table structure for table `menu_unit`
--

CREATE TABLE `menu_unit` (
  `unitID` int(11) NOT NULL,
  `menuID` int(11) NOT NULL,
  `unitType` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stockQuantity` int(11) DEFAULT 0,
  `minLevel` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_unit`
--

INSERT INTO `menu_unit` (`unitID`, `menuID`, `unitType`, `price`, `stockQuantity`, `minLevel`, `created_at`) VALUES
(2, 2, 'slice', '16.50', 10, 3, '2025-11-20 13:29:11'),
(3, 3, 'slice', '13.50', 10, 3, '2025-11-20 13:29:11'),
(4, 4, 'slice', '16.50', 10, 3, '2025-11-20 13:29:11'),
(5, 5, 'slice', '14.00', 10, 3, '2025-11-20 13:29:11'),
(6, 6, 'slice', '14.00', 8, 3, '2025-11-20 13:29:11'),
(7, 7, 'slice', '21.00', 10, 3, '2025-11-20 13:29:11'),
(8, 8, 'box', '25.00', 10, 3, '2025-11-20 13:29:11'),
(9, 9, 'piece', '10.00', 10, 3, '2025-11-20 13:29:11'),
(10, 10, 'piece', '15.00', 10, 3, '2025-11-20 13:29:11'),
(11, 11, 'piece', '3.50', 15, 5, '2025-11-20 13:29:11'),
(12, 12, 'piece', '3.50', 15, 5, '2025-11-20 13:29:11'),
(13, 13, 'piece', '4.50', 15, 5, '2025-11-20 13:29:11'),
(14, 14, 'piece', '12.00', 10, 5, '2025-11-20 13:29:11'),
(15, 15, 'piece', '3.50', 14, 5, '2025-11-20 13:29:11'),
(16, 16, 'set', '9.00', 13, 5, '2025-11-20 13:29:11'),
(17, 17, 'piece', '4.00', 15, 5, '2025-11-20 13:29:11'),
(18, 18, 'set', '11.00', 15, 5, '2025-11-20 13:29:11'),
(19, 19, 'piece', '6.00', 15, 5, '2025-11-20 13:29:11'),
(20, 20, 'piece', '7.00', 14, 5, '2025-11-20 13:29:11'),
(21, 21, 'piece', '8.00', 15, 5, '2025-11-20 13:29:11'),
(22, 22, 'piece', '9.00', 15, 5, '2025-11-20 13:29:11'),
(23, 23, 'piece', '9.00', 3, 5, '2025-11-20 13:29:11'),
(24, 24, 'piece', '10.50', 14, 5, '2025-11-20 13:29:11'),
(25, 25, 'piece', '10.50', 9, 5, '2025-11-20 13:29:11'),
(26, 26, 'piece', '10.50', 14, 5, '2025-11-20 13:29:11'),
(27, 27, 'piece', '3.50', 20, 5, '2025-11-20 13:29:11'),
(28, 28, 'piece', '12.00', 20, 5, '2025-11-20 13:29:11'),
(156, 1, 'slice', '15.00', 12, 3, '2025-11-21 03:32:42');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `paymentMethod` varchar(50) NOT NULL,
  `orderDate` datetime NOT NULL DEFAULT current_timestamp(),
  `pickupDate` date DEFAULT NULL,
  `assignedTo` int(11) DEFAULT NULL,
  `orderType` enum('online','walk-in') DEFAULT NULL,
  `staffID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orderID`, `customerID`, `totalAmount`, `status`, `paymentMethod`, `orderDate`, `pickupDate`, `assignedTo`, `orderType`, `staffID`) VALUES
(1, 1, '14.00', 'Completed', 'Online Banking', '2025-11-14 00:50:53', '2025-11-20', 5, NULL, NULL),
(2, 1, '3.50', 'Pending', 'Online Banking', '2025-11-21 03:12:17', '2025-11-29', 1, NULL, NULL),
(3, 1, '60.00', 'Pending', 'Online Banking', '2025-11-21 09:25:12', '0000-00-00', 1, NULL, NULL),
(4, 1, '60.00', 'Pending', 'Online Banking', '2025-11-21 10:00:30', '0000-00-00', 1, NULL, NULL),
(5, 1, '60.00', 'Pending', 'Online Banking', '2025-11-21 10:02:22', '0000-00-00', 1, NULL, NULL),
(6, 1, '73.50', 'Pending', 'Online Banking', '2025-11-21 10:03:35', '0000-00-00', NULL, NULL, NULL),
(7, 1, '73.50', 'Pending', 'Online Banking', '2025-11-21 10:05:53', '0000-00-00', NULL, NULL, NULL),
(8, 1, '50.00', 'Pending', 'Cash', '2025-11-21 10:10:54', '2025-11-24', NULL, NULL, NULL),
(9, 2, '35.50', 'Pending', 'Online Banking', '2025-11-21 10:10:54', '2025-11-26', NULL, NULL, NULL),
(10, 1, '16.50', 'Pending', 'QR Pay', '2025-11-27 21:45:50', '0000-00-00', NULL, NULL, NULL),
(11, 1, '33.00', 'Pending', 'Online Banking', '2025-11-27 21:46:49', '0000-00-00', NULL, NULL, NULL),
(12, 1, '42.00', 'Pending', 'QR Pay', '2025-11-27 23:04:45', '0000-00-00', NULL, NULL, NULL),
(13, 1, '42.00', 'Pending', 'QR Pay', '2025-11-27 23:11:53', '0000-00-00', NULL, NULL, NULL),
(14, 1, '42.00', 'Pending', 'QR Pay', '2025-11-27 23:29:29', '0000-00-00', NULL, NULL, NULL),
(15, 1, '42.00', 'Pending', 'Online Banking', '2025-11-27 23:29:57', '0000-00-00', NULL, NULL, NULL),
(16, 1, '42.00', 'Pending', 'Online Banking', '2025-11-27 23:49:13', '0000-00-00', NULL, NULL, NULL),
(17, 3, '14.00', 'Pending', 'Online Banking', '2025-11-27 23:54:12', '2025-11-30', NULL, NULL, NULL),
(18, 3, '14.00', 'Pending', 'Online Banking', '2025-11-28 21:42:48', '2025-12-31', NULL, NULL, NULL),
(19, 3, '10.50', 'Pending', 'QR Pay', '2025-11-28 21:44:26', '2025-11-30', 1, NULL, NULL),
(20, 3, '3.50', 'Pending', 'Cash', '2025-12-11 12:18:09', '0000-00-00', NULL, NULL, NULL),
(26, 0, '9.00', 'Pending', 'Cash', '2025-12-29 09:09:42', NULL, NULL, 'walk-in', 6),
(27, 5, '9.00', 'Pending', 'Online Banking', '2026-01-03 10:11:17', '2026-01-05', NULL, NULL, NULL),
(28, 5, '9.00', 'Pending', 'QR Pay', '2026-01-03 10:11:50', '0000-00-00', NULL, NULL, NULL),
(29, 0, '9.00', 'Pending', 'Cash', '2026-01-03 10:13:38', NULL, NULL, 'walk-in', 6),
(30, 1, '17.50', 'Pending', 'Online Banking', '2026-01-04 13:30:33', '0000-00-00', NULL, NULL, NULL),
(31, 0, '19.50', 'Pending', 'QR Pay', '2026-01-08 12:44:53', NULL, NULL, 'walk-in', 6),
(32, 0, '9.00', 'Pending', 'Cash', '2026-01-11 13:53:48', NULL, 1, 'walk-in', 6),
(33, 7, '21.00', 'Completed', 'Online Banking', '2026-01-12 10:52:52', '0000-00-00', 5, NULL, NULL),
(34, 0, '9.00', 'Pending', 'Cash', '2026-01-12 14:28:38', NULL, NULL, 'walk-in', 6),
(35, 0, '23.00', 'Pending', 'Cash', '2026-01-12 15:11:30', NULL, NULL, 'walk-in', 6),
(36, 3, '9.00', 'Pending', 'Online Banking', '2026-01-12 15:31:09', '2026-01-15', NULL, NULL, NULL),
(37, 8, '19.50', 'Completed', 'Online Banking', '2026-01-12 23:27:37', '0000-00-00', 1, NULL, NULL),
(38, 8, '45.00', 'In Process', 'Online Banking', '2026-01-12 23:29:21', '2026-01-20', 5, NULL, NULL),
(39, 0, '23.00', 'Pending', 'Cash', '2026-01-12 23:45:03', NULL, NULL, 'walk-in', 8),
(40, 3, '9.00', 'Pending', 'Cash', '2026-01-19 19:36:48', '0000-00-00', NULL, NULL, NULL),
(41, 7, '9.00', 'Pending', 'Cash', '2026-01-20 15:04:34', '0000-00-00', NULL, NULL, NULL),
(42, 3, '10.50', 'Pending', 'Online Banking', '2026-01-22 13:00:08', '0000-00-00', NULL, NULL, NULL),
(43, 7, '10.50', 'Pending', 'Online Banking', '2026-01-22 15:10:20', '0000-00-00', NULL, NULL, NULL),
(44, 0, '9.00', 'Pending', 'Cash', '2026-01-26 13:55:27', NULL, NULL, 'walk-in', 8),
(45, 7, '28.00', 'Pending', 'Online Banking', '2026-02-07 10:12:52', '2026-03-07', NULL, NULL, NULL),
(46, 7, '14.00', 'Pending', 'Cash', '2026-02-07 10:28:20', '2026-02-09', NULL, NULL, NULL),
(47, 7, '9.00', 'Pending', 'Cash', '2026-03-09 15:09:10', '0000-00-00', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `orderItemID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `pickupDate` date DEFAULT NULL,
  `unitType` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `unitID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`orderItemID`, `orderID`, `quantity`, `pickupDate`, `unitType`, `price`, `unitID`) VALUES
(13, 16, 2, '0000-00-00', NULL, '16.50', 2),
(14, 16, 1, '0000-00-00', NULL, '9.00', 16),
(15, 17, 1, '2025-11-30', NULL, '3.50', 27),
(16, 17, 1, '0000-00-00', NULL, '10.50', 24),
(17, 18, 1, '2025-12-31', NULL, '14.00', 6),
(18, 19, 1, '2025-11-30', NULL, '10.50', 25),
(19, 20, 1, '0000-00-00', NULL, '3.50', 15),
(23, 26, 1, NULL, NULL, '9.00', 23),
(24, 27, 1, '2026-01-05', NULL, '9.00', 23),
(25, 28, 1, '0000-00-00', NULL, '9.00', 23),
(26, 29, 1, NULL, NULL, '9.00', 23),
(27, 30, 1, '0000-00-00', NULL, '10.50', 25),
(28, 30, 1, '0000-00-00', NULL, '7.00', 20),
(29, 31, 1, NULL, NULL, '9.00', 23),
(30, 31, 1, NULL, NULL, '10.50', 25),
(31, 32, 1, NULL, NULL, '9.00', 23),
(32, 33, 1, '0000-00-00', NULL, '10.50', 25),
(33, 33, 1, '0000-00-00', NULL, '10.50', 26),
(34, 34, 1, NULL, NULL, '9.00', 23),
(35, 35, 1, NULL, NULL, '9.00', 23),
(36, 35, 1, NULL, NULL, '14.00', 6),
(37, 36, 1, '2026-01-15', NULL, '9.00', 16),
(38, 37, 1, '0000-00-00', NULL, '10.50', 25),
(39, 37, 1, '0000-00-00', NULL, '9.00', 16),
(40, 38, 10, '2026-01-20', NULL, '4.50', 13),
(41, 39, 1, NULL, NULL, '9.00', 23),
(42, 39, 1, NULL, NULL, '14.00', 6),
(43, 40, 1, '0000-00-00', NULL, '9.00', 23),
(44, 41, 1, '0000-00-00', NULL, '9.00', 23),
(45, 42, 1, '0000-00-00', NULL, '10.50', 25),
(46, 43, 1, '0000-00-00', NULL, '10.50', 25),
(47, 44, 1, NULL, NULL, '9.00', 23),
(48, 45, 2, '2026-03-07', NULL, '10.50', 25),
(49, 45, 1, '2026-03-07', NULL, '7.00', 20),
(50, 46, 1, '2026-02-09', NULL, '14.00', 6),
(51, 47, 1, '0000-00-00', NULL, '9.00', 23);

-- --------------------------------------------------------

--
-- Table structure for table `production`
--

CREATE TABLE `production` (
  `productionID` int(11) NOT NULL,
  `menuID` int(11) DEFAULT NULL,
  `batchQuantity` int(11) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `staffID` int(11) DEFAULT NULL,
  `productionDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE `recipe` (
  `recipeID` int(11) NOT NULL,
  `menuID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `yieldQty` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`recipeID`, `menuID`, `created_at`, `yieldQty`) VALUES
(26, 1, '2025-12-25 06:08:01', 1),
(27, 2, '2025-12-25 06:08:01', 1),
(28, 3, '2025-12-25 06:08:01', 1),
(29, 4, '2025-12-25 06:08:01', 1),
(30, 6, '2025-12-25 06:08:01', 1),
(31, 7, '2025-12-25 06:08:01', 1),
(32, 8, '2025-12-25 06:08:01', 1),
(33, 9, '2025-12-25 06:08:01', 1),
(34, 10, '2025-12-25 06:08:01', 1),
(35, 11, '2025-12-25 06:08:01', 1),
(36, 12, '2025-12-25 06:08:01', 1),
(37, 13, '2025-12-25 06:08:01', 1),
(38, 14, '2025-12-25 06:08:01', 1),
(39, 15, '2025-12-25 06:08:31', 30),
(40, 16, '2025-12-25 06:08:31', 30),
(41, 17, '2025-12-25 06:08:31', 30),
(42, 18, '2025-12-25 06:08:31', 30),
(43, 19, '2025-12-25 06:08:49', 20),
(44, 24, '2025-12-25 06:08:49', 20),
(45, 25, '2025-12-25 06:08:49', 20),
(46, 20, '2025-12-25 06:09:11', 12),
(47, 21, '2025-12-25 06:09:11', 12),
(48, 22, '2025-12-25 06:09:11', 12),
(49, 23, '2025-12-25 06:09:11', 12),
(50, 26, '2025-12-25 06:09:42', 24),
(51, 27, '2025-12-25 06:09:42', 24),
(52, 28, '2025-12-25 06:09:42', 24);

-- --------------------------------------------------------

--
-- Table structure for table `recipe_item`
--

CREATE TABLE `recipe_item` (
  `recipeItemID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL,
  `rawMaterialID` int(11) NOT NULL,
  `quantityPerBatch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_item`
--

INSERT INTO `recipe_item` (`recipeItemID`, `recipeID`, `rawMaterialID`, `quantityPerBatch`) VALUES
(496, 26, 1, 2),
(497, 26, 14, 2),
(498, 26, 15, 2),
(499, 26, 7, 3),
(500, 26, 10, 1),
(501, 26, 26, 2),
(502, 27, 1, 2),
(503, 27, 14, 2),
(504, 27, 15, 3),
(505, 27, 15, 2),
(506, 27, 10, 1),
(507, 27, 18, 2),
(508, 28, 1, 2),
(509, 28, 14, 2),
(510, 28, 15, 2),
(511, 28, 15, 2),
(512, 28, 10, 1),
(513, 28, 16, 2),
(514, 29, 1, 2),
(515, 29, 14, 2),
(516, 29, 15, 2),
(517, 29, 15, 2),
(518, 29, 10, 1),
(519, 29, 12, 3),
(520, 30, 1, 1),
(521, 30, 14, 2),
(522, 30, 15, 2),
(523, 30, 15, 2),
(524, 30, 10, 1),
(525, 30, 12, 2),
(526, 30, 25, 1),
(527, 31, 1, 1),
(528, 31, 14, 2),
(529, 31, 15, 2),
(530, 31, 15, 2),
(531, 31, 10, 1),
(532, 31, 17, 2),
(533, 32, 1, 2),
(534, 32, 14, 2),
(535, 32, 15, 3),
(536, 32, 15, 2),
(537, 32, 10, 1),
(538, 32, 12, 2),
(539, 32, 23, 2),
(540, 33, 15, 3),
(541, 33, 10, 1),
(542, 33, 14, 1),
(543, 33, 25, 1),
(544, 34, 1, 1),
(545, 34, 14, 2),
(546, 34, 15, 2),
(547, 34, 10, 1),
(548, 34, 12, 2),
(549, 35, 1, 2),
(550, 35, 7, 3),
(551, 35, 15, 1),
(552, 35, 10, 1),
(553, 35, 14, 1),
(554, 36, 1, 2),
(555, 36, 7, 2),
(556, 36, 15, 1),
(557, 36, 10, 1),
(558, 36, 20, 2),
(559, 37, 1, 2),
(560, 37, 7, 2),
(561, 37, 15, 1),
(562, 37, 10, 1),
(563, 37, 21, 3),
(564, 38, 1, 2),
(565, 38, 7, 2),
(566, 38, 15, 1),
(567, 38, 10, 1),
(568, 38, 22, 3),
(569, 38, 19, 1),
(570, 39, 1, 1),
(571, 39, 14, 2),
(572, 39, 15, 2),
(573, 39, 10, 1),
(574, 39, 25, 1),
(575, 40, 1, 3),
(576, 40, 14, 6),
(577, 40, 15, 6),
(578, 40, 10, 3),
(579, 40, 25, 3),
(580, 41, 1, 1),
(581, 41, 14, 2),
(582, 41, 15, 2),
(583, 41, 10, 1),
(584, 41, 12, 2),
(585, 42, 1, 3),
(586, 42, 14, 6),
(587, 42, 15, 6),
(588, 42, 10, 3),
(589, 42, 12, 6),
(590, 43, 15, 2),
(591, 43, 10, 1),
(592, 43, 14, 1),
(593, 43, 21, 3),
(594, 44, 1, 2),
(595, 44, 14, 2),
(596, 44, 7, 2),
(597, 44, 15, 1),
(598, 44, 13, 2),
(599, 45, 1, 2),
(600, 45, 14, 2),
(601, 45, 7, 2),
(602, 45, 15, 1),
(603, 45, 24, 2),
(604, 46, 1, 2),
(605, 46, 14, 2),
(606, 46, 15, 1),
(607, 46, 7, 2),
(608, 46, 20, 3),
(609, 47, 1, 2),
(610, 47, 14, 2),
(611, 47, 15, 1),
(612, 47, 7, 2),
(613, 47, 8, 3),
(614, 48, 1, 2),
(615, 48, 14, 2),
(616, 48, 15, 1),
(617, 48, 7, 2),
(618, 48, 23, 3),
(619, 49, 1, 2),
(620, 49, 14, 2),
(621, 49, 15, 1),
(622, 49, 7, 2),
(623, 49, 24, 3),
(624, 50, 1, 2),
(625, 50, 14, 2),
(626, 50, 7, 2),
(627, 50, 10, 1),
(628, 50, 12, 3),
(629, 51, 1, 1),
(630, 51, 14, 1),
(631, 51, 7, 1),
(632, 51, 15, 1),
(633, 51, 21, 2),
(634, 52, 1, 4),
(635, 52, 14, 4),
(636, 52, 7, 4),
(637, 52, 15, 4),
(638, 52, 21, 8);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staffID` int(11) NOT NULL,
  `staffName` varchar(100) NOT NULL,
  `staffEmail` varchar(100) NOT NULL,
  `staffPassword` varchar(255) NOT NULL,
  `staffRole` varchar(50) NOT NULL DEFAULT 'Staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffID`, `staffName`, `staffEmail`, `staffPassword`, `staffRole`, `created_at`) VALUES
(1, 'Aina', 'aina@bitsy.com', 'baker123', 'Baker', '2025-11-13 07:02:35'),
(2, 'Admin Syaza', 'admin@bitsy.com', 'admin123', 'Admin', '2025-11-28 17:01:52'),
(3, 'Nana', 'nana@bitsy.com', 'baker123', 'staff', '2025-11-28 17:01:52'),
(4, 'Ali', 'ali@bitsy.com', 'staff123', 'staff', '2025-11-28 17:01:52'),
(5, 'Siti', 'siti@bitsy.com', 'staff123', 'Baker', '2025-11-28 17:01:52'),
(6, 'Qsara Aishah', 'qsara@bitsy.com', 'Qsara123', 'Cashier', '2025-12-26 12:36:02'),
(7, 'Nurul Najihah', 'najihah@bitsy.com', 'Najihah123', 'Cashier', '2025-12-26 13:48:13'),
(8, 'Nur Syafiqah', 'syafiqah@bitsy.com', 'Syafiqah123', 'Cashier', '2026-01-12 15:33:34');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `minimum_level` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `item_name`, `category`, `quantity`, `minimum_level`) VALUES
(1, 'Flour (kg)', 'Raw Material', 14, 10),
(2, 'Cake flour (kg)', 'Raw Material', 20, 20),
(3, 'Cocoa powder (kg)', 'Raw Material', 28, 15),
(4, 'Baking powder (small pack)', 'Raw Material', 7, 2),
(5, 'Baking soda (small pack)', 'Raw Material', 4, 2),
(6, 'Corn flour (kg)', 'Raw Material', 10, 5),
(7, 'Butter (250g pack)', 'Raw Material', 13, 15),
(8, 'Cream cheese (2kg block)', 'Raw Material', 10, 8),
(9, 'Whipping cream (1L)', 'Raw Material', 14, 10),
(10, 'Fresh milk (1L)', 'Raw Material', 8, 8),
(11, 'Condensed milk (can)', 'Raw Material', 6, 5),
(12, 'Cooking chocolate (kg)', 'Raw Material', 6, 5),
(13, 'White chocolate (kg)', 'Raw Material', 15, 10),
(14, 'Sugar (kg)', 'Raw Material', 20, 10),
(15, 'Egg (pcs)', 'Raw Material', 166, 50),
(16, 'Honey (bottle)', 'Raw Material', 10, 5),
(17, 'Coffee powder (pack)', 'Raw Material', 10, 5),
(18, 'Ovomaltine (pack)', 'Raw Material', 10, 5),
(19, 'Cinnamon powder (pack)', 'Raw Material', 5, 2),
(20, 'Sardine (can)', 'Raw Material', 20, 10),
(21, 'Chicken breast (kg)', 'Raw Material', 15, 5),
(22, 'Lasagna sheet (box)', 'Raw Material', 10, 5),
(23, 'Pistachio paste (jar)', 'Raw Material', 5, 2),
(24, 'Biscoff spread (jar)', 'Raw Material', 10, 5),
(25, 'Vanilla essence (bottle)', 'Raw Material', 5, 2),
(26, 'Fresh carrot (kg)', 'Raw Material', 5, 4),
(27, 'Gelatin powder (pack)', 'Raw Material', 5, 2),
(28, 'Cream powder (kg)', 'Raw Material', 5, 3),
(29, 'Shortening (500g)', 'Raw Material', 20, 10);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customerID`),
  ADD UNIQUE KEY `email` (`customerEmail`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menuID`);

--
-- Indexes for table `menu_new`
--
ALTER TABLE `menu_new`
  ADD PRIMARY KEY (`menuID`);

--
-- Indexes for table `menu_old`
--
ALTER TABLE `menu_old`
  ADD PRIMARY KEY (`menuID`);

--
-- Indexes for table `menu_unit`
--
ALTER TABLE `menu_unit`
  ADD PRIMARY KEY (`unitID`),
  ADD KEY `menuID` (`menuID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `customerID` (`customerID`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`orderItemID`),
  ADD KEY `orderID` (`orderID`),
  ADD KEY `fk_order_items_unit` (`unitID`);

--
-- Indexes for table `production`
--
ALTER TABLE `production`
  ADD PRIMARY KEY (`productionID`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`recipeID`);

--
-- Indexes for table `recipe_item`
--
ALTER TABLE `recipe_item`
  ADD PRIMARY KEY (`recipeItemID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staffID`),
  ADD UNIQUE KEY `staffEmail` (`staffEmail`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `menu_new`
--
ALTER TABLE `menu_new`
  MODIFY `menuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `menu_old`
--
ALTER TABLE `menu_old`
  MODIFY `menuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `menu_unit`
--
ALTER TABLE `menu_unit`
  MODIFY `unitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `orderItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `productionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `recipeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `recipe_item`
--
ALTER TABLE `recipe_item`
  MODIFY `recipeItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=639;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_unit`
--
ALTER TABLE `menu_unit`
  ADD CONSTRAINT `menu_unit_ibfk_1` FOREIGN KEY (`menuID`) REFERENCES `menu_old` (`menuID`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customer` (`customerID`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_unit` FOREIGN KEY (`unitID`) REFERENCES `menu_unit` (`unitID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
