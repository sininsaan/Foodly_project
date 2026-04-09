-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2026 at 11:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foodly_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(4, 'admin', '$2y$10$vPlfm7t1cQhCQN.HlBW1K.7K4c11GKRpAdRXncyEdtf511Ne.eFc2', '2026-03-19 10:05:05');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`) VALUES
(2, 'Beverages'),
(3, 'Meals'),
(1, 'Snacks');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `item_id` int(11) NOT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT 'Snacks',
  `price` decimal(10,2) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`item_id`, `cat_id`, `item_name`, `category`, `price`, `is_available`, `created_at`, `image`) VALUES
(1, NULL, 'Veg Samosa', 'Snacks', 15.00, 1, '2026-03-19 18:54:46', 'samosa.jpg'),
(2, NULL, 'Chole Samose', 'Snacks', 40.00, 1, '2026-03-19 18:54:46', 'cholesamose.jpg'),
(3, NULL, 'Bread Pakoda', 'Snacks', 25.00, 1, '2026-03-19 18:54:46', 'breadpakoda.jpg'),
(4, NULL, 'Bread Roll', 'Snacks', 30.00, 1, '2026-03-19 18:54:46', 'breadroll.jpg'),
(5, NULL, 'Chole Tikki', 'Snacks', 40.00, 1, '2026-03-19 18:54:46', 'choletikki.jpg'),
(6, NULL, 'Bhel Puri', 'Snacks', 30.00, 1, '2026-03-19 18:54:46', 'bhelpuri.jpg'),
(7, NULL, 'French Fries', 'Snacks', 60.00, 1, '2026-03-19 18:54:46', 'frenchfries.jpg'),
(8, NULL, 'Masala Corn', 'Snacks', 40.00, 1, '2026-03-19 18:54:46', 'masalacorn.jpg'),
(9, NULL, 'Sweet Corn', 'Snacks', 35.00, 1, '2026-03-19 18:54:46', 'sweetcorn.jpg'),
(10, NULL, 'Gulab Jamun (per pcs)', 'Snacks', 25.00, 1, '2026-03-19 18:54:46', 'gulabjamun.jpg'),
(11, NULL, 'Vada Pav', 'Snacks', 20.00, 1, '2026-03-19 18:54:46', 'vadapav.jpg'),
(12, NULL, 'Mix Sprouts Chat', 'Snacks', 60.00, 1, '2026-03-19 18:54:46', 'mixsproutschat.jpg'),
(13, NULL, 'Masala Tea', 'Beverages', 10.00, 1, '2026-03-19 18:54:46', 'tea.jpg'),
(14, NULL, 'Hot Milk', 'Beverages', 20.00, 1, '2026-03-19 18:54:46', 'hotmilk.jpg'),
(15, NULL, 'Hot Coffee', 'Beverages', 20.00, 1, '2026-03-19 18:54:46', 'hotcoffee.jpg'),
(16, NULL, 'Cold Coffee', 'Beverages', 40.00, 1, '2026-03-19 18:54:46', 'coldcoffee.jpg'),
(17, NULL, 'Lassi', 'Beverages', 30.00, 1, '2026-03-19 18:54:46', 'lassi.jpg'),
(18, NULL, 'Badam Milk', 'Beverages', 40.00, 1, '2026-03-19 18:54:46', 'badammilk.jpg'),
(19, NULL, 'Butterscotch Milkshake', 'Beverages', 70.00, 1, '2026-03-19 18:54:46', 'butterscotchmilkshake.jpg'),
(20, NULL, 'Chocolate Milkshake', 'Beverages', 70.00, 1, '2026-03-19 18:54:46', 'chocolatemilkshake.jpg'),
(21, NULL, 'Strawberry Shake', 'Beverages', 70.00, 1, '2026-03-19 18:54:46', 'strawberryshake.jpg'),
(22, NULL, 'Oreo Milkshake', 'Beverages', 80.00, 1, '2026-03-19 18:54:46', 'oreomilkshake.jpg'),
(23, NULL, 'Dahi', 'Beverages', 20.00, 1, '2026-03-19 18:54:46', 'dahi.jpg'),
(24, NULL, 'Ice Cream', 'Beverages', 40.00, 1, '2026-03-19 18:54:46', 'icecream.jpg'),
(26, NULL, 'Aloo Pyaz Parantha', 'Parantha', 60.00, 1, '2026-03-19 18:54:46', 'aloopyaazparantha.jpg'),
(27, NULL, 'Pyaz Parantha', 'Parantha', 50.00, 1, '2026-03-19 18:54:46', 'pyaazparantha.jpg'),
(28, NULL, 'Mix Parantha', 'Parantha', 70.00, 1, '2026-03-19 18:54:46', 'mixparantha.jpg'),
(29, NULL, 'Gobi Parantha', 'Parantha', 60.00, 1, '2026-03-19 18:54:46', 'gobiparantha.jpg'),
(30, NULL, 'Egg Parantha', 'Parantha', 70.00, 1, '2026-03-19 18:54:46', 'eggparantha.jpg'),
(31, NULL, 'Paneer Onion Parantha', 'Parantha', 80.00, 1, '2026-03-19 18:54:46', 'paneeronionparantha.jpg'),
(32, NULL, 'Kadahi Paneer Parantha', 'Parantha', 90.00, 1, '2026-03-19 18:54:46', 'kadahipaneerparantha.jpg'),
(33, NULL, 'Shahi Paneer Parantha', 'Parantha', 90.00, 1, '2026-03-19 18:54:46', 'shahipaneerparantha.jpg'),
(34, NULL, 'Egg Bhurji Parantha', 'Parantha', 80.00, 1, '2026-03-19 18:54:46', 'Eggbhurjiparantha.jpg'),
(35, NULL, 'Paneer Bhurji Paratha', 'Parantha', 80.00, 1, '2026-03-19 18:54:46', 'Paneerbhurjiparatha.jpg'),
(36, NULL, 'Vada Sambar (2 pcs)', 'South Indian', 70.00, 1, '2026-03-19 18:54:46', 'vadasambar.jpg'),
(37, NULL, 'Idli Sambar', 'South Indian', 60.00, 1, '2026-03-19 18:54:46', 'idlisambar.jpg'),
(38, NULL, 'Masala Dosa', 'South Indian', 85.00, 1, '2026-03-19 18:54:46', 'masaladosa.jpg'),
(39, NULL, 'Plain Dosa', 'South Indian', 60.00, 1, '2026-03-19 18:54:46', 'plaindosa.jpg'),
(40, NULL, 'Veg Dosa', 'South Indian', 80.00, 1, '2026-03-19 18:54:46', 'vegdosa.jpg'),
(41, NULL, 'Paneer Onion Dosa', 'South Indian', 100.00, 1, '2026-03-19 18:54:46', 'paneeroniondosa.jpg'),
(42, NULL, 'Veg Uttapam', 'South Indian', 80.00, 1, '2026-03-19 18:54:46', 'veguttapam.jpg'),
(43, NULL, 'Poha', 'South Indian', 50.00, 1, '2026-03-19 18:54:46', 'poha.jpg'),
(44, NULL, 'Butter Naan with Gravy', 'Naan Combos', 70.00, 1, '2026-03-19 18:54:46', 'butternaanwithgravy.jpg'),
(45, NULL, 'Butter Naan with Dal/Chole', 'Naan Combos', 100.00, 1, '2026-03-19 18:54:46', 'butternaanwithdalchole.jpg'),
(46, NULL, 'Butter Naan with Shahi Paneer', 'Naan Combos', 120.00, 1, '2026-03-19 18:54:46', 'butternaanwithshahipaneer.jpg'),
(47, NULL, 'Butter Naan with Kadhai Paneer', 'Naan Combos', 120.00, 1, '2026-03-19 18:54:46', 'butternaanwithkadhaipaneer.jpg'),
(48, NULL, 'Aloo Pyaz Naan with Chole', 'Naan Combos', 90.00, 1, '2026-03-19 18:54:46', 'aloopyaaznaanwithchole.jpg'),
(49, NULL, 'Paneer Naan with Gravy', 'Naan Combos', 120.00, 1, '2026-03-19 18:54:46', 'paneernaanwithgravy.jpg'),
(50, NULL, 'Rajma Rice', 'Rice Combos', 70.00, 1, '2026-03-19 18:54:46', 'rajmarice.jpg'),
(51, NULL, 'Chole Rice', 'Rice Combos', 70.00, 1, '2026-03-19 18:54:46', 'cholerice.jpg'),
(52, NULL, 'Dal Makhani Rice', 'Rice Combos', 70.00, 1, '2026-03-19 18:54:46', 'dalmakhnirice.jpg'),
(53, NULL, 'Kadhi Rice', 'Rice Combos', 70.00, 1, '2026-03-19 18:54:46', 'kadhirice.jpg'),
(54, NULL, 'Shahi Paneer Rice', 'Rice Combos', 100.00, 1, '2026-03-19 18:54:46', 'shahipaneerrice.jpg'),
(55, NULL, 'Veg Fried Rice', 'Rice Combos', 90.00, 1, '2026-03-19 18:54:46', 'vegfriedrice.jpg'),
(56, NULL, 'Egg Fried Rice', 'Rice Combos', 100.00, 1, '2026-03-19 18:54:46', 'eggfriedrice.jpg'),
(57, NULL, 'Fried Rice with Manchurian', 'Rice Combos', 110.00, 1, '2026-03-19 18:54:46', 'friedricewithmanchurian.jpg'),
(58, NULL, 'Fried Rice with Rajma/Chole', 'Rice Combos', 100.00, 1, '2026-03-19 18:54:46', 'friedricewithrajmachole.jpg'),
(59, NULL, 'Fried Rice with Shahi Paneer', 'Rice Combos', 110.00, 1, '2026-03-19 18:54:46', 'friedricewithshahipaneer.jpg'),
(60, NULL, 'Chole Puri', 'Meals', 70.00, 1, '2026-03-19 18:54:46', 'chole puri.jpg'),
(61, NULL, 'Chole Bhature', 'Meals', 80.00, 1, '2026-03-19 18:54:46', 'cholebhature.jpg'),
(62, NULL, 'Chole Kulcha', 'Meals', 70.00, 1, '2026-03-19 18:54:46', 'cholekulcha.jpg'),
(63, NULL, 'Veg Spring Roll', 'Chinese', 70.00, 1, '2026-03-19 18:54:46', 'vegspringroll.jpg'),
(64, NULL, 'Veg Manchurian', 'Chinese', 80.00, 1, '2026-03-19 18:54:46', 'vegmanchurian.jpg'),
(65, NULL, 'Veg Noodle', 'Chinese', 100.00, 1, '2026-03-19 18:54:46', 'vegnoodle.jpg'),
(66, NULL, 'Paneer Noodle', 'Chinese', 100.00, 1, '2026-03-19 18:54:46', 'paneernoodle.jpg'),
(67, NULL, 'Egg Noodle', 'Chinese', 100.00, 1, '2026-03-19 18:54:46', 'eggnoodle.jpg'),
(68, NULL, 'Honey Chilly Cauliflower', 'Chinese', 90.00, 1, '2026-03-19 18:54:46', 'honeychilicauliflower.jpg'),
(69, NULL, 'Cheese Chilly (6 pcs)', 'Chinese', 120.00, 1, '2026-03-19 18:54:46', 'cheesechilly.jpg'),
(70, NULL, 'Noodle Manchurian', 'Chinese', 90.00, 1, '2026-03-19 18:54:46', 'noodlemanchurian.jpg'),
(71, NULL, 'Red Sauce Pasta', 'Pasta', 80.00, 1, '2026-03-19 18:54:46', 'redsaucepasta.jpg'),
(72, NULL, 'Mix Sauce Pasta', 'Pasta', 100.00, 1, '2026-03-19 18:54:46', 'mixsaucepasta.jpg'),
(73, NULL, 'White Sauce Pasta', 'Pasta', 90.00, 1, '2026-03-19 18:54:46', 'whitesaucepasta.jpg'),
(74, NULL, 'Butter Pav Bhaji', 'Pasta', 70.00, 1, '2026-03-19 18:54:46', 'butterpavbhaji.jpg'),
(75, NULL, 'Veg Maggi', 'Maggi', 30.00, 1, '2026-03-19 18:54:46', 'vegmaggi.jpg'),
(76, NULL, 'Egg Maggi', 'Maggi', 40.00, 1, '2026-03-19 18:54:46', 'eggmaggi.jpg'),
(77, NULL, 'Plain Maggi', 'Maggi', 25.00, 1, '2026-03-19 18:54:46', 'plainmaggi.jpg'),
(78, NULL, 'Veg Grilled Patti', 'Pattis', 50.00, 1, '2026-03-19 18:54:46', 'veggrilledpatti.jpg'),
(79, NULL, 'Cheese Grilled Patti', 'Pattis', 70.00, 1, '2026-03-19 18:54:46', 'cheesegrilledpatty.jpg'),
(80, NULL, 'Veg Sandwich', 'Sandwich & Roll', 40.00, 1, '2026-03-19 18:54:46', 'vegsandwich.jpg'),
(81, NULL, 'Egg Sandwich', 'Sandwich & Roll', 50.00, 1, '2026-03-19 18:54:46', 'eggsandwich.jpg'),
(82, NULL, 'Veg Grilled Sandwich', 'Sandwich & Roll', 60.00, 1, '2026-03-19 18:54:46', 'veggrilledsandwich.jpg'),
(83, NULL, 'Cheese Grilled Sandwich', 'Sandwich & Roll', 80.00, 1, '2026-03-19 18:54:46', 'cheesegrilledsandwich.jpg'),
(84, NULL, 'Paneer Tikka Grilled Sandwich', 'Sandwich & Roll', 100.00, 1, '2026-03-19 18:54:46', 'paneertikkagrilledsandwich.jpg'),
(85, NULL, 'Veg Roll', 'Sandwich & Roll', 60.00, 1, '2026-03-19 18:54:46', 'vegroll.jpg'),
(86, NULL, 'Paneer Roll', 'Sandwich & Roll', 80.00, 1, '2026-03-19 18:54:46', 'paneerroll.jpg'),
(87, NULL, 'Egg Roll', 'Sandwich & Roll', 70.00, 1, '2026-03-19 18:54:46', 'eggroll.jpg'),
(88, NULL, 'Manchurian Roll', 'Sandwich & Roll', 80.00, 1, '2026-03-19 18:54:46', 'manchurianroll.jpg'),
(89, NULL, 'Cheese Chilly Roll', 'Sandwich & Roll', 90.00, 1, '2026-03-19 18:54:46', 'cheesechillyroll.jpg'),
(90, NULL, 'Bread with Egg Bhurji', 'Sandwich & Roll', 50.00, 1, '2026-03-19 18:54:46', 'breadwitheggbhurji.jpg'),
(91, NULL, 'Bread with Paneer Bhurji', 'Sandwich & Roll', 60.00, 1, '2026-03-19 18:54:46', 'breadwithpaneerbhurji.jpg'),
(92, NULL, 'Bread Omelet', 'Sandwich & Roll', 50.00, 1, '2026-03-19 18:54:46', 'breadomelet.jpg'),
(93, NULL, 'Dahi Bhalla', 'Snacks', 60.00, 1, '2026-03-19 18:54:46', 'dahibhalla.jpg'),
(96, NULL, 'Amul Butter', 'Snacks', 10.00, 1, '2026-04-07 21:25:04', 'amulbutter.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `roll_no` varchar(20) NOT NULL,
  `slot_id` int(11) DEFAULT NULL,
  `total` decimal(8,2) NOT NULL,
  `token` int(11) NOT NULL,
  `status` enum('Pending','Preparing','Ready','Completed','Missed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `roll_no`, `slot_id`, `total`, `token`, `status`, `created_at`) VALUES
(1, '8091', 1, 35.00, 101, 'Completed', '2026-03-19 12:36:33'),
(2, '0000', 1, 20.00, 102, 'Missed', '2026-03-19 12:40:07'),
(3, '8091', 1, 10.00, 103, 'Completed', '2026-03-19 13:15:50'),
(4, '8091', 2, 90.00, 104, 'Completed', '2026-03-19 15:01:14'),
(5, '8091', 6, 15.00, 105, 'Missed', '2026-03-19 17:01:56'),
(6, '8091', 2, 205.00, 1, 'Completed', '2026-03-20 14:29:58'),
(7, '8091', 2, 65.00, 101, 'Completed', '2026-04-06 09:53:29'),
(8, '8091', 6, 90.00, 102, 'Completed', '2026-04-06 12:12:20'),
(9, '8091', 1, 15.00, 103, 'Completed', '2026-04-06 12:37:19'),
(10, '8099', 1, 40.00, 101, 'Missed', '2026-04-07 08:44:44'),
(11, '8091', 3, 25.00, 102, 'Missed', '2026-04-07 10:18:28'),
(12, '8091', 2, 15.00, 103, 'Completed', '2026-04-07 11:05:14'),
(13, '8099', 1, 40.00, 104, 'Missed', '2026-04-07 13:12:50'),
(14, '8099', 1, 15.00, 105, 'Missed', '2026-04-07 13:15:27'),
(15, '8031', 1, 15.00, 106, 'Completed', '2026-04-07 13:18:27'),
(16, '8031', 1, 60.00, 107, 'Completed', '2026-04-07 13:36:43'),
(17, '8031', 1, 30.00, 108, 'Missed', '2026-04-07 13:38:46'),
(18, '8031', 1, 70.00, 109, 'Completed', '2026-04-07 13:45:03'),
(19, '8031', 1, 20.00, 110, 'Completed', '2026-04-07 13:53:08'),
(20, '8031', 1, 60.00, 111, 'Completed', '2026-04-07 14:05:16'),
(21, '8031', 1, 40.00, 112, 'Completed', '2026-04-07 14:09:33'),
(22, '8091', 1, 40.00, 113, 'Completed', '2026-04-07 16:25:00'),
(23, '8099', 1, 40.00, 1, 'Completed', '2026-04-07 21:02:46'),
(24, '8099', 1, 20.00, 2, 'Completed', '2026-04-07 21:33:00'),
(25, '8011', 2, 15.00, 3, 'Completed', '2026-04-08 07:16:54'),
(26, '8011', 2, 15.00, 4, 'Completed', '2026-04-08 07:44:27'),
(27, '8011', 4, 20.00, 5, 'Missed', '2026-04-08 07:46:51'),
(28, '8011', 4, 15.00, 6, 'Completed', '2026-04-08 07:48:56'),
(29, '8011', 4, 15.00, 7, 'Completed', '2026-04-08 07:57:05'),
(30, '8011', 5, 25.00, 8, 'Completed', '2026-04-08 08:44:28'),
(31, '8011', 5, 55.00, 9, 'Completed', '2026-04-08 09:29:55'),
(32, '8011', 6, 20.00, 10, 'Completed', '2026-04-08 09:47:30'),
(33, '8011', 6, 15.00, 11, 'Completed', '2026-04-08 10:06:47'),
(34, '8099', 1, 40.00, 12, 'Missed', '2026-04-08 13:24:13'),
(35, '8099', 1, 30.00, 13, 'Missed', '2026-04-08 13:25:27'),
(36, '8099', 1, 15.00, 14, 'Missed', '2026-04-08 13:26:15'),
(37, '8024', 1, 25.00, 15, 'Completed', '2026-04-08 14:27:42'),
(38, '8024', NULL, 25.00, 0, 'Completed', '2026-04-08 14:30:35'),
(39, '8024', NULL, 45.00, 0, 'Completed', '2026-04-08 14:30:35'),
(40, '8024', NULL, 15.00, 0, 'Completed', '2026-04-08 14:30:35'),
(41, '8011', 1, 95.00, 16, 'Completed', '2026-04-08 18:04:04');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_name`, `price`, `quantity`) VALUES
(1, 1, 'Veg Samosa', 15.00, 1),
(2, 1, 'Masala Tea', 10.00, 2),
(3, 2, 'Vada Pav', 20.00, 1),
(4, 3, 'Masala Tea', 10.00, 1),
(5, 4, 'Chole Samose', 40.00, 1),
(6, 4, 'Bread Pakoda', 25.00, 2),
(7, 5, 'Veg Samosa', 15.00, 1),
(8, 6, 'Idli Sambar', 60.00, 2),
(9, 6, 'Masala Dosa', 85.00, 1),
(10, 7, 'Veg Samosa', 15.00, 1),
(11, 7, 'Bread Pakoda', 25.00, 2),
(12, 8, 'Chole Samose', 40.00, 1),
(13, 8, 'Bread Pakoda', 25.00, 2),
(14, 9, 'Veg Samosa', 15.00, 1),
(15, 10, 'Chole Tikki', 40.00, 1),
(16, 11, 'Gulab Jamun (per pcs)', 25.00, 1),
(17, 12, 'Veg Samosa', 15.00, 1),
(18, 13, 'Chole Samose', 40.00, 1),
(19, 14, 'Veg Samosa', 15.00, 1),
(20, 15, 'Veg Samosa', 15.00, 1),
(21, 16, 'French Fries', 60.00, 1),
(22, 17, 'Lassi', 30.00, 1),
(23, 18, 'Mix Sprouts Chat', 60.00, 1),
(24, 18, 'Masala Tea', 10.00, 1),
(25, 19, 'Hot Coffee', 20.00, 1),
(26, 20, 'Aloo Pyaz Parantha', 60.00, 1),
(27, 21, 'Badam Milk', 40.00, 1),
(28, 22, 'Chole Tikki', 40.00, 1),
(29, 23, 'Chole Samose', 40.00, 1),
(30, 24, 'Hot Coffee', 20.00, 1),
(31, 25, 'Veg Samosa', 15.00, 1),
(32, 26, 'Veg Samosa', 15.00, 1),
(33, 27, 'Vada Pav', 20.00, 1),
(34, 28, 'Veg Samosa', 15.00, 1),
(35, 29, 'Veg Samosa', 15.00, 1),
(36, 30, 'Plain Maggi', 25.00, 1),
(37, 31, 'Veg Samosa', 15.00, 1),
(38, 31, 'Chole Tikki', 40.00, 1),
(39, 32, 'Hot Coffee', 20.00, 1),
(40, 33, 'Veg Samosa', 15.00, 1),
(41, 34, 'Chole Samose', 40.00, 1),
(42, 35, 'Bhel Puri', 30.00, 1),
(43, 36, 'Veg Samosa', 15.00, 1),
(44, 37, 'Bread Pakoda', 25.00, 1),
(45, 41, 'Veg Samosa', 15.00, 2),
(46, 41, 'Chole Samose', 40.00, 1),
(47, 41, 'Bread Pakoda', 25.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `last_reset` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`last_reset`) VALUES
('2026-04-08');

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE `time_slots` (
  `id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `max_capacity` int(11) DEFAULT 30,
  `current_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `last_reset_date` date DEFAULT '2026-01-01'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`id`, `label`, `start_time`, `end_time`, `max_capacity`, `current_count`, `is_active`, `last_reset_date`) VALUES
