-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2015 at 06:45 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP DATABASE drugsdb;
CREATE DATABASE drugsdb;
USE drugsdb;
--
-- Database: `drugsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE IF NOT EXISTS `class` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(100) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=82 ;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`cid`, `class`) VALUES
(1, 'Test'),
(2, 'Test51'),
(5, 'Cathinone'),
(6, 'Test34'),
(7, 'Test7'),
(10, 'TestClass'),
(11, 'ww');

-- --------------------------------------------------------

--
-- Table structure for table `compounds`
--

CREATE TABLE IF NOT EXISTS `compounds` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `cName` varchar(120) NOT NULL,
  `cFormula` varchar(50) NOT NULL,
  `cOName` varchar(50) NOT NULL,
  `cMass` double NOT NULL,
  `cPrecursor` double NOT NULL,
  `cFrag` int(11) NOT NULL,
  `cCAS` varchar(50) NOT NULL,
  `cClass` int(11) NOT NULL,
  `cayman` varchar(30) NOT NULL,
  PRIMARY KEY (`cid`),
  KEY `cClass` (`cClass`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=111 ;

--
-- Dumping data for table `compounds`
--

INSERT INTO `compounds` (`cid`, `cName`, `cFormula`, `cOName`, `cMass`, `cPrecursor`, `cFrag`, `cCAS`, `cClass`, `cayman`) VALUES
(29, 'll', 'll', 'll', 1, 1, 1, '1', 5, ''),
(35, 'myName', 'MyForm', 'MyOname', 1, 1234, 12, '12345', 1, ''),
(36, 'Test', 'TestFormula', 'TestOtherName', 1, 1, 1, '1', 1, 'Test'),
(108, 'alpha-Pyrrolidinopropiophenone', 'C13H17NO', 'FIU_0278', 203.1, 204.1, 98, '19134-50-0', 5, ''),
(109, 'alpha-Pyrrolidinopentiophenone', 'C15H21NO', 'FIU_0277', 231.16, 232.2, 93, '', 5, ''),
(110, 'alpha-Pyrrolidinobutiophenone', 'C14H19NO', 'FIU_0276', 217.1, 218.2, 103, '13415-82-2', 5, '');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `mfrom` varchar(50) NOT NULL,
  `mto` varchar(50) NOT NULL,
  `msubject` varchar(150) NOT NULL,
  `mtext` varchar(400) NOT NULL,
  `mread` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`mid`, `mfrom`, `mto`, `msubject`, `mtext`, `mread`) VALUES
(1, 'carlos@gmail.com', 'admin', 'Test', 'dsfejbdsvjsdfb', 1),
(5, 'fgdf', 'admin', 'sdfdsfs', 'sdfsdsdf', 1),
(6, 'lalal', 'admin', 'lalala', 'ddvdv', 0);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`rid`, `role`, `description`) VALUES
(1, 'admin', 'do everything in the system'),
(2, 'labOP', 'add, modify compounds');

-- --------------------------------------------------------

--
-- Table structure for table `transition`
--

CREATE TABLE IF NOT EXISTS `transition` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `tProduct` double NOT NULL,
  `tCE` int(11) NOT NULL,
  `tAbundance` int(11) NOT NULL,
  `tRIInt` double NOT NULL,
  PRIMARY KEY (`tid`),
  KEY `cid` (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=154 ;

--
-- Dumping data for table `transition`
--

INSERT INTO `transition` (`tid`, `cid`, `tProduct`, `tCE`, `tAbundance`, `tRIInt`) VALUES
(132, 108, 105.1, 24, 20036, 100),
(133, 108, 98.1, 24, 13947, 69.61),
(134, 108, 77, 48, 10752, 53.66),
(135, 108, 133.1, 16, 8939, 44.61),
(136, 108, 70.1, 16, 4184, 20.88),
(137, 108, 56, 44, 4589, 22.9),
(138, 108, 79.1, 44, 4103, 20.48),
(139, 108, 103.1, 36, 2392, 11.94),
(140, 108, 84.1, 32, 1561, 7.79),
(141, 109, 91.1, 20, 41170, 100),
(142, 109, 77.1, 56, 18602, 45.18),
(143, 109, 126.1, 24, 13249, 32.18),
(144, 109, 105.1, 28, 10850, 26.35),
(145, 110, 91.1, 24, 26692, 100),
(146, 110, 77, 48, 11019, 41.28),
(147, 110, 147.1, 16, 7888, 29.55),
(148, 110, 112.1, 24, 8197, 30.71),
(149, 110, 105, 32, 5291, 19.82),
(150, 110, 70.1, 20, 4888, 18.31),
(151, 110, 84.1, 32, 4059, 15.21),
(152, 110, 119.1, 16, 3712, 13.91),
(153, 110, 189.1, 20, 1581, 5.92);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `uName` varchar(150) NOT NULL,
  `uEmail` varchar(200) NOT NULL,
  `uPass` varchar(128) NOT NULL,
  `uRole` int(11) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `uRole` (`uRole`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `uName`, `uEmail`, `uPass`, `uRole`) VALUES
(2, 'Carlos', 'carlos@gmail.com', '0cbc6611f5540bd0809a388dc95a615b', 1),
(3, 'Carlos', 'Carlos@fiu.edu', '0cbc6611f5540bd0809a388dc95a615b', 2),
(6, 'Jose', 'carlos.mrtez2002@gmail.com', '0cbc6611f5540bd0809a388dc95a615b', 2),
(7, 'Test', 'test@gmail.com', '0cbc6611f5540bd0809a388dc95a615b', 2),
(8, 'TempName', 'carlos.mrtez2002@gmail.com', '2aeac48777d7d33ac22cb0c1bac45bf3', 2),
(9, 'TempName', 'cdomi049@fiu.edu', '2aeac48777d7d33ac22cb0c1bac45bf3', 2);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `compounds`
--
ALTER TABLE `compounds`
  ADD CONSTRAINT `compounds_ibfk_1` FOREIGN KEY (`cClass`) REFERENCES `class` (`cid`);

--
-- Constraints for table `transition`
--
ALTER TABLE `transition`
  ADD CONSTRAINT `transition_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `compounds` (`cid`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`uRole`) REFERENCES `role` (`rid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
