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
     * Add or update a score (upsert operation)
     * Demonstrates transaction handling and error management
     */
    public static function addOrUpdateScore(
        int $judgeId, 
        int $userId, 
        int $scoreValue, 
        ?string $comments = null
    ): bool {
        // Validate score value
        if (!Utils::isValidScore($scoreValue)) {
            $config = Config::getInstance();
            throw new InvalidArgumentException(
                "Score must be between {$config->scoreMin} and {$config->scoreMax}"
            );
        }
        
        try {
            Database::beginTransaction();
            
            // Check if score already exists
            $existingScore = self::getByJudgeAndUser($judgeId, $userId);
            
            if ($existingScore) {
                // Update existing score
                $updated = self::update($existingScore['score_id'], [
                    'score_value' => $scoreValue,
                    'comments' => $comments,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Create new score
                $scoreId = self::create([
                    'judge_id' => $judgeId,
                    'user_id' => $userId,
                    'score_value' => $scoreValue,
                    'comments' => $comments
                ]);
                $updated = !empty($scoreId);
            }
            
            Database::commit();
            return $updated;
            
        } catch (Exception $e) {
            Database::rollback();
            throw new Exception("Failed to save score: " . $e->getMessage());
        }
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
     * Demonstrates complex SQL with window functions
     */
    public static function getScoreboard(): array
    {
        $stmt = Database::query(
            "SELECT 
                u.user_id,
                u.username,
                u.display_name,
                u.bio,
                COUNT(s.score_id) as total_judges,
                COALESCE(ROUND(AVG(s.score_value), 2), 0) as average_score,
                COALESCE(SUM(s.score_value), 0) as total_score,
                COALESCE(MAX(s.score_value), 0) as highest_score,
                COALESCE(MIN(s.score_value), 0) as lowest_score,
                MAX(s.updated_at) as last_scored,
                RANK() OVER (ORDER BY COALESCE(SUM(s.score_value), 0) DESC, COALESCE(AVG(s.score_value), 0) DESC) as ranking
            FROM users u
            LEFT JOIN scores s ON u.user_id = s.user_id
            WHERE u.is_active = 1
            GROUP BY u.user_id, u.username, u.display_name, u.bio
            ORDER BY total_score DESC, average_score DESC, u.display_name ASC"
        );
        
        return $stmt->fetchAll();
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
     * Get scoring statistics
     */
    public static function getStatistics(): array
    {
        $stmt = Database::query(
            "SELECT 
                COUNT(DISTINCT s.judge_id) as total_active_judges,
                COUNT(DISTINCT s.user_id) as participants_scored,
                COUNT(s.score_id) as total_scores,
                ROUND(AVG(s.score_value), 2) as overall_average,
                MAX(s.score_value) as highest_score_given,
                MIN(s.score_value) as lowest_score_given,
                (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_participants,
                (SELECT COUNT(*) FROM judges WHERE is_active = 1) as total_judges
            FROM scores s"
        );
        
        $stats = $stmt->fetch();
        
        // Calculate completion percentage
        if ($stats['total_judges'] > 0 && $stats['total_participants'] > 0) {
            $expectedScores = $stats['total_judges'] * $stats['total_participants'];
            $stats['completion_percentage'] = round(
                ($stats['total_scores'] / $expectedScores) * 100, 
                2
            );
        } else {
            $stats['completion_percentage'] = 0;
        }
        
        return $stats;
    }
    
    /**
     * Get top performers
     */
    public static function getTopPerformers(int $limit = 5): array
    {
        $stmt = Database::query(
            "SELECT 
                u.user_id,
                u.display_name,
                COALESCE(SUM(s.score_value), 0) as total_score,
                COALESCE(ROUND(AVG(s.score_value), 2), 0) as average_score,
                COUNT(s.score_id) as scores_received
            FROM users u
            LEFT JOIN scores s ON u.user_id = s.user_id
            WHERE u.is_active = 1
            GROUP BY u.user_id, u.display_name
            ORDER BY total_score DESC, average_score DESC
            LIMIT ?",
            [$limit]
        );
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get recent scoring activity
     */
    public static function getRecentActivity(int $limit = 10): array
    {
        $stmt = Database::query(
            "SELECT 
                s.*,
                j.display_name as judge_name,
                u.display_name as participant_name
            FROM scores s
            JOIN judges j ON s.judge_id = j.judge_id
            JOIN users u ON s.user_id = u.user_id
            ORDER BY s.updated_at DESC
            LIMIT ?",
            [$limit]
        );
        
        return $stmt->fetchAll();
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
