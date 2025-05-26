<?php

require_once __DIR__ . '/../config/database.php';

/**
 * Score Model
 * Handles all scoring operations with advanced PHP 8+ features
 */
class Score extends BaseModel
{
    protected static string $table = 'scores';
    protected static string $primaryKey = 'score_id';

     /**
     * Add or update a score
     */
    public static function addOrUpdateScore(int $judgeId, int $userId, float $scoreValue, ?string $comments = null): bool
    {
        // Check if score already exists
        $sql = "SELECT id FROM scores WHERE judge_id = ? AND user_id = ?";
        $existingScore = Database::query($sql, [$judgeId, $userId])->fetch();
        
        if ($existingScore) {
            // Update existing score
            $sql = "UPDATE scores 
                    SET score_value = ?, comments = ?
                    WHERE id = ?";
            Database::query($sql, [$scoreValue, $comments, $existingScore['id']]);
        } else {
            // Insert new score
            $sql = "INSERT INTO scores 
                    (judge_id, user_id, score_value, comments) 
                    VALUES (?, ?, ?, ?)";
            Database::query($sql, [$judgeId, $userId, $scoreValue, $comments]);
        }
        
        return true;
    }

    /**
     * Get score by judge and user
     */
    public static function getByJudgeAndUser(int $judgeId, int $userId): ?array
    {
        $stmt = Database::query(
            "SELECT * FROM scores WHERE judge_id = ? AND user_id = ? LIMIT 1",
            [$judgeId, $userId]
        );

        return $stmt->fetch() ?: null;
    }

      /**
     * Get scoreboard data with rankings
     */
    public static function getScoreboard(): array
    {
        $sql = "SELECT 
                u.id as user_id,
                u.username,
                u.display_name,
                u.bio,
                COUNT(s.id) as total_judges,
                ROUND(AVG(s.score_value), 2) as average_score,
                SUM(s.score_value) as total_score,
                MAX(s.score_value) as highest_score,
                MIN(s.score_value) as lowest_score,
                MAX(s.created_at) as last_scored,
                RANK() OVER (ORDER BY SUM(s.score_value) DESC, AVG(s.score_value) DESC) as ranking
            FROM users u
            LEFT JOIN scores s ON u.id = s.user_id
            WHERE u.is_active = 1
            GROUP BY u.id, u.username, u.display_name, u.bio
            ORDER BY total_score DESC, average_score DESC, u.display_name ASC";
    
        return Database::query($sql)->fetchAll();
    }

    /**
     * Get detailed scoring matrix (judges vs participants)
     */
    public static function getScoringMatrix(): array
    {
        $stmt = Database::query(
            "SELECT 
                u.user_id,
                u.display_name as participant_name,
                j.judge_id,
                j.display_name as judge_name,
                s.score_value,
                s.comments,
                s.updated_at
            FROM users u
            CROSS JOIN judges j
            LEFT JOIN scores s ON u.user_id = s.user_id AND j.judge_id = s.judge_id
            WHERE u.is_active = 1 AND j.is_active = 1
            ORDER BY u.display_name, j.display_name"
        );

        return $stmt->fetchAll();
    }

     /**
     * Get statistics for dashboard
     */
    public static function getStatistics(): array
    {
        $sql = "SELECT 
                COUNT(DISTINCT u.id) as total_participants,
                COUNT(DISTINCT j.id) as total_judges,
                COUNT(s.id) as total_scores,
                ROUND(AVG(s.score_value), 2) as average_score,
                MAX(s.score_value) as highest_score,
                MIN(s.score_value) as lowest_score,
                MAX(s.created_at) as last_activity
            FROM users u
            CROSS JOIN judges j
            LEFT JOIN scores s ON u.id = s.user_id AND j.id = s.judge_id
            WHERE u.is_active = 1 AND j.is_active = 1";
            
        return Database::query($sql)->fetch();
    }

    /**
     * Get top performers
     */
    public static function getTopPerformers(int $limit = 5): array
    {
        $sql = "SELECT 
                u.id as user_id,
                u.display_name,
                AVG(s.score_value) as average_score,
                SUM(s.score_value) as total_score,
                COUNT(DISTINCT s.judge_id) as judges_count
            FROM users u
            JOIN scores s ON u.id = s.user_id
            GROUP BY u.id, u.display_name
            ORDER BY total_score DESC, average_score DESC
            LIMIT ?";
            
        return Database::query($sql, [$limit])->fetchAll();
    }

    /**
     * Get recent scoring activity
     */
    public static function getRecentActivity(int $limit = 10): array
    {
        $sql = "SELECT 
                s.id as score_id,
                s.judge_id,
                s.user_id,
                s.score_value,
                s.comments,
                s.created_at,
                j.display_name as judge_name,
                u.display_name as participant_name
            FROM scores s
            JOIN judges j ON s.judge_id = j.id
            JOIN users u ON s.user_id = u.id
            ORDER BY s.created_at DESC
            LIMIT ?";
            
        return Database::query($sql, [$limit])->fetchAll();
    }

    /**
     * Delete all scores for a participant
     */
    public static function deleteByUser(int $userId): bool
    {
        $stmt = Database::query(
            "DELETE FROM scores WHERE user_id = ?",
            [$userId]
        );

        return $stmt->rowCount() > 0;
    }

    /**
     * Delete all scores by a judge
     */
    public static function deleteByJudge(int $judgeId): bool
    {
        $stmt = Database::query(
            "DELETE FROM scores WHERE judge_id = ?",
            [$judgeId]
        );

        return $stmt->rowCount() > 0;
    }
}
