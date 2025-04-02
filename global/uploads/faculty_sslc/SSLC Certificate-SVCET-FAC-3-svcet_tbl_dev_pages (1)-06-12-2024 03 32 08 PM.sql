-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 05:06 AM
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
-- Database: `svcet_educnt`
--

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_pages`
--

CREATE TABLE `svcet_tbl_dev_pages` (
  `page_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_type` tinyint(1) NOT NULL COMMENT '1 - module | 2 - Page',
  `portal_type` tinyint(1) NOT NULL COMMENT '1 - faculty, 2 - student, 3 - parent, 4 - developer',
  `module_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 - faculty, 2 - student, 3 - parent',
  `page_link` text DEFAULT NULL,
  `navbar_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 - not show | 1 - show',
  `page_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `page_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_pages`
--

INSERT INTO `svcet_tbl_dev_pages` (`page_id`, `module_id`, `page_title`, `page_type`, `portal_type`, `module_status`, `page_link`, `navbar_status`, `page_status`, `page_deleted`) VALUES
(1, 1, 'Profile ', 1, 1, 1, 'faculty-profile?action=view&route=faculty', 0, 1, 0),
(2, 1, 'View Faculty Profile', 2, 1, 1, 'faculty-profile?action=view&route=faculty', 1, 1, 0),
(3, 1, 'Faculty Profile Dashboard', 2, 1, 1, 'faculty-profile?action=view&route=faculty&type=dashboard', 1, 1, 0),
(4, 1, 'Faculty Overall Profile', 2, 1, 1, 'faculty-profile?action=view&route=faculty&type=overall\n', 1, 1, 0),
(5, 1, 'Student Overall Profile || Faculty Perspective', 2, 1, 2, 'faculty-profile?action=view&route=student&type=overall', 1, 1, 0),
(6, 1, 'View Student Profile || Faculty Perspective', 2, 1, 2, 'faculty-profile?action=view&route=student', 1, 1, 0),
(7, 1, 'View Student Profile', 2, 2, 2, 'student-profile?action=view&route=student', 1, 1, 0),
(8, 1, 'View Student Profile || Parent Perspective', 2, 3, 2, 'parent-profile?action=view&route=student', 1, 1, 0),
(9, 1, 'View Parent Profile', 2, 3, 3, 'parent-profile?action=view&route=parent', 1, 1, 0),
(10, 3, 'Roles & Responsibility', 1, 1, 1, '', 0, 1, 0),
(22, 1, 'Faculty Update Profile Info', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=personal&tab=personal', 0, 1, 0),
(23, 1, 'Faculty Update Contact Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=personal&tab=contact', 0, 1, 0),
(24, 1, 'Faculty Update Address Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=personal&tab=address', 0, 1, 0),
(30, 1, 'Faculty Update Experience Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=experience&tab=industry', 0, 1, 0),
(39, 3, 'View Roles & Responsibility', 2, 2, 2, 'student-roles-responsibilities?action=view&route=student', 1, 1, 0),
(40, 3, 'View Roles & Responsibility', 2, 3, 2, 'parents-roles-responsibilities?action=view&route=student', 1, 1, 0),
(41, 2, 'Add Faculty Achievements', 2, 1, 1, 'faculty-achievements?action=add&route=faculty', 0, 1, 0),
(42, 2, 'Achievements', 1, 1, 1, '', 0, 1, 0),
(43, 2, 'View Faculty Achievements', 2, 1, 1, 'faculty-achievements?action=view&route=faculty', 1, 1, 0),
(45, 2, 'Add Student Achievements', 2, 2, 2, 'student-achievements?action=add&route=student', 0, 1, 0),
(46, 2, 'View Student Achievements ', 2, 2, 2, 'student-achievements?action=view&route=student', 1, 1, 0),
(48, 2, 'View Student Achievements || Faculty Perspective', 2, 1, 2, 'faculty-achievements?action=view&route=student&type=overall', 1, 1, 0),
(49, 1, 'Edit Profile Student', 2, 2, 2, 'student-profile?action=edit&route=student', 0, 1, 0),
(50, 1, 'Edit Profile Faculty', 2, 1, 1, 'faculty-profile?action=edit&route=faculty', 0, 1, 0),
(51, 1, 'Edit Profile Parent', 2, 3, 3, 'parent-profile?action=edit&route=parent', 0, 1, 0),
(52, 1, 'Edit PG Info ', 2, 1, 1, 'faculty-profile?action=edit&route=faculty&type=education&tab=pg', 0, 1, 0),
(53, 1, 'Edit Industry Experience Info ', 2, 1, 1, 'faculty-profile?action=edit&route=faculty&type=experience&tab=industry', 0, 1, 0),
(54, 1, 'Edit Institution Experience Info ', 2, 1, 1, 'faculty-profile?action=edit&route=faculty&type=experience&tab=institution', 0, 1, 0),
(55, 1, 'Edit Skill Expression Info ', 2, 1, 1, 'faculty-profile?action=edit&route=faculty&type=skill&tab=skill', 0, 1, 0),
(56, 1, 'Edit Document Upload Info ', 2, 1, 1, 'faculty-profile?action=edit&route=faculty&type=documentupload&tab=document', 0, 1, 0),
(57, 2, 'View Parent Achievements', 2, 3, 2, 'parent-achievements?action=view&route=student', 1, 1, 0),
(58, 1, 'Update Student Personal Profile ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=personal&tab=personal', 0, 1, 0),
(59, 1, 'Update Student Contact Info ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=personal&tab=contact', 0, 1, 0),
(60, 1, 'Update Student Address Info ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=personal&tab=address', 0, 1, 0),
(62, 1, 'Update Student schooling Info ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=education&tab=schooling', 0, 1, 0),
(63, 1, 'Update Student Degree Info ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=education&tab=degree', 0, 1, 0),
(66, 1, 'Update Student Course Preference Info ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=course&tab=course', 0, 1, 0),
(68, 1, 'Edit Personal Info', 2, 2, 2, 'student-profile?action=edit&route=faculty&type=personal&tab=personal', 0, 1, 0),
(69, 1, 'Edit Address Info', 2, 2, 2, 'student-profile?action=edit&route=student&type=personal&tab=address', 0, 1, 0),
(70, 1, 'Edit Contact Info', 2, 2, 2, 'student-profile?action=edit&route=student&type=personal&tab=contact', 0, 1, 0),
(71, 1, 'Edit SSlC Info', 2, 2, 2, 'student-profile?action=edit&route=student&type=education&tab=sslc', 0, 1, 0),
(72, 1, 'Edit HSC Info ', 2, 2, 2, 'student-profile?action=edit&route=student&type=education&tab=hsc', 0, 1, 0),
(73, 1, 'Edit Diploma Info ', 2, 2, 2, 'student-profile?action=edit&route=student&type=education&tab=diploma', 0, 1, 0),
(74, 1, 'Edit UG Info ', 2, 2, 2, 'student-profile?action=edit&route=student&type=education&tab=ug', 0, 1, 0),
(75, 1, 'Edit PG Info ', 2, 2, 2, 'student-profile?action=edit&route=student&type=education&tab=pg', 0, 1, 0),
(76, 1, 'Edit Document Upload Info ', 2, 2, 2, 'student-profile?action=edit&route=student&type=documentupload&tab=document', 0, 1, 0),
(77, 1, 'Edit Course Preference Info ', 2, 2, 2, 'student-profile?action=add&route=student&type=course&tab=course', 0, 1, 0),
(78, 1, 'Update Student Parent Info', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=personal&tab=parent', 0, 1, 0),
(79, 1, 'Edit Parent Profile', 2, 2, 2, 'student-profile?action=edit&route=faculty&type=personal&tab=parent', 0, 1, 0),
(80, 4, 'Faculty Admission ', 2, 1, 1, 'faculty-admission?action=add&route=faculty&type=entry', 0, 1, 0),
(81, 4, 'Overall Faculty Admission ', 2, 1, 1, 'faculty-admission?action=view&route=faculty&type=overall', 1, 1, 0),
(82, 4, 'Student Admission', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=personal&tab=personal', 1, 1, 0),
(83, 4, 'Overall Student Admission ', 2, 1, 2, 'faculty-student-admission?action=view&route=student&type=overall', 1, 1, 0),
(84, 2, 'Overall Faculty achievements', 2, 1, 1, 'faculty-achievements?action=view&route=faculty&type=overall', 1, 1, 0),
(86, 1, 'attendance entry form', 2, 1, 2, 'faculty-student-attendance?action=add&route=student', 0, 1, 0),
(87, 1, 'students leave request view', 2, 1, 2, 'faculty-student-attendance?action=view&route=student&type=leave_request', 0, 1, 0),
(88, 1, 'students leave request approve', 2, 1, 2, 'faculty-student-attendance?action=add&route=student&type=leave_request', 0, 1, 0),
(89, 1, 'students individual view', 2, 1, 2, 'faculty-student-attendance?action=view&route=student', 0, 1, 0),
(90, 1, 'students overall attendance view', 2, 1, 2, 'faculty-student-attendance?action=view&route=student&type=overall', 0, 1, 0),
(91, 1, 'attendance entry form', 2, 1, 2, 'student-attendance?action=add&route=student', 0, 1, 0),
(92, 1, 'attendance overall', 2, 1, 2, 'student-attendance?action=add&route=student&type=overall', 0, 1, 0),
(93, 1, 'attendance status view', 2, 1, 2, 'student-attendance?action=view&route=student', 0, 1, 0),
(94, 1, 'Student Admission Dashboard', 2, 1, 2, 'faculty-student-admission?action=view&route=student&type=dashboard', 0, 1, 0),
(95, 1, 'Overal Individual View Profile', 2, 1, 1, 'faculty-profile?action=view&route=faculty&type=overall&id=*', 1, 1, 0),
(96, 1, 'academic calendar', 2, 1, 2, 'faculty-academic-calendar?action=view&route=faculty', 0, 1, 0),
(97, 1, 'academic add calendar', 2, 1, 2, 'faculty-academic-calendar?action=add&route=faculty', 0, 1, 0),
(98, 1, 'Faculty Main Dashboard', 2, 1, 1, 'faculty-profile', 1, 1, 0),
(99, 3, 'View Faculty Authorities', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=faculty&type=authorities', 1, 1, 0),
(100, 3, 'View Faculty Committees', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=faculty&type=committees', 1, 1, 0),
(101, 3, 'View Faculty Class Advisors', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=faculty&type=class_advisors', 1, 1, 0),
(102, 3, 'View Faculty Mentors', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=faculty&type=mentors', 1, 1, 0),
(103, 3, 'Edit Faculty Authorities', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=faculty&type=authorities', 1, 1, 0),
(104, 3, 'Edit Faculty Committees', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=faculty&type=committees', 1, 1, 0),
(105, 3, 'Edit Faculty Class Advisors', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=faculty&type=class_advisors', 1, 1, 0),
(106, 3, 'Edit Faculty Mentors', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=faculty&type=mentors', 1, 1, 0),
(107, 3, 'View Student Committees || Faculty Perspective', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=student&type=committees', 1, 1, 0),
(108, 3, 'View Student Representatives || Faculty Perspective', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=student&type=representatives', 1, 1, 0),
(109, 3, 'Edit Student Committees || Faculty Perspective', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=student&type=committees', 1, 1, 0),
(110, 3, 'Edit Student Representatives || Faculty Perspective', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=student&type=representatives', 1, 1, 0),
(115, 1, 'Faculty Update Official Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=personal&tab=official', 0, 1, 0),
(117, 1, 'Faculty Update Degrees Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=education&tab=degrees', 0, 1, 0),
(119, 1, 'Faculty Update Schoolings Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=education&tab=schoolings', 0, 1, 0),
(120, 1, 'Faculty Update Skills Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=skill&tab=knowledge', 0, 1, 0),
(121, 1, 'Faculty Update Document Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=upload&tab=document', 0, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `svcet_tbl_dev_pages`
--
ALTER TABLE `svcet_tbl_dev_pages`
  ADD PRIMARY KEY (`page_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_pages`
--
ALTER TABLE `svcet_tbl_dev_pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
