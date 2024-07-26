-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2024 at 06:40 PM
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
-- Database: `connector`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_pass` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_cname` varchar(255) DEFAULT NULL,
  `user_profile` varchar(255) DEFAULT NULL,
  `user_status` int(10) DEFAULT NULL,
  `user_token` int(20) DEFAULT NULL,
  `user_login_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_email`, `user_pass`, `user_name`, `user_cname`, `user_profile`, `user_status`, `user_token`, `user_login_date`) VALUES
(1, 'cGF0ZWx6YWlkNzIxQGdtYWlsLmNvbQ==', 'emFpZEAxMjM0', 'emFpZF9wYXRlbA==', 'emFpZCBwYXRlbA==', 'profiles/Fantasy-Wolf-Dark-High-Definition-Wallpaper-112160.jpg', NULL, NULL, '2024-07-13 06:09:17'),
(2, 'eXVvLnR1YmVyMjAxOUBnbWFpbC5jb20=', 'MTIzNDU2Nzg5', 'YmF0bWFu', 'aSBhbSBiYXRtYW4=', 'profiles/batman-resolve-in-the-rain.jpg', NULL, NULL, '2024-07-13 06:15:14'),
(3, 'dGVzdEBnbWFpbC5jb20=', 'dGVzdDEyMzQ0', 'dGVzdF9hY2NvdW50', '', 'profiles/360_F_376440225_QjOu6mr0qX0bT271GstBYRKfXtwt6jnM.jpg', NULL, NULL, '2024-07-13 06:16:54'),
(4, 'dGVzdDFAZ21haWwuY29t', 'dGVzdDEyMzQ1', 'dGVzdGFjYw==', '', 'profiles/carbon.png', NULL, NULL, '2024-07-13 07:35:27'),
(5, 'cGF0ZWxlbHphaWQ3MjFAZ21haWwuY29t', 'emFpZEAxMjM0', NULL, NULL, NULL, NULL, NULL, '2024-07-15 13:53:23'),
(6, 'emFpZEBnbWFpbC5jb20=', 'cGFzc3dvcmRAMTIz', 'cmFuZGlfZ2lybA==', 'cmFuZGkwMDE=', 'profiles/7O5AgSGK.jpeg', NULL, NULL, '2024-07-15 17:06:26'),
(7, 'emFpZDFAZ21haWwuY29t', 'emFpZEAxMjM0', 'aW1iYXRtYW4=', 'QmF0bWFuIEdvdGhhbQ==', 'profiles/1000054842.jpg', NULL, NULL, '2024-07-21 16:24:11'),
(8, 'ejEyM0BnbWFsLmNvbQ==', 'ejEyMzQ1Njc4', 'YXNiYXRtYW4=', 'QmF0bWFu', 'profiles/1000054843.jpg', NULL, NULL, '2024-07-21 16:29:33'),
(9, 'dGVzdDJAZ21haWwuY29t', 'dGVzdEAxMjM0', 'VGVzdF9BY2M=', 'VGVzdCBhY2NvdW50IDI=', 'profiles/1000054842.jpg', NULL, NULL, '2024-07-22 04:32:33'),
(10, 'YWJAZ21haWwuY29t', 'YWRtaW5AMTIzNA==', 'YWRtaW5fYWNj', 'ZmFrZSBhZG1pbg==', 'profiles/pexels-pixabay-33045.jpg', NULL, NULL, '2024-07-22 04:49:37'),
(11, 'YWJjZEBnbWFpbC5jcG9t', 'YWJjZDEyMzQ1Ng==', 'aV9hbV96YWlk', 'emFpZCBiaW50', 'profiles/WhatsApp Image 2023-11-10 at 11.56.00 AM.jpeg', NULL, NULL, '2024-07-22 14:31:57'),
(12, 'emFpZHBhdGVsMTIxMTIxQGdtYWlsLmNvbQ==', 'emFpZEAxMjM0', 'QWRtaW4x', 'QWRtaW4gWmFpZA==', 'profiles/1000043751.jpg', NULL, NULL, '2024-07-22 15:56:42'),
(13, 'emFpZHphaWRAZ21haWwuY28uaW4=', 'emFpZDEyMzQ1', 'WmFpZF9wYXRlbA==', 'WmFpZCBQYXRlbCBJbmRpYQ==', 'profiles/1000050793.jpg', NULL, NULL, '2024-07-23 16:33:27'),
(15, 'cHB0QGdtYWlsLmNvbQ==', 'MTIzNDU2Nzg4', 'cGRmX211ha2Vy', 'aSBhbSBwZGYgbWFrZXI=', 'profiles/WhatsApp Image 2023-11-10 at 11.56.00 AM.jpeg', NULL, NULL, '2024-07-25 15:44:49'),
(17, 'emFpZDExMUBnbWFpbC5jb20=', 'emFpZDEyMzQ1', 'enp6enp6eg==', 'ZGFkYWQ=', NULL, NULL, NULL, '2024-07-25 18:11:38'),
(18, 'enNyQGdtYWlsLmNvbQ==', 'MTIzNDU2Nzg5', 'ejExMTEx', 'bmFtZTExMQ==', 'profiles/WhatsApp Image 2023-11-10 at 11.56.00 AM.jpeg', NULL, NULL, '2024-07-25 18:37:48'),
(19, 'ZXhhbXBsZUBnbWFpbC5jb20=', 'MTIzNDU2Nzg5', 'emFpZHphaWQxNjgw', 'emFpZCBwYXRlbA==', 'https://192.168.130.158/connector//profiles/profile.png', NULL, NULL, '2024-07-26 12:28:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_friends`
--

CREATE TABLE `user_friends` (
  `fr_id` int(11) NOT NULL,
  `sender_user_id` int(10) DEFAULT NULL,
  `recipient_user_id` int(10) DEFAULT NULL,
  `is_blocked` varchar(10) DEFAULT NULL,
  `is_star` int(11) DEFAULT NULL,
  `fr_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_friends`
--

INSERT INTO `user_friends` (`fr_id`, `sender_user_id`, `recipient_user_id`, `is_blocked`, `is_star`, `fr_timestamp`) VALUES
(33, 4, 1, '0', 1, '2024-07-25 06:20:20'),
(35, 17, 18, NULL, NULL, '2024-07-25 18:38:28'),
(36, 19, 13, NULL, 1, '2024-07-26 12:54:48'),
(37, 4, 13, NULL, 1, '2024-07-26 12:54:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_requests`
--

CREATE TABLE `user_requests` (
  `req_id` int(11) NOT NULL,
  `sender_user_id` int(10) DEFAULT NULL,
  `recipient_user_id` int(10) DEFAULT NULL,
  `req_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_requests`
--

INSERT INTO `user_requests` (`req_id`, `sender_user_id`, `recipient_user_id`, `req_timestamp`) VALUES
(4, 1, 3, '2024-07-13 07:28:02'),
(47, 4, 10, '2024-07-24 03:25:57'),
(50, 4, 12, '2024-07-24 16:46:29'),
(53, 17, 10, '2024-07-25 18:36:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- Indexes for table `user_friends`
--
ALTER TABLE `user_friends`
  ADD PRIMARY KEY (`fr_id`);

--
-- Indexes for table `user_requests`
--
ALTER TABLE `user_requests`
  ADD PRIMARY KEY (`req_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_friends`
--
ALTER TABLE `user_friends`
  MODIFY `fr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `user_requests`
--
ALTER TABLE `user_requests`
  MODIFY `req_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
