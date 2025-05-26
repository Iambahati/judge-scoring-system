-- Initialize Judge Scoring System Database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `judge_scoring` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `judge_scoring`;

-- Table structure for table `judges`
CREATE TABLE `judges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  INDEX `idx_active_judges` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `bio` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  INDEX `idx_active_users` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `scores`
CREATE TABLE `scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judge_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score_value` decimal(5,2) NOT NULL,
  `comments` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`judge_id`) REFERENCES `judges` (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `events`
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_active_events` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample judges
INSERT INTO `judges` (`username`, `display_name`, `email`) VALUES
('judge_paul', 'Paul Owayo', 'paul@example.com'),
('judge_alex', 'Alex Muturi', 'alex@example.com'),
('judge_moraa', 'Brenda Moraa', 'brenda@example.com'),
('judge_david', 'David Brown', 'david@example.com'),
('tech_expert', 'Tech Expert Panel', 'tech@example.com');

-- Insert sample participants
INSERT INTO `users` (`username`, `display_name`, `email`, `bio`) VALUES
('participant_001', 'John Doe', 'john@participant.com', 'Full-stack developer with 5 years experience'),
('participant_002', 'Jane Smith', 'jane@participant.com', 'Frontend specialist and UI/UX designer'),
('participant_003', 'Mike Johnson', 'mike@participant.com', 'Backend engineer specializing in microservices'),
('participant_004', 'Sarah Williams', 'sarah@participant.com', 'DevOps engineer and cloud architect'),
('participant_005', 'Tom Wilson', 'tom@participant.com', 'Mobile app developer (iOS/Android)'),
('participant_006', 'Lisa Davis', 'lisa@participant.com', 'Data scientist and ML engineer'),
('participant_007', 'Alex Miller', 'alex@participant.com', 'Security researcher and penetration tester'),
('participant_008', 'Emma Garcia', 'emma@participant.com', 'Product manager with technical background');

-- Insert sample scores
INSERT INTO `scores` (`judge_id`, `user_id`, `score_value`, `comments`) VALUES
(1, 1, 85, 'Excellent problem-solving approach'),
(1, 2, 92, 'Outstanding frontend implementation'),
(1, 3, 78, 'Good backend architecture'),
(1, 4, 88, 'Impressive DevOps knowledge'),
(2, 1, 82, 'Solid technical foundation'),
(2, 2, 95, 'Creative UI design solutions'),
(2, 3, 75, 'Clean code structure'),
(2, 5, 90, 'Mobile app expertise evident'),
(3, 1, 87, 'Strong analytical thinking'),
(3, 6, 94, 'Exceptional data insights'),
(3, 7, 91, 'Security-first mindset'),
(3, 8, 83, 'Good technical-business balance'),
(4, 2, 89, 'User experience focus'),
(4, 4, 85, 'Infrastructure knowledge'),
(4, 6, 88, 'Data visualization skills'),
(4, 7, 86, 'Security implementation');

COMMIT;