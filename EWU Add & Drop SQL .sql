-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql110.infinityfree.com
-- Generation Time: Apr 05, 2026 at 02:37 AM
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
-- Database: `if0_41564407_if0_41564407_ewu_add_drop`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`) VALUES
(1, 'Books', '1775144916_photo_10_2026-03-30_12-10-32.jpg'),
(2, 'Electronic Devices', '1775144877_photo_16_2026-03-30_12-10-32.jpg'),
(3, 'Hostel Essentials', '1775144810_photo_5_2026-03-30_12-10-32.jpg'),
(8, 'Life Style', '1775144849_photo_12_2026-03-30_12-10-49.jpg'),
(10, 'Lecture Notes', '1775144654_photo_22_2026-03-30_12-10-32.jpg'),
(12, 'Project Equipment', '1775145180_image eee.png');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_free` tinyint(1) DEFAULT 0,
  `image` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `name`, `category_id`, `price`, `is_free`, `image`, `phone`, `whatsapp`, `facebook`, `instagram`, `created_at`) VALUES
(63, 14, 'Bag Used For Just 1 Time - Bought at Price 4000 tk', 8, '2000.00', 0, '1775145406_photo_14_2026-03-30_12-10-49.jpg', '01758411547', '01758411547', 'https://www.facebook.com/tasmiaatasnim', '', '2026-04-02 15:56:46'),
(64, 14, 'Dress for Sell never used because of size issue - Size small 32, originally bought at 6000 tk', 8, '1500.00', 0, '1775145564_photo_7_2026-03-30_12-10-49.jpg', '01758411547', '01758411547', 'https://www.facebook.com/tasmiaatasnim', '', '2026-04-02 15:59:24'),
(65, 14, 'Shoe Used only 4 times fully fresh - Original price 4000', 8, '500.00', 0, '1775145652_photo_11_2026-03-30_12-10-49.jpg', '01758411547', '01758411547', 'https://www.facebook.com/tasmiaatasnim', '', '2026-04-02 16:00:52'),
(66, 14, 'MS 1200 calculator used lightly for 2 months , fully fresh condition', 2, '1000.00', 0, '1775145756_photo_4_2026-03-30_12-18-26.jpg', '01758411547', '01758411547', 'https://www.facebook.com/tasmiaatasnim', '', '2026-04-02 16:02:36'),
(67, 15, 'Macbook Air M4 fully fresh used 6 months - treated like my child', 2, '80000.00', 0, '1775145985_photo_8_2026-03-30_12-18-26.jpg', '01971379881', '01971379881', 'https://www.facebook.com/muntahatasnim', 'https://www.instagram.com/muntahatasnim', '2026-04-02 16:06:25'),
(68, 15, 'Study Table size 3 feet lenght, 4 feet width', 3, '2000.00', 0, '1775146101_photo_5_2026-03-30_12-18-26.jpg', '01971379881', '01971379881', 'https://www.facebook.com/muntahatasnim', 'https://www.instagram.com/muntahatasnim', '2026-04-02 16:08:21'),
(69, 15, 'Mongur Sir PHY 109 Full Lecture Notes', 10, '0.00', 1, '1775146158_photo_2_2026-03-30_12-18-26.jpg', '01971379881', '01971379881', 'https://www.facebook.com/muntahatasnim', 'https://www.instagram.com/muntahatasnim', '2026-04-02 16:09:18'),
(70, 15, 'Earring fresh condition Original Price 600 tk', 8, '100.00', 0, '1775146263_photo_10_2026-03-30_12-10-49.jpg', '01971379881', '01971379881', 'https://www.facebook.com/muntahatasnim', 'https://www.instagram.com/muntahatasnim', '2026-04-02 16:11:03'),
(71, 15, 'Sumsung a54 Used for 2 Years', 2, '8000.00', 0, '1775146327_photo_14_2026-03-30_12-18-26.jpg', '01971379881', '01971379881', 'https://www.facebook.com/muntahatasnim', 'https://www.instagram.com/muntahatasnim', '2026-04-02 16:12:07'),
(72, 16, 'Study Table size 2 feet lenght, 4 feet width', 3, '2000.00', 0, '1775146522_photo_6_2026-03-30_12-18-26.jpg', '01358411547', '01358411547', 'https://www.facebook.com/inzamamhaquebony', 'https://www.instagram.com/nzamamhaquebony', '2026-04-02 16:15:22'),
(73, 16, 'CW 991 Calculator used for 1 year', 2, '300.00', 0, '1775146596_photo_3_2026-03-30_12-18-26.jpg', '01358411547', '01358411547', 'https://www.facebook.com/inzamamhaquebony', 'https://www.instagram.com/nzamamhaquebony', '2026-04-02 16:16:36'),
(74, 16, 'The Origin of Species - Charles Darwin Original Hardcopy', 1, '2000.00', 0, '1775146777_pf-71b40b91--Books_1200x.webp', '01358411547', '01358411547', 'https://www.facebook.com/inzamamhaquebony', 'https://www.instagram.com/nzamamhaquebony', '2026-04-02 16:19:37'),
(75, 17, 'Bristi Bilash - Humayon Ahmed', 1, '100.00', 0, '1775146975_3923073.jpg', '01971379881', '01971379881', 'https://www.facebook.com/saminsathi', 'https://www.instagram.com/saminsathi', '2026-04-02 16:22:55'),
(76, 17, 'Himu Mama - Humayon Ahmed', 1, '100.00', 0, '1775147028_prod-1246233.04F72C25.jpg', '01971379881', '01971379881', 'https://www.facebook.com/saminsathi', 'https://www.instagram.com/saminsathi', '2026-04-02 16:23:48'),
(78, 14, 'Book Shelf Cabinet Rack length 12inch depth 10inch height 6ft', 3, '3000.00', 0, '1775287982_c7b3e720-01e7-495d-bc18-61d0155d9f9a.jpg', '0175589532', '0175589532', '', '', '2026-04-04 07:33:02'),
(79, 14, 'Samsung Galaxy Buds 3 Pro Full fresh condition', 2, '9500.00', 0, '1775288390_97bd53bb-46bc-44b9-aad3-98f425a02aee.jpg', '0175589655', '0175589655', '', '', '2026-04-04 07:39:50'),
(80, 14, 'Indian elegant jhumka', 8, '150.00', 0, '1775288677_a83417de-d92c-4207-83e0-6937a1f0cc26.jpg', '01880903611', '01880903611', '', '', '2026-04-04 07:44:37');