(1, '10:00 AM - 11:00 AM', '10:00:00', '11:00:00', 30, 0, 1, '2026-04-08'),
(2, '11:00 AM - 12:00 PM', '11:00:00', '12:00:00', 30, 0, 1, '2026-04-08'),
(3, '12:00 PM - 1:00 PM', '12:00:00', '13:00:00', 30, 0, 1, '2026-04-08'),
(4, '1:00 PM - 2:00 PM', '13:00:00', '14:00:00', 30, 0, 1, '2026-04-08'),
(5, '2:00 PM - 3:00 PM', '14:00:00', '15:00:00', 30, 0, 1, '2026-04-08'),
(6, '3:00 PM - 4:00 PM', '15:00:00', '16:00:00', 30, 0, 1, '2026-04-08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `roll_no` varchar(20) NOT NULL,
  `name` varchar(100) DEFAULT '',
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('active','suspended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `roll_no`, `name`, `password`, `created_at`, `phone`, `email`, `status`) VALUES
(1, 'BCA001', '', '12345', '2026-02-24 06:50:08', NULL, NULL, 'active'),
(2, '8091', 'Shruti', '12345', '2026-02-24 07:05:15', '9876543210', 'abc@gmail.com', 'active'),
(3, '8105', '', '12345', '2026-02-24 07:43:37', '9876543210', 'abc@gmail.com', 'active'),
(4, '8031', '', '12345', '2026-02-24 11:16:09', '9876543210', 'abc@gmail.com', 'active'),
(5, '8099', '', '12345', '2026-02-24 11:20:05', '9876543210', 'abc@gmail.com', 'suspended'),
(6, '8024', '', '12345', '2026-02-24 11:35:42', '9876543210', 'abc@gmail.com', 'active'),
(7, '11', '', '1234', '2026-03-09 10:19:04', '9876543210', 'abc@gmail.com', 'suspended'),
(8, '8011', 'Abhishek', '$2y$10$1juH6tFaz6DOiqPunI9Cye7gXICjK8A2BPLImaxeEAtaCYAs8k10W', '2026-04-08 12:01:03', '9876543210', 'abc@gmail.com', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `cat_name` (`cat_name`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slot_id` (`slot_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roll_no` (`roll_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`cat_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`slot_id`) REFERENCES `time_slots` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `reset_slot_counts` ON SCHEDULE EVERY 1 DAY STARTS '2026-03-20 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE time_slots SET current_count = 0$$

CREATE DEFINER=`root`@`localhost` EVENT `mark_missed_orders` ON SCHEDULE EVERY 1 HOUR STARTS '2026-03-19 20:24:18' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE orders 
  SET status = 'Missed'
  WHERE status NOT IN ('Completed', 'Missed')
  AND created_at < NOW() - INTERVAL 2 HOUR$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
