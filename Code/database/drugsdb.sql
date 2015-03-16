-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2015 at 03:48 AM
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`cid`, `class`) VALUES
(1, 'Test'),
(2, 'Test51');

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
  `cRT` double NOT NULL,
  `cCAS` varchar(50) NOT NULL,
  `cClass` int(11) NOT NULL,
  PRIMARY KEY (`cid`),
  KEY `cClass` (`cClass`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `compounds`
--

INSERT INTO `compounds` (`cid`, `cName`, `cFormula`, `cOName`, `cMass`, `cPrecursor`, `cFrag`, `cRT`, `cCAS`, `cClass`) VALUES
(2, 'test', 'test', 'test', 999, 23, 23, 2311, 'test11', 1),
(4, 'lolo', 'lolo', 'lolo', 12, 12, 12, 12, '12', 2),
(5, 'll', 'll', 'll', 1, 1, 1, 1, '1', 1),
(6, 'll', 'll', 'll', 1, 1, 1, 1, '1', 1),
(7, 'll', 'll', 'll', 1, 1, 1, 1, '1', 1),
(8, 'll', 'll', 'll', 1, 1, 1, 1, '1', 1),
(9, 'll', 'll', 'll', 1, 1, 1, 1, '1', 1),
(10, 'll', 'll', 'll', 1, 1, 1, 1, '1', 1),
(11, 'll', 'll', 'll', 1, 1, 1, 1, '1', 1),
(12, 'l', 'l', 'l', 1, 1, 1, 1, '1', 2),
(13, 'l', 'l', 'l', 1, 1, 1, 1, '1', 2),
(14, '1', '1', '1', 1, 1, 1, 1, '1', 1),
(15, '1', '1', '1', 1, 1, 1, 1, '1', 1),
(16, '1', '1', '1', 1, 1, 1, 1, '1', 1),
(17, '1', '1', '1', 1, 1, 1, 1, '1', 1),
(18, '1', '1', '1', 1, 1, 1, 1, '1', 2),
(19, '1', '1', '1', 1, 1, 1, 1, '1', 2),
(22, 'myName', 'MyForm', 'MyOname', 1, 1234, 12, 123, '12345', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `transition`
--

INSERT INTO `transition` (`tid`, `cid`, `tProduct`, `tCE`, `tAbundance`, `tRIInt`) VALUES
(1, 2, 3, 22, 41, 50),
(5, 19, 2, 4, 7, 2),
(6, 2, 12, 12, 12, 12),
(12, 22, 1, 2, 3, 4),
(13, 22, 2, 3, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `userinvite`
--

CREATE TABLE IF NOT EXISTS `userinvite` (
  `iid` int(11) NOT NULL AUTO_INCREMENT,
  `iemail` varchar(50) NOT NULL,
  `iexp` date NOT NULL,
  PRIMARY KEY (`iid`),
  UNIQUE KEY `iemail` (`iemail`),
  UNIQUE KEY `iemail_2` (`iemail`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `userinvite`
--

INSERT INTO `userinvite` (`iid`, `iemail`, `iexp`) VALUES
(1, 'lalalal', '2015-02-19'),
(8, 'carlos.mrtez2002@gmail.com', '2015-02-24'),
(9, 'test@gmail.com', '2015-02-25'),
(10, 'test2@gmail.com', '2015-02-25'),
(11, 'test3@gmail.com', '2015-02-25'),
(12, 'carlos@fiu.edu', '2015-03-08');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `uName`, `uEmail`, `uPass`, `uRole`) VALUES
(2, 'Jose', 'carlos@gmail.com', '0cbc6611f5540bd0809a388dc95a615b', 1),
(3, 'Carlos', 'Carlos@fiu.edu', '0cbc6611f5540bd0809a388dc95a615b', 2),
(6, 'Jose', 'carlos.mrtez2002@gmail.com', '0cbc6611f5540bd0809a388dc95a615b', 2),
(7, 'Test', 'test@gmail.com', '0cbc6611f5540bd0809a388dc95a615b', 2);

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
