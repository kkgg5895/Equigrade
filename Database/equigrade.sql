-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 11:54 AM
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
-- Database: `equigrade`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `rubric_id` int(11) DEFAULT NULL,
  `ai_score` decimal(5,2) DEFAULT NULL,
  `final_score` decimal(5,2) DEFAULT NULL,
  `confidence` decimal(4,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `graded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `submission_id`, `rubric_id`, `ai_score`, `final_score`, `confidence`, `feedback`, `graded_by`, `graded_at`) VALUES
(1, 1, NULL, 95.00, 93.30, 0.95, 'Manual Check Done', 2, '2025-10-23 20:49:46'),
(2, 7, NULL, 59.00, NULL, 1.00, 'Difficult to check through AI', NULL, '2025-10-25 19:13:23'),
(3, 4, NULL, 78.00, NULL, 1.00, 'na', NULL, '2025-10-25 19:48:06'),
(4, 10, NULL, 77.00, 77.00, 81.00, 'Fair attempt. Try improving organization, examples, and coherence for a stronger impact. (AI Confidence: 81%)', NULL, '2025-10-25 19:49:06'),
(5, 11, NULL, 80.00, 80.00, 84.00, 'Good job! Clear presentation with minor improvements needed in structure and analysis. (AI Confidence: 84%)', NULL, '2025-10-25 19:49:20'),
(6, 12, NULL, 71.00, 71.00, 93.00, 'Fair attempt. Try improving organization, examples, and coherence for a stronger impact. (AI Confidence: 93%)', NULL, '2025-10-25 19:51:47'),
(7, 13, NULL, 87.00, 87.00, 92.00, 'Good job! Clear presentation with minor improvements needed in structure and analysis. (AI Confidence: 92%)', NULL, '2025-10-26 19:45:36'),
(8, 14, NULL, 86.00, 86.00, 81.00, 'Good job! Clear presentation with minor improvements needed in structure and analysis. (AI Confidence: 81%)', NULL, '2025-10-27 13:18:02'),
(9, 14, NULL, 36.00, NULL, 1.00, 'Manually Check', NULL, '2025-10-27 13:18:49'),
(10, 15, NULL, 78.00, NULL, 85.00, 'Rubric-based AI grading:\nContent: 90% (weight 40%); Structure: 60% (weight 30%); Research: 80% (weight 30%)', NULL, '2025-10-27 15:22:59'),
(11, 16, NULL, 78.00, NULL, 95.00, 'Rubric-based AI grading:\nContent: 90% (weight 40%); Structure: 60% (weight 30%); Research: 80% (weight 30%)', NULL, '2025-10-27 15:27:08'),
(12, 17, NULL, 87.00, 67.30, 70.00, 'Manual override', 2, '2025-10-30 16:50:40'),
(13, 17, NULL, 67.00, 67.30, 1.00, 'Manual override', 2, '2025-10-30 16:50:40'),
(14, 17, NULL, 67.33, 67.30, 1.00, 'Manual override', 2, '2025-10-30 16:50:40'),
(15, 17, NULL, 38.43, 67.30, 1.00, 'Manual override', 2, '2025-10-30 16:50:40'),
(16, 18, NULL, 72.00, 72.00, 0.60, 'No rubric found; simulated score applied.', 1, '2025-10-30 16:52:01'),
(17, 19, 4, NULL, NULL, NULL, NULL, NULL, '2025-10-30 17:52:52'),
(18, 20, 4, 85.00, 85.00, 99.00, 'AI feedback unavailable, fallback simulated score used.', NULL, '2025-10-30 17:56:20'),
(19, 21, 4, 83.00, 83.00, 92.00, 'AI feedback unavailable, fallback simulated score used.', NULL, '2025-10-30 18:05:48'),
(20, 22, 4, 82.00, 82.00, 94.00, 'AI feedback unavailable, fallback simulated score used.', NULL, '2025-10-30 18:08:48'),
(21, 24, NULL, 80.00, 75.00, NULL, 'Decent understanding of Android development concepts, but requires more depth and better organization.', NULL, '2025-10-30 18:53:43'),
(22, 25, NULL, 80.00, 75.00, NULL, 'Decent understanding of the topic, but requires more depth and better organization.', NULL, '2025-10-30 18:54:21'),
(23, 26, NULL, 75.00, 75.00, NULL, 'Decent understanding of Android development, but requires more detailed content and better structure.', NULL, '2025-10-30 18:58:44'),
(24, 27, NULL, 85.00, 85.00, NULL, 'Strong submission with accurate content and good technical depth; minor improvements needed in clarity and conciseness.', NULL, '2025-10-30 19:24:12'),
(25, 31, NULL, 0.00, 0.00, 99.99, 'No readable text was extracted from the file. Please ensure the file contains text content and is not corrupted or encrypted.', NULL, '2025-11-05 16:01:40'),
(26, 32, NULL, 88.00, 88.00, 95.00, 'The report is well-structured and detailed, effectively outlining the high availability setup using Windows Server 2022. To enhance clarity, consider simplifying technical jargon for broader accessibility and adding a conclusion section summarizing key outcomes and future recommendations.', NULL, '2025-11-05 16:01:58'),
(27, 33, NULL, 88.00, 88.00, 95.00, 'The report is well-structured and detailed, effectively outlining the high availability setup using Windows Server 2022. However, consider adding more diagrams to visually support the textual descriptions and ensure all technical terms are clearly defined for readers unfamiliar with the subject.', NULL, '2025-11-05 16:11:16'),
(28, 34, NULL, 85.00, 85.00, 90.00, 'Your essay effectively outlines the basic concepts of Android development, including the structure of apps and design patterns. However, to enhance your essay, consider adding more detailed examples of how these concepts are applied in real-world applications. Additionally, incorporating case studies or more in-depth analysis of the sources and references could provide a stronger evidence-based argument and enrich your discussion.', NULL, '2025-11-10 19:55:26'),
(29, 35, NULL, 0.00, 0.00, 0.00, 'Your submission does not contain any readable text. Please ensure that the file you submit is not corrupted and is in a format that supports text extraction, such as a PDF, DOCX, or TXT file. If you need assistance with file formats or submission guidelines, please reach out for help.', NULL, '2025-11-10 19:56:09'),
(30, 36, NULL, 88.00, 88.00, 95.00, 'Krish, you have demonstrated a strong understanding of virtualization and high-availability cluster configurations. Your submission is well-structured, and the detailed commands and verification steps show a good grasp of practical implementation. However, to improve further, consider adding more detailed explanations of the rationale behind each configuration choice, which will enhance the understanding of the design decisions for anyone reviewing your work. Additionally, ensure all sections are thoroughly proofread to maintain professionalism in your documentation.', NULL, '2025-11-10 19:56:37'),
(31, 37, NULL, 85.00, 85.00, 90.00, 'Your submission demonstrates a strong understanding of IPv4 and IPv6 addressing, subnetting, and routing configurations. The network diagram and address planning are well-organized, making it easy to follow the network structure and data flow. However, there are a few areas for improvement: 1) Ensure consistency in the presentation of data, especially in tables where alignment and formatting can help in better readability. 2) Expand on the explanation of NAT-PT configuration and its purpose in the network to provide a clearer understanding of its role. 3) It would be beneficial to include a brief discussion on the choice of EIGRP as the routing protocol and its advantages in your network design. Overall, a very competent submission with room for minor enhancements.', NULL, '2025-11-10 20:04:02'),
(32, 38, NULL, 90.00, 90.00, 85.00, 'The submission demonstrates a strong understanding of network configuration, particularly with IPv6 addressing and routing. The structure is logical, though some areas could benefit from clearer separation and headings for better readability. Critical thinking is evident in the setup of routing protocols and address planning, reflecting a practical approach to network design. Presentation could be enhanced by more consistent formatting. Originality is moderate, with standard implementation techniques used effectively.', NULL, '2025-11-10 20:09:02'),
(33, 39, NULL, 89.00, 89.00, 95.00, 'The submission demonstrates a strong understanding of network configuration and address planning. The IPv4 and IPv6 configurations are accurately detailed. However, the structure could be improved for better readability and clarity, especially in separating different sections and configurations. Originality is moderate, with standard configurations used effectively. Further enhancement in presentation could involve clearer delineation of sections and perhaps visual aids like diagrams.', NULL, '2025-11-10 20:38:14');

-- --------------------------------------------------------

--
-- Table structure for table `overrides`
--

CREATE TABLE `overrides` (
  `override_id` int(11) NOT NULL,
  `grade_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `previous_score` decimal(5,2) DEFAULT NULL,
  `new_score` decimal(5,2) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `overridden_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `overrides`
--

INSERT INTO `overrides` (`override_id`, `grade_id`, `teacher_id`, `previous_score`, `new_score`, `reason`, `overridden_at`) VALUES
(1, 1, 2, 93.10, 93.30, 'Manual Check Done', '2025-10-23 20:49:44'),
(2, 1, 2, 93.30, 93.30, 'Manual Check Done', '2025-10-23 20:49:46');

-- --------------------------------------------------------

--
-- Table structure for table `rubrics`
--

CREATE TABLE `rubrics` (
  `rubric_id` int(11) NOT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `criteria_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`criteria_json`)),
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `course` varchar(50) DEFAULT NULL,
  `rubric_title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rubrics`
