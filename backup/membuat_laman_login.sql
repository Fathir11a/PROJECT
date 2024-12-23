-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 22, 2024 at 10:27 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `membuat_laman_login`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`) VALUES
(1, 1, 5, '123', '2024-12-17 17:53:21'),
(5, 1, 5, 'sad', '2024-12-17 18:10:02'),
(6, 1, 5, '213', '2024-12-17 18:16:07'),
(7, 2, 2, 'hawo', '2024-12-17 18:20:42'),
(8, 2, 2, 'hawo', '2024-12-17 18:20:47'),
(9, 2, 1, 'hawo', '2024-12-17 18:21:27'),
(10, 2, 1, '1', '2024-12-17 18:21:36'),
(11, 2, 1, '123', '2024-12-17 18:24:47'),
(12, 2, 1, '1', '2024-12-17 18:28:03'),
(13, 1, 5, '1', '2024-12-17 18:28:23'),
(14, 1, 5, '3', '2024-12-17 18:29:10'),
(15, 1, 5, '1', '2024-12-17 18:31:53'),
(16, 1, 5, '1', '2024-12-17 18:34:22'),
(17, 1, 2, '1', '2024-12-17 18:40:07'),
(18, 1, 5, '1', '2024-12-17 18:41:26'),
(19, 1, 5, '1', '2024-12-17 18:41:47'),
(20, 1, 5, '3', '2024-12-17 18:41:52'),
(21, 1, 5, '3', '2024-12-17 18:41:58'),
(22, 1, 5, '5', '2024-12-17 18:42:12'),
(23, 1, 4, '3', '2024-12-17 18:42:24'),
(24, 1, 3, '5', '2024-12-17 18:42:31'),
(25, 1, 2, '333', '2024-12-17 18:42:40');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL,
  `role` enum('user','admin') NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `coin_balance` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `role`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `profile_picture`, `created_at`, `coin_balance`) VALUES
(1, 'admin', 'admin', 'admin@gmail.com', 'admin', 'agus subagus', '696969696969', 'jl.merpati', NULL, '2024-11-25 03:27:43', 31466),
(2, 'user', 'user', 'user@gmail.com', 'user', 'rafa azka', '39393939393', 'Gantarang', NULL, '2024-11-25 03:27:43', 22006),
(3, 'user', 'fathir', 'fathir@gmail.com', 'fathir', 'fathir wardhana', '9999999999', '789 Oak St, City, Country', NULL, '2024-11-25 03:27:43', 1210),
(4, 'admin', 'nemosec', 'nemosec@gmail.com', 'nemosec123', 'nemosec', NULL, NULL, NULL, '2024-11-26 07:07:27', 3300),
(5, 'user', 'arya', 'arya@test.com', '12345678', 'aryamahesa', NULL, NULL, NULL, '2024-12-07 06:53:53', 1000);

-- --------------------------------------------------------

--
-- Table structure for table `topup_requests`
--

CREATE TABLE `topup_requests` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `username` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `sender_username` varchar(255) NOT NULL,
  `receiver_username` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `status` varchar(50) NOT NULL CHECK (`status` in ('pending','approved','rejected')),
  `admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `topup_requests`
--
ALTER TABLE `topup_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project_id` (`project_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sender_username` (`sender_username`),
  ADD KEY `fk_receiver_username` (`receiver_username`),
  ADD KEY `idx_project_id` (`project_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `topup_requests`
--
ALTER TABLE `topup_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `project` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `project` (`id`);

--
-- Constraints for table `topup_requests`
--
ALTER TABLE `topup_requests`
  ADD CONSTRAINT `fk_project_id` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_topup_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_receiver_username` FOREIGN KEY (`receiver_username`) REFERENCES `project` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sender_username` FOREIGN KEY (`sender_username`) REFERENCES `project` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
