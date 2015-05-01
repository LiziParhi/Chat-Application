-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2015 at 06:53 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `chatbox`
--
CREATE DATABASE IF NOT EXISTS `chatbox` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `chatbox`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `decrementkeepalive`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `decrementkeepalive`(OUT `valido` INT)
BEGIN

DECLARE done INT DEFAULT 0;

DECLARe i INT DEFAULT 1;

DECLARE myval INT DEFAULT 1;

DECLARE cur CURSOR FOR

    SELECT `keepalive`

    FROM `users`;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

OPEN cur;

read_loop: LOOP

FETCH cur INTO myval;

    IF done THEN

        LEAVE read_loop;

	END IF;

    IF myval > 0 THEN

    	UPDATE users SET keepalive = keepalive - 1 WHERE id = i;

    ELSE

    	UPDATE users SET active ='0' WHERE id = i;

    END IF;

    SELECT myval INTO valido;

    SET i = i + 1;

END LOOP;

CLOSE cur;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `image` text,
  `login_time` datetime DEFAULT NULL,
  `keepalive` int(11) DEFAULT NULL,
  `friends` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `active`, `image`, `login_time`, `keepalive`, `friends`) VALUES
(1, 'anshuman', 'ansman', 0, 'uploads/anshuman.png', NULL, 0, 'ricardo:lizilina'),
(2, 'vinyl', 'ansman', 0, 'uploads/vinyl.jpg', NULL, 0, ''),
(3, 'amanda', 'ansman', 0, 'uploads/amanda.jpg', NULL, 0, 'lizilina'),
(4, 'ricardo', 'bhim', 0, 'uploads/ricardo.jpg', NULL, 0, 'anshuman:lizilina'),
(5, 'abaya', 'ansman', 0, 'uploads/abaya.jpg', NULL, 0, ''),
(6, 'lizilina', 'ANSMAN', 0, 'uploads/lizilina.jpg', NULL, 0, 'amanda:segue:anshuman:ricardo');

DELIMITER $$
--
-- Events
--

CREATE DEFINER=`root`@`localhost` EVENT `check_events` ON SCHEDULE EVERY 1 SECOND STARTS '2015-03-12 05:32:10' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN

DECLARE done INT DEFAULT 0;

DECLARe i INT DEFAULT 1;

DECLARE tempid INT DEFAULT 1;

DECLARE tempkeepalive INT DEFAULT 1;

DECLARE cur CURSOR FOR

    SELECT id, keepalive

    FROM `users`;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

OPEN cur;

read_loop: LOOP

FETCH cur INTO tempid, tempkeepalive;

    IF done THEN

        LEAVE read_loop;

	END IF;

    IF tempkeepalive > 0 THEN

    	UPDATE users SET keepalive = tempkeepalive - 1 WHERE id = tempid;

    ELSE

    	UPDATE users SET active ='0' WHERE id = i;

		UPDATE users SET keepalive ='0' WHERE id = i;

    END IF;

	UPDATE users SET id = i WHERE id = tempid;

    SET i = i + 1;

END LOOP;

CLOSE cur;

END$$

DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
