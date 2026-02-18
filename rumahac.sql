-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql210.infinityfree.com
-- Generation Time: Feb 18, 2026 at 08:07 AM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41044518_stj`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `type`, `status`, `created_at`) VALUES
(1, 'Penjualan Produk', 'income', 'active', '2026-02-11 14:27:30'),
(2, 'Jasa', 'income', 'active', '2026-02-11 14:27:30'),
(3, 'Investasi', 'income', 'active', '2026-02-11 14:27:30'),
(4, 'Pendapatan Lainnya', 'income', 'active', '2026-02-11 14:27:30'),
(5, 'Operasional', 'expense', 'active', '2026-02-11 14:27:30'),
(6, 'Sales & Marketing', 'expense', 'active', '2026-02-11 14:27:30'),
(7, 'Gaji Karyawan', 'expense', 'active', '2026-02-11 14:27:30'),
(8, 'Lainnya', 'expense', 'active', '2026-02-11 14:27:30');

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment_history`
--

INSERT INTO `payment_history` (`id`, `transaction_id`, `payment_date`, `amount`, `notes`, `created_by`, `created_at`) VALUES
(5, 11, '2026-02-18', '150000.00', 'Pembayaran cicilan', 1, '2026-02-18 09:18:47'),
(6, 11, '2026-02-18', '250000.00', 'Pembayaran cicilan', 1, '2026-02-18 12:22:15');

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `security_logs`
--

