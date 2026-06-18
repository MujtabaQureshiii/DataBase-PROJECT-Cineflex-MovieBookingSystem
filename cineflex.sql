-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2026 at 05:38 PM
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
-- Database: `cineflex`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `show_id` int(11) DEFAULT NULL,
  `seats` varchar(50) DEFAULT NULL,
  `seat_class` varchar(50) DEFAULT NULL,
  `booking_date` datetime DEFAULT current_timestamp(),
  `status` varchar(30) DEFAULT 'confirmed',
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid',
  `seat_types` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `show_id`, `seats`, `seat_class`, `booking_date`, `status`, `payment_status`, `seat_types`) VALUES
(36, 4, 29, 'A2,A4,A3', 'Box', '2026-05-10 14:14:07', 'confirmed', 'paid', '{\"A2\":{\"seat\":\"A2\",\"type\":\"Adult\"},\"A4\":{\"seat\":\"A4\",\"type\":\"Adult\"},\"A3\":{\"seat\":\"A3\",\"type\":\"Kid\"}}'),
(37, 4, 29, 'A9,A10', 'Platinum', '2026-05-10 14:18:51', 'confirmed', 'paid', '{\"A9\":{\"seat\":\"A9\",\"type\":\"Adult\"},\"A10\":{\"seat\":\"A10\",\"type\":\"Adult\"}}'),
(38, 5, 29, 'D8,D9,D10,D11', 'Box', '2026-05-10 19:33:30', 'confirmed', 'paid', '{\"D8\":{\"seat\":\"D8\",\"type\":\"Adult\"},\"D9\":{\"seat\":\"D9\",\"type\":\"Adult\"},\"D10\":{\"seat\":\"D10\",\"type\":\"Adult\"},\"D11\":{\"seat\":\"D11\",\"type\":\"Adult\"}}'),
(39, 4, 30, 'A5,A6', 'Platinum', '2026-05-10 19:51:18', 'confirmed', 'paid', '{\"A5\":{\"seat\":\"A5\",\"type\":\"Adult\"},\"A6\":{\"seat\":\"A6\",\"type\":\"Adult\"}}'),
(40, 4, 36, 'A7,A8,A9', 'Gold', '2026-05-10 21:44:52', 'confirmed', 'paid', '{\"A7\":{\"seat\":\"A7\",\"type\":\"Adult\"},\"A8\":{\"seat\":\"A8\",\"type\":\"Kid\"},\"A9\":{\"seat\":\"A9\",\"type\":\"Adult\"}}'),
(41, 4, 37, 'C7,C8,C9,C10,C11', 'Box', '2026-05-11 08:15:14', 'confirmed', 'paid', '{\"C7\":{\"seat\":\"C7\",\"type\":\"Adult\"},\"C8\":{\"seat\":\"C8\",\"type\":\"Adult\"},\"C9\":{\"seat\":\"C9\",\"type\":\"Kid\"},\"C10\":{\"seat\":\"C10\",\"type\":\"Adult\"},\"C11\":{\"seat\":\"C11\",\"type\":\"Adult\"}}'),
(42, 4, 29, 'C5,C6,C7', 'Box', '2026-05-11 08:26:20', 'confirmed', 'paid', '{\"C5\":{\"seat\":\"C5\",\"type\":\"Adult\"},\"C6\":{\"seat\":\"C6\",\"type\":\"Kid\"},\"C7\":{\"seat\":\"C7\",\"type\":\"Adult\"}}'),
(43, 4, 29, 'C9,C10,C11', 'Gold', '2026-05-11 08:41:53', 'confirmed', 'paid', '{\"C9\":{\"seat\":\"C9\",\"type\":\"Adult\"},\"C10\":{\"seat\":\"C10\",\"type\":\"Adult\"},\"C11\":{\"seat\":\"C11\",\"type\":\"Adult\"}}');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `duration` varchar(30) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `trailer` varchar(255) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `is_trending` tinyint(1) DEFAULT 0,
  `is_nowshowing` tinyint(1) DEFAULT 0,
  `is_toprated` tinyint(1) DEFAULT 0,
  `status` varchar(20) DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `genre`, `duration`, `poster`, `trailer`, `rating`, `is_trending`, `is_nowshowing`, `is_toprated`, `status`, `created_at`) VALUES
