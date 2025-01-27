-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 27, 2025 at 11:11 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u373116035_LENSFOLIOHUB`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_me`
--

CREATE TABLE `about_me` (
  `id` int(11) NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `profession` varchar(255) DEFAULT NULL,
  `about_me` text DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL,
  `location_text` varchar(255) DEFAULT NULL,
  `view_type` enum('grid','carousel') NOT NULL DEFAULT 'grid',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `avail_hrs` text DEFAULT NULL,
  `portfolio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_me`
--

INSERT INTO `about_me` (`id`, `profile_image`, `name`, `profession`, `about_me`, `age`, `latitude`, `longitude`, `created_at`, `email`, `location_text`, `view_type`, `price`, `avail_hrs`, `portfolio`) VALUES
(1, '../../../../assets/img/profile/gallery-6.jpg', NULL, 'photographer', 'dasasdsa', 12, 14.2838325, 120.8668772, '2024-09-17 08:36:54', '', NULL, 'grid', 0.00, NULL, NULL),
(2, '../../../../assets/img/profile/gallery-6.jpg', NULL, 'photographer,videographer', 'dasasdsa', 12, 14.2838325, 120.8668772, '2024-09-17 08:37:24', '', NULL, 'grid', 0.00, NULL, NULL),
(3, 'default_image.jpg', NULL, 'photographer', 'hello real', 13, 14.2813281, 120.8703823, '2024-09-17 08:40:36', '1@gmail.com', 'Trece Martires City Hall, Governor\'s Drive, Trece Martires, Cavite, Philippines', 'carousel', 0.00, NULL, NULL),
(4, 'default_image.jpg', NULL, 'photographer,videographer', 'Hey Please hire me, I\'m good heheHey Please hire me, I\'m good heheHey Please hire me, I\'m good heheHey Please hire me, I\'m good heheHey Please hire me, I\'m good heheHey Please hire me, I\'m good heheHey Please hire me, I\'m good heheHey Please hire me, I\'m good heheHey Please hire me, I\'m good hehe', 12, 14.2838325, 120.8668772, '2024-09-26 20:22:51', 'test@gmail.com', '', 'carousel', 10.00, NULL, NULL),
(7, '', 'SUPPLIER 1', '', 'Hello, I am Supplier1!\r\nA photographer who loves to capture every moment, because photography is not just an images, it also holds memories you never want to forget. Photography also is an art that can captivates someone\'s heart and I am here to offer you a shots you never want to missed!', 22, 0, 0, '2025-01-10 05:53:19', 'supplier1@gmail.com', '', 'grid', 0.00, ',0', NULL),
(8, 'profile_6790957e1bef62.81570936.jpg', 'SUPPLIER2', 'photographer', 'Welcome! My name is Supplier2 and I am a professional photographer specializing in portrait, landscape, and creative photography. I am offering a high-quality and eye-catching images for four years now and I am dreaming of offering it for a lifetime. And in this lifetime, it is an honor for me to be the one to capture yours!', 20, 14.2990183, 120.9589699, '2025-01-10 06:18:15', 'supplier2@gmail.com', '', 'grid', 1000.00, '0,14', 'https://vercel.com/'),
(9, '', 'SUPPLIER1', '', 'Hello, I am Supplier1!\r\nA photographer who loves to capture every moment, because photography is not just an images, it also holds memories you never want to forget. Photography also is an art that can captivates someone\'s heart and I am here to offer you a shots you never want to missed!', 25, 0, 0, '2025-01-10 07:24:04', 'supplier3@gmail.com', 'Tanza, Cavite, Philippines', 'grid', 0.00, NULL, NULL),
(10, '', 'ron carlo lorenzo tapao', 'photographer', '', 0, 14.384383026670973, 120.82933046455689, '2025-01-13 13:04:36', 'tapaocholo390@gmail.com', 'Amaya I, Tanza, Cavite, Philippines', 'grid', 0.00, NULL, NULL),
(11, 'profile_678653e1bdfd51.03915066.png', 'dahhyunnee', 'photographer', 'TEST 1', 21, 14.4028641, 120.9413813, '2025-01-14 11:48:12', 'cmalicdon14@gmail.com', '', 'grid', 1000.00, '0,8,9', NULL),
(12, '', '', 'photographer', '', 22, 0, 0, '2025-01-14 12:00:34', 'moodvibes683@gmail.com', '', 'grid', 5000.00, NULL, NULL),
(13, 'profile_6786536dc61ff8.28672114.jpg', 'erich blu', 'photographer', 'A Brilliant Photographer so hire me now', 22, 14.372367969173, 120.81684043149, '2025-01-14 12:06:01', 'rckbrnl22@gmail.com', '', 'grid', 0.00, '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `event` enum('photography','videography') NOT NULL,
  `time` varchar(255) NOT NULL,
  `selected_date` date NOT NULL,
  `email_uploader` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('Pending','Accepted','Completed','Decline','Cancelled') NOT NULL DEFAULT 'Pending',
  `cancel_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`id`, `name`, `latitude`, `longitude`, `event`, `time`, `selected_date`, `email_uploader`, `email`, `status`, `cancel_reason`) VALUES
(21, 'TEST TEST TEST FOR EMAIL SUPPLIER 3', 14.283833, 120.866877, 'photography', '0', '2025-01-03', 'supplier2@gmail.com', 'client@gmail.com', 'Cancelled', 'no'),
(22, 'Ivan ablanida', 14.283833, 120.866877, 'photography', '0', '2025-01-03', 'supplier2@gmail.com', 'client@gmail.com', 'Cancelled', 'no'),
(23, 'diana', 14.342933, 120.850314, 'photography', '0', '2025-01-14', 'supplier1@gmail.com', 'bucodancollet@gmail.com', 'Completed', NULL),
(24, 'Diana', 14.342933, 120.850314, 'photography', '9', '0000-00-00', 'cmalicdon14@gmail.com', 'client@gmail.com', 'Completed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `uploader_email` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `role` enum('customer','supplier') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `click_email` varchar(255) NOT NULL,
  `is_seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id`, `email`, `uploader_email`, `text`, `role`, `created_at`, `click_email`, `is_seen`) VALUES
(72, 'client@gmail.com', 'supplier2@gmail.com', 'dsa', 'customer', '2025-01-22 06:24:27', '', 0),
(73, 'supplier2@gmail.com', 'client@gmail.com', 'hey', 'supplier', '2025-01-22 06:25:02', '', 0),
(74, 'client@gmail.com', 'supplier2@gmail.com', 'hey', 'customer', '2025-01-22 06:32:47', '', 0),
(75, 'client@gmail.com', 'supplier2@gmail.com', 'hello bro!', 'customer', '2025-01-22 06:34:29', '', 0),
(76, 'client@gmail.com', 'cmalicdon14@gmail.com', 'hey', 'customer', '2025-01-22 06:34:52', '', 0),
(77, 'client@gmail.com', 'supplier2@gmail.com', 'I want to avail!', 'customer', '2025-01-22 06:42:23', '', 0),
(78, 'supplier2@gmail.com', 'client@gmail.com', 'go', 'supplier', '2025-01-22 06:42:38', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `card_img` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `session_email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `card_img`, `email`, `comments`, `created_at`, `session_email`) VALUES
(4, 'u9.jpg', 'supplier2@gmail.com', 'wow', '2025-01-10 09:09:09', 'client@gmail.com'),
(5, 'u9.jpg', 'supplier2@gmail.com', 'we', '2025-01-10 09:09:30', 'client@gmail.com'),
(6, 'u9.jpg', 'supplier2@gmail.com', 'wow', '2025-01-10 14:24:21', 'supplier1@gmail.com'),
(7, 'u9.jpg', 'supplier2@gmail.com', 'dasdasdas', '2025-01-11 22:07:20', 'supplier1@gmail.com'),
(8, 'u9.jpg', 'supplier2@gmail.com', 'hey', '2025-01-11 22:08:32', 'client@gmail.com'),
(9, 'Rectangle 53.png', 'supplier1@gmail.com', 'wow', '2025-01-11 17:18:40', 'supplier1@gmail.com'),
(10, '472521528_452993697876142_6017095496710765049_n.jpg', 'supplier3@gmail.com', 'wow', '2025-01-13 10:39:40', 'supplier1@gmail.com'),
(11, '472521528_452993697876142_6017095496710765049_n.jpg', 'supplier3@gmail.com', 'wow', '2025-01-14 03:47:00', 'supplier1@gmail.com'),
(12, '456425821_374542075693538_738090013815818665_n.jpg', 'rckbrnl22@gmail.com', 'wow so gorgeous', '2025-01-14 12:12:25', 'rckbrnl22@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `gallery_name` varchar(255) DEFAULT NULL,
  `image_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `email`, `gallery_name`, `image_name`) VALUES
(1, 'supplier2@gmail.com', 'Ivan', 'Frame 1159.png'),
(2, 'supplier2@gmail.com', 'default', 'Frame 1159.png'),
(3, 'supplier2@gmail.com', 'default', 'offer.png');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `uploader` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `context` text DEFAULT NULL,
  `date_to_show` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `image`, `uploader`, `date`, `heading`, `context`, `date_to_show`) VALUES
(11, 'up1.jpg', NULL, '2025-01-23', ' ', ' BRING YOUR JOY TO THE FULLEST CAPTURE THE FUN WITH US HERE IN LENSFOLIOHUB', NULL),
(12, 'up2.jpg', NULL, '2025-01-10', 'WEDDING SHOOT?', 'looking for a photograoher for your wedding? we might help you with that.', NULL),
(13, 'up3.jpg', NULL, '2025-01-24', 'looking for photographers?', 'in todays marketplace, people, events and businesses need photos and videos more than ever before so Lensfoliohub displays your work to everyone.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_uploader` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','completed','cancelled','update','Accepted','declined') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `supplier_email` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_email`, `supplier_email`, `rating`, `review`, `created_at`, `name`) VALUES
(16, 'client@gmail.com', 'supplier1@gmail.com', 5, 'dasdasdsa', '2025-01-10 03:55:24', 'Supplier 1'),
(17, 'client@gmail.com', 'supplier@gmail.com', 4, 'satisfied\r\n', '2025-01-10 06:14:32', 'Supplier2'),
(18, 'client@gmail.com', 'supplier2@gmail.com', 5, 'nice', '2025-01-10 09:08:37', 'client'),
(19, 'client@gmail.com', 'supplier@gmail.com', 5, 'satisfied\r\n', '2025-01-10 06:14:32', 'Supplier2'),
(20, 'bucodancollet@gmail.com', 'supplier1@gmail.com', 5, 'nice\r\n', '2025-01-14 03:54:12', 'Ancollet Bucod');

-- --------------------------------------------------------

--
-- Table structure for table `recovery_requests`
--

CREATE TABLE `recovery_requests` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `recovery_reason` text NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('disabled','active') DEFAULT 'disabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recovery_requests`
--

INSERT INTO `recovery_requests` (`id`, `email`, `recovery_reason`, `request_date`, `status`) VALUES
(5, 'supplier1@gmail.com', 'okay', '2025-01-10 07:15:33', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `reporter_name` varchar(255) NOT NULL,
  `reporter_email` varchar(255) NOT NULL,
  `reported_name` varchar(255) NOT NULL,
  `reported_email` varchar(255) NOT NULL,
  `role` enum('customer','supplier') NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `disable_status` int(11) DEFAULT 1,
  `warning_reason` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `reporter_name`, `reporter_email`, `reported_name`, `reported_email`, `role`, `reason`, `created_at`, `disable_status`, `warning_reason`) VALUES
(16, 'client', 'client@gmail.com', 'Supplier1', 'supplier1@gmail.com', 'customer', 'not pro', '2025-01-10 06:55:26', 1, 'reported');

-- --------------------------------------------------------

--
-- Table structure for table `snapfeed`
--

CREATE TABLE `snapfeed` (
  `id` int(11) NOT NULL,
  `card_img` varchar(255) DEFAULT NULL,
  `card_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `img_title` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hearts_count` int(11) DEFAULT 0,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `snapfeed`
--

INSERT INTO `snapfeed` (`id`, `card_img`, `card_text`, `created_at`, `img_title`, `email`, `hearts_count`, `comments`) VALUES
(38, 'u1.jpg', 'Hello HI', '2025-01-10 20:44:38', 'Diana', 'supplier1@gmail.com', -1, NULL),
(40, 'u3.jpg', 'Hello HI', '2025-01-10 20:44:38', 'Diana', 'supplier1@gmail.com', -1, NULL),
(41, 'u4.jpg', 'Hello HI', '2025-01-10 20:44:38', 'Diana', 'supplier1@gmail.com', 0, NULL),
(42, 'u5.jpg', 'Hello HI', '2025-01-10 20:44:38', 'Diana', 'supplier1@gmail.com', -1, NULL),
(43, 'u6.jpg', 'Hello HI', '2025-01-10 20:44:38', 'Diana', 'supplier2@gmail.com', -1, NULL),
(44, 'u7.jpg', 'Hello HI', '2025-01-10 20:44:38', 'Diana', 'supplier2@gmail.com', 1, NULL),
(45, 'u8.jpg', 'Hello HI', '2025-01-10 20:44:38', 'Diana', 'supplier2@gmail.com', 0, NULL),
(46, 'u9.jpg', 'Hello HI', '2025-01-10 20:44:38', 'Diana', 'supplier2@gmail.com', 4, NULL),
(51, '472521528_452993697876142_6017095496710765049_n.jpg', 'portrait', '2025-01-10 07:27:41', 'ARAYAT', 'supplier3@gmail.com', 3, NULL),
(52, 'Rectangle 53.png', 'tesst tet teste tets', '2025-01-11 23:32:15', 'test', 'supplier1@gmail.com', 0, NULL),
(54, 'DSC08181.JPG', 'フレンドリー', '2025-01-13 13:14:04', 'PORTRAIT', 'tapaocholo390@gmail.com', 0, NULL),
(55, 'DSC_0096.jpg', 'ANONG GENTLE GENTLE?', '2025-01-14 11:58:32', 'imus-plaza.jpg', 'cmalicdon14@gmail.com', 0, NULL),
(56, '456425821_374542075693538_738090013815818665_n.jpg', 'just look at her', '2025-01-14 12:10:11', 'My HD photo', 'rckbrnl22@gmail.com', 0, NULL),
(57, '473670359_922109386677226_1686913724325748102_n.jpg', 'ganito pala sa maynila maingay,mausok, at puno ng pangarap', '2025-01-14 12:24:50', 'Manila nights', 'rckbrnl22@gmail.com', 0, NULL),
(58, 'offer.png', 'ADSADSA', '2025-01-24 15:16:45', 'dsaDS', 'supplier2@gmail.com', 0, NULL),
(59, '2025-01-22 23-28-24.mp4', 'DASDSA', '2025-01-24 15:17:11', 'DASDSADSA', 'supplier2@gmail.com', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sub_news`
--

CREATE TABLE `sub_news` (
  `id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_news`
--

INSERT INTO `sub_news` (`id`, `img`, `title`, `date`) VALUES
(1, 'news2.jpg', 'Photography is a passion, but also an art that can captivates someone\'s heart.', '2024-11-01'),
(2, 'news2.jpg', 'Breaking Sub-News 2', '2024-11-15'),
(3, 'news3.jfif', 'Photography is not just images, it also holds memories you never want to forget.', '2024-11-29'),
(4, 'MSI_MPG.jpg', 'I am here to offer you a shots you never want to missed.', '2024-11-30'),
(5, 'Rectangle 53.png', 'Hey', '2025-01-22'),
(6, 'Rectangle 6.png', 'TEST NOW!', '2025-01-11');

-- --------------------------------------------------------

--
-- Table structure for table `template1`
--

CREATE TABLE `template1` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `template` varchar(255) NOT NULL DEFAULT 'grid',
  `gallery_name` varchar(255) DEFAULT 'default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `template1`
--

INSERT INTO `template1` (`id`, `email`, `profile_image`, `template`, `gallery_name`) VALUES
(22, 'supplier1@gmail.com', 'location.png', 'carousel', 'default'),
(23, 'supplier2@gmail.com', 'sneaker.jpg', 'grid', 'default'),
(24, 'supplier2@gmail.com', 'offer.png', 'grid', 'Ivan'),
(25, 'supplier2@gmail.com', 'image 7.png', 'grid', 'fsddffds');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` enum('customer','supplier','admin') NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_img` varchar(255) DEFAULT 'profile.png',
  `about_me` text DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `day_available` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `disable_status` tinyint(4) NOT NULL DEFAULT 1,
  `address` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL,
  `social_link` varchar(255) DEFAULT NULL,
  `years_in_profession` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `test_password` varchar(255) NOT NULL,
  `verification_code` varchar(255) DEFAULT NULL,
  `verify_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `name`, `email`, `password`, `created_at`, `updated_at`, `profile_img`, `about_me`, `profession`, `location`, `age`, `day_available`, `is_active`, `last_login`, `disable_status`, `address`, `birthday`, `social_link`, `years_in_profession`, `username`, `test_password`, `verification_code`, `verify_status`) VALUES
(26, 'admin', 'admin', 'admin@gmail.com', '$2y$10$opUH24g9VfMnpi1qYe634u2OSYi7auE9AP2kFb1maZVZrx8ex5gMy', '2024-11-30 21:26:12', '2025-01-27 09:20:42', 'profile.jpg', NULL, NULL, NULL, NULL, NULL, 0, '2025-01-27 09:20:42', 1, '', NULL, '', NULL, '', '', NULL, 1),
(46, 'supplier', 'Supplier1', 'supplier1@gmail.com', '$2y$10$TETEDH4Y7wsPqvINufFNEec9BUuWWaH/QVKKa7EkxmfMsaUR0YwAm', '2025-01-10 20:43:11', '2025-01-27 08:57:48', 'profile_6780c4f7d44c86.65992713.jpg', NULL, 'videographer', NULL, NULL, ',2025-01-14', 1, '2025-01-27 08:57:48', 1, 'blk 4 lot 23', '2025-01-10', 'https://www.facebook.com', 2024, '', '', NULL, 1),
(50, 'customer', 'client', 'client@gmail.com', '$2y$10$Uqg3zDXUZPwc66eQEvzQX.zZthau4I71krUY7m4upP2S0plXGHai6', '2025-01-10 21:06:38', '2025-01-27 09:32:38', 'profile_6780cf10964bd9.61209597.jpg', NULL, NULL, NULL, NULL, NULL, 0, '2025-01-27 09:32:38', 1, 'blk 4 lot 23', '2025-01-15', 'https://www.facebook.com/', NULL, '', '', NULL, 1),
(51, 'supplier', 'SUPPLIER2', 'supplier2@gmail.com', '$2y$10$TETEDH4Y7wsPqvINufFNEec9BUuWWaH/QVKKa7EkxmfMsaUR0YwAm', '2025-01-10 06:22:58', '2025-01-27 09:21:46', 'profile_6790957e1bef62.81570936.jpg', NULL, 'photographer', NULL, NULL, ',2025-01-01,2025-01-02,2025-01-03', 1, '2025-01-27 09:21:46', 1, 'tanza', '2002-07-05', 'https://www.facebook.com/collet.kulet', 2, '', '', NULL, 1),
(91, 'supplier', 'Diana', 'bustamantedianamaea@cvsu.edu.ph', '$2y$10$5f3N06R6H7UO9rmIVaLZ/.Q0O.QnBQxvnSrMfN0NMPE5zjOmE4sam', '2025-01-11 17:51:46', '2025-01-11 17:51:46', 'profile.png', NULL, 'photographer', NULL, NULL, NULL, 0, '2025-01-11 17:51:46', 1, 'tanza', '2003-05-07', 'https://m.facebook.com/dianamae.bustamante.3?', 2, '', '', '5793', 0),
(94, 'customer', 'Ancollet Bucod', 'bucodancollet@gmail.com', '$2y$10$mMnLsV1faoFNbL28QBH78u2EJ1reLYTWBx5d.eBvbG6eZNvCq346G', '2025-01-13 11:51:50', '2025-01-15 10:44:58', 'profile.png', NULL, NULL, NULL, NULL, NULL, 0, '2025-01-15 10:44:58', 1, 'tanza', '2001-06-30', NULL, NULL, '', '', '1326', 1),
(95, 'supplier', 'collet', 'nacoomercadejas@gmail.com', '$2y$10$yVjznHKKmorgpoMMZLV7Z.jZuYTtNgwwywjWtvIAqYye/M/kbaJbS', '2025-01-13 12:37:32', '2025-01-13 14:40:34', 'profile.png', NULL, 'photographer', NULL, NULL, NULL, 1, '2025-01-13 14:40:34', 1, 'tanza', '2003-03-06', 'https://www.facebook.com/collet.kulet', 4, '', '', '1301', 1),
(98, 'customer', 'Alvin', 'amacatoalvin@gmail.com', '$2y$10$/HvtcgSJBtfh50k3j0oFzegXHJHOTjsEVtjaa8MfU8gGdcfHlL1tC', '2025-01-14 09:00:59', '2025-01-14 09:00:59', 'profile.png', NULL, NULL, NULL, NULL, NULL, 0, '2025-01-14 09:00:59', 1, 'Talahiban 1st San Juan Batangas', '1974-11-07', NULL, NULL, '', '', '5059', 0),
(101, 'supplier', 'dahhyunnee', 'cmalicdon14@gmail.com', '$2y$10$4fEJnU9SJUM3R0LZMJdrBuvRXpHzTka43qu2kN7ran577fcb4TysG', '2025-01-14 11:34:16', '2025-01-14 12:09:05', 'profile_678653e1bdfd51.03915066.png', NULL, 'photographer', NULL, NULL, ',2025-01-14', 0, '2025-01-14 12:09:05', 1, '134 Anabu 1-A Imus City Cavite', '2002-04-17', 'https://www.facebook.com/clinton.malicdon.2024/', 3, '', '', '5667', 1),
(102, 'supplier', 'Gello Tim', 'moodvibes683@gmail.com', '$2y$10$SLxAhpuGTv9EHL/g6UIuZ.bXUZ3oXk..Y5rQC9QfRJbyyA9pqd5ti', '2025-01-14 11:58:51', '2025-01-14 11:59:47', 'profile.png', NULL, 'photographer', NULL, NULL, NULL, 0, '2025-01-14 11:59:47', 1, 'Amaya 2 Tanza, Cavite', '2002-03-17', 'https://www.facebook.com/gelo.timcang.7?mibextid=ZbWKwL', 1, '', '', '2503', 1),
(103, 'supplier', 'erich blu', 'rckbrnl22@gmail.com', '$2y$10$pcWlgLDBBWGe6URVyXhvDe9oDRe0VoWebkMOwi2CiwtfJtwyx9G1S', '2025-01-14 12:02:36', '2025-01-14 12:21:17', 'profile_6786536dc61ff8.28672114.jpg', NULL, 'photographer', NULL, NULL, ',2025-01-01,2025-01-02,2025-01-03,2025-01-04,2025-01-11,2025-01-09,2025-01-10,2025-02-03,2025-02-02,2025-02-08,2025-01-17,2025-02-06', 0, '2025-01-14 12:21:17', 1, 'tanza', '2002-02-08', 'https://www.facebook.com/collet.kulet', 3, '', '', '6290', 1),
(104, 'supplier', 'ivan', 'supplierej@gmail.com', '$2y$10$kTBfb/IQ4g/f5en8EtQN5.A2hCECBnGIgVRtlHk9Qdpr5/UUPNBY6', '2025-01-21 16:09:00', '2025-01-21 16:09:20', 'profile.png', NULL, 'photographer', NULL, NULL, NULL, 0, '2025-01-21 16:09:20', 1, 'dasdsadsa', '2025-02-08', 'https://www.facebook.com/', 23, '', '', '3566', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_hearts`
--

CREATE TABLE `user_hearts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `card_img` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_hearts`
--

INSERT INTO `user_hearts` (`id`, `email`, `card_img`, `created_at`) VALUES
(9, 'user@example.com', 'image1.jpg', '2025-01-11 22:39:07'),
(13, 'client@gmail.com', 'u8.jpg', '2025-01-11 22:44:57'),
(23, 'client@gmail.com', 'u7.jpg', '2025-01-11 22:58:50'),
(25, 'client@gmail.com', 'u4.jpg', '2025-01-12 02:48:13'),
(26, 'bucodancollet@gmail.com', 'u9.jpg', '2025-01-14 04:10:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_me`
--
ALTER TABLE `about_me`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recovery_requests`
--
ALTER TABLE `recovery_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `snapfeed`
--
ALTER TABLE `snapfeed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_news`
--
ALTER TABLE `sub_news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template1`
--
ALTER TABLE `template1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_hearts`
--
ALTER TABLE `user_hearts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`,`card_img`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_me`
--
ALTER TABLE `about_me`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `recovery_requests`
--
ALTER TABLE `recovery_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `snapfeed`
--
ALTER TABLE `snapfeed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `sub_news`
--
ALTER TABLE `sub_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `template1`
--
ALTER TABLE `template1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `user_hearts`
--
ALTER TABLE `user_hearts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD CONSTRAINT `gallery_images_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
