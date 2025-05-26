<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Judge.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Score.php';

/**
 * Main router - demonstrates PHP 8+ match expressions and modern routing
 */

// Get the requested path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove query parameters and normalize path
$path = rtrim($path, '/') ?: '/';

// Simple routing using match expression (PHP 8.0+)
try {
    $response = match([$method, $path]) {
        ['GET', '/'] => handleHomePage(),
        ['GET', '/scoreboard'] => handleScoreboard(),
        ['GET', '/judge'] => handleJudgePortal(),
        ['POST', '/judge/score' ] => handleJudgeScoring(),
        ['GET', '/admin'] => handleAdminPanel(),
        ['POST', '/admin/judge'] => handleAddJudge(),
        ['POST', '/admin/user'] => handleAddUser(),
        ['GET', '/api/scoreboard'] => handleScoreboardAPI(),
        ['GET', '/api/stats'] => handleStatsAPI(),
        default => handle404()
    };
    
    // If response is an array, render the page
    if (is_array($response)) {
        renderPage($response['view'], $response['data'] ?? []);
    } else {
        echo $response;
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        renderPage('error', ['message' => $e->getMessage()]);
    }
}

/**
 * Route handlers
 */

function handleHomePage(): array
{
    // Redirect to scoreboard as home page
    header('Location: /scoreboard');
    exit;
}

function handleScoreboard(): array
{
    $scoreboard = Score::getScoreboard();
    $stats = Score::getStatistics();
    $recentActivity = Score::getRecentActivity(5);
    
    return [
        'view' => 'scoreboard',
        'data' => [
            'scoreboard' => $scoreboard,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'title' => 'Public Scoreboard',
            'currentPage' => 'scoreboard'
        ]
    ];
}

function handleJudgePortal(): array
{
    $judges = Judge::getActive();
    $selectedJudgeId = (int)($_GET['judge_id'] ?? 0);
    $participants = [];
    $progress = [];
    
    if ($selectedJudgeId > 0) {
        $participants = User::getForJudgeScoring($selectedJudgeId);
        $progress = Judge::getScoringProgress($selectedJudgeId);
    }
    
    return [
        'view' => 'judge',
        'data' => [
            'judges' => $judges,
            'selectedJudgeId' => $selectedJudgeId,
            'participants' => $participants,
            'progress' => $progress,
            'title' => 'Judge Portal',
            'currentPage' => 'judge'
        ]
    ];
}

function handleJudgeScoring(): string
{
    // Validate CSRF token
    if (!Utils::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid CSRF token');
    }
    
    $judgeId = (int)$_POST['judge_id'];
    $userId = (int)$_POST['user_id'];
    $scoreValue = (int)$_POST['score_value'];
    $comments = trim($_POST['comments'] ?? '');
    
    // Sanitize input
    $data = Utils::sanitizeInput([
        'judge_id' => $judgeId,
        'user_id' => $userId,
        'score_value' => $scoreValue,
        'comments' => $comments
    ]);
    
    // Validate inputs
    if ($data['judge_id'] <= 0 || $data['user_id'] <= 0) {
        throw new InvalidArgumentException('Invalid judge or participant ID');
    }
    
    if (!Utils::isValidScore($data['score_value'])) {
        $config = Config::getInstance();
        throw new InvalidArgumentException(
            "Score must be between {$config->scoreMin} and {$config->scoreMax}"
        );
    }
    
    // Save the score
    $success = Score::addOrUpdateScore(
        $data['judge_id'], 
        $data['user_id'], 
        $data['score_value'], 
        $data['comments'] ?: null
    );
    
    if ($success) {
        $_SESSION['success'] = 'Score saved successfully!';
    } else {
        $_SESSION['error'] = 'Failed to save score. Please try again.';
    }
    
    // Redirect back to judge portal
    header("Location: /judge?judge_id={$data['judge_id']}");
    exit;
}

function handleAdminPanel(): array
{
    $judges = Judge::getActive();
    $participants = User::getActive();
    $stats = Score::getStatistics();
    $recentActivity = Score::getRecentActivity(10);
    
    return [
        'view' => 'admin',
        'data' => [
            'judges' => $judges,
            'participants' => $participants,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'title' => 'Admin Panel',
            'currentPage' => 'admin'
        ]
    ];
}

function handleAddJudge(): string
{
    // Validate CSRF token
    if (!Utils::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid CSRF token');
    }
    
    $data = Utils::sanitizeInput([
        'username' => $_POST['username'] ?? '',
        'display_name' => $_POST['display_name'] ?? '',
        'email' => $_POST['email'] ?? ''
    ]);
    
    try {
        $judgeId = Judge::createJudge($data);
        $_SESSION['success'] = "Judge '{$data['display_name']}' added successfully!";
    } catch (InvalidArgumentException $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header('Location: /admin');
    exit;
}

function handleAddUser(): string
{
    // Validate CSRF token
    if (!Utils::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid CSRF token');
    }
    
    $data = Utils::sanitizeInput([
        'username' => $_POST['username'] ?? '',
        'display_name' => $_POST['display_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'bio' => $_POST['bio'] ?? ''
    ]);
    
    try {
        $userId = User::createUser($data);
        $_SESSION['success'] = "Participant '{$data['display_name']}' added successfully!";
    } catch (InvalidArgumentException $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header('Location: /admin');
    exit;
}

function handleScoreboardAPI(): string
{
    header('Content-Type: application/json');
    
    $scoreboard = Score::getScoreboard();
    $stats = Score::getStatistics();
    
    return json_encode([
        'success' => true,
        'data' => [
            'scoreboard' => $scoreboard,
            'stats' => $stats,
            'timestamp' => date('c')
        ]
    ]);
}

function handleStatsAPI(): string
{
    header('Content-Type: application/json');
    
    $stats = Score::getStatistics();
    $topPerformers = Score::getTopPerformers(10);
    $recentActivity = Score::getRecentActivity(20);
    
    return json_encode([
        'success' => true,
        'data' => [
            'stats' => $stats,
            'topPerformers' => $topPerformers,
            'recentActivity' => $recentActivity,
            'timestamp' => date('c')
        ]
    ]);
}

function handle404(): string
{
    http_response_code(404);
    return renderPage('404', ['title' => 'Page Not Found']);
}

/**
 * Render a page with the layout
 */
function renderPage(string $view, array $data = []): void
{
    // Extract data to variables
    extract($data);
    
    // Start output buffering for the content
    ob_start();
    
    // Include the view file
    $viewFile = __DIR__ . "/views/{$view}.php";
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        echo "<div class='alert alert-danger'>View file '{$view}' not found.</div>";
    }
    
    // Get the content
    $content = ob_get_clean();
    
    // Include the layout
    include __DIR__ . '/includes/layout.php';
}
