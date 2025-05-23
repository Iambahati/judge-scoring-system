<?php

require_once __DIR__ . '/../config/database.php';

/**
 * User (Participant) Model
 * Demonstrates modern PHP syntax and database operations
 */
class User extends BaseModel
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'user_id';
    
    /**
     * Get all active participants
     */
    public static function getActive(): array
    {
        return self::all(
            conditions: ['is_active' => 1],
            orderBy: 'display_name ASC'
        );
    }
    
    /**
     * Create new participant with validation
     */
    public static function createUser(array $data): int|string
    {
        // Validate required fields
        $required = ['username', 'display_name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("Field '{$field}' is required");
            }
        }
        
        // Check if username already exists
        if (self::usernameExists($data['username'])) {
            throw new InvalidArgumentException("Username '{$data['username']}' already exists");
        }
        
        // Set default values using null coalescing operator
        $data['is_active'] = $data['is_active'] ?? 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return self::create($data);
    }
    
    /**
     * Check if username exists
     */
    public static function usernameExists(string $username): bool
    {
        $stmt = Database::query(
            "SELECT COUNT(*) as count FROM users WHERE username = ?",
            [$username]
        );
        return $stmt->fetch()['count'] > 0;
    }
    
    /**
     * Get participant with their scores and statistics
     */
    public static function getWithStats(int $userId): ?array
    {
        $stmt = Database::query(
            "SELECT 
                u.*,
                COUNT(s.score_id) as total_scores,
                ROUND(AVG(s.score_value), 2) as average_score,
                SUM(s.score_value) as total_score,
                MAX(s.score_value) as highest_score,
                MIN(s.score_value) as lowest_score,
                MAX(s.updated_at) as last_scored
            FROM users u
            LEFT JOIN scores s ON u.user_id = s.user_id
            WHERE u.user_id = ?
            GROUP BY u.user_id",
            [$userId]
        );
        
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Get participant's detailed scores with judge information
     */
    public static function getDetailedScores(int $userId): array
    {
        $stmt = Database::query(
            "SELECT 
                s.*,
                j.display_name as judge_name,
                j.username as judge_username
            FROM scores s
            JOIN judges j ON s.judge_id = j.judge_id
            WHERE s.user_id = ?
            ORDER BY s.created_at DESC",
            [$userId]
        );
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get current ranking for a participant
     */
    public static function getCurrentRanking(int $userId): int
    {
        $stmt = Database::query(
            "SELECT ranking 
            FROM (
                SELECT 
                    u.user_id,
                    RANK() OVER (ORDER BY COALESCE(SUM(s.score_value), 0) DESC, COALESCE(AVG(s.score_value), 0) DESC) as ranking
                FROM users u
                LEFT JOIN scores s ON u.user_id = s.user_id
                WHERE u.is_active = 1
                GROUP BY u.user_id
            ) rankings
            WHERE user_id = ?",
            [$userId]
        );
        
        $result = $stmt->fetch();
        return $result ? (int)$result['ranking'] : 999;
    }
    
    /**
     * Get participants for judge scoring interface
     */
    public static function getForJudgeScoring(int $judgeId): array
    {
        $stmt = Database::query(
            "SELECT 
                u.*,
                s.score_value,
                s.comments,
                s.updated_at as score_updated_at,
                CASE WHEN s.score_id IS NOT NULL THEN 1 ELSE 0 END as is_scored
            FROM users u
            LEFT JOIN scores s ON u.user_id = s.user_id AND s.judge_id = ?
            WHERE u.is_active = 1
            ORDER BY is_scored ASC, u.display_name ASC",
            [$judgeId]
        );
        
        return $stmt->fetchAll();
    }
}
