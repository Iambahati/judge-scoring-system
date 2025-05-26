<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Judge Scoring System' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
        }
        
        /* Animated background bubbles */
        .background-bubbles {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        
        .bubble {
            position: absolute;
            border-radius: 50%;
            opacity: 0.25;
            animation: floatBubble 18s infinite linear;
            background: linear-gradient(135deg, #3498db 0%, #8e44ad 100%);
        }
        
        .bubble.b1 { width: 120px; height: 120px; left: 10vw; top: 60vh; animation-delay: 0s; }
        .bubble.b2 { width: 80px; height: 80px; left: 70vw; top: 80vh; animation-delay: 3s; }
        .bubble.b3 { width: 200px; height: 200px; left: 50vw; top: 10vh; animation-delay: 6s; }
        .bubble.b4 { width: 100px; height: 100px; left: 80vw; top: 30vh; animation-delay: 9s; }
        .bubble.b5 { width: 60px; height: 60px; left: 20vw; top: 20vh; animation-delay: 12s; }
        
        @keyframes floatBubble {
            0% { transform: translateY(0) scale(1); opacity: 0.25; }
            50% { transform: translateY(-60px) scale(1.1); opacity: 0.35; }
            100% { transform: translateY(0) scale(1); opacity: 0.25; }
        }
        
        .navbar {
            background: linear-gradient(135deg, #2c3e50 60%, #3498db 100%);
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }
        
        .navbar-brand {
            font-weight: bold;
            letter-spacing: 1px;
            font-size: 1.4rem;
        }
        
        .navbar-nav .nav-link {
            transition: color 0.2s, background 0.2s;
            border-radius: 6px;
            margin-right: 0.5rem;
        }
        
        .navbar-nav .nav-link.active, .navbar-nav .nav-link:hover {
            background: rgba(255,255,255,0.12);
            color: #fff !important;
        }
        
        .card {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(8px) saturate(120%);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.10);
            border: 1px solid rgba(255,255,255,0.18);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 12px 32px 0 rgba(31,38,135,0.18);
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(52,152,219,0.08);
        }
        
        .btn-primary, .btn-success, .btn-warning, .btn-danger {
            color: #fff;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2c3e50, #3498db);
        }
        
        .footer {
            background: linear-gradient(135deg, #2c3e50 60%, #3498db 100%);
            color: #fff;
            padding: 2rem 0;
            margin-top: 3rem;
            box-shadow: 0 -2px 8px rgba(44,62,80,0.08);
        }
        
        .footer a {
            color: #fff;
            opacity: 0.85;
            transition: opacity 0.2s, text-decoration 0.2s;
        }
        
        .footer a:hover {
            opacity: 1;
            text-decoration: underline;
        }
        
        .stats-card {
            background: rgba(255,255,255,0.6);
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }
        
        .table {
            background: rgba(255,255,255,0.9);
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table th {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }
        
        .table td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }
        
        .table tbody tr {
            border-bottom: 1px solid #dee2e6;
            transition: background-color 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: #eaf6fb;
        }
        
        .score-input {
            max-width: 100px;
            text-align: center;
            font-weight: bold;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            background: rgba(255,255,255,0.8);
        }
        
        .score-input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.18);
        }
        
        .alert {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }
        
        .ranking-badge {
            font-size: 1.1rem;
            font-weight: bold;
            min-width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(44,62,80,0.10);
        }
        
        .rank-1 {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            color: #333;
        }
        
        .rank-2 {
            background: linear-gradient(135deg, #c0c0c0, #e8e8e8);
            color: #333;
        }
        
        .rank-3 {
            background: linear-gradient(135deg, #cd7f32, #daa520);
            color: white;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .container { padding: 0 8px; }
            .card { margin-bottom: 1rem; }
            .table-responsive { border-radius: 15px; }
            .stats-number { font-size: 2rem; }
        }
    </style>
    <!-- Animated background -->
    <div class="background-bubbles">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="bubble b4"></div>
        <div class="bubble b5"></div>
    </div>
    <?= $additionalHead ?? '' ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-trophy-fill me-2"></i>
                Judge Scoring System
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'scoreboard' ? 'active' : '' ?>" href="/scoreboard">
                            <i class="bi bi-bar-chart-fill me-1"></i>
                            Scoreboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'judge' ? 'active' : '' ?>" href="/judge">
                            <i class="bi bi-person-badge-fill me-1"></i>
                            Judge Portal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'admin' ? 'active' : '' ?>" href="/admin">
                            <i class="bi bi-gear-fill me-1"></i>
                            Admin Panel
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text">
                            <i class="bi bi-clock-fill me-1"></i>
                            <span id="current-time"></span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="bi bi-trophy-fill me-2"></i>Judge Scoring System</h5>
                    <p class="mb-0">Built with LAMP Stack & Bootstrap</p>
                </div>
                <div class="col-md-4">
                    <h6>Quick Links</h6>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/scoreboard" class="text-white text-decoration-none">Scoreboard</a>
                        <a href="/judge" class="text-white text-decoration-none">Judge Portal</a>
                        <a href="/admin" class="text-white text-decoration-none">Admin</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6>System Status</h6>
                    <div class="d-flex justify-content-center align-items-center">
                        <span class="badge bg-success me-2">
                            <i class="bi bi-check-circle-fill"></i> Online
                        </span>
                        <small id="last-update"></small>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <p class="mb-0">
                &copy; <?= date('Y') ?> Judge Scoring System. 
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    

    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: true,
                hour: 'numeric',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('current-time').textContent = timeString;
        }
        
        // Update last update time
        function updateLastUpdateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: true,
                hour: 'numeric',
                minute: '2-digit'
            });
            const lastUpdateElement = document.getElementById('last-update');
            if (lastUpdateElement) {
                lastUpdateElement.textContent = `Updated: ${timeString}`;
            }
        }
        
        // Initialize time updates
        updateTime();
        updateLastUpdateTime();
        setInterval(updateTime, 1000);
        
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
        
        // Add fade-in animation to cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('fade-in');
                }, index * 100);
            });
        });
        
        // Form validation helpers
        function validateScoreInput(input) {
            const value = parseInt(input.value);
            const min = <?= Config::getInstance()->scoreMin ?>;
            const max = <?= Config::getInstance()->scoreMax ?>;
            
            if (isNaN(value) || value < min || value > max) {
                input.classList.add('is-invalid');
                return false;
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                return true;
            }
        }
        
        // Loading state management
        function showLoading() {
            const loadingElements = document.querySelectorAll('.loading');
            loadingElements.forEach(el => el.classList.add('show'));
        }
        
        function hideLoading() {
            const loadingElements = document.querySelectorAll('.loading');
            loadingElements.forEach(el => el.classList.remove('show'));
        }
        
        // Utility function for making AJAX requests
        async function makeRequest(url, options = {}) {
            showLoading();
            try {
                const response = await fetch(url, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...options.headers
                    },
                    ...options
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return await response.json();
            } catch (error) {
                console.error('Request failed:', error);
                throw error;
            } finally {
                hideLoading();
            }
        }
    </script>
    
    <?= $additionalScripts ?? '' ?>
</body>
</html>