(7, 'Lootera', 'Set in 1950s India, Lootera is a romantic drama about an aristocratic girl, Pakhi, and a mysterious young archaeologist, Varun. Their love story begins in a quiet Bengal village but takes a tragic turn when Varun disappears, leaving behind betrayal and unanswered questions. Years later, fate reunites them under unexpected and painful circumstances. As truths unfold, their story becomes one of love, loss, and redemption.', 'Romantic', '136', 'uploads/posters/poster_1753259834_1200.jpg', 'uploads/trailers/trailer_1753259834_8114.mp4', 7.3, 1, 0, 0, 'active', '2025-07-23 13:37:14'),
(8, 'Darbar', '\"Darbar\" is a 2020 Indian Tamil-language action thriller film where Rajinikanth plays Aaditya Arunachalam, a police commissioner in Mumbai tasked with curbing drug trafficking and prostitution. He uncovers a larger conspiracy involving an international drug lord, leading him to pursue his own agenda. The film is directed by A.R. Murugadoss and produced by Lyca Productions.', 'Action', '160', 'uploads/posters/poster_1753260337_5960.jpg', 'uploads/trailers/trailer_1753260337_9136.mp4', 5.9, 1, 1, 0, 'active', '2025-07-23 13:45:37'),
(9, 'Pathan', 'Pathaan is a 2023 Indian Hindi-language action thriller film about an exiled Indian RAW agent who must team up with an ISI agent to stop a former RAW agent from unleashing a deadly virus upon India. The film stars Shah Rukh Khan, Deepika Padukone, and John Abraham. It is the fourth installment in the YRF Spy Universe.', 'Action', '146 minutes', 'uploads/posters/poster_1753270709_1797.jpg', 'uploads/trailers/trailer_1753270709_6480.mp4', 5.8, 1, 1, 1, 'active', '2025-07-23 16:38:29'),
(11, 'shang chi', '\"Shang-Chi and the Legend of the Ten Rings\" is a 2021 Marvel Cinematic Universe film that follows Shang-Chi, a martial arts master, as he confronts his past and his powerful father, the leader of the Ten Rings organization. The story delves into themes of identity, family, and confronting one\'s past, while also introducing audiences to Chinese mythology and culture within the MCU.', 'Action', '132 minutes', 'uploads/posters/poster_1753272386_3903.jpg', 'uploads/trailers/trailer_1753272386_4303.mp4', 7.3, 1, 0, 1, 'active', '2025-07-23 17:06:26'),
(12, 'black widow', '\"Black Widow\" is a 2021 Marvel film that serves as an origin story and an epilogue to Natasha Romanoff\'s character within the Marvel Cinematic Universe. Set after \"Captain America: Civil War,\" the movie sees Black Widow on the run from the law and forced to confront her past as a Russian spy. She reunites with her estranged family, who were also former spies, as they work together to take down a dangerous organization called the Red Room. The film explores themes of family, free will, and Natasha\'s journey to reconcile with her past.', 'Action/Sci-fi', '133 minutes', 'uploads/posters/poster_1753273203_1162.jpg', 'uploads/trailers/trailer_1753273203_4446.mp4', 6.6, 0, 1, 0, 'active', '2025-07-23 17:20:03'),
(13, 'avengers endgame', '\"Avengers: Endgame\" is the epic conclusion to the Infinity Saga, where the remaining Avengers attempt to undo Thanos\'s devastating actions from \"Infinity War\". After Thanos\'s snap wiped out half of all life, the surviving heroes must find a way to restore balance to the universe and bring back their fallen comrades. The film sees the Avengers venturing through time, facing emotional challenges, and ultimately battling Thanos in a final, climactic showdown.', 'Action/Sci-fi', '182 minutes', 'uploads/posters/poster_1753273837_7032.jpg', 'uploads/trailers/trailer_1753273837_3556.mp4', 9, 1, 1, 1, 'active', '2025-07-23 17:30:37'),
(14, 'The Amazing Spider-Man', 'The Amazing Spider-Man (2012) is a reboot of the Spider-Man film series, focusing on Peter Parker\'s origin story as he navigates high school, first love with Gwen Stacy, and his newfound spider-powers. Peter\'s quest to understand his parents\' disappearance leads him to Oscorp and Dr. Curt Connors, whose transformation into the Lizard sets Peter on a collision course with a powerful enemy. The movie explores themes of responsibility, family, and the choices that shape a hero.', 'Action/Sci-fi', '136 minutes', 'uploads/posters/poster_1753274452_5093.jpg', 'uploads/trailers/trailer_1753274452_4501.mp4', 7.5, 1, 0, 1, 'active', '2025-07-23 17:40:52'),
(15, 'Black Panther', '\"Black Panther\" is a 2018 superhero film that follows T\'Challa, who, after the death of his father, returns to the isolated, technologically advanced African nation of Wakanda to take his rightful place as king and Black Panther. He must defend his nation from a powerful enemy, Erik Killmonger, who seeks to overthrow him and seize control of Wakanda\'s advanced technology. The film explores themes of leadership, isolationism, and the responsibility that comes with power, all while showcasing a strong, unapologetically Black superhero and cast.', 'Action/Sci-fi', '134 minutes', 'uploads/posters/poster_1753274933_7664.jpg', 'uploads/trailers/trailer_1753274933_4157.mp4', 7.3, 1, 1, 0, 'active', '2025-07-23 17:48:53'),
(16, 'Animal', '\"Animal\" is a 2023 Indian action-drama film about a man, Ranvijay \"Vijay\" Singh, who embarks on a violent quest for revenge after an assassination attempt on his powerful industrialist father. The film explores Vijay\'s troubled relationship with his father and his transformation into a vengeful figure driven by a need to protect his family.', 'Action', '201 minutes', 'uploads/posters/poster_1753275529_7786.jpg', 'uploads/trailers/trailer_1753275529_8665.mp4', 6.5, 1, 1, 0, 'active', '2025-07-23 17:58:49'),
(17, 'Avengers Infinity War', 'Avengers: Infinity War features the Avengers and their superhero allies uniting to battle the powerful Thanos, who seeks to collect all six Infinity Stones to inflict his twisted will on reality. The fate of the universe hangs in the balance as the heroes must make sacrifices to stop Thanos before he can wipe out half of all life.', 'Action/Sci-fi', '149 minutes', 'uploads/posters/poster_1753277118_2017.jpg', 'uploads/trailers/trailer_1753277118_8212.mp4', 8.4, 1, 1, 1, 'active', '2025-07-23 18:25:18'),
(18, 'Spider-Man: No Way Home', '\"Spider-Man: No Way Home\" explores the consequences of Peter Parker\'s identity being revealed to the world, as seen in the previous film. Desperate to fix things, Peter seeks Doctor Strange\'s help, but a spell goes wrong, causing villains from other universes who previously fought Spider-Man to cross over. Now, Peter must confront these formidable foes while also figuring out what it truly means to be Spider-Man.', 'Action/Sci-fi', '157 minutes', 'uploads/posters/poster_1753278185_1705.jpg', 'uploads/trailers/trailer_1753278185_2444.mp4', 8.2, 1, 1, 1, 'active', '2025-07-23 18:43:05'),
(19, 'Pushpa: The Rise', '\"Pushpa\" can refer to several things, but most prominently, it\'s the name of a popular Indian Telugu-language action drama film series, starring Allu Arjun as the titular character Pushpa Raj. The series follows Pushpa\'s rise through the ranks of a red sandalwood smuggling syndicate. The first film, \"Pushpa: The Rise,\" was a major success, and its sequel, \"Pushpa 2: The Rule,\" is highly anticipated. Beyond the films, \"Pushpa\" can also be a common Indian given name, and in Sanskrit, \"Pushpa\" (पुष्प) means \"flower\".', 'Action', '179 minutes', 'uploads/posters/poster_1753278751_6083.jpg', 'uploads/trailers/trailer_1753278751_6448.mp4', 7.6, 1, 1, 0, 'active', '2025-07-23 18:52:31'),
(20, 'Tiger 3', 'Tiger 3 is an Indian action thriller film where Tiger and Zoya, portrayed by Salman Khan and Katrina Kaif, must clear their names after being framed as traitors, while also saving their family and country. The plot involves a Pakistani terrorist named Aatish, played by Emraan Hashmi, who has a personal vendetta against Tiger. The film is the fifth installment in the YRF Spy Universe, following War and Pathaan.', 'Action', '155 minutes', 'uploads/posters/poster_1753279379_3896.jpg', 'uploads/trailers/trailer_1753279379_3161.mp4', 5.7, 1, 1, 1, 'active', '2025-07-23 19:02:59'),
(21, 'Jawan', '\"Jawan\" is a 2023 Indian Hindi-language action thriller film directed by Atlee and starring Shah Rukh Khan in a dual role. The film revolves around a prison warden who recruits inmates to commit crimes that expose corruption and injustice in India, ultimately leading to an unexpected reunion. It features Nayanthara and Vijay Sethupathi alongside Khan.', 'Action', '169 minutes', 'uploads/posters/poster_1753280055_2984.jpg', 'uploads/trailers/trailer_1753280055_9790.mp4', 8, 1, 1, 1, 'active', '2025-07-23 19:14:15'),
(22, '100 days of love', '\"100 Days of Love\" is a Malayalam romantic comedy-drama about Balan, a journalist, who falls for Sheela, a schoolmate he meets by chance after a breakup. Their paths cross again when Balan tries to return Sheela\'s lost camera, leading to a connection and a shared project for her parents\' anniversary. The film explores their evolving relationship, including Sheela\'s engagement and Balan\'s efforts to win her over.', 'Romantic', '119 minutes', 'uploads/posters/poster_1753280738_3991.jpg', 'uploads/trailers/trailer_1753280738_8530.mp4', 7.5, 1, 1, 0, 'active', '2025-07-23 19:25:38'),
(23, 'Shaitaan', 'Shaitaan is a 2024 supernatural thriller about a family whose weekend getaway turns into a nightmare when a stranger, using black magic, possesses their teenage daughter\'s body. The film explores themes of demonic possession, family love, and the lengths parents will go to protect their children. It\'s a remake of the Gujarati film Vash.', 'Horror', '132 minutes', 'uploads/posters/poster_1753284044_3345.jpg', 'uploads/trailers/trailer_1753284044_1981.mp4', 6.6, 1, 1, 1, 'active', '2025-07-23 20:20:44'),
(25, 'musafa', 'dh3gd3ku3r', 'fiction', '120', 'uploads/posters/poster_1778470207_5749.jpg', 'uploads/trailers/trailer_1778470207_6465.mp4', 9.1, 0, 0, 0, 'active', '2026-05-11 08:30:07');

