-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2025 at 08:43 AM
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
-- Database: `carevault`
--

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `doctor_name` varchar(100) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `user_id`, `visit_date`, `doctor_name`, `diagnosis`, `medications`, `notes`) VALUES
(30, 20, '2025-04-22', 'Jester', 'Fever', 'paracetamol', 'Needs to be hydrated and well rested'),
(31, 20, '2025-04-23', 'Jester', 'asthma', 'Salbutamol', 'Keep well rested and Hydrated'),
(37, 25, '2025-04-23', 'Jester', 'Asthma', 'Dry power Inhaler', 'Avoid activities that may trigger asthma');

-- --------------------------------------------------------

--
-- Table structure for table `medications`
--

CREATE TABLE `medications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `medicine_name` varchar(100) DEFAULT NULL,
  `dosage` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('ongoing','completed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medications`
--

INSERT INTO `medications` (`id`, `user_id`, `medicine_name`, `dosage`, `start_date`, `end_date`, `status`) VALUES
(23, 20, 'paracetamol', '500g', '2025-04-22', '2025-04-26', 'completed'),
(24, 20, 'Salbutamol', '10ml', '2025-04-23', '2025-04-30', 'completed'),
(30, 25, 'Dry power Inhaler', '3 sprays a day', '2025-04-23', '2025-05-23', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `records` text DEFAULT NULL,
  `gender` enum('male','female','','') NOT NULL,
  `phone_no` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `username`, `password`, `birth_date`, `blood_type`, `records`, `gender`, `phone_no`, `address`, `user`) VALUES
(20, 'Gwyn Kenshin', 'Novillia', 'gnovillia_230000001045@uic.edu.ph', 'Ishin', '$2y$10$kDV8FGSxtk076znlSZuVCOqOLbeHjyW1YfbQP242dxmOXkJxFw3TO', '2005-06-15', 'A', 'Asthma', 'male', 2147483647, 'C.D.E,Buhangin, Davao City', 'Patient'),
(21, 'kalben', 'Benogsudan', 'walbengwapo@gmail.com', 'karma', '$2y$10$upQYQ1BQ.TBfjIewovBrKO255WAJBR9OcaJewP68kk51dK0J/kvg6', '2025-04-02', 'O', 'none', 'male', 2147483647, 'C.D.E,Buhangin, Davao City', 'Patient'),
(23, 'Kyle', 'Arigo', 'karigo_230000001045@uic.edu.ph', 'arigo', '$2y$10$rB4mtq9HKgzAjVTzg4ICPOkm0X3d4kIRg6gh8YpkuLJVHUTDG9NkG', '2005-06-15', 'A', 'None', 'male', 2147483647, 'C.D.E,Buhangin, Davao City', 'Patient'),
(25, 'Walben', 'Benogsudan', 'wbenogsudan_230000001045@uic.edu.ph', 'walben', '$2y$10$fgf6asACggROdrYcYatQwOaiCy4ZJxhncAjx0HJDXlU72MATvapL2', '2005-06-15', 'A', 'Asthma', 'male', 2147483647, 'C.D.E,Buhangin, Davao City', 'Patient'),
(26, 'Sigma', 'Koz', 'skibidi@gmail.com', 'Sigma', '$2y$10$Q2HWI8VbKA5Wt5ad7HW5D.CrzyJPYgTkUGd7SQIYAswS/r8RLAcGa', '2025-04-01', 'Z', 'none', 'male', 911, 'C.D.E,Buhangin, Davao City', 'Doctor'),
(28, 'walben', 'benogsduan', 'walbengwapoz@gmail.com', 'walben2', '$2y$10$XruwIXfvRbtzmRt5yGyihut032rfaMa6pxdCd8PVzSS/krWHeh/ye', '2025-10-01', 'O', 'awdasasdfasdf', 'male', 911, 'asdasdasdawdasd', 'Patient');

-- --------------------------------------------------------

--
-- Table structure for table `vaccinations`
--

CREATE TABLE `vaccinations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vaccine_name` varchar(100) DEFAULT NULL,
  `date_given` date DEFAULT NULL,
  `vaccine_brand` varchar(255) DEFAULT NULL,
  `dose_number` int(11) NOT NULL,
  `next_sched` date DEFAULT NULL,
  `batch_code` varchar(255) DEFAULT NULL,
  `administering_prof` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccinations`
--

INSERT INTO `vaccinations` (`id`, `user_id`, `vaccine_name`, `date_given`, `vaccine_brand`, `dose_number`, `next_sched`, `batch_code`, `administering_prof`, `location`) VALUES
(5, 20, 'covid-19', '2025-04-22', 'phizer', 10, '2025-05-07', '11111', 'Jester', 'Davao City'),
(8, 25, 'covid-19', '2025-04-23', 'Phizer', 10, '2025-05-07', '11123', 'Jester', 'Davao City');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `medications`
--
ALTER TABLE `medications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vaccinations`
--
ALTER TABLE `vaccinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `medications`
--
ALTER TABLE `medications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `vaccinations`
--
ALTER TABLE `vaccinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medications`
--
ALTER TABLE `medications`
  ADD CONSTRAINT `medications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vaccinations`
--
ALTER TABLE `vaccinations`
  ADD CONSTRAINT `vaccinations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
