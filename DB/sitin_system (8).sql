-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 04:25 AM
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
(32, 'admin', 'Welcome!', '2025-05-08 00:45:17');

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
(54, 230, 32, '2025-05-08 02:17:12');

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
(53, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'Computer Application', '526', '2025-05-22', '10:10:00', '12', 'pending', '2025-05-08 10:06:09'),
(54, '55555555', 'dawdawd, wadawd awdawdwa', 'BSCS', '4th Year', 'Embedded System & IOT', '528', '2025-05-22', '02:22:00', '17', 'pending', '2025-05-08 10:17:38');

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
(38, 51, '22222222', 'Test, Test Test', 'BSCS', '3rd Year', 'System Integration & Architecture', '526', '2025-06-05', '09:45:00', '6', 'approved', 'Approved', 1, '2025-05-08 09:43:11');

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
(2, 'Craftopia', 'Real', 'dwadaw', 'https://elvebredd.com/', 'uploads/681bafc5c3556_Firefly Modern, minimalist logo for Craftopia, an eco-friendly AI-powered craft app. Use soft green .jpg', '2025-05-07 19:08:53', 1);

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
  `session_count` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit_in`
--

INSERT INTO `sit_in` (`id`, `idno`, `fullname`, `purpose`, `laboratory`, `pc_number`, `time_in`, `time_out`, `date`, `created_at`, `status`, `session_count`) VALUES
(35, '22682504', 'Palacio, Real Jhon', 'C Programming', '526', NULL, '19:27:32', '19:28:02', '2025-03-27', '2025-03-19 05:36:29', 'active', 29),
(36, '00000000', 'Palacio, Real Jhon', 'Java Programming', '526', NULL, '10:05:03', '19:45:40', '2025-03-27', '2025-03-19 05:42:02', 'active', 29),
(37, '45645645', 'wsdsf, sdfsdfds', 'C# Programming', '524', NULL, '07:55:52', '08:56:33', '2025-03-20', '2025-03-19 06:00:50', 'active', 30),
(38, '20201234', 'Atabay, Easun Jane', 'C# Programming', '528', NULL, '11:29:03', '22:36:29', '2025-03-26', '2025-03-19 23:38:52', 'active', 29),
(39, '12413245', 'eqew, wewe', 'C# Programming', '524', NULL, '07:41:58', '07:42:37', '2025-03-20', '2025-03-19 23:41:58', 'active', 30),
(40, '12345678', 'Test, Test', 'C# Programming', '526', NULL, '11:35:43', '09:08:49', '2025-03-26', '2025-03-26 03:35:43', 'active', 30),
(50, '22222222', 'Test, Test', 'C Programming', '524', NULL, '09:45:22', '09:45:30', '2025-03-27', '2025-03-27 01:45:22', 'active', 30),
(51, '23242424', 'Atabay, Easun', 'C# Programming', '526', NULL, '10:30:00', '10:30:07', '2025-03-27', '2025-03-27 02:30:00', 'active', 30),
(52, '04555555', 'sfswfewesf, wefwfw', 'Php Programming', '542', NULL, '22:37:34', '22:37:41', '2025-04-02', '2025-04-02 14:37:34', 'active', 29),
(69, '22222222', 'Test, Test Test', 'System Integration & Architecture', '526', NULL, '22:49:14', '22:50:00', '2025-05-07', '2025-05-07 14:49:14', 'active', 29),
(70, '22222222', 'Test, Test Test', 'System Integration & Architecture', '526', NULL, '22:51:18', '22:52:06', '2025-05-07', '2025-05-07 14:51:18', 'active', 29),
(71, '22222222', 'Test, Test Test', 'Embedded System & IOT', '528', NULL, '22:53:04', '22:53:20', '2025-05-07', '2025-05-07 14:53:04', 'active', 28),
(72, '22222222', 'Test, Test Test', 'Computer Application', '526', NULL, '22:54:24', '23:03:50', '2025-05-07', '2025-05-07 14:54:24', 'active', 28),
(73, '45645645', 'awdwad, awdawdawd wadwad', 'Computer Application', '526', NULL, '22:56:06', '22:58:44', '2025-05-07', '2025-05-07 14:56:06', 'active', 29),
(74, '22222222', 'Test, Test Test', 'System Integration & Architecture', '528', NULL, '23:04:52', '23:05:06', '2025-05-07', '2025-05-07 15:04:52', 'active', 27),
(75, '22222222', 'Test, Test Test', 'Web Design & Development', '524', NULL, '23:09:10', '00:08:49', '2025-05-07', '2025-05-07 15:09:10', 'active', 28),
(76, '55555555', 'dawdawd, wadawd awdawdwa', 'Project Management', '528', NULL, '23:17:35', '23:18:51', '2025-05-07', '2025-05-07 15:17:35', 'active', 30),
(77, '55555555', 'dawdawd, wadawd awdawdwa', 'Computer Application', '528', NULL, '23:22:30', '23:40:00', '2025-05-07', '2025-05-07 15:22:30', 'active', 29),
(78, '55555555', 'dawdawd, wadawd awdawdwa', 'Digital Logic & Design', '524', NULL, '23:40:42', '01:18:39', '2025-05-07', '2025-05-07 15:40:42', 'active', 28),
(79, '78888888', 'ddddddd, dddd', 'Java Programming', '526', NULL, '23:45:11', '23:45:28', '2025-05-07', '2025-05-07 15:45:11', 'active', 29),
(80, '99999999', 'dfsdsfsdf, sdfsdfds dsfsdf', 'System Integration & Architecture', '526', NULL, '23:46:55', '00:23:45', '2025-05-07', '2025-05-07 15:46:55', 'active', 29),
(81, '22222222', 'Test, Test Test', 'Computer Application', '524', NULL, '00:13:13', '01:18:27', '2025-05-08', '2025-05-07 16:13:13', 'active', 28),
(82, '88888888', 'dwadwa, dwadwad wadawd', 'C# Programming', '528', NULL, '00:24:31', '01:18:35', '2025-05-08', '2025-05-07 16:24:31', 'active', 29),
(83, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'Web Design & Development', '544', NULL, '00:46:12', '01:18:32', '2025-05-08', '2025-05-07 16:46:12', 'active', 29),
(84, '78888888', 'ddddddd, dddd ddd', 'System Integration & Architecture', '526', NULL, '01:14:56', '01:18:29', '2025-05-08', '2025-05-07 17:14:56', 'active', 28),
(85, '22222222', 'Test, Test Test', 'Java Programming', '530', '1', '01:19:18', '08:34:10', '2025-05-08', '2025-05-07 17:19:18', 'active', 29),
(86, '55555555', 'dawdawd, wadawd awdawdwa', 'Embedded System & IOT', '542', '1', '01:25:44', '01:31:13', '2025-05-08', '2025-05-07 17:25:44', 'active', 27),
(87, '78888888', 'ddddddd, dddd ddd', 'System Integration & Architecture', '526', '1', '01:41:11', '01:41:47', '2025-05-08', '2025-05-07 17:41:11', 'active', 27),
(88, '88888888', 'dwadwa, dwadwad wadawd', 'System Integration & Architecture', '530', '2', '02:01:51', '08:34:00', '2025-05-08', '2025-05-07 18:01:51', 'active', 29),
(89, '44444444', 'sasafaff, ffff ffff', 'System Integration & Architecture', '524', '7', '02:05:16', NULL, '2025-05-08', '2025-05-07 18:05:16', 'active', 30),
(90, '77777777', 'sdfsdd, sdfsfsd sdfsdfs', 'Project Management', '524', '12', '02:11:37', NULL, '2025-05-08', '2025-05-07 18:11:37', 'active', 30),
(91, '33333333', 'llllll, llll', 'Java Programming', '526', NULL, '02:24:08', '02:24:16', '2025-05-08', '2025-05-07 18:24:08', 'active', 29),
(92, '22222222', 'Test, Test Test', 'System Integration & Architecture', '526', '2', '08:58:01', '09:42:32', '2025-05-08', '2025-05-08 00:58:01', 'active', 28),
(93, '22222222', 'Test, Test Test', 'System Integration & Architecture', '526', '6', '09:43:11', '09:53:30', '2025-05-08', '2025-05-08 01:43:11', 'active', 27);

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
(30, '22222222', 'Test', 'Test', 'Test', 'BSCS', '3rd Year', '1@gmail.com', 'America', '12', '$2y$10$x8yz.Y7Uw2NSuTTTtJgrJ.beXNX2FbDE2vawFeB4n60ilJ37IL5n2', '2025-03-27 01:44:31', NULL, 0),
(32, '23242424', 'Atabay', 'Easun', 'Javinez', 'BSIT', '4th Year', 'easunjanea@gmail.com', 'Ramos', 'Easun', '$2y$10$s44ECIaUBUqWrdr/6ItYB.73DjpRZj9qCXjlcGGgdf93LCFnlglCK', '2025-03-27 02:29:43', NULL, 0),
(33, '12121212', 'Catubig', 'Mark', 'Cawater', 'BSIT', '3rd Year', '12@gmail.com', 'America', 'Water', '$2y$10$qji7Oclp3ahT360L/eyA9OrjnIlbYJ.7zVs2QFKl3WRlN0YbQMU9a', '2025-03-27 02:37:25', NULL, 0),
(35, '45645645', 'awdwad', 'awdawdawd', 'wadwad', 'BSIT', '2nd Year', 'prealjhon@gmail.com', 'dwadwa', 'ee', '$2y$10$ge5qRnMtnecRqR2oy2fna.ZOegi1GvwPXf9/7OJuzCUGz.G.FAn8.', '2025-03-27 14:43:05', NULL, 0),
(125, '99999999', 'dfsdsfsdf', 'sdfsdfds', 'dsfsdf', 'BSCS', '2nd Year', 'awdawdwa@gmail.com', 'Ramos', 'gg', '$2y$10$d0rnCP5kFd6Ftpz6Qpv0eOt5JUN1CVHBIQb5DXogquHS7QLieR.t6', '2025-03-27 15:19:26', NULL, 0),
(229, '88888888', 'dwadwa', 'dwadwad', 'wadawd', 'BSCS', '3rd Year', 'dwadwadawd@gmail.com', 'UC main', 'kk', '$2y$10$zh4ModrHXRfYO.fXjoKuPeK7j0Mm/KsoM/3VVpzVBA9Lva8QiySI.', '2025-03-27 16:19:25', NULL, 0),
(230, '55555555', 'dawdawd', 'wadawd', 'awdawdwa', 'BSCS', '4th Year', 'wadafaff@gmail.com', 'awdawd', 'dd', '$2y$10$4cSGY6iWiMzLLlF.oLFHdO0mtbLNT6enqwM1fSO2rZM0WEre2QpNK', '2025-03-28 00:50:59', NULL, 2),
(231, '44444444', 'sasafaff', 'ffff', 'ffff', 'BSIS', '1st Year', 'fffff@gmail.com', 'ffff', 'ff', '$2y$10$NLCtz/EuQ5mohFynS6LmSuefMR.ugtTWScHOynMMPNyb/lawxaoJG', '2025-03-28 15:00:58', NULL, 0),
(232, '33333333', 'llllll', 'llll', 'lll', 'BSCS', '3rd Year', 'llll@gmail.com', 'lll', 'll', '$2y$10$UaxIp1bdIsrYLmQ7h4j3WOHOARDvw.2L87JsRER7h3/XRgIkDE6me', '2025-03-28 15:06:08', NULL, 0),
(233, '78888888', 'ddddddd', 'dddd', 'ddd', 'BSCS', '3rd Year', 'ddd@gmail.com', 'dd', 'bb', '$2y$10$fSSyL0kbNQMI/aPBcGMCtuocFNXOK9OMGeqNanSLxP43Pv9qL6YQC', '2025-03-28 15:08:32', NULL, 0),
(234, '77777777', 'sdfsdd', 'sdfsfsd', 'sdfsdfs', 'BSCS', '2nd Year', 'sdfsdfs@gmail.com', 'adsdsads', 'nn', '$2y$10$.dmb45eZbQk1/gjuCClKeOWVX1lusk.Ra7fiw2AZxUTsjUP106Qai', '2025-04-02 13:45:16', NULL, 0),
(235, '04555555', 'sfswfewesf', 'wefwfw', 'fwewfw', 'BSIS', '2nd Year', 'sdfsfsdfsd@gmail.com', 'sadasf', '567', '$2y$10$H8SlGu8H3pMch4mqYaHpW.9gcGK603LstaI6xR3DDqb/omgq.sa8a', '2025-04-02 13:45:47', NULL, 0);

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
  ADD UNIQUE KEY `unique_active_session` (`idno`,`time_out`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `reservation_logs`
--
ALTER TABLE `reservation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sit_in`
--
ALTER TABLE `sit_in`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

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
