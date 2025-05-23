-- Initialize Judge Scoring System Database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `judge_scoring` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `judge_scoring`;

-- --------------------------------------------------------
-- Table structure for table `judges`
-- --------------------------------------------------------

CREATE TABLE `judges` (
  `judge_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `display_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`judge_id`),
  UNIQUE KEY `username` (`username`),
  INDEX `idx_active_judges` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `users` (participants)
-- --------------------------------------------------------

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `display_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  INDEX `idx_active_users` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `scores`
-- --------------------------------------------------------

CREATE TABLE `scores` (
  `score_id` int(11) NOT NULL AUTO_INCREMENT,
  `judge_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score_value` int(3) NOT NULL CHECK (`score_value` >= 1 AND `score_value` <= 100),
  `comments` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`score_id`),
  UNIQUE KEY `unique_judge_user` (`judge_id`, `user_id`),
  FOREIGN KEY (`judge_id`) REFERENCES `judges` (`judge_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  INDEX `idx_score_value` (`score_value`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `events` (for future enhancement)
-- --------------------------------------------------------

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`),
  INDEX `idx_active_events` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Insert sample judges
-- --------------------------------------------------------

INSERT INTO `judges` (`username`, `display_name`, `email`) VALUES
('judge_paul', 'Paul Owayo', 'paul@example.com'),
('judge_alex', 'Alex Muturi', 'alex@example.com'),
('judge_moraa', 'Brenda Moraa', 'brenda@example.com'),
('judge_david', 'David Brown', 'davi@example.com'),
('tech_expert', 'Tech Expert Panel', 'tech@example.com');

-- --------------------------------------------------------
-- Insert sample participants (users)
-- --------------------------------------------------------

INSERT INTO `users` (`username`, `display_name`, `email`, `bio`) VALUES
('participant_001', 'John Doe', 'john@participant.com', 'Full-stack developer with 5 years experience'),
('participant_002', 'Jane Smith', 'jane@participant.com', 'Frontend specialist and UI/UX designer'),
('participant_003', 'Mike Johnson', 'mike@participant.com', 'Backend engineer specializing in microservices'),
('participant_004', 'Sarah Williams', 'sarah@participant.com', 'DevOps engineer and cloud architect'),
('participant_005', 'Tom Wilson', 'tom@participant.com', 'Mobile app developer (iOS/Android)'),
('participant_006', 'Lisa Davis', 'lisa@participant.com', 'Data scientist and ML engineer'),
('participant_007', 'Alex Miller', 'alex@participant.com', 'Security researcher and penetration tester'),
('participant_008', 'Emma Garcia', 'emma@participant.com', 'Product manager with technical background');

-- --------------------------------------------------------
-- Insert sample scores to demonstrate functionality
-- --------------------------------------------------------

INSERT INTO `scores` (`judge_id`, `user_id`, `score_value`, `comments`) VALUES
-- Judge Alice's scores
(1, 1, 85, 'Excellent problem-solving approach'),
(1, 2, 92, 'Outstanding frontend implementation'),
(1, 3, 78, 'Good backend architecture'),
(1, 4, 88, 'Impressive DevOps knowledge'),

-- Judge Bob's scores
(2, 1, 82, 'Solid technical foundation'),
(2, 2, 95, 'Creative UI design solutions'),
(2, 3, 75, 'Clean code structure'),
(2, 5, 90, 'Mobile app expertise evident'),

-- Judge Carol's scores
(3, 1, 87, 'Strong analytical thinking'),
(3, 6, 94, 'Exceptional data insights'),
(3, 7, 91, 'Security-first mindset'),
(3, 8, 83, 'Good technical-business balance'),

-- Judge David's scores
(4, 2, 89, 'User experience focus'),
(4, 4, 85, 'Infrastructure knowledge'),
(4, 6, 88, 'Data visualization skills'),
(4, 7, 86, 'Security implementation');

-- --------------------------------------------------------
-- Create views for easier data access
-- --------------------------------------------------------

-- View for scoreboard with aggregated scores
CREATE VIEW `scoreboard_view` AS
SELECT 
    u.user_id,
    u.username,
    u.display_name,
    COUNT(s.score_id) as total_judges,
    ROUND(AVG(s.score_value), 2) as average_score,
    SUM(s.score_value) as total_score,
    MAX(s.score_value) as highest_score,
    MIN(s.score_value) as lowest_score,
    MAX(s.updated_at) as last_scored
FROM users u
LEFT JOIN scores s ON u.user_id = s.user_id
WHERE u.is_active = 1
GROUP BY u.user_id, u.username, u.display_name
ORDER BY total_score DESC, average_score DESC;

-- View for judge scoring progress
CREATE VIEW `judge_progress_view` AS
SELECT 
    j.judge_id,
    j.username as judge_username,
    j.display_name as judge_name,
    COUNT(s.score_id) as scores_given,
    (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_participants,
    ROUND((COUNT(s.score_id) / (SELECT COUNT(*) FROM users WHERE is_active = 1)) * 100, 2) as completion_percentage
FROM judges j
LEFT JOIN scores s ON j.judge_id = s.judge_id
WHERE j.is_active = 1
GROUP BY j.judge_id, j.username, j.display_name;

-- --------------------------------------------------------
-- Create stored procedures for common operations
-- --------------------------------------------------------

DELIMITER $$

-- Procedure to add or update a score
CREATE PROCEDURE AddOrUpdateScore(
    IN p_judge_id INT,
    IN p_user_id INT,
    IN p_score_value INT,
    IN p_comments TEXT
)
BEGIN
    DECLARE score_exists INT DEFAULT 0;
    
    -- Check if score already exists
    SELECT COUNT(*) INTO score_exists 
    FROM scores 
    WHERE judge_id = p_judge_id AND user_id = p_user_id;
    
    IF score_exists > 0 THEN
        -- Update existing score
        UPDATE scores 
        SET score_value = p_score_value, 
            comments = p_comments,
            updated_at = CURRENT_TIMESTAMP
        WHERE judge_id = p_judge_id AND user_id = p_user_id;
    ELSE
        -- Insert new score
        INSERT INTO scores (judge_id, user_id, score_value, comments)
        VALUES (p_judge_id, p_user_id, p_score_value, p_comments);
    END IF;
END$$

-- Function to get participant's current ranking
CREATE FUNCTION GetParticipantRanking(p_user_id INT) 
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE user_rank INT DEFAULT 0;
    
    SELECT ranking INTO user_rank
    FROM (
        SELECT user_id,
               RANK() OVER (ORDER BY SUM(score_value) DESC, AVG(score_value) DESC) as ranking
        FROM scores
        GROUP BY user_id
    ) as rankings
    WHERE user_id = p_user_id;
    
    RETURN IFNULL(user_rank, 999);
END$$

DELIMITER ;

COMMIT;
