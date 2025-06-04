-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 08:35 AM
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
-- Database: `stonepath_estates`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'john', '$2y$10$bTOG/Ns4TTFkTq.zXkskmuoDFebC.0wEHUR5/q8xss1wk7qqnjBcS'),
(2, 'katwe', '$2y$10$wLVzWOWCapG5z3CmmmurLukdMV/j4zQqC/rUx3ZZIfbhaPe79dmKK');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `status` enum('available','rented','sold') DEFAULT 'available',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `title`, `description`, `category`, `preview_image`, `price`, `location`, `status`, `image`, `created_at`) VALUES
(3, 'House A', '6 Bedroom house with a backyard', 'Sell', 'uploads/properties/prop_3_6836ac961917b.jpeg', 100000.00, 'Kyanja', 'available', NULL, '2025-05-28 06:26:30'),
(4, 'House B', '3 bedroom house', 'rent', 'uploads/properties/prop_4_6836acc3eab9f.jpeg', 200000.00, 'Ntinda', 'available', NULL, '2025-05-28 06:27:15');

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

CREATE TABLE `property_images` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_images`
--

INSERT INTO `property_images` (`id`, `property_id`, `image_path`, `is_main`) VALUES
(7, 3, 'uploads/properties/prop_3_6836ac961917b.jpeg', 0),
(8, 3, 'uploads/properties/prop_3_6836ac9619397.jpeg', 0),
(9, 3, 'uploads/properties/prop_3_6836ac961975d.jpeg', 0),
(10, 3, 'uploads/properties/prop_3_6836ac9619b61.jpeg', 0),
(11, 3, 'uploads/properties/prop_3_6836ac9619f75.jpeg', 0),
(12, 4, 'uploads/properties/prop_4_6836acc3ea7b6.jpeg', 0),
(13, 4, 'uploads/properties/prop_4_6836acc3ea9f5.jpeg', 0),
(14, 4, 'uploads/properties/prop_4_6836acc3eab9f.jpeg', 0),
(15, 4, 'uploads/properties/prop_4_6836acc3ead59.jpg', 0),
(16, 4, 'uploads/properties/prop_4_6836acc3eaf07.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `property_registrations`
--

CREATE TABLE `property_registrations` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `property_location` varchar(255) NOT NULL,
  `property_id` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `username`, `password`, `gender`, `phone`, `dob`, `created_at`) VALUES
(2, 'Jonathan John', 'Odong', 'odongjonathan9@gmail.com', 'john', '$2y$10$A0xGw/qCepNstxyAyl7GF.0wGDu3tNuNXn8keUxAOYAQ26vrpSUSe', 'male', '+256787042619', '2002-12-09', '2025-03-18 13:38:30'),
(3, 'michael', 'katwe', 'katwemichael@gmail.com', 'katwe', '$2y$10$vDA.sAUkdWfrrmkPltKty.mqF/gz3KBHotC.XmXasm0pLL7XUPJDe', 'male', '0700100200', '2000-01-01', '2025-05-27 06:38:02');

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
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_images`
--
ALTER TABLE `property_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `property_registrations`
--
ALTER TABLE `property_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `property_id` (`property_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `property_registrations`
--
ALTER TABLE `property_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `property_images`
--
ALTER TABLE `property_images`
  ADD CONSTRAINT `property_images_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
