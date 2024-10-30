-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2024 at 10:47 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `expense_tracker_db`
--
CREATE DATABASE IF NOT EXISTS `expense_tracker_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `expense_tracker_db`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `type` enum('Income','Expense') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('Income','Expense') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `user_id`, `type`, `amount`, `description`, `category`, `date`, `created_at`) VALUES
(24, 42, 'Expense', '500.00', '', 'Food', '2024-09-07 15:07:00', '2024-09-07 13:07:32'),
(26, 42, 'Income', '100.00', '', 'Freelance', '2024-10-16 15:07:00', '2024-10-16 13:08:07'),
(27, 42, 'Income', '100.00', '', 'Investments', '2024-10-16 15:08:00', '2024-10-16 13:08:18'),
(34, 42, 'Expense', '200.00', '', 'Rent', '2024-10-16 15:27:00', '2024-10-16 13:27:36'),
(35, 42, 'Expense', '100.00', '', 'Healthcare', '2024-10-16 15:27:00', '2024-10-16 13:27:53'),
(36, 42, 'Income', '250.00', '', 'Gift', '2024-09-07 15:29:00', '2024-09-07 13:29:35'),
(40, 42, 'Expense', '5010.00', '', 'Healthcare', '2024-10-18 13:03:00', '2024-10-18 11:03:43'),
(49, 42, 'Expense', '4000.00', '', 'Gym', '2024-10-18 14:43:00', '2024-10-18 12:44:17'),
(50, 42, 'Income', '4000.00', '', 'Gym', '2024-10-18 14:53:00', '2024-10-18 12:53:36'),
(51, 42, 'Income', '4000.00', '', 'Gym', '2024-10-18 14:56:00', '2024-10-18 12:56:15'),
(52, 42, 'Income', '5000.00', '', 'Friend', '2024-10-18 15:14:00', '2024-10-18 13:15:15'),
(53, 42, 'Income', '5000.00', '', 'Friend', '2024-10-21 07:20:00', '2024-10-21 05:21:28'),
(54, 42, 'Expense', '5000.00', '', 'Friend', '2024-10-21 07:40:00', '2024-10-21 05:41:02'),
(55, 42, 'Income', '5000.00', '', 'Friend', '2024-10-21 08:00:00', '2024-10-21 06:00:23'),
(56, 42, 'Expense', '5000.00', '', 'Friend', '2024-10-21 08:02:00', '2024-10-21 06:02:15'),
(57, 42, 'Income', '5000.00', '', 'Friend', '2024-10-21 08:20:00', '2024-10-21 06:20:46'),
(58, 42, 'Income', '5000.00', '', 'Friend', '2024-10-21 08:26:00', '2024-10-21 06:27:00'),
(59, 42, 'Income', '5000.00', '', 'Gym', '2024-10-21 08:48:00', '2024-10-21 06:48:49'),
(60, 42, 'Income', '10000.00', '', 'Gym', '2024-10-21 08:54:00', '2024-10-21 06:54:22'),
(61, 42, 'Expense', '10000.00', '', 'Gym', '2024-10-21 08:56:00', '2024-10-21 06:57:18'),
(73, 42, 'Income', '5000.00', 'N/A', 'Freelance', '2024-10-21 13:43:00', '2024-10-21 11:44:04');

-- --------------------------------------------------------

--
-- Table structure for table `static_categories`
--

CREATE TABLE `static_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `type` enum('Income','Expense') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `static_categories`
--

INSERT INTO `static_categories` (`id`, `category_name`, `type`) VALUES
(14, 'Salary', 'Income'),
(15, 'Freelance', 'Income'),
(16, 'Investments', 'Income'),
(17, 'Business', 'Income'),
(18, 'Gift', 'Income'),
(19, 'Food', 'Expense'),
(20, 'Transportation', 'Expense'),
(21, 'Utilities', 'Expense'),
(22, 'Rent', 'Expense'),
(23, 'Entertainment', 'Expense'),
(24, 'Healthcare', 'Expense');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `otp` varchar(6) NOT NULL,
  `otp_created_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approve` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `otp`, `otp_created_at`, `created_at`, `approve`) VALUES
(7, 'admin', ' main', 'akhil.pandeyy1@gmail.com', '$2y$10$1CLiw3XWvVRRumxpTxVRpuQLXl67oji66uqHuM7ZlreZcAK8lypCG', 'admin', '307277', '2024-10-21 13:59:00', '2024-10-15 05:24:21', 1),
(42, 'Akhil', 'Pandey', 'akhil.p@biztechnosys.com', '$2y$10$XjhkY88rQxfw1eWPbEtldOdylaLMuLFffcEpq1O0VNxQBbTlaN4ky', 'user', '268208', '2024-10-22 08:10:30', '2024-10-15 09:53:08', 1),
(57, 'Jay', 'Patel', 'techakki29@gmail.com', '$2y$10$LBxgt8Wo7PY8iFlFJDXgceulGa5HF96Vha34BL78SJFxxxUit8dk.', 'user', '', NULL, '2024-10-30 06:46:57', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `static_categories`
--
ALTER TABLE `static_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `static_categories`
--
ALTER TABLE `static_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