-- --------------------------------------------------------

--
-- Table structure for table `movie_cast`
--

CREATE TABLE `movie_cast` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `role` varchar(80) DEFAULT NULL,
  `status` varchar(10) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie_cast`
--

INSERT INTO `movie_cast` (`id`, `movie_id`, `name`, `image`, `role`, `status`) VALUES
(1, 2, 'qqfqwrqrqrw', 'uploads/cast/cast_1753198936_2411.jfif', '', 'active'),
(2, 6, 'Ranvi', 'uploads/cast/cast_1753259561_8768.jpg', 'Lead actor', 'active'),
(3, 6, 'Shirin', 'uploads/cast/cast_1753259561_1753.jpg', 'Supporting Actress', 'active'),
(4, 6, 'Sonakhsi', 'uploads/cast/cast_1753259561_1179.jpg', 'lead actress', 'active'),
(5, 6, 'Vikramaditya', 'uploads/cast/cast_1753259561_2107.jpg', 'Supporting Address', 'active'),
(6, 6, 'Vikrant', 'uploads/cast/cast_1753259561_1646.jpg', 'Director not cast', 'active'),
(7, 7, 'Ranvi', 'uploads/cast/cast_1753259834_1925.jpg', 'Lead actor', 'active'),
(8, 7, 'Shirin', 'uploads/cast/cast_1753259834_9044.jpg', 'Supporting Actress', 'active'),
(9, 7, 'Sonakhsi', 'uploads/cast/cast_1753259834_3475.jpg', 'lead actress', 'active'),
(10, 7, 'Vikramaditya', 'uploads/cast/cast_1753259834_3273.jpg', 'Supporting Address', 'active'),
(11, 7, 'Vikrant', 'uploads/cast/cast_1753259834_4467.jpg', 'Director not cast', 'active'),
(12, 8, 'rajnikanth', 'uploads/cast/cast_1753260337_4905.jpg', 'Lead character', 'active'),
(13, 8, 'Nayanthara', 'uploads/cast/cast_1753260337_8163.jpg', 'Female lead', 'active'),
(14, 8, 'Suniel Shetty', 'uploads/cast/cast_1753260337_9710.jpg', 'Major supporting antagonist', 'active'),
(15, 8, 'Nivetha Thomas', 'uploads/cast/cast_1753260337_1646.jpg', 'Supporting', 'active'),
(16, 8, 'Yogi Babu', 'uploads/cast/cast_1753260337_4774.jpg', 'Supporting sidekick role.', 'active'),
(17, 9, 'Sharukh khan', 'uploads/cast/cast_1753270709_8676.jpg', 'lead actor', 'active'),
(18, 9, 'Deepika Padokun', 'uploads/cast/cast_1753270709_2304.jpg', 'lead actress', 'active'),
(19, 9, 'Jhon Abhrhahim', 'uploads/cast/cast_1753270709_5575.jpg', 'Villan', 'active'),
(20, 9, 'Dimple Kapadia', 'uploads/cast/cast_1753270709_1896.jpg', 'Supporting Character', 'active'),
(21, 9, 'Ashutosh Rana', 'uploads/cast/cast_1753270709_7028.jpg', 'Supporting Character', 'active'),
(22, 10, 'Tony Leung Chiu-wai', 'uploads/cast/cast_1753272143_4351.jpg', '', 'active'),
(23, 10, 'Awkwafina', 'uploads/cast/cast_1753272143_4213.jpg', '', 'active'),
(24, 10, 'Fala Chen', 'uploads/cast/cast_1753272143_4356.jpg', '', 'active'),
(25, 10, 'Michelle Yeoh', 'uploads/cast/cast_1753272143_9041.jpg', '', 'active'),
(26, 11, 'Simu Liu', 'uploads/cast/cast_1753272386_1825.jpg', 'Male Lead Actor (Hero)', 'active'),
(27, 11, 'Tony Leung Chiu-wai', 'uploads/cast/cast_1753272386_1347.jpg', 'Main Villain', 'active'),
(28, 11, 'Awkwafina', 'uploads/cast/cast_1753272386_8288.jpg', 'Supporting Character', 'active'),
(29, 11, 'Fala Chen', 'uploads/cast/cast_1753272386_4809.jpg', 'Supporting Character', 'active'),
(30, 11, 'Michelle Yeoh', 'uploads/cast/cast_1753272386_8300.jpg', 'Supporting Character', 'active'),
(31, 12, 'Scarlett Johansson', 'uploads/cast/cast_1753273203_4604.jpg', 'Main Lead Actor', 'active'),
(32, 12, 'Rachel Weisz', 'uploads/cast/cast_1753273203_6687.jpg', 'Supporting Character', 'active'),
(33, 12, 'Olga Kurylenko', 'uploads/cast/cast_1753273203_5382.jpg', 'Villain', 'active'),
(34, 12, 'Florence Pugh', 'uploads/cast/cast_1753273203_7744.jpg', 'Female Lead Supporting', 'active'),
(35, 12, 'David Harbour', 'uploads/cast/cast_1753273203_2307.jpg', 'Supporting Character', 'active'),
(36, 13, 'Tony Stark', 'uploads/cast/cast_1753273837_4269.jpg', 'Main Lead Hero', 'active'),
(37, 13, 'Steve Rogers', 'uploads/cast/cast_1753273837_3663.jpg', 'Co-Lead Hero', 'active'),
(38, 13, 'Chris Hemsworth', 'uploads/cast/cast_1753273837_1635.jpg', 'Core Avenger / God of Thunder', 'active'),
(39, 13, 'Mark Ruffalo', 'uploads/cast/cast_1753273837_4133.jpg', 'Science & Strength Support', 'active'),
(40, 13, 'Scarlett Johansson', 'uploads/cast/cast_1753273837_2061.jpg', 'Sacrificial Heroine', 'active'),
(41, 14, 'Andrew Garfield', 'uploads/cast/cast_1753274452_5090.jpg', 'Main Lead Hero', 'active'),
(42, 14, 'Emma Stone', 'uploads/cast/cast_1753274452_5718.jpg', 'Female Lead', 'active'),
(43, 14, 'Rhys Ifans', 'uploads/cast/cast_1753274452_8754.jpg', 'Main Villain', 'active'),
(44, 14, 'Denis Leary', 'uploads/cast/cast_1753274452_7320.jpg', 'Supporting Character', 'active'),
(45, 14, 'Martin Sheen', 'uploads/cast/cast_1753274452_7882.jpg', 'Supporting Character', 'active'),
(46, 15, 'Chadwick Boseman', 'uploads/cast/cast_1753274933_7978.jpg', 'Main Lead Hero', 'active'),
(47, 15, 'Michael B. Jordan', 'uploads/cast/cast_1753274933_3124.jpg', 'Main Villain', 'active'),
(48, 15, 'Lupita Nyong’o', 'uploads/cast/cast_1753274933_7296.jpg', 'Female Lead', 'active'),
(49, 15, 'Danai Gurira', 'uploads/cast/cast_1753274933_9026.jpg', 'Supporting Character', 'active'),
(50, 15, 'Letitia Wright', 'uploads/cast/cast_1753274933_7132.jpg', 'Supporting Character', 'active'),
(51, 16, 'Ranbir Kapoor', 'uploads/cast/cast_1753275529_9727.jpg', 'Main Lead Hero', 'active'),
(52, 16, 'Anil Kapoor', 'uploads/cast/cast_1753275529_2699.jpg', 'Supporting Character', 'active'),
(53, 16, 'Rashmika Mandanna', 'uploads/cast/cast_1753275529_8254.jpg', 'Female Lead', 'active'),
(54, 16, 'Bobby Deol', 'uploads/cast/cast_1753275529_6378.jpg', 'Main Villain', 'active'),
(55, 16, 'Tripti Dimri', 'uploads/cast/cast_1753275529_7781.jpg', 'Special Role', 'active'),
(56, 17, 'Josh Brolin', 'uploads/cast/cast_1753277118_6314.jpg', 'Main Villain', 'active'),
(57, 17, 'Tony Stark', 'uploads/cast/cast_1753277118_5328.jpg', 'Male Lead Hero', 'active'),
(58, 17, 'Benedict Cumberbatch', 'uploads/cast/cast_1753277118_2444.jpg', 'Key Hero', 'active'),
(59, 17, 'Chris Hemsworth', 'uploads/cast/cast_1753277118_2903.jpg', 'Core Hero', 'active'),
(60, 17, 'Chris Pratt', 'uploads/cast/cast_1753277118_1612.jpg', 'Supporting Lead', 'active'),
(61, 18, 'Tom Holland', 'uploads/cast/cast_1753278185_8011.jpg', 'Main Lead Hero', 'active'),
(62, 18, 'Zendaya', 'uploads/cast/cast_1753278185_8619.jpg', 'Female Lead', 'active'),
(63, 18, 'Andrew Garfield', 'uploads/cast/cast_1753278185_3650.jpg', 'Supporting Lead Hero', 'active'),
(64, 18, 'Tobey Maguire', 'uploads/cast/cast_1753278185_6631.jpg', 'Supporting Lead Hero', 'active'),
(65, 18, 'Willem Dafoe', 'uploads/cast/cast_1753278185_2931.jpg', 'Main Villain', 'active'),
(66, 19, 'Allu Arjun', 'uploads/cast/cast_1753278751_7557.jpg', 'Main Lead Hero', 'active'),
(67, 19, 'Rashmika Mandanna', 'uploads/cast/cast_1753278751_8320.jpg', 'Female Lead', 'active'),
(68, 19, 'Sunil', 'uploads/cast/cast_1753278751_4330.jpg', 'Main Villain', 'active'),
(69, 19, 'Fahadh Faasil', 'uploads/cast/cast_1753278751_1577.jpg', 'Second Villain', 'active'),
(70, 19, 'Ajay Ghosh', 'uploads/cast/cast_1753278751_1892.jpg', 'Supporting Villain', 'active'),
(71, 20, 'Salman Khan', 'uploads/cast/cast_1753279379_7840.jpg', 'Main Lead Hero', 'active'),
(72, 20, 'Katrina Kaif', 'uploads/cast/cast_1753279379_6332.jpg', 'Female Lead', 'active'),
(73, 20, 'Emraan Hashmi', 'uploads/cast/cast_1753279379_6474.jpg', 'Main Villain', 'active'),
(74, 20, 'Revathy', 'uploads/cast/cast_1753279379_8322.jpg', 'RAW Chief', 'active'),
(75, 20, 'Shah Rukh Khan', 'uploads/cast/cast_1753279379_7702.jpg', 'Special Appearance / Supportive Hero', 'active'),
(76, 21, 'Shah Rukh Khan (Azad )', 'uploads/cast/cast_1753280055_8093.jpg', 'Double Roles (Lead)', 'active'),
(77, 21, 'Shah Rukh Khan (Vikram Rathore)', 'uploads/cast/cast_1753280055_7628.jpg', 'Double Roles (Lead)', 'active'),
(78, 21, 'Nayanthara', 'uploads/cast/cast_1753280055_3700.jpg', '', 'active'),
(79, 21, 'Vijay Sethupathi', 'uploads/cast/cast_1753280055_2034.jpg', '', 'active'),
(80, 21, 'Deepika Padukone', 'uploads/cast/cast_1753280055_6766.jpg', '', 'active'),
(81, 22, 'Dulquer Salmaan', 'uploads/cast/cast_1753280738_4290.jpg', 'Male Lead', 'active'),
(82, 22, 'Nithya Menen', 'uploads/cast/cast_1753280738_6915.jpg', 'Female Lead', 'active'),
(83, 22, 'Sekhar Menon', 'uploads/cast/cast_1753280738_4007.jpg', 'Supporting Role', 'active'),
(84, 22, 'Aju Varghese', 'uploads/cast/cast_1753280738_2497.jpg', 'Comic Relief / Side Character', 'active'),
(85, 22, 'V.K. Prakash', 'uploads/cast/cast_1753280738_2894.jpg', 'Supporting Character', 'active'),
(86, 23, 'Ajay Devgn', 'uploads/cast/cast_1753284044_9063.jpg', 'Main Lead', 'active'),
(87, 23, 'R. Madhavan', 'uploads/cast/cast_1753284044_2458.jpg', 'Main Villain', 'active'),
(88, 23, 'Janki Bodiwala', 'uploads/cast/cast_1753284044_1948.jpg', 'Daughter / Victim', 'active'),
(89, 23, 'Jyothika', 'uploads/cast/cast_1753284044_8931.jpg', 'Mother / Supporting Lead', 'active'),
(90, 23, 'Anngad Raaj', 'uploads/cast/cast_1753284044_8811.jpg', 'Younger Brother / Supporting', 'active'),
(91, 24, 'abc', 'uploads/cast/cast_1778469550_4565.jpg', 'lead', 'active'),
(92, 24, 'abc', 'uploads/cast/cast_1778469550_9706.jpg', 'hero', 'active'),
(93, 24, 'bwkjdbw', 'uploads/cast/cast_1778469550_8016.jpg', 'idhwi', 'active'),
(94, 25, 'gjhvd', 'uploads/cast/cast_1778470207_3509.jpg', 'lead', 'active'),
(95, 25, 'jhvdjhed', 'uploads/cast/cast_1778470207_1134.jpg', 'hero', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `rating` int(11) DEFAULT 1,
  `review` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` varchar(12) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `movie_id`, `user_id`, `name`, `rating`, `review`, `created_at`, `status`) VALUES
