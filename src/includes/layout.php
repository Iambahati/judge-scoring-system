<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Judge Scoring System' ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
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
        
        .progress {
            height: 10px;
            border-radius: 10px;
            background-color: #e9ecef;
        }
        
        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }
        
        .table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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
            background-color: #f8f9fa;
        }
        
        .score-input {
            max-width: 100px;
            text-align: center;
            font-weight: bold;
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }
        
        .score-input:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .alert {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .footer {
            background: var(--primary-color);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .stats-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--secondary-color);
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: block;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                border-radius: 15px;
            }
            
            .stats-number {
                font-size: 2rem;
            }
        }
    </style>
    
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
                Demonstrating modern PHP 8.2+ features with Bootstrap 5.
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
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