-- --------------------------------------------------------

--
-- Table structure for table `product_reports`
--

CREATE TABLE `product_reports` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `reported_by` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reports`
--

INSERT INTO `product_reports` (`id`, `product_id`, `reported_by`, `reason`, `created_at`) VALUES
(10, 65, 17, 'It was unavailable', '2026-04-02 16:27:18');

-- --------------------------------------------------------

--
-- Table structure for table `saved_products`
--

CREATE TABLE `saved_products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_products`
--

INSERT INTO `saved_products` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(27, 15, 64, '2026-04-02 16:12:21'),
(28, 15, 63, '2026-04-02 16:12:30'),
(29, 16, 68, '2026-04-02 16:13:45'),
(30, 16, 67, '2026-04-02 16:13:50'),
(31, 16, 66, '2026-04-02 16:14:00'),
(32, 17, 74, '2026-04-02 16:23:56'),
(33, 17, 65, '2026-04-02 16:27:20'),
(35, 14, 76, '2026-04-02 17:41:23'),
(36, 14, 70, '2026-04-02 17:44:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `created_at`, `role`) VALUES
(13, 'Md.Mahir Hossain', '2023-3-60-101@std.ewubd.edu', '01571379881', '$2y$10$IfFDokXkghEdmWgHIRLi7O0Sgn1JpQJA..XgM7Di1IUnputyHGdKG', '2026-04-02 15:37:58', 'admin'),
(14, 'Tasmia Tasnim', '2023-3-60-266@std.ewubd.edu', '01758411547', '$2y$10$DUqPRrLiTyts/fKzuAur3OvXRZg0pPrOGX9.jIpaWd95h8tAB1bNi', '2026-04-02 15:53:53', 'admin'),
(15, 'Muntaha Tasnim Ohi', '2023-3-60-102@std.ewubd.edu', '01971379881', '$2y$10$Ga5LD1KVNSKGbsUGkKhaX.eDkwKkXHqFb/eMRVWZ2sgMo06kYF2pq', '2026-04-02 16:03:44', 'user'),
(16, 'Inzamam Bony', '2023-3-60-103@std.ewubd.edu', '01358411547', '$2y$10$MHvt9a4WICHHRznOa.Taluqs382cnR6fZ5D0BhDvIrKi1afTu6It.', '2026-04-02 16:13:26', 'user'),
(17, 'Tamanna Samin Sathi', '2024-3-60-202@std.ewubd.edu', '01755411547', '$2y$10$Om6TmLlXeSBAzPNCpqVoIeG3EUbhbdCmvaYPZfb7BnuPcGDnsxls6', '2026-04-02 16:20:43', 'user'),
(19, 'Md. Alice', '2023-3-60-144@std.ewubd.edu', '01571379881', '$2y$10$JqUfLh6KLvejHOsbytJxmuE6hchGobo3LUFtQzwdLtoUydF7MYrQ.', '2026-04-05 05:43:23', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_reports`
--

CREATE TABLE `user_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reported_by` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_reports`
--

INSERT INTO `user_reports` (`id`, `user_id`, `reported_by`, `reason`, `created_at`) VALUES
(6, 16, 17, 'Did not contact', '2026-04-02 16:28:09'),
(7, 15, 16, 'It was not complete syllabus', '2026-04-02 16:28:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_reports`
--
ALTER TABLE `product_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_products`
--
ALTER TABLE `saved_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product_unique` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_reports`
--
ALTER TABLE `user_reports`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `product_reports`
--
ALTER TABLE `product_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `saved_products`
--
ALTER TABLE `saved_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_reports`
--
ALTER TABLE `user_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `saved_products`
--
ALTER TABLE `saved_products`
  ADD CONSTRAINT `saved_products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
