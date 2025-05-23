<?php

/**
 * Database Configuration and Connection Class
 */

declare(strict_types=1);

class Database
{
    private static ?PDO $connection = null;
    
    // Using PHP 8.0+ constructor property promotion
    public function __construct(
        private readonly string $host = 'db',
        private readonly string $dbname = 'judge_scoring',
        private readonly string $username = 'root',
        private readonly string $password = 'rootpassword',
        private readonly string $charset = 'utf8mb4'
    ) {}
    
    /**
     * Get database connection using singleton pattern
     */
    public static function getConnection(): PDO
    {
        return self::$connection ??= (new self())->connect();
    }
    
    /**
     * Create PDO connection with proper error handling
     */
    private function connect(): PDO
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
        ];
        
        try {
            $pdo = new PDO($dsn, $this->username, $this->password, $options);
            
            // Set SQL mode for strict validation
            $pdo->exec("SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
            
            return $pdo;
        } catch (PDOException $e) {
            $errorCode = match ($e->getCode()) {
                1049 => 'Database does not exist',
                1045 => 'Access denied - check credentials',
                2002 => 'Connection refused - check if MySQL is running',
                default => 'Database connection failed'
            };
            
            throw new Exception("$errorCode: " . $e->getMessage());
        }
    }
    
    /**
     * Execute query with parameters
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Get last inserted ID
     */
    public static function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }
    
    /**
     * Rollback transaction
     */
    public static function rollback(): bool
    {
        return self::getConnection()->rollback();
    }
}

/**
 * Base Model Class with Active Record Pattern
 */
abstract class BaseModel
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    
    /**
     * Find record by ID - uses nullable return type
     */
    public static function find(int|string $id): ?array
    {
        $table = static::$table;
        $primaryKey = static::$primaryKey;
        
        $stmt = Database::query(
            "SELECT * FROM `{$table}` WHERE `{$primaryKey}` = ? LIMIT 1",
            [$id]
        );
        
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Get all records with optional conditions
     */
    public static function all(
        array $conditions = [],
        string $orderBy = '',
        int $limit = 0,
        int $offset = 0
    ): array {
        $table = static::$table;
        $sql = "SELECT * FROM `{$table}`";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "`{$column}` = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        return Database::query($sql, $params)->fetchAll();
    }
    
    /**
     * Insert new record
     */
    public static function create(array $data): int|string
    {
        $table = static::$table;
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($data), '?');
        
        $sql = "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        Database::query($sql, array_values($data));
        return Database::lastInsertId();
    }
    
    /**
     * Update record by ID
     */
    public static function update(int|string $id, array $data): bool
    {
        $table = static::$table;
        $primaryKey = static::$primaryKey;
        $setClause = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClause[] = "`{$column}` = ?";
            $params[] = $value;
        }
        $params[] = $id;
        
        $sql = "UPDATE `{$table}` SET " . implode(', ', $setClause) . 
               " WHERE `{$primaryKey}` = ?";
        
        $stmt = Database::query($sql, $params);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Delete record by ID
     */
    public static function delete(int|string $id): bool
    {
        $table = static::$table;
        $primaryKey = static::$primaryKey;
        
        $stmt = Database::query(
            "DELETE FROM `{$table}` WHERE `{$primaryKey}` = ?",
            [$id]
        );
        
        return $stmt->rowCount() > 0;
    }
}

/**
 * Configuration class
 */
final readonly class Config
{
    public function __construct(
        public string $appName = 'Judge Scoring System',
        public string $appVersion = '1.0.0',
        public bool $debug = true,
        public string $timezone = 'Africa/Nairobi',
        public int $scoreMin = 1,
        public int $scoreMax = 100
    ) {}
    
    public static function getInstance(): self
    {
        static $instance = null;
        return $instance ??= new self();
    }
}

// Set timezone
date_default_timezone_set(Config::getInstance()->timezone);

/**
 * Utility class with static methods
 */
class Utils
{
    /**
     * Sanitize input data
     */
    public static function sanitizeInput(array $data): array
    {
        return array_map(
            fn($value) => is_string($value) ? trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8')) : $value,
            $data
        );
    }
    
    /**
     * Validate score value
     */
    public static function isValidScore(mixed $score): bool
    {
        $config = Config::getInstance();
        return is_numeric($score) && 
               $score >= $config->scoreMin && 
               $score <= $config->scoreMax;
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Format display timestamp
     */
    public static function formatDateTime(string $datetime): string
    {
        return (new DateTime($datetime))->format('M j, Y g:i A');
    }
    
    /**
     * Calculate ranking badge class
     */
    public static function getRankingBadgeClass(int $rank): string
    {
        return match(true) {
            $rank === 1 => 'badge-warning',  // Gold
            $rank === 2 => 'badge-secondary', // Silver
            $rank === 3 => 'badge-info',     // Bronze
            default => 'badge-light'
        };
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
