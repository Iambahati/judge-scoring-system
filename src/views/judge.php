<!-- Judge Portal View -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="mb-0">
            <i class="bi bi-person-badge-fill text-primary me-2"></i>
            Judge Portal
        </h1>
        <p class="text-muted mt-2">Score participants and track your progress</p>
    </div>
</div>

<!-- Judge Selection -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-person-check-fill me-2"></i>
                    Select Judge
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($judges)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        No judges found. Please contact the administrator to add judges to the system.
                    </div>
                <?php else: ?>
                    <form method="GET" action="/judge" class="row g-3">
                        <div class="col-md-8">
                            <select name="judge_id" class="form-select" required onchange="this.form.submit()">
                                <option value="">Select a judge to start scoring...</option>
                                <?php foreach ($judges as $judge): ?>
                                    <option value="<?= $judge['judge_id'] ?>" <?= $selectedJudgeId == $judge['judge_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($judge['display_name']) ?> 
                                        (<?= htmlspecialchars($judge['username']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-right-circle-fill me-1"></i>
                                Continue
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($selectedJudgeId > 0 && !empty($progress)): ?>
    <!-- Judge Progress -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Scoring Progress
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Participants Scored:</span>
                        <strong><?= $progress['scores_given'] ?> / <?= $progress['total_participants'] ?></strong>
                    </div>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" 
                             style="width: <?= $progress['completion_percentage'] ?>%"
                             role="progressbar"
                             aria-valuenow="<?= $progress['completion_percentage'] ?>"
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <?= number_format($progress['completion_percentage'], 1) ?>%
                        </div>
                    </div>
                    <?php if ($progress['completion_percentage'] >= 100): ?>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            All participants scored! Great job!
                        </div>
                    <?php else: ?>
                        <small class="text-muted">
                            <?= $progress['total_participants'] - $progress['scores_given'] ?> participants remaining
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Scoring Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Score range: <?= Config::getInstance()->scoreMin ?>-<?= Config::getInstance()->scoreMax ?> points
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            You can update scores anytime
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Comments are optional but helpful
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Scores are automatically saved
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Participants Scoring -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>
                Score Participants
            </h5>
            <div class="d-flex gap-2">
                <span class="badge bg-success"><?= count(array_filter($participants, fn($p) => $p['is_scored'])) ?> Scored</span>
                <span class="badge bg-warning text-dark"><?= count(array_filter($participants, fn($p) => !$p['is_scored'])) ?> Pending</span>
            </div>
        </div>
        
        <div class="card-body p-0">
            <?php if (empty($participants)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-people display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">No participants found</h5>
                    <p class="text-muted">Please contact the administrator to add participants to the system.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Participant</th>
                                <th class="text-center" style="width: 150px;">Score</th>
                                <th style="width: 300px;">Comments</th>
                                <th class="text-center" style="width: 150px;">Action</th>
                                <th class="text-center" style="width: 100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participants as $participant): ?>
                                <tr class="<?= $participant['is_scored'] ? 'table-light' : '' ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-placeholder bg-<?= $participant['is_scored'] ? 'success' : 'secondary' ?> text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                                <?= strtoupper(substr($participant['display_name'], 0, 2)) ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($participant['display_name']) ?></h6>
                                                <small class="text-muted">@<?= htmlspecialchars($participant['username']) ?></small>
                                                <?php if (!empty($participant['bio'])): ?>
                                                    <div class="small text-muted mt-1" style="max-width: 250px;">
                                                        <?= htmlspecialchars(substr($participant['bio'], 0, 80)) ?>
                                                        <?= strlen($participant['bio']) > 80 ? '...' : '' ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center">
                                        <form method="POST" action="/judge/score" class="score-form" data-user-id="<?= $participant['user_id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= Utils::generateCSRFToken() ?>">
                                            <input type="hidden" name="judge_id" value="<?= $selectedJudgeId ?>">
                                            <input type="hidden" name="user_id" value="<?= $participant['user_id'] ?>">
                                            
                                            <div class="input-group input-group-sm">
                                                <input type="number" 
                                                       name="score_value" 
                                                       class="form-control score-input"
                                                       value="<?= $participant['score_value'] ?? '' ?>"
                                                       min="<?= Config::getInstance()->scoreMin ?>"
                                                       max="<?= Config::getInstance()->scoreMax ?>"
                                                       placeholder="0-100"
                                                       required
                                                       onchange="validateScoreInput(this)">
                                                <span class="input-group-text">pts</span>
                                            </div>
                                    </td>
                                    
                                    <td>
                                        <textarea name="comments" 
                                                  class="form-control form-control-sm" 
                                                  rows="2" 
                                                  placeholder="Optional comments..."
                                                  maxlength="500"><?= htmlspecialchars($participant['comments'] ?? '') ?></textarea>
                                    </td>
                                    
                                    <td class="text-center">
                                        <button type="submit" class="btn btn-sm btn-<?= $participant['is_scored'] ? 'warning' : 'primary' ?>">
                                            <i class="bi bi-<?= $participant['is_scored'] ? 'pencil' : 'plus-circle' ?>-fill me-1"></i>
                                            <?= $participant['is_scored'] ? 'Update' : 'Score' ?>
                                        </button>
                                        </form>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php if ($participant['is_scored']): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Scored
                                            </span>
                                            <?php if ($participant['score_updated_at']): ?>
                                                <div class="small text-muted mt-1">
                                                    <?= Utils::formatDateTime($participant['score_updated_at']) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock-fill"></i>
                                                Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning-charge-fill me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <button class="btn btn-outline-primary w-100" onclick="scrollToNextUnscored()">
                                <i class="bi bi-arrow-down-circle me-2"></i>
                                Next Unscored
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-info w-100" onclick="showScoringTips()">
                                <i class="bi bi-lightbulb me-2"></i>
                                Scoring Tips
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="/scoreboard" class="btn btn-outline-success w-100">
                                <i class="bi bi-eye me-2"></i>
                                View Scoreboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Scoring Tips Modal -->
<div class="modal fade" id="scoringTipsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-lightbulb-fill text-warning me-2"></i>
                    Scoring Tips
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary">Score Ranges</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="badge bg-danger me-2">1-25</span>
                                Needs significant improvement
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-warning text-dark me-2">26-50</span>
                                Below expectations
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-info me-2">51-75</span>
                                Meets expectations
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-success me-2">76-90</span>
                                Exceeds expectations
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-primary me-2">91-100</span>
                                Outstanding performance
                            </li>
                        </ul>
                        
                        <h6 class="text-primary mt-4">Best Practices</h6>
                        <ul>
                            <li>Be consistent across all participants</li>
                            <li>Consider multiple evaluation criteria</li>
                            <li>Add meaningful comments when possible</li>
                            <li>Review and update scores if needed</li>
                            <li>Take breaks to maintain objectivity</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    Got it!
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Scroll to next unscored participant
function scrollToNextUnscored() {
    const unscoredRows = document.querySelectorAll('tr:not(.table-light)');
    if (unscoredRows.length > 1) { // Skip header row
        const firstUnscored = unscoredRows[1];
        firstUnscored.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Highlight the row briefly
        firstUnscored.style.backgroundColor = '#fff3cd';
        setTimeout(() => {
            firstUnscored.style.backgroundColor = '';
        }, 2000);
        
        // Focus on the score input
        const scoreInput = firstUnscored.querySelector('.score-input');
        if (scoreInput) {
            setTimeout(() => scoreInput.focus(), 500);
        }
    } else {
        // Show completion message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    All participants have been scored!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            document.body.removeChild(toast);
        });
    }
}

// Show scoring tips modal
function showScoringTips() {
    const modal = new bootstrap.Modal(document.getElementById('scoringTipsModal'));
    modal.show();
}

// Enhanced form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.score-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const scoreInput = this.querySelector('.score-input');
            const isValid = validateScoreInput(scoreInput);
            
            if (!isValid) {
                e.preventDefault();
                scoreInput.focus();
                
                // Show validation error
                const toast = document.createElement('div');
                toast.className = 'toast align-items-center text-white bg-danger border-0 position-fixed';
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Please enter a valid score (<?= Config::getInstance()->scoreMin ?>-<?= Config::getInstance()->scoreMax ?>)
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                document.body.appendChild(toast);
                
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                toast.addEventListener('hidden.bs.toast', () => {
                    document.body.removeChild(toast);
                });
                
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
            submitBtn.disabled = true;
            
            // Re-enable after 3 seconds in case of issues
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    });
    
    // Auto-save functionality (optional enhancement)
    const scoreInputs = document.querySelectorAll('.score-input');
    scoreInputs.forEach(input => {
        let timeoutId;
        
        input.addEventListener('input', function() {
            clearTimeout(timeoutId);
            
            // Auto-save after 2 seconds of no typing
            timeoutId = setTimeout(() => {
                if (this.value && validateScoreInput(this)) {
                    // You could implement auto-save here
                    console.log('Auto-save triggered for participant', this.closest('form').dataset.userId);
                }
            }, 2000);
        });
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + N for next unscored
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        scrollToNextUnscored();
    }
    
    // Ctrl/Cmd + ? for tips
    if ((e.ctrlKey || e.metaKey) && e.key === '?') {
        e.preventDefault();
        showScoringTips();
    }
});
</script>
