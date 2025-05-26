<?php

require_once __DIR__ . '/../config/database.php';

/**
 * Judge Model
 */
class Judge extends BaseModel
{
    protected static string $table = 'judges';
    protected static string $primaryKey = 'judge_id';

    /**
     * Get all active judges
     */
    public static function getActive(): array
    {
        $sql = "SELECT 
                j.id AS judge_id, 
                j.username, 
                j.display_name, 
                j.email,
                j.is_active,
                j.created_at
            FROM 
                judges j
            WHERE 
                j.is_active = 1
            ORDER BY 
                j.display_name ASC";

        return Database::query($sql)->fetchAll();
    }

    /**
     * Create new judge with validation
     */
    public static function createJudge(array $data): int|string
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

        // Set default values
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
            "SELECT COUNT(*) as count FROM judges WHERE username = ?",
            [$username]
        );
        return $stmt->fetch()['count'] > 0;
    }

    /**
     * Get judge's scoring progress
     */
    public static function getScoringProgress(int $judgeId): array
    {
        $sql = "SELECT 
                j.id as judge_id,
                j.display_name,
                COUNT(s.id) as scores_given,
                (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_participants,
                ROUND((COUNT(s.id) / (SELECT COUNT(*) FROM users WHERE is_active = 1)) * 100, 2) as completion_percentage
            FROM judges j
            LEFT JOIN scores s ON j.id = s.judge_id
            WHERE j.id = ?
            GROUP BY j.id, j.display_name";

        return Database::query($sql, [$judgeId])->fetch() ?: [];
    }

    /**
     * Get participants not yet scored by this judge
     */
    public static function getUnscoredParticipants(int $judgeId): array
    {
        $stmt = Database::query(
            "SELECT u.* 
            FROM users u
            WHERE u.is_active = 1 
            AND u.user_id NOT IN (
                SELECT s.user_id 
                FROM scores s 
                WHERE s.judge_id = ?
            )
            ORDER BY u.display_name ASC",
            [$judgeId]
        );

        return $stmt->fetchAll();
    }
}
