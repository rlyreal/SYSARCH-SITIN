-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2025 at 04:37 AM
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
-- Database: `sitin_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `admin_name`, `message`, `date`) VALUES
(3, ' ', 'Holiday karon!', '2025-03-11 03:38:35'),
(4, ' ', 'qwdwqdq', '2025-03-11 03:51:57'),
(5, ' ', 'naay klase?', '2025-03-19 02:28:32'),
(6, ' ', 'gauwan!', '2025-03-19 23:00:05'),
(7, ' ', 'gauwan karon!', '2025-03-19 23:02:16'),
(8, ' ', 'klase karon?', '2025-03-19 23:14:44'),
(9, ' ', 'klase ron!', '2025-03-19 23:16:38'),
(13, ' ', 'uwan man!', '2025-03-19 23:22:08'),
(17, ' ', 'Goodmorning!!', '2025-03-20 00:05:45'),
(24, ' ', 'Klase na!', '2025-03-20 01:17:36'),
(25, ' ', 'Absent sa karon!', '2025-03-27 11:25:56'),
(26, 'admin', 'Klase ugma!', '2025-04-02 14:02:11'),
(27, 'admin', 'Good Morning!', '2025-05-08 00:18:49'),
(28, 'admin', 'Hello!', '2025-05-08 00:25:33'),
(29, 'admin', 'Attention!', '2025-05-08 00:29:57'),
(30, 'admin', 'Announcement!', '2025-05-08 00:29:57'),
(31, 'admin', 'Good Morning!', '2025-05-08 00:32:27'),
(32, 'admin', 'Welcome!', '2025-05-08 00:45:17'),
(33, 'admin', 'Goodjob!', '2025-05-13 10:30:03'),
(34, 'admin', 'Hello!', '2025-05-13 17:15:54'),
(35, 'admin', 'Hello!', '2025-05-13 18:11:19'),
(36, 'admin', 'Hello!', '2025-05-14 02:29:01'),
(37, 'admin', 'Hello!', '2025-05-14 02:30:29'),
(38, 'admin', 'Hello!', '2025-05-14 02:50:04'),
(39, 'admin', 'Good Morning!', '2025-05-14 03:13:27'),
(40, 'admin', 'Good Morning!', '2025-05-14 03:17:02'),
(41, 'admin', 'Good Morning!', '2025-05-14 03:17:11');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_reads`
--

CREATE TABLE `announcement_reads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_reads`
--

INSERT INTO `announcement_reads` (`id`, `user_id`, `announcement_id`, `read_at`) VALUES
(1, 30, 3, '2025-05-08 00:25:13'),
(2, 30, 4, '2025-05-08 00:25:13'),
(3, 30, 5, '2025-05-08 00:25:13'),
(4, 30, 6, '2025-05-08 00:25:13'),
(5, 30, 7, '2025-05-08 00:25:13'),
(6, 30, 8, '2025-05-08 00:25:13'),
(7, 30, 9, '2025-05-08 00:25:13'),
(8, 30, 13, '2025-05-08 00:25:13'),
(9, 30, 17, '2025-05-08 00:25:13'),
(10, 30, 24, '2025-05-08 00:25:13'),
(11, 30, 25, '2025-05-08 00:25:13'),
(12, 30, 26, '2025-05-08 00:25:13'),
(13, 30, 27, '2025-05-08 00:25:13'),
(16, 30, 28, '2025-05-08 00:25:41'),
(17, 30, 29, '2025-05-08 00:30:04'),
(18, 30, 30, '2025-05-08 00:30:04'),
(20, 230, 3, '2025-05-08 00:30:16'),
(21, 230, 4, '2025-05-08 00:30:16'),
(22, 230, 5, '2025-05-08 00:30:16'),
(23, 230, 6, '2025-05-08 00:30:16'),
(24, 230, 7, '2025-05-08 00:30:16'),
(25, 230, 8, '2025-05-08 00:30:16'),
(26, 230, 9, '2025-05-08 00:30:16'),
(27, 230, 13, '2025-05-08 00:30:16'),
(28, 230, 17, '2025-05-08 00:30:16'),
(29, 230, 24, '2025-05-08 00:30:16'),
(30, 230, 25, '2025-05-08 00:30:16'),
(31, 230, 26, '2025-05-08 00:30:16'),
(32, 230, 27, '2025-05-08 00:30:16'),
(33, 230, 28, '2025-05-08 00:30:16'),
(34, 230, 29, '2025-05-08 00:30:16'),
(35, 230, 30, '2025-05-08 00:30:16'),
(51, 30, 31, '2025-05-08 00:32:37'),
(52, 30, 32, '2025-05-08 00:45:24'),
(53, 230, 31, '2025-05-08 02:17:12'),
(54, 230, 32, '2025-05-08 02:17:12'),
(55, 30, 33, '2025-05-13 10:30:19');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `sit_in_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `feedback_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `sit_in_id`, `rating`, `feedback_text`, `created_at`) VALUES
(1, 35, 5, 'nindot', '2025-03-26 05:07:14'),
(4, 35, 5, 'Nice kaayo!', '2025-03-27 13:11:52'),
(5, 35, 5, 'Nice!', '2025-03-27 13:27:20'),
(7, 52, 5, 'Nindot!', '2025-04-02 14:38:04');

-- --------------------------------------------------------

--
-- Table structure for table `lab_schedule`
--