INSERT INTO `security_logs` (`id`, `user_id`, `event`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, NULL, 'csrf_validation_failed', '{\"action\":\"login\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 03:48:45'),
(2, NULL, 'csrf_validation_failed', '{\"action\":\"login\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 03:48:52'),
(3, NULL, 'csrf_validation_failed', '{\"action\":\"login\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 03:54:30'),
(4, NULL, 'csrf_validation_failed', '{\"action\":\"login\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 03:54:38'),
(5, NULL, 'csrf_validation_failed', '{\"action\":\"login\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 03:54:52'),
(6, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 03:54:59'),
(7, 1, 'csrf_validation_failed', '{\"action\":\"add_user\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 03:59:40'),
(8, 1, 'csrf_validation_failed', '{\"action\":\"add_user\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 03:59:52'),
(9, 1, 'csrf_validation_failed', '{\"action\":\"add_user\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 03:59:54'),
(10, 1, 'csrf_validation_failed', '{\"action\":\"add_user\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 03:59:59'),
(11, 1, 'csrf_validation_failed', '{\"action\":\"add_user\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 04:00:01'),
(12, 1, 'csrf_validation_failed', '{\"action\":\"add_user\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 04:00:04'),
(13, 1, 'csrf_validation_failed', '{\"action\":\"add_user\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 04:11:45'),
(14, 1, 'csrf_validation_failed', '{\"action\":\"add_user\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 04:12:49'),
(15, 1, 'csrf_validation_failed', '{\"action\":\"add_user\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 04:13:11'),
(16, 1, 'session_timeout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 04:27:01'),
(17, 1, 'logout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 04:27:01'),
(18, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 04:27:07'),
(19, NULL, 'login_failed', '{\"username\":\"Bsjs\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 04:48:45'),
(20, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 04:48:54'),
(21, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 04:59:18'),
(22, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 04:59:41'),
(23, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 04:59:46'),
(24, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 04:59:52'),
(25, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 04:59:58'),
(26, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 04:59:59'),
(27, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:00:00'),
(28, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:00:15'),
(29, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:00:18'),
(30, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:00:29'),
(31, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:00:31'),
(32, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:00:39'),
(33, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:00:41'),
(34, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:00:53'),
(35, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:00:55'),
(36, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:01:00'),
(37, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:01:02'),
(38, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:01:07'),
(39, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:01:08'),
(40, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:01:21'),
(41, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:01:23'),
(42, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:01:25'),
(43, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 05:01:27'),
(44, 1, 'session_timeout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:02:50'),
(45, 1, 'logout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:02:50'),
(46, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:02:56'),
(47, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:03:24'),
(48, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:03:30'),
(49, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:03:33'),
(50, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:03:34'),
(51, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:03:35'),
(52, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:03:36'),
(53, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:03:37'),
(54, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:05:10'),
(55, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:06:44'),
(56, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:06:44'),
(57, 1, 'csrf_validation_failed', '{\"action\":\"unknown\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 05:12:43'),
(58, 1, 'session_timeout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 06:17:23'),
(59, 1, 'logout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 06:17:23'),
(60, 1, 'session_timeout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 07:22:58'),
(61, 1, 'logout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 07:22:58'),
(62, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 07:27:59'),
(63, 1, 'session_timeout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 07:58:05'),
(64, 1, 'logout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 07:58:05'),
(65, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 07:58:10'),
(66, 1, 'session_timeout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 08:37:50'),
(67, 1, 'logout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 08:37:50'),
(68, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 08:37:55'),
(69, 1, 'session_timeout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 09:09:18'),
(70, 1, 'logout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 09:09:18'),
(71, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 09:09:24'),
(72, NULL, 'login_failed', '{\"username\":\"Admin \"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 09:43:04'),
(73, NULL, 'login_failed', '{\"username\":\"Admin \"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 09:43:14'),
(74, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 09:43:24'),
(75, 1, 'session_timeout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:13:38'),
(76, 1, 'logout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:13:38'),
(77, NULL, 'unauthorized_access', '{\"url\":\"\\/\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:13:39'),
(78, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:13:44'),
(79, 1, 'unauthorized_access', '{\"url\":\"\\/index.php?i=1\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-18 12:42:34'),
(80, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:42:56'),
(81, 1, 'unauthorized_access', '{\"url\":\"\\/index.php\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:42:56'),
(82, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:43:04'),
(83, 1, 'unauthorized_access', '{\"url\":\"\\/log.php?i=1\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-18 12:43:54'),
(84, NULL, 'unauthorized_access', '{\"url\":\"\\/index.php\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-18 12:43:57'),
(85, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-18 12:44:04'),
(86, 1, 'unauthorized_access', '{\"url\":\"\\/index.php?i=1\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:51:23'),
(87, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:51:28'),
(88, 1, 'unauthorized_access', '{\"url\":\"\\/index.php?i=1\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-18 12:53:26'),
(89, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:53:41'),
(90, 1, 'unauthorized_access', '{\"url\":\"\\/index.php\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:53:42'),
(91, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:53:49'),
(92, 1, 'unauthorized_access', '{\"url\":\"\\/index.php?i=1\"}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-18 12:58:32'),
(93, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:58:46'),
(94, 1, 'unauthorized_access', '{\"url\":\"\\/index.php\"}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:58:47'),
(95, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 12:58:58'),
(96, 1, 'session_timeout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 13:03:55'),
(97, 1, 'logout', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 13:03:55'),
(98, 1, 'login_success', '{\"user_id\":1}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 13:04:04'),
(99, 3, 'login_success', '{\"user_id\":3}', '160.22.6.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-18 13:05:28');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'company_name', 'Setia Jaya', '2026-02-11 14:27:30', '2026-02-11 14:27:30'),
(2, 'company_address', 'Sendangharjo, Kec. Karangrayung, Kabupaten Grobogan, Jawa Tengah', '2026-02-11 14:27:30', '2026-02-11 14:27:30'),
(3, 'company_phone', '0813-2929-9229', '2026-02-11 14:27:30', '2026-02-11 14:27:30'),
(4, 'company_email', 'info@setiajaya.com', '2026-02-11 14:27:30', '2026-02-11 14:27:30'),
(5, 'tax_rate', '11', '2026-02-11 14:27:30', '2026-02-11 14:27:30'),
(6, 'currency', 'IDR', '2026-02-11 14:27:30', '2026-02-11 14:27:30');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `category` varchar(100) NOT NULL,
  `status` enum('lunas','tempo','cicilan') DEFAULT 'lunas',
  `amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `date`, `description`, `type`, `category`, `status`, `amount`, `paid_amount`, `due_date`, `image_path`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(8, '2026-02-17', 'Besi 20 batang', 'expense', 'Gaji Karyawan', 'lunas', '55000.00', '0.00', NULL, 'uploads/69949bb8e7c30.jpg', '', 1, '2026-02-17 16:47:52', '2026-02-18 08:39:57'),
(9, '2026-02-17', 'Bbm', 'expense', 'Operasional', 'lunas', '150000.00', '0.00', NULL, NULL, '', 1, '2026-02-17 16:49:11', '2026-02-17 16:49:11'),
(10, '2026-02-18', 'ds', 'income', '', 'lunas', '125000.00', '0.00', NULL, 'uploads/69957a8d4f74b.png', 'ds', 1, '2026-02-18 07:28:57', '2026-02-18 08:38:37'),
(11, '2026-02-18', 'dsdeer', 'income', 'Investasi', 'cicilan', '1250000.00', '500000.00', '2026-02-18', NULL, '', 1, '2026-02-18 09:18:36', '2026-02-18 12:22:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','operator','viewer') DEFAULT 'operator',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$8iEsu5T6BUrBuaLJLZsXDePyWl9jyHWReSwzptG63.6tbp6CJGECu', 'Administrator', NULL, 'admin', 'active', '2026-02-18 05:04:04', '2026-02-11 14:27:29', '2026-02-18 13:04:04'),
(2, 'Setia', '$2y$10$ivlDSuPU1b3CcP/oPVL29uitBCRmFTQm/UMlT8b4mb1z/zUHAMd52', 'Kiss', 'duta.plafon@gmail.com', 'admin', 'active', '2026-02-12 00:00:00', '2026-02-12 16:12:33', '2026-02-12 16:12:33'),
(3, 'Udin', '$2y$10$4gup3LPdRuqxvDvzXuerV.9CrPIiJSdMqzI3Yv5uEgr8Jz9wlf5TG', 'Udin', 'saefudin.gunungjati@gmail.com', 'operator', 'active', '2026-02-18 05:05:28', '2026-02-18 03:06:44', '2026-02-18 13:05:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_event` (`event`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_transactions_date` (`date`),
  ADD KEY `idx_transactions_type` (`type`),
  ADD KEY `idx_transactions_status` (`status`),
  ADD KEY `idx_transactions_due_date` (`due_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD CONSTRAINT `payment_history_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_history_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
