<!-- Scoreboard View -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">
                <i class="bi bi-trophy-fill text-warning me-2"></i>
                Live Scoreboard
            </h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="refreshScoreboard()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Refresh
                </button>
                <button class="btn btn-outline-secondary" onclick="toggleAutoRefresh()">
                    <i class="bi bi-play-fill me-1" id="auto-refresh-icon"></i>
                    <span id="auto-refresh-text">Auto Refresh</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= $stats['total_participants'] ?? 0 ?></div>
                <h6 class="card-subtitle text-muted">Total Participants</h6>
                <small class="text-success">
                    <i class="bi bi-people-fill"></i>
                    <?= $stats['participants_scored'] ?? 0 ?> scored
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= $stats['total_judges'] ?? 0 ?></div>
                <h6 class="card-subtitle text-muted">Active Judges</h6>
                <small class="text-info">
                    <i class="bi bi-person-badge"></i>
                    <?= $stats['total_active_judges'] ?? 0 ?> participating
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= $stats['total_scores'] ?? 0 ?></div>
                <h6 class="card-subtitle text-muted">Total Scores</h6>
                <small class="text-primary">
                    <i class="bi bi-graph-up"></i>
                    Avg: <?= $stats['overall_average'] ?? 0 ?>
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= number_format($stats['completion_percentage'] ?? 0, 1) ?>%</div>
                <h6 class="card-subtitle text-muted">Completion</h6>
                <div class="progress mt-2">
                    <div class="progress-bar bg-success" style="width: <?= $stats['completion_percentage'] ?? 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Indicator -->
