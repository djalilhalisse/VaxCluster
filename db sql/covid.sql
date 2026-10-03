-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2024 at 06:14 PM
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
-- Database: `covid`
--

-- --------------------------------------------------------

--
-- Table structure for table `effet_secondaire`
--

CREATE TABLE `effet_secondaire` (
  `id_effet` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `date_apparition` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `effet_secondaire`
--

INSERT INTO `effet_secondaire` (`id_effet`, `description`, `date_apparition`) VALUES
(1, 'Douleurs musculaires', NULL),
(2, 'Diarhée', NULL),
(3, 'Fatigue', NULL),
(4, 'Fievre', NULL),
(5, 'Frisson', NULL),
(6, 'Insomnie', NULL),
(7, 'Maux de tete', NULL),
(8, 'Nausées', NULL),
(9, 'Neuralgie', NULL),
(10, 'Palpitation', NULL),
(11, 'Toux', NULL),
(12, 'Troubles Digestifs', NULL),
(13, 'Vertige', NULL),
(14, 'Allergie Cutanée', NULL),
(15, 'Bruifees Sectaleuse', NULL),
(16, 'Ecoulement', NULL),
(17, 'Méningite', NULL),
(18, 'Métrorragie', NULL),
(19, 'Nounissement', NULL),
(20, 'Syndrom Grippal', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `maladie_chronique`
--

CREATE TABLE `maladie_chronique` (
  `id_maladie` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maladie_chronique`
--

INSERT INTO `maladie_chronique` (`id_maladie`, `nom`) VALUES
(31, 'Anemie'),
(32, 'Arthrose'),
(33, 'Allergie'),
(34, 'Bronchopneumopathie chronique obstructive (BPCO)'),
(35, 'Cardiaque'),
(36, 'Colopathie'),
(37, 'Diphtérie-Tétanos'),
(38, 'Diabete Type 1'),
(39, 'Diabete Type 2'),
(40, 'Dyslipidémie'),
(41, 'Déficit en Proteine'),
(42, 'Gastrite'),
(43, 'Hypertension artérielle (HTA)'),
(44, 'Hemiplégie'),
(45, 'Hypothyroide Glucome'),
(46, 'Hypothyroidie'),
(47, 'Hypovitaminose B12'),
(48, 'Insuffisance Rénale'),
(49, 'No Background'),
(50, 'Rectocolite hémorragique (RCUH)'),
(51, 'Rhinite Allergique'),
(52, 'Thyroidie'),
(53, 'Accident Vasculaire Cérébral (AVC)'),
(54, 'Varices');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id_patient` varchar(20) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `sexe` varchar(10) DEFAULT NULL,
  `adresse` varchar(255) NOT NULL,
  `tel` varchar(15) NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_maladie_chronique`
--

CREATE TABLE `patient_maladie_chronique` (
  `id_patient` varchar(20) NOT NULL,
  `id_maladie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_vaccination`
--

CREATE TABLE `patient_vaccination` (
  `id_patient` varchar(20) NOT NULL,
  `id_vaccination` int(11) NOT NULL,
  `date_apparition` date DEFAULT NULL,
  `duree_manifestation` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `pass`) VALUES
(1, 'User1', 'password123'),
(2, 'User2', 'abc123'),
(3, 'User3', 'qwerty');

-- --------------------------------------------------------

--
-- Table structure for table `vaccination`
--

CREATE TABLE `vaccination` (
  `id_vaccination` int(11) NOT NULL,
  `type_vaccin` varchar(100) DEFAULT NULL,
  `num_lot` varchar(50) NOT NULL,
  `date_peremption` date NOT NULL,
  `lieu_vaccination` varchar(255) NOT NULL,
  `date_vaccination` date NOT NULL,
  `heure_vaccination` time NOT NULL,
  `site` varchar(50) NOT NULL,
  `dose` varchar(50) NOT NULL,
  `season` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vaccination_effet_secondaire`
--

CREATE TABLE `vaccination_effet_secondaire` (
  `id_vaccination` int(11) NOT NULL,
  `id_effet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `effet_secondaire`
--
ALTER TABLE `effet_secondaire`
  ADD PRIMARY KEY (`id_effet`);

--
-- Indexes for table `maladie_chronique`
--
ALTER TABLE `maladie_chronique`
  ADD PRIMARY KEY (`id_maladie`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id_patient`);

--
-- Indexes for table `patient_maladie_chronique`
--
ALTER TABLE `patient_maladie_chronique`
  ADD PRIMARY KEY (`id_patient`,`id_maladie`),
  ADD KEY `id_maladie` (`id_maladie`);

--
-- Indexes for table `patient_vaccination`
--
ALTER TABLE `patient_vaccination`
  ADD PRIMARY KEY (`id_patient`,`id_vaccination`),
  ADD KEY `id_vaccination` (`id_vaccination`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vaccination`
--
ALTER TABLE `vaccination`
  ADD PRIMARY KEY (`id_vaccination`);

--
-- Indexes for table `vaccination_effet_secondaire`
--
ALTER TABLE `vaccination_effet_secondaire`
  ADD PRIMARY KEY (`id_vaccination`,`id_effet`),
  ADD KEY `id_effet` (`id_effet`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `effet_secondaire`
--
ALTER TABLE `effet_secondaire`
  MODIFY `id_effet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `maladie_chronique`
--
ALTER TABLE `maladie_chronique`
  MODIFY `id_maladie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vaccination`
--
ALTER TABLE `vaccination`
  MODIFY `id_vaccination` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `patient_maladie_chronique`
--
ALTER TABLE `patient_maladie_chronique`
  ADD CONSTRAINT `patient_maladie_chronique_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_maladie_chronique_ibfk_2` FOREIGN KEY (`id_maladie`) REFERENCES `maladie_chronique` (`id_maladie`) ON DELETE CASCADE;

--
-- Constraints for table `patient_vaccination`
--
ALTER TABLE `patient_vaccination`
  ADD CONSTRAINT `patient_vaccination_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_vaccination_ibfk_2` FOREIGN KEY (`id_vaccination`) REFERENCES `vaccination` (`id_vaccination`) ON DELETE CASCADE;

--
-- Constraints for table `vaccination_effet_secondaire`
--
ALTER TABLE `vaccination_effet_secondaire`
  ADD CONSTRAINT `vaccination_effet_secondaire_ibfk_1` FOREIGN KEY (`id_vaccination`) REFERENCES `vaccination` (`id_vaccination`) ON DELETE CASCADE,
  ADD CONSTRAINT `vaccination_effet_secondaire_ibfk_2` FOREIGN KEY (`id_effet`) REFERENCES `effet_secondaire` (`id_effet`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
