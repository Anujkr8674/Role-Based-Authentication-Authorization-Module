-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2026 at 10:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `admin_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `auth_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `admin_id`, `name`, `email`, `password`, `created_at`, `auth_token`, `token_expiry`) VALUES
(1, 'ADM000001', 'Admin Anuj', 'anujkr8674@gmail.com', '$2y$10$Iha..gV5Z.Jjo3ExpcFreeuYjOK1VyCxGDiePU.C0S91GAq9eStzu', '2025-07-12 12:27:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `auth_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `employee_id`, `name`, `email`, `password`, `phone`, `auth_token`, `token_expiry`, `last_login`, `created_at`) VALUES
(2, '721558', 'Ram', 'anujnick57@gmail.com', '$2y$10$JICT3RNH2RQNyde1yLJqLuZuDPQlTzi2w6iwSXyQr6B2v2VK06mxq', '8674823100', NULL, NULL, '2026-03-30 22:59:18', '2026-03-30 15:06:32'),
(3, '985588', 'Ram nayak', 'anujfeb162001@gmail.com', '$2y$10$ULhHKh7Wm1TVN4IKPk.53.IYdXmrbDA1YDv/p0KPnbhlWrds25AGi', NULL, NULL, NULL, '2026-03-30 22:22:15', '2026-03-30 16:46:29'),
(4, '476447', 'Laxman', 'kranuj5757@gmail.com', '$2y$10$8qS4jjmQKE.3KBe4uiehq.c1eGpc4UnQW6Kj5scQTIT2EC3YFdxKS', NULL, NULL, NULL, '2026-03-31 12:59:40', '2026-03-31 07:29:19');

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `id` int(11) NOT NULL,
  `manager_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `auth_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`id`, `manager_id`, `name`, `email`, `password`, `phone`, `auth_token`, `token_expiry`, `last_login`, `created_at`, `updated_at`) VALUES
(2, '561432', 'Anuj', 'anujnick57@gmail.com', '$2y$10$CMSHChbbu/0kWPRfrbGmxejTLaFeVd2fEpgYfE5kvJ/4FRMg4fsVe', '8674823120', NULL, NULL, '2026-03-30 22:42:41', '2026-03-30 14:52:55', '2026-03-30 17:12:52'),
(3, '321514', 'Ram', 'anujkr8674@gmail.com', '$2y$10$MY6NA38VSz7HnUblgjZ7U.9XzPS9xmumIKzsEoRwq67piRnpfvZD2', NULL, NULL, NULL, NULL, '2026-03-30 15:50:28', '2026-03-30 15:50:28'),
(5, '655517', 'Sukesh', 'kranuj5757@gmail.com', '$2y$10$ijmHZ7/esiGApoj8hQoSaOvb/EoR5BWeUtptgtmfcNUXB73hoy.2q', NULL, NULL, NULL, '2026-03-31 13:17:28', '2026-03-31 07:39:49', '2026-03-31 07:47:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_employee_id` (`employee_id`),
  ADD UNIQUE KEY `uniq_employee_email` (`email`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_manager_id` (`manager_id`),
  ADD UNIQUE KEY `uniq_manager_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
