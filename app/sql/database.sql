--
-- Database: `project-g8t4`
--
CREATE DATABASE IF NOT EXISTS `project-g8t4` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `project-g8t4`;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `round`;
CREATE TABLE IF NOT EXISTS `round` (
  `round` int NOT NULL,
  `status` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`round`)
);

--
-- Truncate table before insert `round`
--

TRUNCATE TABLE `round`;

INSERT INTO `round` VALUES (1,'Awaiting bootstrap upload');
--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course` (
  `course` varchar(64) NOT NULL,
  `school` varchar(9999) NOT NULL,
  `title` varchar(101) NOT NULL,
  `description` varchar(1001) NOT NULL,
  `exam date` int(9) NOT NULL,
  `exam start` varchar(6) NOT NULL,
  `exam end` varchar(6) NOT NULL,
  PRIMARY KEY (`course`)
);

--
-- Truncate table before insert `course`
--

TRUNCATE TABLE `course`;
--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course`,`school`,`title`,`description`,`exam date`,`exam start`,`exam end`) VALUES
('IS100','SIS','Calculus','The basic objective of Calculus is to relate small-scale (differential) quantities to large-scale (integrated) quantities. This is accomplished by means of the Fundamental Theorem of Calculus. Students should demonstrate an understanding of the integral as a cumulative sum, of the derivative as a rate of change, and of the inverse relationship between integration and differentiation.','20131119','8:30','11:45'),
('IS101','SIS','Advanced Calculus','This is a second course on calculus. It is more advanced definitely.','20131118','12:00','15:15'),
('IS102','SIS','Java programming','This course teaches you on Java programming. I love Java definitely.','20131117','15:30','18:45');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
CREATE TABLE IF NOT EXISTS `section` (
  `course` varchar(64) NOT NULL,
  `section` varchar(4) NOT NULL,
  `day` int(2) NOT NULL,
  `start` varchar(6) NOT NULL,
  `end` varchar(6) NOT NULL,
  `instructor` varchar(101) NOT NULL,
  `venue` varchar(101) NOT NULL,
  `size` int NOT NULL,
  PRIMARY KEY (`course`,`section`)
);

--
-- Truncate table before insert `section`
--

TRUNCATE TABLE `section`;
--
-- Dumping data for table `section`
--

INSERT INTO `section` (`course`,`section`,`day`,`start`,`end`,`instructor`,`venue`,`size`) VALUES
('IS100','S1','1','8:30','11:45','Albert KHOO','Seminar Rm 2-1','10'),
('IS100','S2','2','12:00','15:15','Billy KHOO','Seminar Rm 2-2','10'),
('IS101','S1','3','15:30','18:45','Cheri KHOO','Seminar Rm 2-3','10');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `userid` varchar(129) NOT NULL,
  `password` varchar(129) NOT NULL,
  `name` varchar(101) NOT NULL,
  `school` varchar(9999) NOT NULL,
  `edollar` FLOAT NOT NULL,
  PRIMARY KEY (`userid`)
);

--
-- Truncate table before insert `student`
--

TRUNCATE TABLE `student`;
--
-- Dumping data for table `student`
--

INSERT INTO `student` (`userid`,`password`,`name`,`school`,`edollar`) VALUES
('amy.ng.2009','qwerty128','Amy NG','SIS','200'),
('ben.ng.2009','qwerty129','Ben NG','SIS','200'),
('calvin.ng.2009','qwerty130','Calvin NG','SIS','200');

-- --------------------------------------------------------

--
-- Table structure for table `prerequisite`
--

DROP TABLE IF EXISTS `prerequisite`;
CREATE TABLE IF NOT EXISTS `prerequisite` (
  `course` varchar(64) NOT NULL,
  `prerequisite` varchar(64) NOT NULL,
  PRIMARY KEY (`course`,`prerequisite`)
);

--
-- Truncate table before insert `prerequisite`
--

TRUNCATE TABLE `prerequisite`;
--
-- Dumping data for table `prerequisite`
--

INSERT INTO `prerequisite` (`course`,`prerequisite`) VALUES
('IS101','IS100'),
('IS103','IS102'),
('IS104','IS103');


-- --------------------------------------------------------



--
-- Table structure for table `course_completed`
--

DROP TABLE IF EXISTS `course_completed`;
CREATE TABLE IF NOT EXISTS `course_completed` (
  `userid` varchar(129) NOT NULL,
  `code` varchar(64) NOT NULL,
  PRIMARY KEY (`userid`,`code`)
);

--
-- Truncate table before insert `course_completed`
--

TRUNCATE TABLE `course_completed`;
--
-- Dumping data for table `course_completed`
--

INSERT INTO `course_completed` (`userid`, `code`) VALUES
('amy.ng.2009','IS100'),
('ben.ng.2009','IS102'),
('ben.ng.2009','IS103');

-- --------------------------------------------------------

--
-- Table structure for table `bid`
--

DROP TABLE IF EXISTS `bid`;
CREATE TABLE IF NOT EXISTS `bid` (
  `userid` varchar(129) NOT NULL,
  `amount` FLOAT NOT NULL,
  `code` varchar(64) NOT NULL,
  `section` varchar(4) NOT NULL,
  PRIMARY KEY (`userid`,`code`)
  );

--
-- Truncate table before insert `bid`
--

TRUNCATE TABLE `bid`;
--
-- Dumping data for table `bid`
--

INSERT INTO `bid` (`userid`, `amount`,`code`,`section`) VALUES
('ben.ng.2009','11','IS100','S1'),
('calvin.ng.2009','12','IS100','S1'),
('dawn.ng.2009','13','IS100','S1');


-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `username` varchar(128) NOT NULL,
  `password` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `admin`
--

TRUNCATE TABLE `admin`;
--
-- Dumping data for table `admin`
--
-- 
INSERT INTO `admin` (`username`, `password`, `name`) VALUES
('admin',  '$2y$10$LTfkjWkC7fPE8BDrETCzYeXsnZvxV4raTngyz1.vLlpyXewUhesBO', 'John TAN');


-- --------------------------------------------------------

--
-- Table structure for table `section-student`
--

DROP TABLE IF EXISTS `section-student`;
CREATE TABLE IF NOT EXISTS `section-student` (
  `userid` varchar(128) NOT NULL,
  `course` varchar(255) NOT NULL,
  `section` varchar(255) NOT NULL,
  `amount` FLOAT NOT NULL,
  PRIMARY KEY (`userid`,`course`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `section-student`
--

TRUNCATE TABLE `section-student`;
--
-- Dumping data for table `admin`
--
INSERT INTO `section-student` (`userid`,`course`,`section`,`amount`) VALUES
('amy.ng.2009','IS100','S1','15'),
('ben.ng.2009','IS100','S2','15'),
('calvin.ng.2009','IS101','S1','20');

-- --------------------------------------------------------


--
-- Table structure for table `minbid`
--

DROP TABLE IF EXISTS `minbid`;
CREATE TABLE IF NOT EXISTS `minbid` (
  `code` varchar(64) NOT NULL,
  `section` varchar(4) NOT NULL,
  `minimum` FLOAT NOT NULL,
  PRIMARY KEY (`code`,`section`)
  );

--
-- Truncate table before insert minbid`
--

TRUNCATE TABLE `minbid`;
--
-- Dumping data for table `minbid`;
--

-- --------------------------------------------------------
