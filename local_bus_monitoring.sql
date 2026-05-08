-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2025 at 06:25 PM
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
-- Database: `local_bus_monitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `admin_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `admin_password`) VALUES
(1, 'admin', '$2y$10$y/m.24.71LcTPgwmU5amouQnmO9AVlF6RSNOTEucesgvHgpTRu18q');

-- --------------------------------------------------------

--
-- Table structure for table `bus`
--

CREATE TABLE `bus` (
  `bus_id` int(11) NOT NULL,
  `bus_num` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus`
--

INSERT INTO `bus` (`bus_id`, `bus_num`, `capacity`, `route_id`, `schedule_id`) VALUES
(1, 'BUS-101', 40, 1, 1),
(2, 'BUS-202', 35, 2, 2),
(3, 'BUS-303', 50, 3, 3),
(4, 'BUS-404', 40, 1, 4),
(5, 'BUS-505', 35, 2, 5),
(6, 'BUS-606', 50, 3, 6),
(7, 'BUS-707', 30, 6, 7),
(8, 'BUS-808', 40, 4, 8),
(9, 'BUS-909', 35, 5, 9),
(10, 'BUS-010', 40, 1, 10),
(11, 'BUS-111', 40, 7, 11),
(12, 'BUS-222', 35, 8, 12),
(13, 'BUS-333', 40, 9, 13),
(14, 'BUS-444', 35, 10, 14),
(15, 'BUS-555', 40, 11, 15),
(16, 'BUS-666', 50, 14, 16),
(17, 'BUS-331', 71, 13, 4);

-- --------------------------------------------------------

--
-- Table structure for table `bus_location`
--

CREATE TABLE `bus_location` (
  `location_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `location_lat` decimal(9,6) NOT NULL,
  `location_lng` decimal(9,6) NOT NULL,
  `timestamps` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_location`
--

INSERT INTO `bus_location` (`location_id`, `bus_id`, `location_lat`, `location_lng`, `timestamps`) VALUES
(1, 1, 23.780887, 90.279239, '2025-12-17 11:07:24'),
(2, 1, 23.781500, 90.280000, '2025-12-17 11:12:24'),
(3, 1, 23.782000, 90.281000, '2025-12-17 11:17:24'),
(4, 2, 23.824482, 90.412518, '2025-12-17 11:09:24'),
(5, 2, 23.825000, 90.413000, '2025-12-17 11:14:24'),
(6, 2, 23.826000, 90.414000, '2025-12-17 11:17:24'),
(7, 3, 23.733840, 90.392780, '2025-12-17 11:05:24'),
(8, 3, 23.734500, 90.393500, '2025-12-17 11:11:24'),
(9, 3, 23.735000, 90.394000, '2025-12-17 11:17:24'),
(10, 4, 23.780500, 90.279500, '2025-12-17 11:02:24'),
(11, 4, 23.781000, 90.280500, '2025-12-17 11:10:24'),
(12, 5, 23.825500, 90.413500, '2025-12-17 11:08:24'),
(13, 5, 23.826500, 90.414500, '2025-12-17 11:17:24'),
(14, 11, 23.780700, 90.279800, '2025-12-17 11:11:24'),
(15, 11, 23.781200, 90.280200, '2025-12-17 11:15:24'),
(16, 12, 23.779800, 90.279000, '2025-12-17 11:12:24'),
(17, 13, 23.782300, 90.281500, '2025-12-17 11:13:24'),
(18, 16, 23.734000, 90.393000, '2025-12-17 11:10:24');

-- --------------------------------------------------------

--
-- Table structure for table `driver`
--

CREATE TABLE `driver` (
  `driver_id` int(11) NOT NULL,
  `driver_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver`
--

INSERT INTO `driver` (`driver_id`, `driver_name`, `phone_number`, `password`) VALUES
(1, 'Rahim Uddin', '01711111111', '$2y$10$gDG.zQ72avCOyGPPr.h6EuYmABRz/pVYCdHd1PATZ9EDv4LwLKMc.'),
(2, 'Karim Ahmed', '01722222222', '$2y$10$aqi7VSCWF.Ve2jKba5iUI.k6nrusUL41o10aSpkMp3DcQxqrvFn7i'),
(3, 'Abdul Mannan', '01733333333', '$2y$10$gDG.zQ72avCOyGPPr.h6EuYmABRz/pVYCdHd1PATZ9EDv4LwLKMc.'),
(5, 'Nirob', '01766209481', '$2y$10$mcAPO73GfslThbEJRQeSwe2NyjEVjuEYX/8XDcUT/hNUjtpDbCh.O'),
(6, 'Rafia', '01766209481', '$2y$10$0LIMSZYIa6wjSmkvWUhKNObbivRC2UqEWiyFz5NbjlrBhb.7MjqdK');

-- --------------------------------------------------------

--
-- Table structure for table `route`
--

CREATE TABLE `route` (
  `route_id` int(11) NOT NULL,
  `route_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route`
--

INSERT INTO `route` (`route_id`, `route_name`) VALUES
(1, 'Campus to City Center'),
(2, 'City Center to Airport'),
(3, 'Campus to Railway Station'),
(4, 'Airport to Campus'),
(5, 'Railway Station to City Center'),
(6, 'Campus Circular Route'),
(7, 'Campus to North Gate'),
(8, 'Campus to South Gate'),
(9, 'Campus to Dormitory Area'),
(10, 'Campus to New Market'),
(11, 'City Center to Campus Night'),
(12, 'Airport to City Center Express'),
(13, 'Airport to University 2'),
(14, 'Railway Station to Campus Direct'),
(15, 'Hostel to Campus Direct'),
(16, 'Staff Quarter to Campus');

-- --------------------------------------------------------

--
-- Table structure for table `route_stops`
--

CREATE TABLE `route_stops` (
  `rs_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `stop_id` int(11) NOT NULL,
  `stop_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route_stops`
--

INSERT INTO `route_stops` (`rs_id`, `route_id`, `stop_id`, `stop_order`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 2),
(3, 1, 3, 3),
(4, 2, 3, 1),
(5, 2, 4, 2),
(6, 3, 1, 1),
(7, 3, 5, 2),
(8, 4, 4, 1),
(9, 4, 3, 2),
(10, 4, 2, 3),
(11, 4, 1, 4),
(12, 5, 5, 1),
(13, 5, 3, 2),
(14, 6, 1, 1),
(15, 6, 6, 2),
(16, 6, 7, 3),
(17, 6, 8, 4),
(18, 6, 9, 5),
(19, 6, 1, 6),
(20, 7, 1, 1),
(21, 7, 11, 2),
(22, 8, 1, 1),
(23, 8, 12, 2),
(24, 9, 1, 1),
(25, 9, 13, 2),
(26, 10, 1, 1),
(27, 10, 14, 2),
(28, 11, 3, 1),
(29, 11, 1, 2),
(30, 14, 5, 1),
(31, 14, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `schedule_id` int(11) NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `route_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`schedule_id`, `departure_time`, `arrival_time`, `route_id`, `admin_id`, `driver_id`) VALUES
(1, '08:00:00', '08:45:00', 1, 1, 1),
(2, '09:00:00', '09:40:00', 2, 1, 2),
(3, '10:00:00', '10:50:00', 3, 1, 1),
(4, '11:00:00', '11:45:00', 1, 1, 2),
(5, '12:00:00', '12:40:00', 2, 1, 3),
(6, '14:00:00', '14:50:00', 3, 1, 1),
(7, '15:00:00', '15:30:00', 6, 1, 2),
(8, '16:00:00', '16:45:00', 4, 1, 3),
(9, '17:00:00', '17:30:00', 5, 1, 1),
(10, '18:00:00', '18:45:00', 1, 1, 2),
(11, '07:30:00', '07:50:00', 7, 1, 1),
(12, '07:45:00', '08:05:00', 8, 1, 2),
(13, '08:15:00', '08:40:00', 9, 1, 3),
(14, '08:30:00', '09:00:00', 10, 1, 1),
(15, '21:00:00', '21:45:00', 11, 1, 2),
(16, '06:30:00', '07:15:00', 14, 1, 3),
(17, '09:02:00', '22:02:00', 12, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `stop`
--

CREATE TABLE `stop` (
  `stop_id` int(11) NOT NULL,
  `stop_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stop`
--

INSERT INTO `stop` (`stop_id`, `stop_name`) VALUES
(1, 'Campus Main Gate'),
(2, 'Science Building'),
(3, 'City Center'),
(4, 'Airport Terminal'),
(5, 'Railway Station'),
(6, 'Library Building'),
(7, 'Student Center'),
(8, 'Engineering Building'),
(9, 'Business School'),
(10, 'Medical Center'),
(11, 'North Gate'),
(12, 'South Gate'),
(13, 'Dormitory Area'),
(14, 'New Market'),
(15, 'Staff Quarter'),
(16, 'University 2');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `phone_number`, `password`) VALUES
(1, 'Nahin', 'zannatun.nayem@g.bracu.ac.bd', '01314663932', '$2y$10$0kDPQN9ek/ZJ1c9sx7sSE.tz9.bdGA8O2JGrWXir55M19QEgmxc/6'),
(2, 'Rakib Hasan', 'rakib@example.com', '01711111111', '$2y$10$0bpEjDLy76Sw5PX1/NI5yesdm2lYHp4GRkHecOlHKk8pFM0A1PJNC'),
(3, 'Fatima Ahmed', 'fatima@example.com', '01722222222', '$2y$10$5ilLDfLHhEVrm7JRjpJbke1MouCfbj..BqZ4ObXWsII39PTctPhtW'),
(4, 'Hasan Ali', 'hasan@example.com', '01733333333', '$2y$10$xf6Pl/qA6EjNREHK.HZMsuEM.CPkBYG.JV6h0poW9JZhnSA5ZwlkW'),
(5, 'Sadia Rahman', 'sadia@example.com', '01744444444', '$2y$10$0bpEjDLy76Sw5PX1/NI5yesdm2lYHp4GRkHecOlHKk8pFM0A1PJNC'),
(6, 'MD. RAKIBUL HAQUE SARDAR', 'rakibullhaques@gmail.com', '01571205499', '$2y$10$Hv67LcgfjDAVgs1y9F9TouBSwmNmJVnBVgG6G.2VxAxeFrYOwZIyq');

-- --------------------------------------------------------

--
-- Table structure for table `user_favourite_route`
--

CREATE TABLE `user_favourite_route` (
  `fav_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_favourite_route`
--

INSERT INTO `user_favourite_route` (`fav_id`, `user_id`, `route_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 3),
(5, 3, 2),
(6, 3, 4),
(7, 4, 6),
(8, 5, 1),
(9, 5, 5),
(11, 6, 4),
(12, 6, 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bus`
--
ALTER TABLE `bus`
  ADD PRIMARY KEY (`bus_id`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `bus_location`
--
ALTER TABLE `bus_location`
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `bus_id` (`bus_id`);

--
-- Indexes for table `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`driver_id`);

--
-- Indexes for table `route`
--
ALTER TABLE `route`
  ADD PRIMARY KEY (`route_id`);

--
-- Indexes for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD PRIMARY KEY (`rs_id`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `stop_id` (`stop_id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `stop`
--
ALTER TABLE `stop`
  ADD PRIMARY KEY (`stop_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_favourite_route`
--
ALTER TABLE `user_favourite_route`
  ADD PRIMARY KEY (`fav_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `route_id` (`route_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bus`
--
ALTER TABLE `bus`
  MODIFY `bus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `bus_location`
--
ALTER TABLE `bus_location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `driver`
--
ALTER TABLE `driver`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `route`
--
ALTER TABLE `route`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `route_stops`
--
ALTER TABLE `route_stops`
  MODIFY `rs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stop`
--
ALTER TABLE `stop`
  MODIFY `stop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_favourite_route`
--
ALTER TABLE `user_favourite_route`
  MODIFY `fav_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bus`
--
ALTER TABLE `bus`
  ADD CONSTRAINT `bus_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `route` (`route_id`),
  ADD CONSTRAINT `bus_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`schedule_id`);

--
-- Constraints for table `bus_location`
--
ALTER TABLE `bus_location`
  ADD CONSTRAINT `bus_location_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `bus` (`bus_id`);

--
-- Constraints for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD CONSTRAINT `route_stops_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `route` (`route_id`),
  ADD CONSTRAINT `route_stops_ibfk_2` FOREIGN KEY (`stop_id`) REFERENCES `stop` (`stop_id`);

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `route` (`route_id`),
  ADD CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `schedule_ibfk_3` FOREIGN KEY (`driver_id`) REFERENCES `driver` (`driver_id`);

--
-- Constraints for table `user_favourite_route`
--
ALTER TABLE `user_favourite_route`
  ADD CONSTRAINT `user_favourite_route_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `user_favourite_route_ibfk_2` FOREIGN KEY (`route_id`) REFERENCES `route` (`route_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
