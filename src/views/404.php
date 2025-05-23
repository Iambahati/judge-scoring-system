<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center shadow">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle-fill text-warning display-1"></i>
                    </div>
                    <h1 class="display-4 mb-4">404</h1>
                    <h2 class="mb-4">Page Not Found</h2>
                    <p class="lead mb-4">The page you're looking for doesn't exist or has been moved.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/" class="btn btn-primary">
                            <i class="bi bi-house-fill me-2"></i>
                            Go Home
                        </a>
                        <a href="/scoreboard" class="btn btn-outline-primary">
                            <i class="bi bi-bar-chart-fill me-2"></i>
                            View Scoreboard
                        </a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    If you think this is a mistake, please contact the administrator.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Report 404 error (in a production environment, this would log to an error tracking service)
document.addEventListener('DOMContentLoaded', function() {
    console.error('404 error: Page not found', window.location.pathname);
});
</script>