--

INSERT INTO `rubrics` (`rubric_id`, `assignment_id`, `title`, `description`, `criteria_json`, `created_by`, `created_at`, `course`, `rubric_title`) VALUES
(1, NULL, 'Essay Grading Rubric', 'Evaluates essay structure, clarity, and referencing.', NULL, 2, '2025-10-23 20:58:22', NULL, NULL),
(2, NULL, 'Wwe', '2', NULL, 2, '2025-10-25 19:10:14', NULL, NULL),
(3, NULL, 'Android Development Rubric', 'Evaluates content, structure, and research quality of student submissions.', '[\r\n    {\"criterion\": \"content\", \"weight\": 40},\r\n    {\"criterion\": \"structure\", \"weight\": 30},\r\n    {\"criterion\": \"research\", \"weight\": 30}\r\n  ]', 1, '2025-10-27 15:17:28', 'NIT3213', NULL),
(4, NULL, '', NULL, '[{\"criterion\":\"Content\",\"weight\":40},{\"criterion\":\"Structure\",\"weight\":30},{\"criterion\":\"Research\",\"weight\":30}]', 1, '2025-10-27 16:19:23', 'NIT3213', 'Essay Rubric'),
(5, NULL, ': Android Activity Lifecycle — Technical Report', 'Write a short technical report (300–400 words) explaining the Android Activity Lifecycle.\r\nYour report should describe each stage (onCreate, onStart, onResume, onPause, onStop, onDestroy)\r\nand discuss why lifecycle management is crucial for performance and stability in Android apps.\r\nInclude at least one example scenario (e.g., handling configuration changes or saving UI state).', '[{\"criterion\":\"Content Accuracy\",\"weight\":40},{\"criterion\":\"Technical Depth\",\"weight\":30},{\"criterion\":\"Clarity and Structure\",\"weight\":20},{\"criterion\":\"Writing and Structure\",\"weight\":10}]', 2, '2025-10-30 19:13:48', 'BN204', ': Android Activity Lifecycle — Technical Report'),
(6, NULL, ': Android Activity Lifecycle — Technical Report', 'Write a short technical report (300–400 words) explaining the Android Activity Lifecycle.\r\nYour report should describe each stage (onCreate, onStart, onResume, onPause, onStop, onDestroy)\r\nand discuss why lifecycle management is crucial for performance and stability in Android apps.\r\nInclude at least one example scenario (e.g., handling configuration changes or saving UI state).', '[{\"criterion\":\"Content Accuracy\",\"weight\":40},{\"criterion\":\"Technical Depth\",\"weight\":30},{\"criterion\":\"Clarity and Structure\",\"weight\":20},{\"criterion\":\"Writing Quality\",\"weight\":10}]', 2, '2025-10-30 19:15:27', 'NIT3213', ': Android Activity Lifecycle — Technical Report');