<div class="loading text-center mb-3">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Scoreboard Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-list-ol me-2"></i>
            Rankings
        </h5>
        <span class="badge bg-primary">
            <?= count($scoreboard) ?> Participants
        </span>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="scoreboard-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">Rank</th>
                        <th>Participant</th>
                        <th class="text-center">Judges</th>
                        <th class="text-center">Total Score</th>
                        <th class="text-center">Average</th>
                        <th class="text-center">Range</th>
                        <th class="text-center">Last Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($scoreboard)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    <h5>No scores available yet</h5>
                                    <p>Waiting for judges to start scoring participants...</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($scoreboard as $index => $participant): ?>
                            <tr class="participant-row" data-user-id="<?= $participant['user_id'] ?>">
                                <td class="text-center">
                                    <div class="ranking-badge <?= match((int)$participant['ranking']) {
                                        1 => 'rank-1',
                                        2 => 'rank-2', 
                                        3 => 'rank-3',
                                        default => 'bg-light text-dark'
                                    } ?>">
                                        <?php if ($participant['ranking'] <= 3): ?>
                                            <i class="bi bi-trophy-fill"></i>
                                        <?php else: ?>
                                            <?= $participant['ranking'] ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                            <?= strtoupper(substr($participant['display_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($participant['display_name']) ?></h6>
                                            <small class="text-muted">@<?= htmlspecialchars($participant['username']) ?></small>
                                            <?php if (!empty($participant['bio'])): ?>
                                                <div class="small text-muted mt-1" style="max-width: 300px;">
                                                    <?= htmlspecialchars(substr($participant['bio'], 0, 100)) ?>
                                                    <?= strlen($participant['bio']) > 100 ? '...' : '' ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge <?= $participant['total_judges'] == $stats['total_judges'] ? 'bg-success' : 'bg-warning text-dark' ?>">
                                        <?= $participant['total_judges'] ?>/<?= $stats['total_judges'] ?>
                                    </span>
                                </td>
                                
                                <td class="text-center">
                                    <h5 class="mb-0 text-primary fw-bold">
                                        <?= number_format($participant['total_score']) ?>
                                    </h5>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge bg-info">
                                        <?= number_format($participant['average_score'], 1) ?>
                                    </span>
                                </td>
                                
                                <td class="text-center">
                                    <?php if ($participant['total_scores'] > 0): ?>
                                        <small class="text-muted">
                                            <?= $participant['lowest_score'] ?> - <?= $participant['highest_score'] ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center">
                                    <?php if ($participant['last_scored']): ?>
                                        <small class="text-muted">
                                            <?= Utils::formatDateTime($participant['last_scored']) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">No scores</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($recentActivity)): ?>
<!-- Recent Activity -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-activity me-2"></i>
                    Recent Scoring Activity
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($recentActivity as $activity): ?>
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-primary rounded-circle p-2 me-3">
                                <i class="bi bi-plus-lg text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong><?= htmlspecialchars($activity['judge_name']) ?></strong>
                                        scored
                                        <strong><?= htmlspecialchars($activity['participant_name']) ?></strong>
                                        with <span class="badge bg-primary"><?= $activity['score_value'] ?> points</span>
                                        <?php if (!empty($activity['comments'])): ?>
                                            <div class="small text-muted mt-1">
                                                "<?= htmlspecialchars(substr($activity['comments'], 0, 100)) ?><?= strlen($activity['comments']) > 100 ? '...' : '' ?>"
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        <?= Utils::formatDateTime($activity['updated_at']) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
let autoRefreshInterval = null;
let isAutoRefreshing = false;

// Auto-refresh functionality
function toggleAutoRefresh() {
    const icon = document.getElementById('auto-refresh-icon');
    const text = document.getElementById('auto-refresh-text');
    
    if (isAutoRefreshing) {
        clearInterval(autoRefreshInterval);
        icon.className = 'bi bi-play-fill me-1';
        text.textContent = 'Auto Refresh';
        isAutoRefreshing = false;
    } else {
        autoRefreshInterval = setInterval(refreshScoreboard, 30000); // 30 seconds
        icon.className = 'bi bi-pause-fill me-1';
        text.textContent = 'Stop Auto Refresh';
        isAutoRefreshing = true;
    }
}

// Manual refresh
async function refreshScoreboard() {
    try {
        showLoading();
        const data = await makeRequest('/api/scoreboard');
        
        if (data.success) {
            updateScoreboardTable(data.data.scoreboard);
            updateStats(data.data.stats);
            updateLastUpdateTime();
            
            // Show success feedback
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Scoreboard updated!
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
    } catch (error) {
        console.error('Failed to refresh scoreboard:', error);
        
        // Show error feedback
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        alert.innerHTML = `
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Failed to refresh scoreboard. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            if (document.body.contains(alert)) {
                document.body.removeChild(alert);
            }
        }, 5000);
    } finally {
        hideLoading();
    }
}

// Update scoreboard table with new data
function updateScoreboardTable(scoreboard) {
    const tbody = document.querySelector('#scoreboard-table tbody');
    
    if (scoreboard.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                        <h5>No scores available yet</h5>
                        <p>Waiting for judges to start scoring participants...</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    scoreboard.forEach((participant, index) => {
        const rankClass = participant.ranking <= 3 ? 
            `rank-${participant.ranking}` : 
            'bg-light text-dark';
            
        html += `
            <tr class="participant-row fade-in" data-user-id="${participant.user_id}">
                <td class="text-center">
                    <div class="ranking-badge ${rankClass}">
                        ${participant.ranking <= 3 ? '<i class="bi bi-trophy-fill"></i>' : participant.ranking}
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                            ${participant.display_name.substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <h6 class="mb-0">${escapeHtml(participant.display_name)}</h6>
                            <small class="text-muted">@${escapeHtml(participant.username)}</small>
                            ${participant.bio ? `<div class="small text-muted mt-1" style="max-width: 300px;">${escapeHtml(participant.bio.substring(0, 100))}${participant.bio.length > 100 ? '...' : ''}</div>` : ''}
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge ${participant.total_judges == <?= $stats['total_judges'] ?> ? 'bg-success' : 'bg-warning text-dark'}">
                        ${participant.total_judges}/<?= $stats['total_judges'] ?>
                    </span>
                </td>
                <td class="text-center">
                    <h5 class="mb-0 text-primary fw-bold">
                        ${new Intl.NumberFormat().format(participant.total_score)}
                    </h5>
                </td>
                <td class="text-center">
                    <span class="badge bg-info">
                        ${parseFloat(participant.average_score).toFixed(1)}
                    </span>
                </td>
                <td class="text-center">
                    ${participant.total_scores > 0 ? 
                        `<small class="text-muted">${participant.lowest_score} - ${participant.highest_score}</small>` :
                        '<span class="text-muted">-</span>'
                    }
                </td>
                <td class="text-center">
                    ${participant.last_scored ? 
                        `<small class="text-muted">${formatDateTime(participant.last_scored)}</small>` :
                        '<span class="text-muted">No scores</span>'
                    }
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Update statistics
function updateStats(stats) {
    document.querySelector('.stats-card:nth-child(1) .stats-number').textContent = stats.total_participants || 0;
    document.querySelector('.stats-card:nth-child(2) .stats-number').textContent = stats.total_judges || 0;
    document.querySelector('.stats-card:nth-child(3) .stats-number').textContent = stats.total_scores || 0;
    document.querySelector('.stats-card:nth-child(4) .stats-number').textContent = (stats.completion_percentage || 0).toFixed(1) + '%';
    
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        progressBar.style.width = (stats.completion_percentage || 0) + '%';
    }
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}

// Start auto-refresh on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-start refresh after 1 minute of page load
    setTimeout(() => {
        if (!isAutoRefreshing) {
            toggleAutoRefresh();
        }
    }, 60000);
});
</script>
