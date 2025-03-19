-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2025 at 08:16 AM
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
(1, 'admin', 'Walay klase ugma!!!', '2025-03-07 06:04:03'),
(2, 'Super Admin', 'Klase karon!', '2025-03-10 20:30:46'),
(3, ' ', 'Holiday karon!', '2025-03-11 03:38:35'),
(4, ' ', 'qwdwqdq', '2025-03-11 03:51:57'),
(5, ' ', 'naay klase?', '2025-03-19 02:28:32');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(35, '22682504', 'Palacio, Real Jhon', 'C# Programming', '526', '13:36:29', '13:42:53', '2025-03-19', '2025-03-19 05:36:29', 'active', 29),
(36, '00000000', 'Palacio, Real Jhon', 'Java Programming', '524', '13:42:02', NULL, '2025-03-19', '2025-03-19 05:42:02', 'active', 30),
(37, '45645645', 'wsdsf, sdfsdfds', 'ASP.Net Programming', '526', '14:00:50', NULL, '2025-03-19', '2025-03-19 06:00:50', 'active', 30);

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
(18, '45645645', 'wsdsf', 'sdfsdfds', 'fdsfs', 'BSCS', '1st Year', 'dsfdsfsd@gmail.com', 'fsdfsfsdf', 'zz', '$2y$10$5EBS1AHSDJWlVoTE4nZGQOZXVkmjIXoJpKWgI7kifTafFfJja8c4O', '2025-03-07 05:05:55', NULL);

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sit_in`
--
ALTER TABLE `sit_in`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