-- --------------------------------------------------------

--
-- Table structure for table `rubric_criteria`
--

CREATE TABLE `rubric_criteria` (
  `criterion_id` int(11) NOT NULL,
  `rubric_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `weight` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rubric_criteria`
--

INSERT INTO `rubric_criteria` (`criterion_id`, `rubric_id`, `name`, `description`, `weight`) VALUES
(1, 1, 'Content Quality', 'Quality of ideas and arguments.', 40.00),
(2, 1, 'Structure and Flow', 'Logical Flow and clear organization', 30.00),
(3, 1, 'Grammer and Clarity', 'Grammer, Spelling and clarity', 20.00),
(4, 1, 'References', 'Proper Referencing and Criterion', 10.00),
(5, 2, 'str', 'wwe', 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submission_id` int(11) NOT NULL,
  `assignment_title` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `rubric_id` int(11) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','graded') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`submission_id`, `assignment_title`, `user_id`, `assignment_id`, `course`, `rubric_id`, `filename`, `file_path`, `submitted_at`, `status`) VALUES
(1, '', 1, NULL, NULL, NULL, 'Assignment1_KrishGirdhar_s8074216.docx', 'C:\\XAMPPP\\htdocs\\equigrade-frontend/uploads/d891ee9cac27f2d3.docx', '2025-10-23 09:33:19', 'pending'),
(4, '2', 1, NULL, 'BN206', NULL, 'Assignment_1_Virtualisation.docx', '', '2025-10-25 07:52:16', 'pending'),
(5, '2', 1, NULL, 'BN204', NULL, 'Medium_Quality_Essay_Climate_Change.pdf', '', '2025-10-25 07:52:48', 'pending'),
(6, 'Essay', 1, NULL, 'BN206', NULL, 'Medium_Quality_Essay_Climate_Change.pdf', '', '2025-10-25 07:56:21', 'pending'),
(7, 'XD', 1, NULL, 'NIT3213', NULL, 'Medium_Quality_Essay_Climate_Change.pdf', '', '2025-10-25 08:02:15', 'pending'),
(8, '2', 1, NULL, 'BN206', NULL, '1761380954_Medium_Quality_Essay_Climate_Change.pdf', '', '2025-10-25 08:29:14', 'pending'),
(9, '25', 1, NULL, 'BN204', NULL, '1761381863_Medium_Quality_Essay_Climate_Change.pdf', '', '2025-10-25 08:44:23', 'pending'),
(10, '2', 1, NULL, 'BN204', NULL, '1761382146_Medium_Quality_Essay_Climate_Change.pdf', 'C:\\XAMPPP\\htdocs\\equigrade-frontend/uploads/1761382146_Medium_Quality_Essay_Climate_Change.pdf', '2025-10-25 08:49:06', 'pending'),
(11, '3', 1, NULL, 'BN206', NULL, '1761382160_Medium_Quality_Essay_Climate_Change.pdf', 'C:\\XAMPPP\\htdocs\\equigrade-frontend/uploads/1761382160_Medium_Quality_Essay_Climate_Change.pdf', '2025-10-25 08:49:20', 'pending'),
(12, 'Essay', 1, NULL, 'BN206', NULL, '1761382307_Medium_Quality_Essay_Climate_Change.pdf', 'C:\\XAMPPP\\htdocs\\equigrade-frontend/uploads/1761382307_Medium_Quality_Essay_Climate_Change.pdf', '2025-10-25 08:51:47', 'pending'),
(13, '3', 1, NULL, 'BN206', NULL, '1761468336_PMAF-2024.pdf', 'C:\\XAMPPP\\htdocs\\equigrade-frontend/uploads/1761468336_PMAF-2024.pdf', '2025-10-26 08:45:36', 'pending'),
(14, 'Essay 1', 3, NULL, 'BN206', NULL, '1761531481_Assignment1_KrishGirdhar_s8074216.docx', 'C:\\XAMPPP\\htdocs\\equigrade-frontend/uploads/1761531481_Assignment1_KrishGirdhar_s8074216.docx', '2025-10-27 02:18:02', 'pending'),
(15, 'Essay 2', 1, NULL, 'NIT3213', NULL, '1761538979_2.txt', '', '2025-10-27 04:22:59', 'pending'),
(16, '1', 3, NULL, 'NIT3213', NULL, '1761539228_2.txt', '', '2025-10-27 04:27:08', 'pending'),
(17, '2', 1, NULL, 'NIT3222', NULL, '1761539599_Assignment_1_Virtualisation.docx', '', '2025-10-27 04:33:19', 'pending'),
(18, '3', 1, NULL, 'BN206', NULL, '1761803521_Assignment_1_Virtualisation.docx', '', '2025-10-30 05:52:01', 'pending'),
(19, '3', 1, NULL, 'NIT3123', NULL, '1761807172_Medium_Quality_Essay_Climate_Change.pdf', '', '2025-10-30 06:52:52', 'pending'),
(20, '3', 1, NULL, 'NIT3123', NULL, '1761807380_Medium_Quality_Essay_Climate_Change.pdf', '', '2025-10-30 06:56:20', 'pending'),
(21, 'Essay 1', 3, NULL, 'BN206', NULL, 'Medium_Quality_Essay_Climate_Change.pdf', '', '2025-10-30 07:05:48', 'pending'),
(22, 'Essay 1', 3, NULL, 'BN206', NULL, 'Medium_Quality_Essay_Climate_Change.pdf', '', '2025-10-30 07:08:48', 'pending'),
(23, 'Essay 5', 1, NULL, 'NIT3213', 4, '1761809091_Medium_Quality_Essay_Climate_Change.pdf', '', '2025-10-30 07:24:51', 'pending'),
(24, 'Essay', 1, NULL, 'NIT3213', 4, '690319847e82b_2.txt', '', '2025-10-30 07:53:43', 'pending'),
(25, 'Essay 7', 3, NULL, 'NIT3213', 4, '690319a8966b9_2.txt', '', '2025-10-30 07:54:21', 'pending'),
(26, 'Essay 8', 3, NULL, 'NIT3213', 4, '1761811121_2.txt', '', '2025-10-30 07:58:44', 'pending'),
(27, 'Android_Lifecycle_Report', 3, NULL, 'NIT3213', 6, '1761812647_Understanding the Android Activity.txt', '', '2025-10-30 08:24:12', 'pending'),
(28, '2', 3, NULL, '1', NULL, '1761886480_Assignment1_KrishGirdhar_s8074216.docx', '', '2025-10-31 04:54:40', 'pending'),
(29, 'Andorid Lifestyle', 3, NULL, 'NIT3213', NULL, '1761887297_Assignment_1_Virtualisation.docx', '', '2025-10-31 05:08:17', 'pending'),
(30, '2', 3, NULL, 'BN204', NULL, '1761887359_2.txt', '', '2025-10-31 05:09:19', 'pending'),
(31, 'Android', 1, NULL, 'N/A', NULL, 'submission_690ada30415541.32572952.pdf', '', '2025-11-05 05:01:40', 'pending'),
(32, 'Android', 1, NULL, 'N/A', NULL, 'submission_690ada41635c41.71264286.docx', '', '2025-11-05 05:01:58', 'pending'),
(33, 'Android', 1, NULL, 'NIT3213 — Android Development', NULL, 'submission_690adc71b72515.13869251.docx', '', '2025-11-05 05:11:16', 'pending'),
(34, 'Android_Lifecycle_Report', 1, NULL, 'NIT3213 — Android Development', NULL, 'submission_6911a86a78e930.46998076.txt', '', '2025-11-10 08:55:26', 'pending'),
(35, 'Android_Lifecycle_Report', 1, NULL, 'NIT3213 — Android Development', NULL, 'submission_6911a8a50e8e99.56950876.pdf', '', '2025-11-10 08:56:09', 'pending'),
(36, 'Android_Lifecycle_Report', 1, NULL, 'NIT3213 — Android Development', NULL, 'submission_6911a8c0cb5eb3.57444503.docx', '', '2025-11-10 08:56:37', 'pending'),
(37, 'Practical', 4, NULL, 'NIT3213 — Android Development', NULL, 'submission_6911aa79eb1973.83673645.docx', '', '2025-11-10 09:04:02', 'pending'),
(38, 'Practical', 4, NULL, 'NIT3213 — Android Development', NULL, 'submission_6911aba1ef7b36.72208530.docx', '', '2025-11-10 09:09:02', 'pending'),
(39, '25', 1, NULL, 'NIT3213 — Android Development', NULL, 'submission_6911b27c3be105.49185776.docx', '', '2025-11-10 09:38:14', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('student','teacher','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `password_migrated` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `role`, `created_at`, `reset_token`, `reset_expires`, `password_migrated`) VALUES
(1, 'Krish Girdhar', 's8074216@live.vu.edu.au', '$2y$10$22f2Wfn6iHmZ2eLBxNCKl.zAf6dgNzNWPnIk71GzCoBn.6KGs9rie', 'student', '2025-10-23 09:28:28', 'd044d62640be7344dd58daf2024e337f17ea56443bfd4b2e8fe803a3aaf98fc8', '2025-11-09 09:53:00', 1),
(2, 'Mehakpreet Kaur', 's8074762@live.vu.edu.au', '$2y$10$6PhzQPLluKSrwSy4ejRf7uNJgC7aRftPj7OKKUCWbJ5E310u4vUja', 'teacher', '2025-10-23 09:28:45', NULL, NULL, 1),
(3, 'Amandeep Kaur', 's8135868@live.vu.edu.au', '$2y$10$fo5lxcnbXRMbKap0k669Juf6.zp5HSciICbAR5M7cQEQ/LLJ5yBPq', 'student', '2025-10-27 02:17:18', NULL, NULL, 1),
(4, 'Ritish Girdhar', 'kkgg5895@gmail.com', '$2y$10$Z8tFWp4fg3NInpLd.y0vYufKsRvVnyM0VUzSjh7WDJL/mWwR5lAlK', 'student', '2025-11-10 09:00:27', NULL, NULL, 0),
(5, 'Administrator', 'admin@equigrade.com', '$2y$10$C79M7f8MiQd/uxb9PK5h9O2f2U/8WqsBj7TtERETfx4GMM8k4R1SG', 'admin', '2025-11-10 09:29:35', NULL, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `overrides`
--
ALTER TABLE `overrides`
  ADD PRIMARY KEY (`override_id`),
  ADD KEY `grade_id` (`grade_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `rubrics`
--
ALTER TABLE `rubrics`
  ADD PRIMARY KEY (`rubric_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `rubric_criteria`
--
ALTER TABLE `rubric_criteria`
  ADD PRIMARY KEY (`criterion_id`),
  ADD KEY `rubric_id` (`rubric_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `idx_sub_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `overrides`
--
ALTER TABLE `overrides`
  MODIFY `override_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rubrics`
--
ALTER TABLE `rubrics`
  MODIFY `rubric_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rubric_criteria`
--
ALTER TABLE `rubric_criteria`
  MODIFY `criterion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `overrides`
--
ALTER TABLE `overrides`
  ADD CONSTRAINT `overrides_ibfk_1` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`grade_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `overrides_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `rubrics`
--
ALTER TABLE `rubrics`
  ADD CONSTRAINT `rubrics_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `rubric_criteria`
--
ALTER TABLE `rubric_criteria`
  ADD CONSTRAINT `rubric_criteria_ibfk_1` FOREIGN KEY (`rubric_id`) REFERENCES `rubrics` (`rubric_id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_submission_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
