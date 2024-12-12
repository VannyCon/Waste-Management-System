-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2024 at 10:19 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wmvms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$sYsR.oI4ALKjZDHBOj6B5Oj46epJCE3JSiotV8Bp5c/wPVzJNAcQO');

-- --------------------------------------------------------

--
-- Stand-in structure for view `dashboard`
-- (See below for the actual view)
--
CREATE TABLE `dashboard` (
`penalty_year` int(4)
,`penalty_month` int(2)
,`total_penalties` decimal(65,0)
,`total_penalty_paid` decimal(65,0)
,`total_penalty_unpaid` decimal(65,0)
,`total_unsolved` bigint(21)
,`total_solved` bigint(21)
,`total_report` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `enforcers`
--

CREATE TABLE `enforcers` (
  `id` int(11) UNSIGNED NOT NULL,
  `Fullname` varchar(100) NOT NULL,
  `email` varchar(59) NOT NULL,
  `Age` int(50) NOT NULL,
  `Gender` enum('Male','Female','','') NOT NULL,
  `Contact_number` int(50) NOT NULL,
  `Address` varchar(100) NOT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `Password` varchar(100) NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enforcers`
--

INSERT INTO `enforcers` (`id`, `Fullname`, `email`, `Age`, `Gender`, `Contact_number`, `Address`, `Username`, `Password`, `Created_at`) VALUES
(10, 'Amado', 'chelcontrevida@gmail.com', 56, 'Male', 214748364, 'oldsagay', 'amado', 'amado', '2024-10-16 02:02:12'),
(15, 'dummy', 'chelcontrevida@gmail.com', 16, 'Male', 923424324, 'dummy', 'enforcer', 'enforcer', '2024-10-24 13:20:52');

-- --------------------------------------------------------

--
-- Stand-in structure for view `monthly_violation_count`
-- (See below for the actual view)
--
CREATE TABLE `monthly_violation_count` (
`month_name` varchar(64)
,`year` varchar(4)
,`violation_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `penalty_summary`
-- (See below for the actual view)
--
CREATE TABLE `penalty_summary` (
`total_penalties` decimal(65,0)
,`total_penalty_paid` decimal(65,0)
,`total_penalty_unpaid` decimal(65,0)
,`total_unsolved` bigint(21)
,`total_solved` bigint(21)
,`total_report` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `summary_report_view`
-- (See below for the actual view)
--
CREATE TABLE `summary_report_view` (
`total_report` bigint(21)
,`total_active_violation` bigint(21)
,`total_count_violation` bigint(21)
,`total_citizen_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_enforcer_report`
--

CREATE TABLE `tbl_enforcer_report` (
  `id` int(11) NOT NULL,
  `violationID` varchar(255) NOT NULL,
  `resident_name` varchar(255) NOT NULL,
  `violators_name` varchar(255) NOT NULL,
  `violators_age` varchar(255) NOT NULL,
  `violators_gender` varchar(255) NOT NULL,
  `violators_location` varchar(255) NOT NULL,
  `violation_type` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL,
  `offenses` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `penalty` int(255) NOT NULL,
  `isPaid` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_enforcer_report`
--

INSERT INTO `tbl_enforcer_report` (`id`, `violationID`, `resident_name`, `violators_name`, `violators_age`, `violators_gender`, `violators_location`, `violation_type`, `datetime`, `offenses`, `latitude`, `longitude`, `penalty`, `isPaid`) VALUES
(5, 'VIOLATION-871304', '1', 'Dope JOhn', '12', 'male', 'Brgy Old Sagay Plaza Public Market', 'Throw Cans in plaza', '2024-10-26 11:11:00', '', '10.9486093', '123.3365324', 100, 1),
(6, 'VIOLATION-133509', '1', 'John Dope', '34', 'female', 'Brgy Old sagay', 'asdasd', '2024-10-26 20:15:00', 'First Offense', '10.95036028', '123.32355716', 500, 1),
(8, 'VIOLATION-491023', 'mr.kupids', 'kupidss111', '12', 'male', 'Brgy Old Sagay Plaza Public Market', 'Illegal Segrigication', '2024-11-05 09:38:00', '', '10.9485899', '123.3365294', 200, 1),
(10, 'VIOLATION-300690', 'Enforcer', 'Dope JOhn', '16', 'female', '2222213213', '22', '2024-12-12 20:00:00', 'First Offense', '9.2864512', '123.2666624', 500, 0),
(11, 'VIOLATION-133509', '1', 's', '23', 'male', 's', 's', '2024-11-12 20:05:00', 'Third Offense ', '10.95036028', '123.32355716', 3000, 1),
(12, 'VIOLATION-941594', '1', 'asd', '23', 'Male', 'asdasd', 'asdasd', '2024-12-24 19:49:00', '', '10.6856448', '122.9881344', 500, 1),
(13, 'VIOLATION-113127', '1', 'sd', '23', 'Male', 'asdsa', '2323', '2024-11-30 20:14:00', '', '10.2825984', '123.9416832', 5000, 1),
(14, 'VIOLATION-052100', 'Enforcer', 'Lowie', '32', 'female', 'Crotons', 'Naghaboy plastic', '2024-12-11 14:55:00', 'First Offense', '10.7201501', '122.5621063', 500, 1),
(15, 'VIOLATION-367827', 'Enforcer', '1', '1', 'Male', '1', '1', '2024-12-11 15:10:00', 'First Offense', '10.7201501', '122.5621063', 500, 1),
(16, 'VIOLATION-900398', 'Enforcer', 'rena', '34', 'Female', 'santan', 'aksdhgsvdg', '2024-12-11 15:14:00', 'First Offense', '10.9403281', '123.4228272', 500, 1),
(17, 'VIOLATION-409110', 'Enforcer', 'rena', '34', 'Female', 'santan', 'aksdhgsvdg', '2024-12-11 15:14:00', 'First Offense', '10.9403281', '123.4228272', 500, 1),
(18, 'VIOLATION-722433', 'Enforcer', 'rena', '45', 'Female', 'crotons', 'Illegal Dumping', '2024-12-12 17:14:00', 'First Offense', '10.8986368', '123.3944576', 500, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_resident_report`
--

CREATE TABLE `tbl_resident_report` (
  `id` int(11) UNSIGNED NOT NULL,
  `violationID` varchar(255) NOT NULL,
  `resident_name` varchar(255) NOT NULL,
  `violators_name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `violators_location` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `admin_approval` tinyint(1) NOT NULL,
  `isActive` tinyint(1) NOT NULL,
  `enforcer_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_resident_report`
--

INSERT INTO `tbl_resident_report` (`id`, `violationID`, `resident_name`, `violators_name`, `description`, `violators_location`, `latitude`, `longitude`, `date`, `time`, `admin_approval`, `isActive`, `enforcer_id`) VALUES
(37, 'VIOLATION-282228', '1', 'rens', 'naghaboy basura', 'manzanilla', '10.8920832', '123.3977344', '2024-12-19', '13:59:00', 0, 1, ''),
(39, 'VIOLATION-607984', '1', 'Racel Villasis', 'Illegal Dumping', 'Manzanilia', '10.8986368', '123.3944576', '2024-12-12', '17:02:00', 1, 1, '10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `Fullname` varchar(100) NOT NULL,
  `Age` int(50) NOT NULL,
  `Gender` enum('Male','Female','Other') NOT NULL,
  `Contactnumber` int(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `Address` varchar(100) NOT NULL,
  `IDPhoto` varchar(255) NOT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `Password` varchar(100) NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` enum('Pending','Approved','','') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Fullname`, `Age`, `Gender`, `Contactnumber`, `email`, `Address`, `IDPhoto`, `Username`, `Password`, `Created_at`, `Status`) VALUES
(29, '1', 1, 'Male', 1, 'renaespinosa24@gmail.com', '1', '66f62fb43cad2-mvc1.png', '1', '$2y$10$RClyOk4koU7ZFQH4AjLdOOBoVW9.Di/mGzstXAQvEKypnnwY0/oZK', '2024-09-27 04:08:20', 'Approved'),
(32, '2', 2, 'Female', 2, 'renaespinosa24@gmail.com', '2', '66f63e86a0dea-290d444ddfe28476d54d2f55b3d659a0.jpg', '2', '$2y$10$4RtaXisKqKQdJNHDWuhcvuNVhs4eQ9VQgjoougABzO7EwSaAwrkLO', '2024-09-27 05:11:34', 'Approved'),
(33, 'Rey', 39, 'Male', 2147483647, 'renaespinosa24@gmail.com', 'manzanilla A', '66f6478669787-mvc1.png', 'rey', '$2y$10$PnDQptKR4Nuuu8/4yDquAOo.SqQPC8jc2iN6thzx01oEXCk06jixi', '2024-09-27 05:49:58', 'Approved'),
(34, '1', 1, 'Male', 122, 'renaespinosa24@gmail.com', '1', '', '1', '$2y$10$WfO7Ct7cS36aniwzHPDH0.EFcjwd5j2L6BwOucXJa8R6VDjyK/KeW', '2024-10-03 04:43:35', 'Approved'),
(35, 'hey', 12, 'Female', 2147483647, 'renaespinosa24@gmail.com', 'manzanilla', '', 'hey', '$2y$10$HJNgO5XsOfWWEKyejCo5f.W.sZBhGhxJpk46xwsUWvzg.CK1jgiRO', '2024-10-03 04:45:03', ''),
(36, '3', 3, 'Male', 3, 'renaespinosa24@gmail.com', '3', '', '3', '$2y$10$bijSg8CMotBUdObyqEae3eBy.GYFJC8nO7Y2IfQPdZogaiEX04JdS', '2024-10-03 04:52:10', 'Approved'),
(37, '45', 5, 'Male', 5, 'renaespinosa24@gmail.com', '5', '', '5', '$2y$10$IH3inYffi8/sPsPofx22UebGvOkwtRdFg4vVtiXRN2EFZCwRxU5jW', '2024-10-03 04:57:55', 'Approved'),
(38, 'dummy1', 12, 'Male', 2147483647, 'renaespinosa24@gmail.com', 'Old Sagay', '38-ada-portada-2.png', 'dummy1', '$2y$10$73shE.jhaOlvexkLXfd8eOC0UobkctcfFyMaRRqKAocVjsER6Wu.2', '2024-10-26 04:02:52', 'Approved'),
(39, 'dummy2', 24, 'Male', 9123213, 'renaespinosa24@gmail.com', 'Sagay', '39.png', 'dummy2', '$2y$10$9b9TMnXkkaTxGejlYgzgo.BcDg.dVHLjEySHIpRKk809V/n10cnJG', '2024-10-26 04:12:23', 'Approved'),
(40, 'mr.kupids', 41, 'Male', 928282723, 'renaespinosa24@gmail.com', 'Sagay', '40.png', 'kupids', '$2y$10$ESkQ/LlHWhB8nwifIJk1AeKib.rl/nUrzD0rznBANcdjtOmMpxRbW', '2024-11-05 01:34:48', 'Approved'),
(41, 'testdummy', 12, 'Male', 9123213, 'chelcontrevidax1@gmail.com', '3123213', '41.png', 'mydummy003', '$2y$10$Of3svtDVQYEhtORqZHsKB.KYgdhhE.MIegLt7V/OTCTdomb87YNWK', '2024-11-20 22:40:26', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `user_administrator`
--

CREATE TABLE `user_administrator` (
  `id` int(50) UNSIGNED NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_administrator`
--

INSERT INTO `user_administrator` (`id`, `username`, `password`) VALUES
(1, 'barangay', 'oldsagay2024'),
(2, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `violation_management`
--

CREATE TABLE `violation_management` (
  `id` int(50) UNSIGNED NOT NULL,
  `Enforcer_Name` varchar(100) NOT NULL,
  `Name_of_Violator` varchar(100) NOT NULL,
  `Age` int(100) NOT NULL,
  `Gender` enum('Female','Male','','') NOT NULL,
  `Location` varchar(250) NOT NULL,
  `Types_of_Violation` varchar(250) NOT NULL,
  `DateTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `Latitude` float(10,8) NOT NULL,
  `Longitude` float(11,8) NOT NULL,
  `Evidence` varchar(255) NOT NULL,
  `Penalty` float(10,9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violation_management`
--

INSERT INTO `violation_management` (`id`, `Enforcer_Name`, `Name_of_Violator`, `Age`, `Gender`, `Location`, `Types_of_Violation`, `DateTime`, `Latitude`, `Longitude`, `Evidence`, `Penalty`) VALUES
(18, '2', '2', 2, 'Male', '2', '2', '2024-10-18 00:44:51', 2.00000000, 2.00000000, '671203e369c71-Screenshot (6).png', 10.000000000),
(19, 'sss', '3333', 3, 'Male', '3', '3', '2024-10-18 01:30:01', 3.00000000, 3.00000000, '67120e79ba9dd-Screenshot (6).png', 3.000000000);

-- --------------------------------------------------------

--
-- Structure for view `dashboard`
--
DROP TABLE IF EXISTS `dashboard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `dashboard`  AS SELECT year(`e`.`datetime`) AS `penalty_year`, month(`e`.`datetime`) AS `penalty_month`, sum(`e`.`penalty`) AS `total_penalties`, sum(case when `e`.`isPaid` = 1 then `e`.`penalty` else 0 end) AS `total_penalty_paid`, sum(case when `e`.`isPaid` = 0 then `e`.`penalty` else 0 end) AS `total_penalty_unpaid`, count(case when `e`.`isPaid` = 0 then 1 end) AS `total_unsolved`, count(case when `e`.`isPaid` = 1 then 1 end) AS `total_solved`, (select count(0) from `tbl_resident_report` `r` where `r`.`isActive` = 1 and year(`r`.`date`) = year(`e`.`datetime`) and month(`r`.`date`) = month(`e`.`datetime`)) AS `total_report` FROM `tbl_enforcer_report` AS `e` GROUP BY year(`e`.`datetime`), month(`e`.`datetime`) ORDER BY year(`e`.`datetime`) DESC, month(`e`.`datetime`) DESC ;

-- --------------------------------------------------------

--
-- Structure for view `monthly_violation_count`
--
DROP TABLE IF EXISTS `monthly_violation_count`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `monthly_violation_count`  AS SELECT date_format(`tbl_enforcer_report`.`datetime`,'%M') AS `month_name`, date_format(`tbl_enforcer_report`.`datetime`,'%Y') AS `year`, count(0) AS `violation_count` FROM `tbl_enforcer_report` GROUP BY date_format(`tbl_enforcer_report`.`datetime`,'%Y'), date_format(`tbl_enforcer_report`.`datetime`,'%M') ORDER BY date_format(`tbl_enforcer_report`.`datetime`,'%Y') DESC, month(`tbl_enforcer_report`.`datetime`) DESC ;

-- --------------------------------------------------------

--
-- Structure for view `penalty_summary`
--
DROP TABLE IF EXISTS `penalty_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `penalty_summary`  AS SELECT sum(`tbl_enforcer_report`.`penalty`) AS `total_penalties`, sum(case when `tbl_enforcer_report`.`isPaid` = 1 then `tbl_enforcer_report`.`penalty` else 0 end) AS `total_penalty_paid`, sum(case when `tbl_enforcer_report`.`isPaid` = 0 then `tbl_enforcer_report`.`penalty` else 0 end) AS `total_penalty_unpaid`, count(case when `tbl_enforcer_report`.`isPaid` = 0 then 1 end) AS `total_unsolved`, count(case when `tbl_enforcer_report`.`isPaid` = 1 then 1 end) AS `total_solved`, (select count(0) from `tbl_resident_report` where `tbl_resident_report`.`isActive` = 1) AS `total_report` FROM `tbl_enforcer_report` ;

-- --------------------------------------------------------

--
-- Structure for view `summary_report_view`
--
DROP TABLE IF EXISTS `summary_report_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `summary_report_view`  AS SELECT (select count(0) from `tbl_resident_report` where `tbl_resident_report`.`admin_approval` = 'approved') AS `total_report`, (select count(0) from `tbl_resident_report` where `tbl_resident_report`.`isActive` = 1) AS `total_active_violation`, (select count(0) from `tbl_enforcer_report`) AS `total_count_violation`, (select count(0) from `users`) AS `total_citizen_count` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enforcers`
--
ALTER TABLE `enforcers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_enforcer_report`
--
ALTER TABLE `tbl_enforcer_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_resident_report`
--
ALTER TABLE `tbl_resident_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_administrator`
--
ALTER TABLE `user_administrator`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `violation_management`
--
ALTER TABLE `violation_management`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enforcers`
--
ALTER TABLE `enforcers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `tbl_enforcer_report`
--
ALTER TABLE `tbl_enforcer_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tbl_resident_report`
--
ALTER TABLE `tbl_resident_report`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `user_administrator`
--
ALTER TABLE `user_administrator`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `violation_management`
--
ALTER TABLE `violation_management`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
