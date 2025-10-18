-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2025 at 03:17 PM
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
-- Database: `stu_track_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `type` enum('Income','Expense') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `name`, `type`) VALUES
(1, 'Allowance', 'Income'),
(2, 'Scholarship', 'Income'),
(3, 'Part-time Job', 'Income'),
(4, 'Gift', 'Income'),
(5, 'Food', 'Expense'),
(6, 'Bills', 'Expense'),
(7, 'Transport', 'Expense'),
(8, 'Entertainment', 'Expense'),
(9, 'Books & Supplies', 'Expense'),
(10, 'Health & Personal Ca', 'Expense');

-- --------------------------------------------------------

--
-- Table structure for table `daily_journal`
--

CREATE TABLE `daily_journal` (
  `entry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `entry_text` longtext NOT NULL,
  `entry_date` date NOT NULL,
  `mood` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_journal`
--

INSERT INTO `daily_journal` (`entry_id`, `user_id`, `entry_text`, `entry_date`, `mood`) VALUES
(19, 4, 'Have completed the assignment', '2025-08-28', 'Excited'),
(21, 4, 'Not be well', '2025-08-27', 'Sad'),
(22, 4, 'Completing assignment', '2025-08-29', 'Stressed'),
(23, 11, 'Finish the assignment', '2025-08-29', 'Excited');

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `exercise_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `exerciseName` varchar(255) NOT NULL,
  `duration` varchar(11) NOT NULL,
  `caloriesBurn` int(11) NOT NULL,
  `dateRegister` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`exercise_id`, `user_id`, `exerciseName`, `duration`, `caloriesBurn`, `dateRegister`) VALUES
(3, 4, 'Sit Up', '00:30:00', 200, '2025-08-28'),
(5, 4, 'Push Up', '00:30:00', 450, '2025-08-28');

-- --------------------------------------------------------

--
-- Table structure for table `exercises_history`
--

CREATE TABLE `exercises_history` (
  `history_id` int(11) NOT NULL,
  `exercise_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `exerciseName` varchar(255) NOT NULL,
  `duration` varchar(11) NOT NULL,
  `caloriesBurn` int(11) NOT NULL,
  `dateRegister` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercises_history`
--

INSERT INTO `exercises_history` (`history_id`, `exercise_id`, `user_id`, `exerciseName`, `duration`, `caloriesBurn`, `dateRegister`) VALUES
(3, 3, 4, 'Sit Up', '00:30:00', 200, '2025-08-28');

-- --------------------------------------------------------

--
-- Table structure for table `habit`
--

CREATE TABLE `habit` (
  `habit_id` int(11) NOT NULL,
  `habit_name` varchar(255) NOT NULL,
  `is_daily` tinyint(1) NOT NULL,
  `specific_date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `habit`
--

INSERT INTO `habit` (`habit_id`, `habit_name`, `is_daily`, `specific_date`, `user_id`, `created_at`) VALUES
(4, 'Drink 2L water', 0, '2025-08-28', 4, '2025-08-28 00:00:00'),
(6, 'Drink 2L water', 1, NULL, 4, '2025-08-29 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `habit_history`
--

CREATE TABLE `habit_history` (
  `id` int(11) NOT NULL,
  `habit_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `is_done` tinyint(1) NOT NULL,
  `date` date DEFAULT NULL,
  `habit_name` varchar(255) DEFAULT NULL,
  `habit_remark` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `habit_history`
--

INSERT INTO `habit_history` (`id`, `habit_id`, `user_id`, `is_done`, `date`, `habit_name`, `habit_remark`) VALUES
(4, 4, 4, 0, '2025-08-28', 'Drink 2L water', NULL),
(6, 6, 4, 0, '2025-08-28', 'Drink 2L water', NULL),
(7, 6, 4, 0, '2025-08-29', 'Drink 2L water', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `user_id`, `amount`, `category_id`, `datetime`, `description`) VALUES
(1, 1, 5.00, 7, '2025-08-26 03:34:46', 'Grab'),
(2, 1, 30.00, 1, '2025-08-26 03:35:17', 'alowance for grab'),
(4, 6, 300.00, 1, '2025-08-28 17:33:00', 'Allowance from parents'),
(5, 4, 300.00, 1, '2025-08-28 18:05:51', 'Allowance for the daily meals');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `remember_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `remember_token`) VALUES
(4, 'Richard2606', '5bab724960007f691536ce1243c9bd15', 'lrichardting@gmail.com', NULL),
(5, 'John234', '0f798a849fc8f07d4af1f52838fdb970', 'johnSmith@gmail.com', NULL),
(11, 'Richard26062004', 'ca2b46b4960815fa27f334a13299b552', 'richard2606@gmail.com', 'b6f508676ef7e7d4e7011bfa28dcf80ea962d6052694ffa5f82b14881fc3a766');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `daily_journal`
--
ALTER TABLE `daily_journal`
  ADD PRIMARY KEY (`entry_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`exercise_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exercises_history`
--
ALTER TABLE `exercises_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `exercise_id` (`exercise_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `habit`
--
ALTER TABLE `habit`
  ADD PRIMARY KEY (`habit_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `habit_history`
--
ALTER TABLE `habit_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `habit_id` (`habit_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `daily_journal`
--
ALTER TABLE `daily_journal`
  MODIFY `entry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `exercise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `exercises_history`
--
ALTER TABLE `exercises_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `habit`
--
ALTER TABLE `habit`
  MODIFY `habit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `habit_history`
--
ALTER TABLE `habit_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_journal`
--
ALTER TABLE `daily_journal`
  ADD CONSTRAINT `daily_journal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `exercises`
--
ALTER TABLE `exercises`
  ADD CONSTRAINT `exercises_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `exercises_history`
--
ALTER TABLE `exercises_history`
  ADD CONSTRAINT `exercises_history_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`exercise_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `exercises_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `habit`
--
ALTER TABLE `habit`
  ADD CONSTRAINT `habit_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `habit_history`
--
ALTER TABLE `habit_history`
  ADD CONSTRAINT `habit_history_ibfk_1` FOREIGN KEY (`habit_id`) REFERENCES `habit` (`habit_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `habit_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