(1, 2, 3, 'Mujtaba', 4, 'very achi movie', '2026-05-10 20:20:47', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `shows`
--

CREATE TABLE `shows` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `theater_id` int(11) DEFAULT NULL,
  `show_time` datetime DEFAULT NULL,
  `seats` int(11) DEFAULT NULL,
  `price` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shows`
--

INSERT INTO `shows` (`id`, `movie_id`, `theater_id`, `show_time`, `seats`, `price`) VALUES
(29, 13, 3, '2026-05-11 15:00:00', 20, 1998),
(30, 22, 7, '2026-05-13 21:00:00', 50, 2100),
(31, 15, 6, '2026-05-15 03:00:00', 50, 2400),
(33, 8, 5, '2026-05-15 20:00:00', 55, 2200),
(34, 9, 3, '2026-05-18 21:10:00', 60, 2800),
(35, 14, 8, '2026-05-12 22:30:00', 43, 1900),
(36, 18, 7, '2026-05-13 00:00:00', 56, 2200),
(37, 22, 4, '2026-05-13 23:00:00', 50, 2000),
(38, 20, 6, '2026-05-14 23:30:00', 53, 2200),
(40, 25, 7, '2026-05-12 08:30:00', 59, 1500),
(41, 25, 7, '2026-05-12 08:30:00', 59, 1500);

-- --------------------------------------------------------

--
-- Table structure for table `theaters`
--

CREATE TABLE `theaters` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theaters`
--

INSERT INTO `theaters` (`id`, `name`, `city`, `address`) VALUES
(3, 'Nueplex Cinemas', 'Karachi', 'Clifton'),
(4, 'Cinepax Cinemas', 'Karachi', 'Multiple locations in Karachi (e.g., Safa Gold Mall, Karachi Marina Club)'),
(5, 'The Arena', 'Karachi', 'Gulshan-e-Iqbal, Karachi'),
(6, 'IMAX Karachi', 'Karachi', 'Near Mall of Karachi, Saddar'),
(7, 'Capri Cinema', 'Karachi', 'Saddar, Karachi'),
(8, 'Star Cinemas', 'Karachi', 'Multiple locations (e.g., Star City Mall, Karachi)');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(80) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `reg_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `reg_date`) VALUES
(1, 'Admin', 'admin@cineflex.com', '0000000000', 'admin123', 'admin', '2026-05-09 20:43:26'),
(4, 'Mujtaba Qureshi', 'mujtaba@gmail.com', '03302980973', 'mujtaba123', 'user', '2026-05-10 14:07:24'),
(5, 'Adil Saeed', 'adilsaeed@gmail.com', '03006789789', 'adil123', 'user', '2026-05-10 19:32:04'),
(6, 'Haseem Samo', 'haseemsamo@gmail.com', '0312345678987', 'haseem123', 'user', '2026-05-10 19:35:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `show_id` (`show_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movie_cast`
--
ALTER TABLE `movie_cast`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shows`
--
ALTER TABLE `shows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Indexes for table `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `movie_cast`
--
ALTER TABLE `movie_cast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shows`
--
ALTER TABLE `shows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `theaters`
--
ALTER TABLE `theaters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shows`
--
ALTER TABLE `shows`
  ADD CONSTRAINT `shows_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shows_ibfk_2` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