CREATE TABLE `lab_schedule` (
  `SCHED_ID` int(11) NOT NULL,
  `DAY` enum('Monday','Tuesday','Wednesday','Thursday') DEFAULT NULL,
  `LABORATORY` enum('Lab 517','Lab 524','Lab 526','Lab 528') DEFAULT NULL,
  `TIME_START` time DEFAULT NULL,
  `TIME_END` time DEFAULT NULL,
  `SUBJECT` varchar(255) DEFAULT NULL,
  `PROFESSOR` varchar(50) DEFAULT NULL,
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_schedule`
--

INSERT INTO `lab_schedule` (`SCHED_ID`, `DAY`, `LABORATORY`, `TIME_START`, `TIME_END`, `SUBJECT`, `PROFESSOR`, `CREATED_AT`) VALUES
(1, 'Monday', 'Lab 517', '00:34:00', '00:34:00', 'dwadawda', 'wdadawdwaw', '2025-05-13 16:32:05'),
(2, 'Tuesday', 'Lab 526', '02:38:00', '03:34:00', 'dwadwa', 'wadwadwa', '2025-05-13 16:34:48'),
(4, '', 'Lab 528', '01:54:00', '00:57:00', 'dwadaw', 'wadwa', '2025-05-13 16:55:03'),
(5, 'Wednesday', 'Lab 526', '00:59:00', '04:55:00', 'qqqqq', 'qqqqq', '2025-05-13 16:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `NOTIF_ID` int(11) NOT NULL,
  `USER_ID` int(11) DEFAULT NULL,
  `RESERVATION_ID` int(11) DEFAULT NULL,
  `ANNOUNCEMENT_ID` int(11) DEFAULT NULL,
  `MESSAGE` text DEFAULT NULL,
  `IS_READ` tinyint(1) NOT NULL DEFAULT 0,
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  `ADMIN_NOTIFICATION` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`NOTIF_ID`, `USER_ID`, `RESERVATION_ID`, `ANNOUNCEMENT_ID`, `MESSAGE`, `IS_READ`, `CREATED_AT`, `ADMIN_NOTIFICATION`) VALUES
(1, 1, 63, NULL, 'New reservation request for Laboratory 526 by Test Test', 0, '2025-05-14 01:46:14', 0),
(2, 30, 63, NULL, 'You\'ve submitted a reservation request for Laboratory 526', 1, '2025-05-14 01:46:14', 0),
(3, 230, 54, NULL, 'Your reservation for Laboratory  has been declined', 1, '2025-05-14 01:47:45', 0),
(4, 230, 54, NULL, 'Your reservation for Laboratory  has been declined', 1, '2025-05-14 01:47:45', 0),
(5, 230, 54, NULL, 'Your reservation for Laboratory  has been declined', 1, '2025-05-14 01:47:49', 0),
(6, 230, 54, NULL, 'Your reservation for Laboratory 528 has been approved', 1, '2025-05-14 01:47:56', 0),
(7, 30, 63, NULL, 'Your reservation for Laboratory 526 has been approved', 1, '2025-05-14 02:03:27', 0),
(8, 30, 55, NULL, 'Your reservation for Laboratory 524 has been declined', 1, '2025-05-14 02:13:21', 0),
(9, 30, 56, NULL, 'Your reservation for Laboratory 524 has been declined', 1, '2025-05-14 02:13:29', 0),
(10, 30, 57, NULL, 'Your reservation for Laboratory 524 has been declined', 1, '2025-05-14 02:13:40', 0),
(11, 30, 58, NULL, 'Your reservation for Laboratory 524 has been declined', 1, '2025-05-14 02:13:43', 0),
(12, 30, 59, NULL, 'Your reservation for Laboratory 524 has been declined', 1, '2025-05-14 02:13:46', 0),
(13, 30, 60, NULL, 'Your reservation for Laboratory 524 has been declined', 1, '2025-05-14 02:13:50', 0),
(14, 30, 61, NULL, 'Your reservation for Laboratory 524 has been declined', 1, '2025-05-14 02:13:53', 0),
(15, 30, 62, NULL, 'Your reservation for Laboratory 530 has been declined', 1, '2025-05-14 02:13:56', 0),
(16, 1, 64, NULL, 'New reservation request for Laboratory 530 by Test Test', 0, '2025-05-14 02:16:55', 0),
(17, 30, 64, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-14 02:16:55', 0),
(18, 30, 64, NULL, 'Your reservation for Laboratory 530 has been approved', 1, '2025-05-14 02:17:42', 0),
(19, 235, NULL, 38, 'New announcement: Hello!', 1, '2025-05-14 02:50:04', 0),
(20, 33, NULL, 38, 'New announcement: Hello!', 0, '2025-05-14 02:50:04', 0),
(21, 30, NULL, 38, 'New announcement: Hello!', 1, '2025-05-14 02:50:04', 0),
(22, 32, NULL, 38, 'New announcement: Hello!', 0, '2025-05-14 02:50:04', 0),
(23, 232, NULL, 38, 'New announcement: Hello!', 0, '2025-05-14 02:50:04', 0),
(24, 231, NULL, 38, 'New announcement: Hello!', 1, '2025-05-14 02:50:04', 0),
(25, 35, NULL, 38, 'New announcement: Hello!', 1, '2025-05-14 02:50:04', 0),
(26, 230, NULL, 38, 'New announcement: Hello!', 1, '2025-05-14 02:50:04', 0),
(27, 234, NULL, 38, 'New announcement: Hello!', 1, '2025-05-14 02:50:04', 0),
(28, 233, NULL, 38, 'New announcement: Hello!', 0, '2025-05-14 02:50:04', 0),
(29, 229, NULL, 38, 'New announcement: Hello!', 1, '2025-05-14 02:50:04', 0),
(30, 125, NULL, 38, 'New announcement: Hello!', 1, '2025-05-14 02:50:04', 0),
(31, 235, NULL, 39, 'New announcement: Good Morning!', 1, '2025-05-14 03:13:27', 0),
(32, 33, NULL, 39, 'New announcement: Good Morning!', 0, '2025-05-14 03:13:27', 0),
(33, 30, NULL, 39, 'New announcement: Good Morning!', 1, '2025-05-14 03:13:27', 0),
(34, 32, NULL, 39, 'New announcement: Good Morning!', 0, '2025-05-14 03:13:27', 0),
(35, 232, NULL, 39, 'New announcement: Good Morning!', 0, '2025-05-14 03:13:27', 0),
(36, 231, NULL, 39, 'New announcement: Good Morning!', 1, '2025-05-14 03:13:27', 0),
(37, 35, NULL, 39, 'New announcement: Good Morning!', 1, '2025-05-14 03:13:27', 0),
(38, 230, NULL, 39, 'New announcement: Good Morning!', 1, '2025-05-14 03:13:27', 0),
(39, 234, NULL, 39, 'New announcement: Good Morning!', 1, '2025-05-14 03:13:27', 0),
(40, 233, NULL, 39, 'New announcement: Good Morning!', 0, '2025-05-14 03:13:27', 0),
(41, 229, NULL, 39, 'New announcement: Good Morning!', 1, '2025-05-14 03:13:27', 0),
(42, 125, NULL, 39, 'New announcement: Good Morning!', 1, '2025-05-14 03:13:27', 0),
(43, 235, NULL, 40, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:02', 0),
(44, 33, NULL, 40, 'New announcement: Good Morning!', 0, '2025-05-14 03:17:02', 0),
(45, 30, NULL, 40, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:02', 0),
(46, 32, NULL, 40, 'New announcement: Good Morning!', 0, '2025-05-14 03:17:02', 0),
(47, 232, NULL, 40, 'New announcement: Good Morning!', 0, '2025-05-14 03:17:02', 0),
(48, 231, NULL, 40, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:02', 0),
(49, 35, NULL, 40, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:02', 0),
(50, 230, NULL, 40, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:02', 0),
(51, 234, NULL, 40, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:02', 0),
(52, 233, NULL, 40, 'New announcement: Good Morning!', 0, '2025-05-14 03:17:02', 0),
(53, 229, NULL, 40, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:02', 0),
(54, 125, NULL, 40, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:02', 0),
(55, 235, NULL, 41, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:11', 0),
(56, 33, NULL, 41, 'New announcement: Good Morning!', 0, '2025-05-14 03:17:11', 0),
(57, 30, NULL, 41, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:11', 0),
(58, 32, NULL, 41, 'New announcement: Good Morning!', 0, '2025-05-14 03:17:11', 0),
(59, 232, NULL, 41, 'New announcement: Good Morning!', 0, '2025-05-14 03:17:11', 0),
(60, 231, NULL, 41, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:11', 0),
(61, 35, NULL, 41, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:11', 0),
(62, 230, NULL, 41, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:11', 0),
(63, 234, NULL, 41, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:11', 0),
(64, 233, NULL, 41, 'New announcement: Good Morning!', 0, '2025-05-14 03:17:11', 0),
(65, 229, NULL, 41, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:11', 0),
(66, 125, NULL, 41, 'New announcement: Good Morning!', 1, '2025-05-14 03:17:11', 0),
(67, 1, 65, NULL, 'New reservation request for Laboratory 544 by Test Test', 0, '2025-05-14 05:01:51', 0),
(68, 30, 65, NULL, 'You\'ve submitted a reservation request for Laboratory 544', 1, '2025-05-14 05:01:51', 0),
(69, 30, 65, NULL, 'Your reservation for Laboratory 544 has been approved', 1, '2025-05-14 05:02:13', 0),
(70, 1, 66, NULL, 'New reservation request for Laboratory 528 by ffff sasafaff', 0, '2025-05-14 06:04:31', 0),
(71, 231, 66, NULL, 'You\'ve submitted a reservation request for Laboratory 528', 1, '2025-05-14 06:04:31', 0),
(72, 231, 66, NULL, 'Your reservation for Laboratory 528 has been approved', 1, '2025-05-14 06:05:15', 0),
(73, 1, 67, NULL, 'New reservation request for Laboratory 530 by Test Test', 0, '2025-05-14 15:00:50', 0),
(74, 30, 67, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-14 15:00:50', 0),
(75, 30, 67, NULL, 'Your reservation for Laboratory 530 has been approved', 1, '2025-05-14 15:01:22', 0),
(76, 1, 68, NULL, 'New reservation request for Laboratory 530 by Test Test', 0, '2025-05-14 15:02:23', 0),
(77, 30, 68, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-14 15:02:23', 0),
(78, 30, 68, NULL, 'Your reservation for Laboratory 530 has been approved', 1, '2025-05-14 15:02:41', 0),
(79, 1, 69, NULL, 'New reservation request for Laboratory 530 by Test Test', 0, '2025-05-14 15:09:33', 0),
(80, 30, 69, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-14 15:09:33', 0),
(81, 30, 69, NULL, 'Your reservation for Laboratory 530 has been approved', 1, '2025-05-14 15:09:56', 0),
(82, 1, 70, NULL, 'New reservation request for Laboratory 530 by Test Test', 0, '2025-05-14 15:16:09', 0),
(83, 30, 70, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-14 15:16:09', 0),
(84, 30, 70, NULL, 'Your reservation for Laboratory 530 has been approved', 1, '2025-05-14 15:16:32', 0),
(85, 1, 71, NULL, 'New reservation request for Laboratory 526 by wadawd dawdawd', 0, '2025-05-14 15:21:22', 0),
(86, 230, 71, NULL, 'You\'ve submitted a reservation request for Laboratory 526', 1, '2025-05-14 15:21:22', 0),
(87, 230, 71, NULL, 'Your reservation for Laboratory 526 has been approved', 1, '2025-05-14 15:22:14', 0),
(88, 1, 72, NULL, 'New reservation request for Laboratory 524 by sdfsfsd sdfsdd', 0, '2025-05-14 16:27:53', 0),
(89, 234, 72, NULL, 'You\'ve submitted a reservation request for Laboratory 524', 1, '2025-05-14 16:27:53', 0),
(90, 234, 72, NULL, 'New reservation from sdfsdd, sdfsfsd sdfsdfs for 524', 1, '2025-05-14 16:27:53', 1),
(91, 1, 73, NULL, 'New reservation request for Laboratory 528 by dwadwad dwadwa', 0, '2025-05-14 16:35:59', 0),
(92, 229, 73, NULL, 'You\'ve submitted a reservation request for Laboratory 528', 1, '2025-05-14 16:35:59', 0),
(93, 229, 73, NULL, 'New reservation from dwadwa, dwadwad wadawd for 528', 1, '2025-05-14 16:35:59', 1),
(94, 229, 73, NULL, 'Your reservation for Laboratory 528 has been approved', 1, '2025-05-14 16:36:39', 0),
(95, 234, 72, NULL, 'Your reservation for Laboratory 524 has been declined', 1, '2025-05-14 16:45:37', 0),
(96, 1, 74, NULL, 'New reservation request for Laboratory 542 by sdfsfsd sdfsdd', 0, '2025-05-14 16:46:18', 0),
(97, 234, 74, NULL, 'You\'ve submitted a reservation request for Laboratory 542', 1, '2025-05-14 16:46:18', 0),
(98, 234, 74, NULL, 'New reservation from sdfsdd, sdfsfsd sdfsdfs for 542', 1, '2025-05-14 16:46:18', 1),
(99, 234, 74, NULL, 'Your reservation for Laboratory 542 has been declined', 1, '2025-05-14 16:53:18', 0),
(100, 1, 75, NULL, 'New reservation request for Laboratory 530 by sdfsfsd sdfsdd', 0, '2025-05-14 16:53:55', 0),
(101, 234, 75, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-14 16:53:55', 0),
(102, 234, 75, NULL, 'New reservation from sdfsdd, sdfsfsd sdfsdfs for 530', 1, '2025-05-14 16:53:55', 1),
(103, 234, 75, NULL, 'Your reservation for Laboratory 530 has been declined', 1, '2025-05-14 17:03:57', 0),
(104, 1, 76, NULL, 'New reservation request for Laboratory 530 by sdfsfsd sdfsdd', 0, '2025-05-14 17:04:31', 0),
(105, 234, 76, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-14 17:04:31', 0),
(106, 234, 76, NULL, 'New reservation from sdfsdd, sdfsfsd sdfsdfs for 530', 1, '2025-05-14 17:04:31', 1),
(107, 234, 76, NULL, 'Your reservation for Laboratory 530 has been declined', 1, '2025-05-14 17:09:18', 0),
(108, 1, 77, NULL, 'New reservation request for Laboratory 526 by sdfsfsd sdfsdd', 0, '2025-05-14 17:16:02', 0),
(109, 234, 77, NULL, 'You\'ve submitted a reservation request for Laboratory 526', 1, '2025-05-14 17:16:02', 0),
(110, 234, 77, NULL, 'New reservation from sdfsdd, sdfsfsd sdfsdfs for 526', 1, '2025-05-14 17:16:02', 1),
(111, 1, 78, NULL, 'New reservation request for Laboratory 530 by wefwfw sfswfewesf', 0, '2025-05-14 17:51:27', 0),
(112, 235, 78, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-14 17:51:27', 0),
(113, 1, 78, NULL, 'New reservation from sfswfewesf, wefwfw fwewfw for 530', 1, '2025-05-14 17:51:27', 1),
(114, 235, 78, NULL, 'Your reservation for Laboratory 530 has been declined', 1, '2025-05-14 17:59:12', 0),
(115, 1, 79, NULL, 'New reservation request for Laboratory 526 by wefwfw sfswfewesf', 0, '2025-05-14 17:59:46', 0),
(116, 235, 79, NULL, 'You\'ve submitted a reservation request for Laboratory 526', 1, '2025-05-14 17:59:46', 0),
(117, 1, 79, NULL, 'New reservation from sfswfewesf, wefwfw fwewfw for 526', 1, '2025-05-14 17:59:46', 1),
(118, 235, 79, NULL, 'Your reservation for Laboratory 526 has been declined', 1, '2025-05-14 18:15:25', 0),
(119, 1, 80, NULL, 'New reservation request for Laboratory 528 by wefwfw sfswfewesf', 0, '2025-05-14 18:15:58', 0),
(120, 235, 80, NULL, 'You\'ve submitted a reservation request for Laboratory 528', 1, '2025-05-14 18:15:58', 0),
(121, 1, 81, NULL, 'New reservation request for Laboratory 526 by wefwfw sfswfewesf', 0, '2025-05-14 23:31:04', 0),
(122, 235, 81, NULL, 'You\'ve submitted a reservation request for Laboratory 526', 1, '2025-05-14 23:31:04', 0),
(123, 235, 81, NULL, 'New reservation request from wefwfw sfswfewesf for 526', 1, '2025-05-14 23:31:04', 1),
(124, 235, 81, NULL, 'Your reservation for Laboratory 526 has been declined', 1, '2025-05-14 23:39:20', 0),
(125, 1, 82, NULL, 'New reservation request for Laboratory 530 by wefwfw sfswfewesf', 0, '2025-05-14 23:39:59', 0),
(126, 235, 82, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-14 23:39:59', 0),
(127, 235, 82, NULL, 'New reservation request from wefwfw sfswfewesf for 530', 1, '2025-05-14 23:39:59', 1),
(128, 235, 82, NULL, 'Your reservation for Laboratory 530 has been declined', 1, '2025-05-14 23:54:03', 0),
(129, 1, 83, NULL, 'New reservation request for Laboratory 530 by wefwfw sfswfewesf', 0, '2025-05-14 23:59:02', 0),
(130, 235, 83, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-14 23:59:02', 0),
(131, 235, 83, NULL, 'New reservation request for 530 on 2025-05-22', 1, '2025-05-14 23:59:02', 1),
(132, 1, 84, NULL, 'New reservation request for Laboratory 542 by wefwfw sfswfewesf', 0, '2025-05-15 00:07:48', 0),
(133, 235, 84, NULL, 'You\'ve submitted a reservation request for Laboratory 542', 1, '2025-05-15 00:07:48', 0),
(134, 235, 84, NULL, 'New reservation request for 542 on 2025-05-20', 1, '2025-05-15 00:07:48', 1),
(135, 235, 84, NULL, 'New reservation request from wefwfw sfswfewesf for 542', 1, '2025-05-15 00:07:48', 1),
(136, 1, 85, NULL, 'New reservation request for Laboratory 530 by wefwfw sfswfewesf', 0, '2025-05-15 00:08:17', 0),
(137, 235, 85, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-15 00:08:17', 0),
(138, 235, 85, NULL, 'New reservation request for 530 on 2025-05-28', 1, '2025-05-15 00:08:17', 1),
(139, 1, 86, NULL, 'New reservation request for Laboratory 530 by wefwfw sfswfewesf', 0, '2025-05-15 00:12:25', 0),
(140, 235, 86, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-15 00:12:25', 0),
(141, 235, 86, NULL, 'New reservation request for 530 on 2025-05-22', 1, '2025-05-15 00:12:25', 1),
(142, 235, 86, NULL, 'Your reservation for Laboratory 530 has been declined', 1, '2025-05-15 00:13:14', 0),
(143, 235, 85, NULL, 'Your reservation for Laboratory 530 has been declined', 1, '2025-05-15 00:13:19', 0),
(144, 235, 84, NULL, 'Your reservation for Laboratory 542 has been declined', 1, '2025-05-15 00:13:24', 0),
(145, 235, 83, NULL, 'Your reservation for Laboratory 530 has been declined', 1, '2025-05-15 00:13:29', 0),
(146, 1, 87, NULL, 'New reservation request for Laboratory 524 by wefwfw sfswfewesf', 0, '2025-05-15 00:20:13', 0),
(147, 235, 87, NULL, 'You\'ve submitted a reservation request for Laboratory 524', 1, '2025-05-15 00:20:13', 0),
(148, 235, 87, NULL, 'New reservation request for 524 on 2025-05-28', 1, '2025-05-15 00:20:13', 1),
(149, 1, 88, NULL, 'New reservation request for Laboratory 530 by wefwfw sfswfewesf', 0, '2025-05-15 00:26:40', 0),
(150, 235, 88, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-15 00:26:40', 0),
(151, 235, 88, NULL, 'New reservation request for 530 on 2025-05-22', 1, '2025-05-15 00:26:40', 1),
(152, 1, 89, NULL, 'New reservation request for Laboratory 542 by wefwfw sfswfewesf', 0, '2025-05-15 00:33:13', 0),
(153, 235, 89, NULL, 'You\'ve submitted a reservation request for Laboratory 542', 1, '2025-05-15 00:33:13', 0),
(154, 235, 89, NULL, 'New reservation request for 542 on 2025-06-06', 1, '2025-05-15 00:33:13', 1),
(155, 1, 90, NULL, 'New reservation request for Laboratory 530 by wefwfw sfswfewesf', 0, '2025-05-15 00:40:33', 0),
(156, 235, 90, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-15 00:40:33', 0),
(157, 235, 90, NULL, 'New reservation request for 530 on 2025-05-18', 1, '2025-05-15 00:40:33', 1),
(158, 1, 91, NULL, 'New reservation request for Laboratory 530 by wefwfw sfswfewesf', 0, '2025-05-15 00:47:56', 0),
(159, 235, 91, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-15 00:47:56', 0),
(160, 235, 91, NULL, 'New reservation request for 530 on 2025-05-21', 1, '2025-05-15 00:47:56', 1),
(161, 235, 91, NULL, 'Your reservation for Laboratory 530 has been declined', 0, '2025-05-15 00:54:07', 0),
(162, 235, 90, NULL, 'Your reservation for Laboratory 530 has been declined', 0, '2025-05-15 00:54:10', 0),
(163, 235, 89, NULL, 'Your reservation for Laboratory 542 has been declined', 0, '2025-05-15 00:54:12', 0),
(164, 235, 89, NULL, 'Your reservation for Laboratory 542 has been declined', 0, '2025-05-15 00:54:12', 0),
(165, 235, 88, NULL, 'Your reservation for Laboratory 530 has been declined', 0, '2025-05-15 00:54:15', 0),
(166, 235, 87, NULL, 'Your reservation for Laboratory 524 has been declined', 0, '2025-05-15 00:54:17', 0),
(167, 235, 87, NULL, 'Your reservation for Laboratory 524 has been declined', 0, '2025-05-15 00:54:17', 0),
(168, 235, 80, NULL, 'Your reservation for Laboratory 528 has been declined', 0, '2025-05-15 00:54:19', 0),
(169, 234, 77, NULL, 'Your reservation for Laboratory 526 has been declined', 0, '2025-05-15 00:54:23', 0),
(170, 1, 92, NULL, 'New reservation request for Laboratory 542 by Real Jhon Palacio', 0, '2025-05-15 02:13:07', 0),
(171, 236, 92, NULL, 'You\'ve submitted a reservation request for Laboratory 542', 1, '2025-05-15 02:13:07', 0),
(172, 236, 92, NULL, 'New reservation request for 542 on 2025-05-21', 1, '2025-05-15 02:13:07', 1),
(173, 236, 92, NULL, 'Your reservation for Laboratory 542 has been approved', 0, '2025-05-15 02:13:50', 0),
(174, 1, 93, NULL, 'New reservation request for Laboratory 530 by Real Jhon Palacio', 0, '2025-05-15 02:15:52', 0),
(175, 237, 93, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-15 02:15:52', 0),
(176, 237, 93, NULL, 'New reservation request for 530 on 2025-05-21', 1, '2025-05-15 02:15:52', 1),
(177, 237, 93, NULL, 'Your reservation for Laboratory 530 has been approved', 0, '2025-05-15 02:16:14', 0),
(178, 1, 94, NULL, 'New reservation request for Laboratory 530 by Real Jhon Palacio', 0, '2025-05-15 02:21:20', 0),
(179, 238, 94, NULL, 'You\'ve submitted a reservation request for Laboratory 530', 1, '2025-05-15 02:21:20', 0),
(180, 238, 94, NULL, 'New reservation request for 530 on 2025-05-27', 1, '2025-05-15 02:21:20', 1),
(181, 238, 94, NULL, 'Your reservation for Laboratory 530 has been approved', 1, '2025-05-15 02:21:46', 0),
(182, 1, 95, NULL, 'New reservation request for Laboratory 542 by Real Jhon Palacio', 0, '2025-05-15 02:33:40', 0),
(183, 238, 95, NULL, 'You\'ve submitted a reservation request for Laboratory 542', 1, '2025-05-15 02:33:40', 0),
(184, 238, 95, NULL, 'New reservation request for 542 on 2025-05-29', 1, '2025-05-15 02:33:40', 1),
(185, 238, 95, NULL, 'Your reservation for Laboratory 542 has been approved', 0, '2025-05-15 02:34:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pc_status`
--

CREATE TABLE `pc_status` (
  `id` int(11) NOT NULL,
  `laboratory` varchar(10) NOT NULL,
  `pc_number` int(11) NOT NULL,
  `status` enum('unavailable') NOT NULL DEFAULT 'unavailable',
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pc_status`
--

INSERT INTO `pc_status` (`id`, `laboratory`, `pc_number`, `status`, `updated_by`, `updated_at`) VALUES
(1, '530', 8, 'unavailable', 1, '2025-05-13 23:11:01'),
(3, '526', 3, 'unavailable', 1, '2025-05-14 10:02:15');

-- --------------------------------------------------------

--
-- Table structure for table `points_history`
--

CREATE TABLE `points_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `points_history`
--

INSERT INTO `points_history` (`id`, `user_id`, `points`, `created_at`) VALUES
(2, 230, 1, '2025-05-14 04:06:11'),
(3, 230, 1, '2025-05-14 04:06:23'),
(4, 230, 1, '2025-05-14 04:06:33'),
(5, 230, 1, '2025-05-14 04:06:51'),
(6, 230, 1, '2025-05-14 04:06:54'),
(7, 230, 1, '2025-05-14 04:07:00'),
(8, 30, 1, '2025-05-14 04:07:15'),
(9, 30, 1, '2025-05-14 04:07:20'),
(10, 30, 1, '2025-05-14 04:07:23'),
(11, 32, 1, '2025-05-14 04:42:34'),
(12, 32, 1, '2025-05-14 04:42:36'),
(13, 32, 1, '2025-05-14 04:42:41'),
(14, 30, 1, '2025-05-14 04:57:42'),
(15, 30, 1, '2025-05-14 04:59:50'),
(16, 30, 1, '2025-05-14 04:59:52'),
(17, 30, 1, '2025-05-14 05:02:20'),
(18, 30, 1, '2025-05-14 05:02:20'),
(19, 30, 1, '2025-05-14 05:02:30'),
(20, 30, 1, '2025-05-14 05:02:36'),
(21, 30, 1, '2025-05-14 05:02:38'),
(22, 30, 1, '2025-05-14 05:02:40'),
(23, 230, 1, '2025-05-14 06:02:35'),
(24, 230, 1, '2025-05-14 06:02:37'),
(25, 230, 1, '2025-05-14 06:02:39'),
(26, 231, 1, '2025-05-14 06:06:11'),
(27, 32, 1, '2025-05-14 15:08:15'),
(28, 30, 1, '2025-05-14 15:16:42'),
(29, 30, 1, '2025-05-14 15:16:44'),
(30, 30, 1, '2025-05-14 15:16:46'),
(31, 231, 1, '2025-05-14 15:17:50'),
(32, 231, 1, '2025-05-14 15:17:53'),
(33, 230, 1, '2025-05-14 15:22:22'),
(34, 230, 1, '2025-05-14 15:22:24'),
(35, 125, 1, '2025-05-14 15:31:22'),
(36, 125, 1, '2025-05-15 02:23:33'),
(37, 125, 1, '2025-05-15 02:23:37'),
(38, 32, 1, '2025-05-15 02:36:17'),
(39, 32, 1, '2025-05-15 02:36:25');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `idno` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `course` varchar(50) NOT NULL,
  `year_level` varchar(20) NOT NULL,
  `purpose` varchar(100) NOT NULL,
  `laboratory` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `time_in` time NOT NULL,
  `pc_number` varchar(10) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `idno`, `full_name`, `course`, `year_level`, `purpose`, `laboratory`, `date`, `time_in`, `pc_number`, `status`, `created_at`) VALUES
(1, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-27', '18:53:00', '18', 'approved', '2025-05-07 18:48:14'),
(2, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-27', '18:53:00', '18', 'disapproved', '2025-05-07 18:48:43'),
(3, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '524', '2025-06-06', '22:57:00', '19', 'approved', '2025-05-07 18:53:47'),
(4, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '528', '2025-05-27', '19:43:00', '8', 'approved', '2025-05-07 19:39:09'),
(5, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '528', '2025-05-27', '19:43:00', '8', 'approved', '2025-05-07 19:39:09'),
(6, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Embedded System & IOT', '526', '2025-05-29', '19:50:00', '1', 'approved', '2025-05-07 19:45:24'),
(7, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Web Design & Development', '526', '2025-05-27', '19:01:00', '6', 'approved', '2025-05-07 19:57:51'),
(8, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Web Design & Development', '526', '2025-05-21', '19:02:00', '7', 'approved', '2025-05-07 19:58:57'),
(9, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '526', '2025-06-04', '20:09:00', '17', 'approved', '2025-05-07 20:04:38'),
(10, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '526', '2025-06-04', '20:09:00', '17', 'disapproved', '2025-05-07 20:04:38'),
(11, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '524', '2025-05-29', '20:10:00', '11', 'disapproved', '2025-05-07 20:05:50'),
(13, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '524', '2025-05-28', '12:16:00', '12', 'approved', '2025-05-07 20:11:10'),
(16, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Web Design & Development', '526', '0000-00-00', '00:00:00', '6', 'disapproved', '2025-05-07 20:20:11'),
(17, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Embedded System & IOT', '526', '2025-05-20', '20:26:00', '6', 'approved', '2025-05-07 20:21:58'),
(18, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Embedded System & IOT', '528', '2025-05-28', '13:22:00', '12', 'approved', '2025-05-07 20:22:55'),
(20, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '528', '2025-05-20', '20:37:00', '13', 'approved', '2025-05-07 20:32:47'),
(21, '45645645', 'awdwad, awdawdawd wadwad', 'BSIT', '2nd Year', 'System Integration & Architecture', '526', '2025-05-28', '20:50:00', '16', 'approved', '2025-05-07 20:47:01'),
(22, '45645645', 'awdwad, awdawdawd wadwad', 'BSIT', '2nd Year', 'Web Design & Development', '524', '2025-05-21', '20:59:00', '1', 'approved', '2025-05-07 20:54:38'),
(23, '45645645', 'awdwad, awdawdawd wadwad', 'BSIT', '2nd Year', 'Embedded System & IOT', '524', '2025-05-19', '20:02:00', '1', 'approved', '2025-05-07 20:59:41'),
(25, '45645645', 'awdwad, awdawdawd wadwad', 'BSIT', '2nd Year', 'Computer Application', '526', '2025-05-27', '13:04:00', '6', 'approved', '2025-05-07 21:04:41'),
(26, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-21', '22:52:00', '11', 'approved', '2025-05-07 22:48:58'),
(27, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-21', '22:53:00', '11', 'approved', '2025-05-07 22:51:02'),
(28, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Embedded System & IOT', '528', '2025-05-17', '14:52:00', '1', 'approved', '2025-05-07 22:52:52'),
(29, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '526', '2025-05-29', '22:58:00', '11', 'approved', '2025-05-07 22:54:09'),
(30, '45645645', 'awdwad, awdawdawd wadwad', 'BSIT', '2nd Year', 'Computer Application', '526', '2025-05-28', '22:59:00', '17', 'approved', '2025-05-07 22:55:53'),
(31, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '528', '2025-05-22', '15:08:00', '6', 'approved', '2025-05-07 23:04:37'),
(32, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Web Design & Development', '524', '2025-05-18', '15:08:00', '1', 'approved', '2025-05-07 23:08:53'),
(33, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Project Management', '528', '2025-05-21', '23:21:00', '11', 'approved', '2025-05-07 23:17:16'),
(34, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Computer Application', '528', '2025-05-22', '23:27:00', '1', 'approved', '2025-05-07 23:22:13'),
(35, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Computer Application', '528', '2025-05-22', '23:27:00', '1', 'disapproved', '2025-05-07 23:22:13'),
(36, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Digital Logic & Design', '524', '2025-05-12', '23:45:00', '1', 'approved', '2025-05-07 23:40:27'),
(37, '99999999', 'dfsdsfsdf, sdfsdfds dsfsdf', 'BSCS', '2nd Year', 'System Integration & Architecture', '526', '2025-05-21', '23:50:00', '21', 'approved', '2025-05-07 23:46:37'),
(38, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '524', '2025-05-23', '00:17:00', '2', 'approved', '2025-05-08 00:12:50'),
(39, '88888888', 'dwadwa, dwadwad wadawd', 'BSCS', '3rd Year', 'C# Programming', '528', '2025-06-03', '00:28:00', '7', 'approved', '2025-05-08 00:24:15'),
(40, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'Web Design & Development', '544', '2025-05-04', '04:42:00', '18', 'approved', '2025-05-08 00:43:03'),
(41, '78888888', 'ddddddd, dddd ddd', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-21', '04:55:00', '12', 'approved', '2025-05-08 00:51:25'),
(42, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Java Programming', '530', '2025-05-04', '01:24:00', '1', 'approved', '2025-05-08 01:19:05'),
(43, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Embedded System & IOT', '542', '2025-05-18', '01:30:00', '1', 'approved', '2025-05-08 01:25:31'),
(44, '78888888', 'ddddddd, dddd ddd', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-22', '01:44:00', '1', 'approved', '2025-05-08 01:40:39'),
(45, '88888888', 'dwadwa, dwadwad wadawd', 'BSCS', '3rd Year', 'System Integration & Architecture', '530', '2025-05-19', '02:05:00', '2', 'approved', '2025-05-08 02:01:23'),
(46, '44444444', 'sasafaff, ffff ffff', 'BSIS', '1st Year', 'System Integration & Architecture', '524', '2025-05-19', '02:07:00', '7', 'approved', '2025-05-08 02:04:57'),
(47, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'Project Management', '524', '2025-05-27', '06:15:00', '12', 'approved', '2025-05-08 02:11:17'),
(48, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'Project Management', '524', '2025-05-27', '06:15:00', '12', 'disapproved', '2025-05-08 02:11:17'),
(49, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'C Programming', '542', '2025-05-22', '00:34:00', '16', 'disapproved', '2025-05-08 08:34:52'),
(50, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-20', '08:01:00', '2', 'approved', '2025-05-08 08:57:44'),
(51, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-06-05', '09:45:00', '6', 'approved', '2025-05-08 09:42:56'),
(52, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '528', '2025-05-22', '09:59:00', '11', 'disapproved', '2025-05-08 09:54:10'),
(53, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '526', '2025-05-22', '10:10:00', '12', 'disapproved', '2025-05-08 10:06:09'),
(54, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Embedded System & IOT', '528', '2025-05-22', '02:22:00', '17', 'approved', '2025-05-08 10:17:38'),
(55, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '8', 'disapproved', '2025-05-14 02:11:51'),
(56, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '6', 'disapproved', '2025-05-14 02:12:33'),
(57, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '6', 'disapproved', '2025-05-14 02:14:47'),
(58, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '6', 'disapproved', '2025-05-14 02:14:51'),
(59, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '18', 'disapproved', '2025-05-14 02:15:01'),
(60, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '18', 'disapproved', '2025-05-14 02:16:21'),
(61, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '18', 'disapproved', '2025-05-14 02:16:23'),
(62, '22222222', 'Test, Test Test', 'BSCS', '1', 'Web Design & Development', '530', '2025-05-30', '09:43:00', '11', 'disapproved', '2025-05-14 09:39:59'),
(63, '22222222', 'Test, Test Test', 'BSCS', '1', 'Embedded System & IOT', '526', '2025-05-22', '09:49:00', '12', 'approved', '2025-05-14 09:46:14'),
(64, '22222222', 'Test, Test Test', 'BSCS', '1', 'Database', '530', '2025-05-20', '10:20:00', '11', 'approved', '2025-05-14 10:16:55'),
(65, '22222222', 'Test, Test Test', 'BSCS', '1', 'System Integration & Architecture', '544', '2025-05-22', '13:05:00', '7', 'approved', '2025-05-14 13:01:51'),
(66, '44444444', 'sasafaff, ffff ffff', 'BSIS', '1st Year', 'System Integration & Architecture', '528', '2025-05-22', '14:08:00', '28', 'approved', '2025-05-14 14:04:30'),
(67, '22222222', 'Test, Test Test', 'BSCS', '1', 'Web Design & Development', '530', '2025-05-30', '23:03:00', '11', 'approved', '2025-05-14 23:00:50'),
(68, '22222222', 'Test, Test Test', 'BSCS', '1', 'Computer Application', '530', '2025-05-28', '23:05:00', '6', 'approved', '2025-05-14 23:02:23'),
(69, '22222222', 'Test, Test Test', 'BSCS', '1', 'Computer Application', '530', '2025-05-22', '14:09:00', '6', 'approved', '2025-05-14 23:09:33'),
(70, '22222222', 'Test, Test Test', 'BSCS', '1', 'System Integration & Architecture', '530', '2025-05-28', '23:19:00', '11', 'approved', '2025-05-14 23:16:09'),
(71, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Web Design & Development', '526', '2025-05-28', '23:23:00', '11', 'approved', '2025-05-14 23:21:22'),
(72, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'Computer Application', '524', '2025-05-19', '00:30:00', '21', 'disapproved', '2025-05-15 00:27:53'),
(73, '88888888', 'dwadwa, dwadwad wadawd', 'BSCS', '3rd Year', 'Web Design & Development', '528', '2025-05-29', '04:35:00', '16', 'approved', '2025-05-15 00:35:59'),
(74, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'Database', '542', '2025-05-21', '00:50:00', '11', 'disapproved', '2025-05-15 00:46:18'),
(75, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'Python Programming', '530', '2025-05-23', '03:56:00', '11', 'disapproved', '2025-05-15 00:53:55'),
(76, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'Embedded System & IOT', '530', '2025-05-18', '01:08:00', '16', 'disapproved', '2025-05-15 01:04:31'),
(77, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'C++ Programming', '526', '2025-05-25', '01:19:00', '13', 'disapproved', '2025-05-15 01:16:02'),
(78, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'Computer Application', '530', '2025-05-30', '01:56:00', '16', 'disapproved', '2025-05-15 01:51:27'),
(79, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'Python Programming', '526', '2025-05-20', '01:02:00', '13', 'disapproved', '2025-05-15 01:59:46'),
(80, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'Computer Application', '528', '2025-05-21', '02:15:00', '6', 'disapproved', '2025-05-15 02:15:58'),
(81, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'Java Programming', '526', '2025-05-21', '07:34:00', '17', 'disapproved', '2025-05-15 07:31:04'),
(82, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'System Integration & Architecture', '530', '2025-05-26', '07:42:00', '11', 'disapproved', '2025-05-15 07:39:59'),
(83, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'Computer Application', '530', '2025-05-22', '07:01:00', '6', 'disapproved', '2025-05-15 07:59:02'),
(84, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'System Integration & Architecture', '542', '2025-05-20', '08:11:00', '6', 'disapproved', '2025-05-15 08:07:48'),
(85, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'System Integration & Architecture', '530', '2025-05-28', '08:12:00', '11', 'disapproved', '2025-05-15 08:08:17'),
(86, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'System Integration & Architecture', '530', '2025-05-22', '00:12:00', '11', 'disapproved', '2025-05-15 08:12:25'),
(87, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'Computer Application', '524', '2025-05-28', '00:25:00', '6', 'disapproved', '2025-05-15 08:20:13'),
(88, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'Computer Application', '530', '2025-05-22', '20:29:00', '11', 'disapproved', '2025-05-15 08:26:40'),
(89, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'Computer Application', '542', '2025-06-06', '08:37:00', '6', 'disapproved', '2025-05-15 08:33:13'),
(90, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'Computer Application', '530', '2025-05-18', '08:43:00', '6', 'disapproved', '2025-05-15 08:40:33'),
(91, '04555555', 'sfswfewesf, wefwfw fwewfw', 'BSIS', '2nd Year', 'Web Design & Development', '530', '2025-05-21', '08:52:00', '6', 'disapproved', '2025-05-15 08:47:56'),
(92, '22682504', 'Palacio, Real Jhon Samson', 'BSIT', '4th Year', 'Web Design & Development', '542', '2025-05-21', '10:18:00', '16', 'approved', '2025-05-15 10:13:06'),
(93, '22682505', 'Palacio, Real Jhon Samson', 'BSIT', '3rd Year', 'Embedded System & IOT', '530', '2025-05-21', '10:20:00', '6', 'approved', '2025-05-15 10:15:52'),
(94, '22222227', 'Palacio, Real Jhon Samson', 'BSIT', '3rd Year', 'Embedded System & IOT', '530', '2025-05-27', '10:25:00', '14', 'approved', '2025-05-15 10:21:20'),
(95, '22222227', 'Palacio, Real Jhon Samson', 'BSIT', '3rd Year', 'Web Design & Development', '542', '2025-05-29', '10:36:00', '6', 'approved', '2025-05-15 10:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_logs`
--

CREATE TABLE `reservation_logs` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `idno` varchar(20) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `year_level` varchar(20) DEFAULT NULL,
  `purpose` varchar(100) DEFAULT NULL,
  `laboratory` varchar(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `pc_number` varchar(10) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `action_by` int(11) DEFAULT NULL,
  `action_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_logs`
--

INSERT INTO `reservation_logs` (`id`, `reservation_id`, `idno`, `full_name`, `course`, `year_level`, `purpose`, `laboratory`, `date`, `time_in`, `pc_number`, `status`, `action_type`, `action_by`, `action_date`) VALUES
(1, 4, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '528', '2025-05-27', '19:43:00', '8', 'approved', 'Approved', 1, '2025-05-07 19:39:31'),
(2, 4, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '528', '2025-05-27', '19:43:00', '8', 'approved', 'Approved', 1, '2025-05-07 19:39:31'),
(3, 5, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '528', '2025-05-27', '19:43:00', '8', 'approved', 'Approved', 1, '2025-05-07 19:39:37'),
(4, 6, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Embedded System & IOT', '526', '2025-05-29', '19:50:00', '1', 'approved', 'Approved', 1, '2025-05-07 19:45:51'),
(5, 7, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Web Design & Development', '526', '2025-05-27', '19:01:00', '6', 'approved', 'Approved', 1, '2025-05-07 19:58:12'),
(6, 8, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Web Design & Development', '526', '2025-05-21', '19:02:00', '7', 'approved', 'Approved', 1, '2025-05-07 19:59:21'),
(7, 9, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '526', '2025-06-04', '20:09:00', '17', 'approved', 'Approved', 1, '2025-05-07 20:05:04'),
(8, 13, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '524', '2025-05-28', '12:16:00', '12', 'approved', 'Approved', 1, '2025-05-07 20:11:31'),
(9, 17, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Embedded System & IOT', '526', '2025-05-20', '20:26:00', '6', 'approved', 'Approved', 1, '2025-05-07 20:22:05'),
(10, 18, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Embedded System & IOT', '528', '2025-05-28', '13:22:00', '12', 'approved', 'Approved', 1, '2025-05-07 20:23:11'),
(11, 21, '45645645', 'awdwad, awdawdawd wadwad', 'BSIT', '2nd Year', 'System Integration & Architecture', '526', '2025-05-28', '20:50:00', '16', 'approved', 'Approved', 1, '2025-05-07 20:47:43'),
(12, 20, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '528', '2025-05-20', '20:37:00', '13', 'approved', 'Approved', 1, '2025-05-07 20:54:09'),
(13, 22, '45645645', 'awdwad, awdawdawd wadwad', 'BSIT', '2nd Year', 'Web Design & Development', '524', '2025-05-21', '20:59:00', '1', 'approved', 'Approved', 1, '2025-05-07 20:54:52'),
(14, 23, '45645645', 'awdwad, awdawdawd wadwad', 'BSIT', '2nd Year', 'Embedded System & IOT', '524', '2025-05-19', '20:02:00', '1', 'approved', 'Approved', 1, '2025-05-07 21:03:33'),
(15, 25, '45645645', 'awdwad, awdawdawd wadwad', 'BSIT', '2nd Year', 'Computer Application', '526', '2025-05-27', '13:04:00', '6', 'approved', 'Approved', 1, '2025-05-07 21:04:56'),
(16, 26, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-21', '22:52:00', '11', 'approved', 'Approved', 1, '2025-05-07 22:49:14'),
(17, 27, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-21', '22:53:00', '11', 'approved', 'Approved', 1, '2025-05-07 22:51:18'),
(18, 28, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Embedded System & IOT', '528', '2025-05-17', '14:52:00', '1', 'approved', 'Approved', 1, '2025-05-07 22:53:04'),
(19, 29, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '526', '2025-05-29', '22:58:00', '11', 'approved', 'Approved', 1, '2025-05-07 22:54:24'),
(20, 30, '45645645', 'awdwad, awdawdawd wadwad', 'BSIT', '2nd Year', 'Computer Application', '526', '2025-05-28', '22:59:00', '17', 'approved', 'Approved', 1, '2025-05-07 22:56:06'),
(21, 31, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '528', '2025-05-22', '15:08:00', '6', 'approved', 'Approved', 1, '2025-05-07 23:04:52'),
(22, 32, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Web Design & Development', '524', '2025-05-18', '15:08:00', '1', 'approved', 'Approved', 1, '2025-05-07 23:09:10'),
(23, 33, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Project Management', '528', '2025-05-21', '23:21:00', '11', 'approved', 'Approved', 1, '2025-05-07 23:17:35'),
(24, 34, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Computer Application', '528', '2025-05-22', '23:27:00', '1', 'approved', 'Approved', 1, '2025-05-07 23:22:30'),
(25, 36, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Digital Logic & Design', '524', '2025-05-12', '23:45:00', '1', 'approved', 'Approved', 1, '2025-05-07 23:40:42'),
(26, 37, '99999999', 'dfsdsfsdf, sdfsdfds dsfsdf', 'BSCS', '2nd Year', 'System Integration & Architecture', '526', '2025-05-21', '23:50:00', '21', 'approved', 'Approved', 1, '2025-05-07 23:46:55'),
(27, 38, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '524', '2025-05-23', '00:17:00', '2', 'approved', 'Approved', 1, '2025-05-08 00:13:13'),
(28, 39, '88888888', 'dwadwa, dwadwad wadawd', 'BSCS', '3rd Year', 'C# Programming', '528', '2025-06-03', '00:28:00', '7', 'approved', 'Approved', 1, '2025-05-08 00:24:31'),
(29, 40, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'Web Design & Development', '544', '2025-05-04', '04:42:00', '18', 'approved', 'Approved', 1, '2025-05-08 00:46:12'),
(30, 41, '78888888', 'ddddddd, dddd ddd', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-21', '04:55:00', '12', 'approved', 'Approved', 1, '2025-05-08 01:14:56'),
(31, 42, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Java Programming', '530', '2025-05-04', '01:24:00', '1', 'approved', 'Approved', 1, '2025-05-08 01:19:18'),
(32, 43, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Embedded System & IOT', '542', '2025-05-18', '01:30:00', '1', 'approved', 'Approved', 1, '2025-05-08 01:25:44'),
(33, 44, '78888888', 'ddddddd, dddd ddd', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-22', '01:44:00', '1', 'approved', 'Approved', 1, '2025-05-08 01:41:11'),
(34, 45, '88888888', 'dwadwa, dwadwad wadawd', 'BSCS', '3rd Year', 'System Integration & Architecture', '530', '2025-05-19', '02:05:00', '2', 'approved', 'Approved', 1, '2025-05-08 02:01:51'),
(35, 46, '44444444', 'sasafaff, ffff ffff', 'BSIS', '1st Year', 'System Integration & Architecture', '524', '2025-05-19', '02:07:00', '7', 'approved', 'Approved', 1, '2025-05-08 02:05:16'),
(36, 47, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'Project Management', '524', '2025-05-27', '06:15:00', '12', 'approved', 'Approved', 1, '2025-05-08 02:11:37'),
(37, 50, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-05-20', '08:01:00', '2', 'approved', 'Approved', 1, '2025-05-08 08:58:01'),
(38, 51, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-06-05', '09:45:00', '6', 'approved', 'Approved', 1, '2025-05-08 09:43:11'),
(39, 53, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '526', '2025-05-22', '10:10:00', '12', 'disapproved', 'Disapproved', 1, '2025-05-13 20:05:52'),
(40, 54, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Embedded System & IOT', '528', '2025-05-22', '02:22:00', '17', 'disapproved', 'Disapproved', 1, '2025-05-14 09:47:45'),
(41, 54, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Embedded System & IOT', '528', '2025-05-22', '02:22:00', '17', 'disapproved', 'Disapproved', 1, '2025-05-14 09:47:45'),
(42, 54, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Embedded System & IOT', '528', '2025-05-22', '02:22:00', '17', 'disapproved', 'Disapproved', 1, '2025-05-14 09:47:49'),
(43, 54, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Embedded System & IOT', '528', '2025-05-22', '02:22:00', '17', 'approved', 'Approved', 1, '2025-05-14 09:47:56'),
(44, 63, '22222222', 'Test, Test Test', 'BSCS', '1', 'Embedded System & IOT', '526', '2025-05-22', '09:49:00', '12', 'approved', 'Approved', 1, '2025-05-14 10:03:27'),
(45, 55, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '8', 'disapproved', 'Disapproved', 1, '2025-05-14 10:13:21'),
(46, 56, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '6', 'disapproved', 'Disapproved', 1, '2025-05-14 10:13:29'),
(47, 57, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '6', 'disapproved', 'Disapproved', 1, '2025-05-14 10:13:40'),
(48, 58, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '6', 'disapproved', 'Disapproved', 1, '2025-05-14 10:13:43'),
(49, 59, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '18', 'disapproved', 'Disapproved', 1, '2025-05-14 10:13:46'),
(50, 60, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '18', 'disapproved', 'Disapproved', 1, '2025-05-14 10:13:50'),
(51, 61, '22222222', 'Test, Test Test', 'BSCS', '1', 'Java Programming', '524', '2025-05-21', '02:16:00', '18', 'disapproved', 'Disapproved', 1, '2025-05-14 10:13:53'),
(52, 62, '22222222', 'Test, Test Test', 'BSCS', '1', 'Web Design & Development', '530', '2025-05-30', '09:43:00', '11', 'disapproved', 'Disapproved', 1, '2025-05-14 10:13:56'),
(56, 76, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'BSCS', '2nd Year', 'Embedded System & IOT', '530', '2025-05-18', '01:08:00', '16', 'disapproved', 'Disapproved', 1, '2025-05-15 01:09:18'),
(74, 95, '22222227', 'Palacio, Real Jhon Samson', 'BSIT', '3rd Year', 'Web Design & Development', '542', '2025-05-29', '10:36:00', '6', 'approved', 'Approved', 1, '2025-05-15 10:34:08');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `professor` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `resource_link` varchar(512) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `title`, `professor`, `description`, `resource_link`, `cover_image`, `created_at`, `added_by`) VALUES
(1, 'White Rabbit', 'Rabbi', 'wadwd', 'https://www.roblox.com/home', 'uploads/681bac369e8dc_rabbi.jpg', '2025-05-07 18:53:42', 1),
(2, 'Database', 'Beverly Lahaylahay', 'A database is an organized collection of structured information, or data, typically stored electronically in a computer system. ', 'https://www.oracle.com/ph/database/what-is-database/', '/uploads/resources/1747272072_dbd.jpg', '2025-05-07 19:08:53', 1),
(4, 'SYSARCH', 'Jeff Salimbangon', 'This examines the strategies, and methods for blending a set of interdependent systems into a functioning and unified whole thereby enabling interaction and exchange of data seamlessly. ', 'https://www.interviewbit.com/blog/system-architecture/', '/uploads/resources/1747272286_awa.png', '2025-05-15 01:24:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sit_in`
--

CREATE TABLE `sit_in` (
  `id` int(11) NOT NULL,
  `idno` varchar(20) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `purpose` varchar(50) NOT NULL,
  `laboratory` varchar(10) NOT NULL,
  `pc_number` varchar(10) DEFAULT NULL,
  `time_in` time DEFAULT curtime(),
  `time_out` time DEFAULT NULL,
  `date` date DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `session_count` int(11) DEFAULT 30,
  `source` enum('direct','reservation') DEFAULT 'direct',
  `reservation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit_in`
--

INSERT INTO `sit_in` (`id`, `idno`, `fullname`, `purpose`, `laboratory`, `pc_number`, `time_in`, `time_out`, `date`, `created_at`, `status`, `session_count`, `source`, `reservation_id`) VALUES
(35, '22682504', 'Palacio, Real Jhon', 'C Programming', '526', NULL, '19:27:32', '19:28:02', '2025-03-27', '2025-03-19 05:36:29', 'active', 29, 'direct', NULL),
(36, '00000000', 'Palacio, Real Jhon', 'Java Programming', '526', NULL, '10:05:03', '19:45:40', '2025-03-27', '2025-03-19 05:42:02', 'active', 29, 'direct', NULL),
(37, '45645645', 'wsdsf, sdfsdfds', 'C# Programming', '524', NULL, '07:55:52', '08:56:33', '2025-03-20', '2025-03-19 06:00:50', 'active', 30, 'direct', NULL),
(38, '20201234', 'Atabay, Easun Jane', 'C# Programming', '528', NULL, '11:29:03', '22:36:29', '2025-03-26', '2025-03-19 23:38:52', 'active', 29, 'direct', NULL),
(39, '12413245', 'eqew, wewe', 'C# Programming', '524', NULL, '07:41:58', '07:42:37', '2025-03-20', '2025-03-19 23:41:58', 'active', 30, 'direct', NULL),
(40, '12345678', 'Test, Test', 'C# Programming', '526', NULL, '11:35:43', '09:08:49', '2025-03-26', '2025-03-26 03:35:43', 'active', 30, 'direct', NULL),
(50, '22222222', 'Test, Test', 'C Programming', '524', NULL, '09:45:22', '09:45:30', '2025-03-27', '2025-03-27 01:45:22', 'active', 30, 'reservation', 49),
(52, '04555555', 'sfswfewesf, wefwfw', 'Php Programming', '542', NULL, '22:37:34', '22:37:41', '2025-04-02', '2025-04-02 14:37:34', 'active', 30, 'direct', NULL),
(69, '22222222', 'Test, Test Test', 'System Integration & Architecture', '526', NULL, '22:49:14', '22:50:00', '2025-05-07', '2025-05-07 14:49:14', 'active', 29, 'reservation', 1),
(70, '22222222', 'Test, Test Test', 'System Integration & Architecture', '526', NULL, '22:51:18', '22:52:06', '2025-05-07', '2025-05-07 14:51:18', 'active', 29, 'reservation', 1),
(71, '22222222', 'Test, Test Test', 'Embedded System & IOT', '528', NULL, '22:53:04', '22:53:20', '2025-05-07', '2025-05-07 14:53:04', 'active', 28, 'reservation', 6),
(72, '22222222', 'Test, Test Test', 'Computer Application', '526', NULL, '22:54:24', '23:03:50', '2025-05-07', '2025-05-07 14:54:24', 'active', 28, 'reservation', 3),
(73, '45645645', 'awdwad, awdawdawd wadwad', 'Web Design & Development', '526', NULL, '09:30:31', NULL, '2025-05-15', '2025-05-07 14:56:06', 'active', 28, 'direct', 25),
(74, '22222222', 'Test, Test Test', 'System Integration & Architecture', '528', NULL, '23:04:52', '23:05:06', '2025-05-07', '2025-05-07 15:04:52', 'active', 27, 'reservation', 1),
(75, '22222222', 'Test, Test Test', 'Web Design & Development', '524', NULL, '23:09:10', '00:08:49', '2025-05-07', '2025-05-07 15:09:10', 'active', 28, 'reservation', 7),
(76, '55555555', 'dawdawd, wadawd awdawdwa', 'Project Management', '528', NULL, '23:17:35', '23:18:51', '2025-05-07', '2025-05-07 15:17:35', 'active', 30, 'reservation', 33),
(77, '55555555', 'dawdawd, wadawd awdawdwa', 'Computer Application', '528', NULL, '23:22:30', '23:40:00', '2025-05-07', '2025-05-07 15:22:30', 'active', 29, 'reservation', 34),
(78, '55555555', 'dawdawd, wadawd awdawdwa', 'Digital Logic & Design', '524', NULL, '23:40:42', '01:18:39', '2025-05-07', '2025-05-07 15:40:42', 'active', 28, 'reservation', 36),
(79, '78888888', 'ddddddd, dddd', 'Java Programming', '526', NULL, '23:45:11', '23:45:28', '2025-05-07', '2025-05-07 15:45:11', 'active', 29, 'direct', NULL),
(81, '22222222', 'Test, Test Test', 'Computer Application', '524', NULL, '00:13:13', '01:18:27', '2025-05-08', '2025-05-07 16:13:13', 'active', 28, 'reservation', 3),
(82, '88888888', 'dwadwa, dwadwad wadawd', 'C# Programming', '528', NULL, '00:24:31', '01:18:35', '2025-05-08', '2025-05-07 16:24:31', 'active', 29, 'reservation', 39),
(83, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'C# Programming', '528', NULL, '09:37:30', NULL, '2025-05-15', '2025-05-07 16:46:12', 'active', 29, 'direct', 40),
(84, '78888888', 'ddddddd, dddd ddd', 'System Integration & Architecture', '526', NULL, '01:14:56', '01:18:29', '2025-05-08', '2025-05-07 17:14:56', 'active', 28, 'reservation', 41),
(85, '22222222', 'Test, Test Test', 'Java Programming', '530', '1', '01:19:18', '08:34:10', '2025-05-08', '2025-05-07 17:19:18', 'active', 29, 'reservation', 42),
(86, '55555555', 'dawdawd, wadawd awdawdwa', 'System Integration & Architecture', '526', '1', '14:02:26', '22:55:43', '2025-05-14', '2025-05-07 17:25:44', 'active', 27, 'direct', NULL),
(87, '78888888', 'ddddddd, dddd ddd', 'System Integration & Architecture', '526', '1', '01:41:11', '01:41:47', '2025-05-08', '2025-05-07 17:41:11', 'active', 27, 'reservation', 41),
(88, '88888888', 'dwadwa, dwadwad wadawd', 'System Integration & Architecture', '530', '2', '02:01:51', '08:34:00', '2025-05-08', '2025-05-07 18:01:51', 'active', 29, 'reservation', 45),
(89, '44444444', 'sasafaff, ffff ffff', 'System Integration & Architecture', '524', '7', '02:05:16', '17:37:26', '2025-05-08', '2025-05-07 18:05:16', 'active', 30, 'reservation', 46),
(91, '33333333', 'llllll, llll', 'Java Programming', '526', NULL, '02:24:08', '02:24:16', '2025-05-08', '2025-05-07 18:24:08', 'active', 29, 'direct', NULL),
(92, '22222222', 'Test, Test Test', 'System Integration & Architecture', '526', '2', '08:58:01', '09:42:32', '2025-05-08', '2025-05-08 00:58:01', 'active', 28, 'reservation', 1),
(93, '22222222', 'Test, Test Test', 'System Integration & Architecture', '530', '6', '19:04:45', '17:36:38', '2025-05-13', '2025-05-08 01:43:11', 'active', 28, 'reservation', 1),
(98, '22222222', 'Test, Test Test', 'System Integration & Architecture', '544', '7', '13:02:13', '14:03:08', '2025-05-14', '2025-05-14 05:02:13', 'active', 29, 'reservation', 1),
(99, '23242424', 'Atabay, Easun', 'Computer Application', '528', NULL, '10:36:10', NULL, '2025-05-15', '2025-05-14 05:06:54', 'active', 27, 'direct', NULL),
(100, '44444444', 'sasafaff, ffff ffff', 'Digital Logic & Design', '530', '28', '09:15:28', NULL, '2025-05-15', '2025-05-14 06:05:15', 'active', 28, 'direct', NULL),
(101, '22222222', 'Test, Test Test', 'Web Design & Development', '530', '11', '23:01:22', '23:01:36', '2025-05-14', '2025-05-14 15:01:22', 'active', 28, 'reservation', 7),
(102, '22222222', 'Test, Test Test', 'Computer Application', '530', '6', '23:02:41', '23:09:06', '2025-05-14', '2025-05-14 15:02:41', 'active', 27, 'reservation', 3),
(103, '22222222', 'Test, Test Test', 'Computer Application', '530', '6', '23:09:56', '23:11:57', '2025-05-14', '2025-05-14 15:09:56', 'active', 26, 'reservation', 3),
(104, '22222222', 'Test, Test Test', 'Computer Application', '530', '11', '09:16:15', NULL, '2025-05-15', '2025-05-14 15:16:32', 'active', 26, 'direct', 70),
(105, '55555555', 'dawdawd, wadawd awdawdwa', 'Computer Application', '517', '11', '09:34:46', NULL, '2025-05-15', '2025-05-14 15:22:13', 'active', 26, 'direct', 71),
(106, '99999999', 'dfsdsfsdf, sdfsdfds', 'Computer Application', '526', NULL, '09:15:09', NULL, '2025-05-15', '2025-05-14 15:30:54', 'active', 29, 'direct', NULL),
(107, '88888888', 'dwadwa, dwadwad wadawd', 'System Integration & Architecture', '526', '16', '09:34:09', '09:34:36', '2025-05-15', '2025-05-14 16:36:39', 'active', 27, 'direct', 73),
(110, '22222227', 'Palacio, Real Jhon Samson', 'Embedded System & IOT', '530', '14', '10:21:46', '10:23:25', '2025-05-15', '2025-05-15 02:21:46', 'active', 29, 'reservation', 94),
(111, '22222227', 'Palacio, Real Jhon Samson', 'Web Design & Development', '542', '6', '10:34:08', '10:35:03', '2025-05-15', '2025-05-15 02:34:08', 'active', 28, 'reservation', 95);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `year_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `id_no` varchar(50) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `id_no`, `last_name`, `first_name`, `middle_name`, `course`, `year_level`, `email`, `address`, `username`, `password`, `created_at`, `profile_picture`, `points`) VALUES
(30, '22222222', 'Test', 'Test', 'Test', 'BSCS', '1st Year', '1@gmail.com', 'America', '12', '$2y$10$x8yz.Y7Uw2NSuTTTtJgrJ.beXNX2FbDE2vawFeB4n60ilJ37IL5n2', '2025-03-27 01:44:31', 'uploads/wqwe.png', 0),
(32, '23242424', 'Atabay', 'Easun', 'Javinez', 'BSIT', '4th Year', 'easunjanea@gmail.com', 'Ramos', 'Easun', '$2y$10$s44ECIaUBUqWrdr/6ItYB.73DjpRZj9qCXjlcGGgdf93LCFnlglCK', '2025-03-27 02:29:43', NULL, 0),
(33, '12121212', 'Catubig', 'Mark', 'Cawater', 'BSIT', '3rd Year', '12@gmail.com', 'America', 'Water', '$2y$10$qji7Oclp3ahT360L/eyA9OrjnIlbYJ.7zVs2QFKl3WRlN0YbQMU9a', '2025-03-27 02:37:25', NULL, 0),
(35, '45645645', 'awdwad', 'awdawdawd', 'wadwad', 'BSIT', '2nd Year', 'prealjhon@gmail.com', 'dwadwa', 'ee', '$2y$10$ge5qRnMtnecRqR2oy2fna.ZOegi1GvwPXf9/7OJuzCUGz.G.FAn8.', '2025-03-27 14:43:05', NULL, 0),
(125, '99999999', 'dfsdsfsdf', 'sdfsdfds', '', 'BSCS', '2nd Year', 'awdawdwa@gmail.com', 'Ramos', 'gg', '$2y$10$d0rnCP5kFd6Ftpz6Qpv0eOt5JUN1CVHBIQb5DXogquHS7QLieR.t6', '2025-03-27 15:19:26', NULL, 0),
(229, '88888888', 'dwadwa', 'dwadwad', 'wadawd', 'BSCS', '3rd Year', 'dwadwadawd@gmail.com', 'UC main', 'kk', '$2y$10$zh4ModrHXRfYO.fXjoKuPeK7j0Mm/KsoM/3VVpzVBA9Lva8QiySI.', '2025-03-27 16:19:25', NULL, 0),
(230, '55555555', 'dawdawd', 'wadawd', 'awdawdwa', 'BSCS', '4th Year', 'wadafaff@gmail.com', 'awdawd', 'dd', '$2y$10$4cSGY6iWiMzLLlF.oLFHdO0mtbLNT6enqwM1fSO2rZM0WEre2QpNK', '2025-03-28 00:50:59', NULL, 2),
(231, '44444444', 'sasafaff', 'ffff ffff', 'Wayne', 'BSIS', '1st Year', 'fffff@gmail.com', 'ffff', 'ff', '$2y$10$NLCtz/EuQ5mohFynS6LmSuefMR.ugtTWScHOynMMPNyb/lawxaoJG', '2025-03-28 15:00:58', NULL, 0),
(233, '78888888', 'ddddddd', 'dddd', 'ddd', 'BSCS', '3rd Year', 'ddd@gmail.com', 'dd', 'bb', '$2y$10$fSSyL0kbNQMI/aPBcGMCtuocFNXOK9OMGeqNanSLxP43Pv9qL6YQC', '2025-03-28 15:08:32', NULL, 0),
(234, '77777777', 'sdfsdd', 'sdfsfsd', 'sdfsdfs', 'BSCS', '2nd Year', 'sdfsdfs@gmail.com', 'adsdsads', 'nn', '$2y$10$.dmb45eZbQk1/gjuCClKeOWVX1lusk.Ra7fiw2AZxUTsjUP106Qai', '2025-04-02 13:45:16', NULL, 0),
(235, '04555555', 'sfswfewesf', 'wefwfw', 'fwewfw', 'BSIS', '2nd Year', 'sdfsfsdfsd@gmail.com', 'sadasf', '567', '$2y$10$H8SlGu8H3pMch4mqYaHpW.9gcGK603LstaI6xR3DDqb/omgq.sa8a', '2025-04-02 13:45:47', NULL, 0),
(238, '22222227', 'Palacio', 'Real Jhon', 'Samson', 'BSIT', '3rd Year', 'realjhonpalacio@gmail.com', 'Cebu City', 'real', '$2y$10$ZJINN6XE.ilBFTBiorHLMutrGNqiv8FOFV2KDZYJMQhXgWMnPPXVa', '2025-05-15 02:20:56', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_read` (`user_id`,`announcement_id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sit_in_id` (`sit_in_id`);

--
-- Indexes for table `lab_schedule`
--
ALTER TABLE `lab_schedule`
  ADD PRIMARY KEY (`SCHED_ID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`NOTIF_ID`);

--
-- Indexes for table `pc_status`
--
ALTER TABLE `pc_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_pc` (`laboratory`,`pc_number`);

--
-- Indexes for table `points_history`
--
ALTER TABLE `points_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservation_logs`
--
ALTER TABLE `reservation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `action_by` (`action_by`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `sit_in`
--
ALTER TABLE `sit_in`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_active_session` (`idno`,`time_out`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_no` (`id_no`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lab_schedule`
--
ALTER TABLE `lab_schedule`
  MODIFY `SCHED_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `NOTIF_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `pc_status`
--
ALTER TABLE `pc_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `points_history`
--
ALTER TABLE `points_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `reservation_logs`
--
ALTER TABLE `reservation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sit_in`
--
ALTER TABLE `sit_in`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=239;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD CONSTRAINT `announcement_reads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `announcement_reads_ibfk_2` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`sit_in_id`) REFERENCES `sit_in` (`id`);

--
-- Constraints for table `points_history`
--
ALTER TABLE `points_history`
  ADD CONSTRAINT `points_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reservation_logs`
--
ALTER TABLE `reservation_logs`
  ADD CONSTRAINT `reservation_logs_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`),
  ADD CONSTRAINT `reservation_logs_ibfk_2` FOREIGN KEY (`action_by`) REFERENCES `admin` (`id`);

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `admin` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
