-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 01:17 AM
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
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(24, ' ', 'Klase na!', '2025-03-20 01:17:36');

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
(1, 35, 5, 'nindot', '2025-03-26 05:07:14');

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

INSERT INTO `sit_in` (`id`, `idno`, `fullname`, `purpose`, `laboratory`, `time_in`, `time_out`, `date`, `created_at`, `status`, `session_count`) VALUES
(35, '22682504', 'Palacio, Real Jhon', 'C# Programming', '526', '10:27:45', NULL, '2025-03-26', '2025-03-19 05:36:29', 'active', 30),
(36, '00000000', 'Palacio, Real Jhon', 'Java Programming', '524', '13:42:02', '07:45:15', '2025-03-19', '2025-03-19 05:42:02', 'active', 30),
(37, '45645645', 'wsdsf, sdfsdfds', 'C# Programming', '524', '07:55:52', '08:56:33', '2025-03-20', '2025-03-19 06:00:50', 'active', 30),
(38, '20201234', 'Atabay, Easun Jane', 'C# Programming', '528', '11:29:03', NULL, '2025-03-26', '2025-03-19 23:38:52', 'active', 30),
(39, '12413245', 'eqew, wewe', 'C# Programming', '524', '07:41:58', '07:42:37', '2025-03-20', '2025-03-19 23:41:58', 'active', 30),
(40, '12345678', 'Test, Test', 'C# Programming', '526', '11:35:43', NULL, '2025-03-26', '2025-03-26 03:35:43', 'active', 30),
(41, '11111111', '', '', '', '07:18:27', NULL, '2025-03-27', '2025-03-26 23:18:27', 'active', 30),
(42, '00000000', '', '', '', '07:24:16', NULL, '2025-03-27', '2025-03-26 23:24:16', 'active', 30),
(43, '22222222', '', '', '', '07:30:34', NULL, '2025-03-27', '2025-03-26 23:30:34', 'active', 30),
(44, '22222222', '', '', '', '07:36:46', NULL, '2025-03-27', '2025-03-26 23:36:46', 'active', 30),
(45, '092934578', '', '', '', '07:41:39', NULL, '2025-03-27', '2025-03-26 23:41:39', 'active', 30),
(46, '22222222', '', '', '', '07:49:00', NULL, '2025-03-27', '2025-03-26 23:49:00', 'active', 30),
(47, '11111111', '', '', '', '07:52:06', NULL, '2025-03-27', '2025-03-26 23:52:06', 'active', 30);

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
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `id_no`, `last_name`, `first_name`, `middle_name`, `course`, `year_level`, `email`, `address`, `username`, `password`, `created_at`, `profile_picture`) VALUES
(2, '22682504', 'Palacio', 'Real Jhon', 'Samson', 'BSIT', '3rd Year', 'realjhonpalacio@gmail.com', 'Nipa', '00000', '$2y$10$4ugo.Uq4cio1vHebhZNGse7n4TBAwl1ko/s.Ik9JjJRpMaOLF62YC', '2025-02-18 16:32:01', NULL),
(12, '20201234', 'Atabay', 'Easun Jane', 'Javinez', 'BSIT', '4th Year', 'prealjhon@gmail.com', 'Nipa', 'easun', '$2y$10$628SCo3aPE68t1XVB/cAY.YtNn3LKU5B6SIBLNv8zuW4Cs4PwTT4K', '2025-02-18 16:52:24', NULL),
(17, '12413245', 'eqew', 'wewe', 'wewew', 'BSIT', '2nd Year', 'wewewewe@gmail.com', 'ewrwed', 'ww', '$2y$10$rGzHSQhzYPZCSVW7sFbQKenk0uVJhhLc1qe3mUnWL.dDnl.1MllvS', '2025-03-07 04:40:14', NULL),
(18, '45645645', 'wsdsf', 'sdfsdfds', 'fdsfs', 'BSCS', '1st Year', 'dsfdsfsd@gmail.com', 'fsdfsfsdf', 'zz', '$2y$10$5EBS1AHSDJWlVoTE4nZGQOZXVkmjIXoJpKWgI7kifTafFfJja8c4O', '2025-03-07 05:05:55', NULL),
(19, '12345678', 'Test', 'Test', 'Test', 'BSIT', '4th Year', 'Test@gmail.com', 'Test', 'Test', '$2y$10$GGUHJJi3oBj2.UycmmF4D.wS.ftF/2GhtQIqpS7dYaQMoWavrjbJS', '2025-03-26 03:35:04', NULL),
(24, '092934578', 'Test3', 'Test3', 'Test3', 'BSIT', '3rd Year', 'Test12@gmail.com', 'Nipa', 'Test3', '$2y$10$zWTftO.XlG1MwiSi7Cjx1OcPVartzXaylzRRQsHy358NWnylQ21HC', '2025-03-26 23:41:39', NULL),
(25, '22222222', 'Palacio', 'palacio', 'palacio', 'BSIT', '1st Year', '2@gmail.com', 'Masbate', 'palacio', '$2y$10$sNd0GoMoyhKxcNULzUkcceZ1T3gMqtMWAePdFtUiWfv4Wx32eQEC6', '2025-03-26 23:49:00', NULL),
(26, '11111111', 'Atabay', 'atabay', 'atabay', 'BSCS', '2nd Year', 'atabay@gmail.com', 'atabay', 'atabay', '$2y$10$wPb8ek8ZN50lfG9XxJLL1OnmNnaykk8Ut9HDd/czcY2r87YHGSXB2', '2025-03-26 23:52:06', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sit_in_id` (`sit_in_id`);

--
-- Indexes for table `sit_in`
--
ALTER TABLE `sit_in`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sit_in`
--
ALTER TABLE `sit_in`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`sit_in_id`) REFERENCES `sit_in` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
