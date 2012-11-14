-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 14, 2012 at 09:45 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `unhcr2hxl`
--

-- --------------------------------------------------------

--
-- Table structure for table `unhcr2hxl_countrypcode`
--

CREATE TABLE IF NOT EXISTS `unhcr2hxl_countrypcode` (
  `id` int(11) NOT NULL,
  `pcode` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `unhcr2hxl_countrypcode`
--

INSERT INTO `unhcr2hxl_countrypcode` (`id`, `pcode`, `name`) VALUES
(132, 'mrt', 'Mauritania'),
(157, 'ner', 'Niger'),
(26, 'bfa', 'Burkina Faso'),
(87, 'gin', 'Guinea'),
(139, 'mli', 'Mali'),
(220, 'tgo', 'Togo');

-- --------------------------------------------------------

--
-- Table structure for table `unhcr2hxl_settlementpcode`
--

CREATE TABLE IF NOT EXISTS `unhcr2hxl_settlementpcode` (
  `id` int(11) NOT NULL,
  `pcode` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `unhcr2hxl_settlementpcode`
--

INSERT INTO `unhcr2hxl_settlementpcode` (`id`, `pcode`, `name`) VALUES
(65, 'gaoudel', 'Gaoudel'),
(92, 'mbeidoun', 'Mbeidoun'),
(63, 'chinegodrar', 'Chinegodrar'),
(64, 'UNHCR-POC-4228', 'Mangaize (Camp)'),
(83, 'tigzefan', 'Tigzefan'),
(129, 'UNHCR-POC-80', 'Deou'),
(140, 'UNHCR-POC-1259', 'Abala (Camp)'),
(61, 'abala', 'Abala'),
(82, 'kizamou', 'Kizamou'),
(62, 'miel', 'Miel'),
(93, 'UNHCR-POC-2327', 'Bobo Djoulasssou'),
(148, 'barani', 'Barani'),
(149, 'sabressoro', 'Sabressoro'),
(130, 'UNHCR-POC-4240', 'Gandafabou Kélwélé'),
(146, 'agando', 'Agando'),
(88, 'tankademi', 'Tankademi'),
(145, 'tichachite', 'Tichachite'),
(90, 'UNHCR-POC-4224', 'Mentao'),
(141, 'djibo', 'Djibo'),
(134, 'UNHCR-POC-4225', 'Damba'),
(159, 'UNHCR-POC-2328', 'Somgande'),
(139, 'fererio', 'Férério'),
(144, 'chinwaren', 'Chinwaren'),
(160, 'intadabdab', 'Intadabdab'),
(164, 'UNHCR-POC-4466', 'Tabareybarey '),
(161, 'tibirgalene', 'Tibirgalene'),
(153, 'tinfagat', 'Tinfagat'),
(162, 'banibangou', 'Banibangou'),
(86, 'mbera', 'Mbéra'),
(128, 'UNHCR-POC-81-Test', 'Dibissi'),
(131, 'countouregnegne', 'Gountoure Gnegne'),
(168, 'goromgoromurbain', 'Gorom-Gorom (Urbain)'),
(143, 'chigoumar', 'Chigoumar'),
(151, 'tinhedja', 'Tin-Hedja'),
(56, 'fassalanere', 'Fassala / Néré'),
(133, 'oudalanautressites', 'Oudalan/Autres Sites'),
(150, 'tougan', 'Tougan'),
(161, 'tibirgalene', 'Tibirgalene'),
(163, 'niamey', 'Niamey'),
(160, 'intadabdab', 'Intadabdab'),
(165, 'guinea', 'Guinea'),
(166, 'togo', 'Togo');

-- --------------------------------------------------------

--
-- Table structure for table `unhcr2hxl_sourcetranslation`
--

CREATE TABLE IF NOT EXISTS `unhcr2hxl_sourcetranslation` (
  `input` varchar(100) NOT NULL,
  `output` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `unhcr2hxl_sourcetranslation`
--

INSERT INTO `unhcr2hxl_sourcetranslation` (`input`, `output`) VALUES
('Enregistrement Niveau 1(CNE, CADEV, UNHCR)', 'CNE,CADEV,UNHCR'),
('UNHCR', 'UNHCR'),
('Enregistrement Niveau 1 CONAREF, UNHCR', 'CONAREF,UNHCR'),
('Enregistrement Niveau 1(CNE, AKARASS, UNHCR)', 'CNE,AKARASS,UNHCR'),
('Registration', 'UNHCR'),
('Registration Level 1', 'UNHCR'),
('Registration level1', 'UNHCR'),
('Governement', 'gov'),
('Government & UNHCR', 'UNHCR,gov'),
('UNHCR Level 1 Registration', 'UNHCR'),
('Enregistrement Level 1', 'UNHCR'),
('Enregistreme level 1', 'UNHCR'),
('CNE', 'CNE'),
('proGres DB', 'UNHCR');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
