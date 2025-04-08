-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2025 at 03:40 AM
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
(26, 'admin', 'Klase ugma!', '2025-04-02 14:02:11');

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
(35, '22682504', 'Palacio, Real Jhon', 'C Programming', '526', '19:27:32', '19:28:02', '2025-03-27', '2025-03-19 05:36:29', 'active', 29),
(36, '00000000', 'Palacio, Real Jhon', 'Java Programming', '526', '10:05:03', NULL, '2025-03-27', '2025-03-19 05:42:02', 'active', 30),
(37, '45645645', 'wsdsf, sdfsdfds', 'C# Programming', '524', '07:55:52', '08:56:33', '2025-03-20', '2025-03-19 06:00:50', 'active', 30),
(38, '20201234', 'Atabay, Easun Jane', 'C# Programming', '528', '11:29:03', '22:36:29', '2025-03-26', '2025-03-19 23:38:52', 'active', 29),
(39, '12413245', 'eqew, wewe', 'C# Programming', '524', '07:41:58', '07:42:37', '2025-03-20', '2025-03-19 23:41:58', 'active', 30),
(40, '12345678', 'Test, Test', 'C# Programming', '526', '11:35:43', '09:08:49', '2025-03-26', '2025-03-26 03:35:43', 'active', 30),
(50, '22222222', 'Test, Test', 'C Programming', '524', '09:45:22', '09:45:30', '2025-03-27', '2025-03-27 01:45:22', 'active', 30),
(51, '23242424', 'Atabay, Easun', 'C# Programming', '526', '10:30:00', '10:30:07', '2025-03-27', '2025-03-27 02:30:00', 'active', 30),
(52, '04555555', 'sfswfewesf, wefwfw', 'Php Programming', '542', '22:37:34', '22:37:41', '2025-04-02', '2025-04-02 14:37:34', 'active', 29);

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
(30, '22222222', 'Test', 'Test', 'Test', 'BSCS', '3rd Year', '1@gmail.com', 'America', '12', '$2y$10$x8yz.Y7Uw2NSuTTTtJgrJ.beXNX2FbDE2vawFeB4n60ilJ37IL5n2', '2025-03-27 01:44:31', NULL),
(32, '23242424', 'Atabay', 'Easun', 'Javinez', 'BSIT', '4th Year', 'easunjanea@gmail.com', 'Ramos', 'Easun', '$2y$10$s44ECIaUBUqWrdr/6ItYB.73DjpRZj9qCXjlcGGgdf93LCFnlglCK', '2025-03-27 02:29:43', NULL),
(33, '12121212', 'Catubig', 'Mark', 'Cawater', 'BSIT', '3rd Year', '12@gmail.com', 'America', 'Water', '$2y$10$qji7Oclp3ahT360L/eyA9OrjnIlbYJ.7zVs2QFKl3WRlN0YbQMU9a', '2025-03-27 02:37:25', NULL),
(35, '45645645', 'awdwad', 'awdawdawd', 'wadwad', 'BSIT', '2nd Year', 'prealjhon@gmail.com', 'dwadwa', 'ee', '$2y$10$ge5qRnMtnecRqR2oy2fna.ZOegi1GvwPXf9/7OJuzCUGz.G.FAn8.', '2025-03-27 14:43:05', NULL),
(125, '99999999', 'dfsdsfsdf', 'sdfsdfds', 'dsfsdf', 'BSCS', '2nd Year', 'awdawdwa@gmail.com', 'Ramos', 'gg', '$2y$10$d0rnCP5kFd6Ftpz6Qpv0eOt5JUN1CVHBIQb5DXogquHS7QLieR.t6', '2025-03-27 15:19:26', NULL),
(229, '88888888', 'dwadwa', 'dwadwad', 'wadawd', 'BSCS', '3rd Year', 'dwadwadawd@gmail.com', 'UC main', 'kk', '$2y$10$zh4ModrHXRfYO.fXjoKuPeK7j0Mm/KsoM/3VVpzVBA9Lva8QiySI.', '2025-03-27 16:19:25', NULL),
(230, '55555555', 'dawdawd', 'wadawd', 'awdawdwa', 'BSCS', '4th Year', 'wadafaff@gmail.com', 'awdawd', 'dd', '$2y$10$4cSGY6iWiMzLLlF.oLFHdO0mtbLNT6enqwM1fSO2rZM0WEre2QpNK', '2025-03-28 00:50:59', NULL),
(231, '44444444', 'sasafaff', 'ffff', 'ffff', 'BSIS', '1st Year', 'fffff@gmail.com', 'ffff', 'ff', '$2y$10$NLCtz/EuQ5mohFynS6LmSuefMR.ugtTWScHOynMMPNyb/lawxaoJG', '2025-03-28 15:00:58', NULL),
(232, '33333333', 'llllll', 'llll', 'lll', 'BSCS', '3rd Year', 'llll@gmail.com', 'lll', 'll', '$2y$10$UaxIp1bdIsrYLmQ7h4j3WOHOARDvw.2L87JsRER7h3/XRgIkDE6me', '2025-03-28 15:06:08', NULL),
(233, '78888888', 'ddddddd', 'dddd', 'ddd', 'BSCS', '3rd Year', 'ddd@gmail.com', 'dd', 'bb', '$2y$10$fSSyL0kbNQMI/aPBcGMCtuocFNXOK9OMGeqNanSLxP43Pv9qL6YQC', '2025-03-28 15:08:32', NULL),
(234, '77777777', 'sdfsdd', 'sdfsfsd', 'sdfsdfs', 'BSCS', '2nd Year', 'sdfsdfs@gmail.com', 'adsdsads', 'nn', '$2y$10$.dmb45eZbQk1/gjuCClKeOWVX1lusk.Ra7fiw2AZxUTsjUP106Qai', '2025-04-02 13:45:16', NULL),
(235, '04555555', 'sfswfewesf', 'wefwfw', 'fwewfw', 'BSIS', '2nd Year', 'sdfsfsdfsd@gmail.com', 'sadasf', '567', '$2y$10$H8SlGu8H3pMch4mqYaHpW.9gcGK603LstaI6xR3DDqb/omgq.sa8a', '2025-04-02 13:45:47', NULL);

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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sit_in`
--
ALTER TABLE `sit_in`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

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
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`sit_in_id`) REFERENCES `sit_in` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
