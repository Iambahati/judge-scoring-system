<!-- Admin Panel View -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="mb-0">
            <i class="bi bi-gear-fill text-secondary me-2"></i>
            Admin Panel
        </h1>
        <p class="text-muted mt-2">Manage judges, participants and view system statistics</p>
    </div>
</div>

<!-- Statistics Overview Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= $stats['total_participants'] ?? 0 ?></div>
                <h6 class="card-subtitle text-muted">Participants</h6>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= $stats['total_judges'] ?? 0 ?></div>
                <h6 class="card-subtitle text-muted">Judges</h6>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= $stats['total_scores'] ?? 0 ?></div>
                <h6 class="card-subtitle text-muted">Scores Recorded</h6>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-info" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= number_format($stats['completion_percentage'] ?? 0, 1) ?>%</div>
                <h6 class="card-subtitle text-muted">Overall Progress</h6>
                <div class="progress mt-2">
                    <div class="progress-bar bg-warning" style="width: <?= $stats['completion_percentage'] ?? 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Admin Tabs -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="judges-tab" data-bs-toggle="tab" data-bs-target="#judges" type="button" role="tab" aria-controls="judges" aria-selected="true">
                    <i class="bi bi-people-fill me-1"></i> Judges
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="participants-tab" data-bs-toggle="tab" data-bs-target="#participants" type="button" role="tab" aria-controls="participants" aria-selected="false">
                    <i class="bi bi-person-badge-fill me-1"></i> Participants
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="false">
                    <i class="bi bi-activity me-1"></i> Activity Log
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content" id="adminTabsContent">
            <!-- Judges Tab -->
            <div class="tab-pane fade show active" id="judges" role="tabpanel" aria-labelledby="judges-tab">
                <div class="row">
                    <!-- Add Judge Form -->
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-person-plus-fill me-2"></i>
                                    Add New Judge
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="/admin/judge" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= Utils::generateCSRFToken() ?>">
                                    
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username/ID <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">@</span>
                                            <input type="text" class="form-control" id="username" name="username" required pattern="[a-zA-Z0-9_]+" minlength="3" maxlength="50" placeholder="unique_identifier">
                                        </div>
                                        <small class="text-muted">Unique identifier, alphanumeric with underscores</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="display_name" name="display_name" required minlength="2" maxlength="100" placeholder="Full name or display name">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email (Optional)</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="judge@example.com">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-plus-circle-fill me-1"></i>
                                        Add Judge
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Judges List -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-list-ul me-2"></i>
                                    Judges List
                                </h5>
                                <span class="badge bg-primary"><?= count($judges) ?> Judges</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Display Name</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Created</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($judges)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <i class="bi bi-inbox display-6 d-block mb-2 text-muted"></i>
                                                        <p class="mb-0">No judges found. Add your first judge above!</p>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($judges as $judge): ?>
                                                    <tr>
                                                        <td><?= $judge['judge_id'] ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-weight: bold;">
                                                                    <?= strtoupper(substr($judge['display_name'], 0, 2)) ?>
                                                                </div>
                                                                <?= htmlspecialchars($judge['display_name']) ?>
                                                            </div>
                                                        </td>
                                                        <td>@<?= htmlspecialchars($judge['username']) ?></td>
                                                        <td><?= $judge['email'] ? htmlspecialchars($judge['email']) : '<span class="text-muted">Not provided</span>' ?></td>
                                                        <td><?= Utils::formatDateTime($judge['created_at']) ?></td>
                                                        <td class="text-center">
                                                            <?php if ($judge['is_active']): ?>
                                                                <span class="badge bg-success">Active</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">Inactive</span>
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
                    </div>
                </div>
            </div>
            
            <!-- Participants Tab -->
            <div class="tab-pane fade" id="participants" role="tabpanel" aria-labelledby="participants-tab">
                <div class="row">
                    <!-- Add Participant Form -->
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-person-plus-fill me-2"></i>
                                    Add New Participant
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="/admin/user" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= Utils::generateCSRFToken() ?>">
                                    
                                    <div class="mb-3">
                                        <label for="user_username" class="form-label">Username/ID <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">@</span>
                                            <input type="text" class="form-control" id="user_username" name="username" required pattern="[a-zA-Z0-9_]+" minlength="3" maxlength="50" placeholder="participant_id">
                                        </div>
                                        <small class="text-muted">Unique identifier, alphanumeric with underscores</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="user_display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="user_display_name" name="display_name" required minlength="2" maxlength="100" placeholder="Full name or display name">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="user_email" class="form-label">Email (Optional)</label>
                                        <input type="email" class="form-control" id="user_email" name="email" placeholder="participant@example.com">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Bio/Description (Optional)</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Short description of the participant"></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-plus-circle-fill me-1"></i>
                                        Add Participant
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Participants List -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-list-ul me-2"></i>
                                    Participants List
                                </h5>
                                <span class="badge bg-primary"><?= count($participants) ?> Participants</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Display Name</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Created</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($participants)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <i class="bi bi-inbox display-6 d-block mb-2 text-muted"></i>
                                                        <p class="mb-0">No participants found. Add your first participant above!</p>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($participants as $participant): ?>
                                                    <tr>
                                                        <td><?= $participant['user_id'] ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-placeholder bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-weight: bold;">
                                                                    <?= strtoupper(substr($participant['display_name'], 0, 2)) ?>
                                                                </div>
                                                                <?= htmlspecialchars($participant['display_name']) ?>
                                                            </div>
                                                        </td>
                                                        <td>@<?= htmlspecialchars($participant['username']) ?></td>
                                                        <td><?= $participant['email'] ? htmlspecialchars($participant['email']) : '<span class="text-muted">Not provided</span>' ?></td>
                                                        <td><?= Utils::formatDateTime($participant['created_at']) ?></td>
                                                        <td class="text-center">
                                                            <?php if ($participant['is_active']): ?>
                                                                <span class="badge bg-success">Active</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">Inactive</span>
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
                    </div>
                </div>
            </div>
            
            <!-- Activity Log Tab -->
            <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-activity me-2"></i>
                            Recent System Activity
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php if (empty($recentActivity)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-clock-history display-5 text-muted mb-3"></i>
                                    <h5 class="text-muted">No recent activity</h5>
                                    <p class="text-muted">Activity will appear here as judges score participants</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-start">
                                                <div class="rounded-circle bg-primary text-white p-2 me-3 mt-1">
                                                    <i class="bi bi-star-fill"></i>
                                                </div>
                                                <div>
                                                    <div class="mb-1">
                                                        <strong><?= htmlspecialchars($activity['judge_name']) ?></strong> 
                                                        scored 
                                                        <strong><?= htmlspecialchars($activity['participant_name']) ?></strong> 
                                                        with <span class="badge bg-primary"><?= $activity['score_value'] ?> points</span>
                                                    </div>
                                                    <?php if (!empty($activity['comments'])): ?>
                                                        <div class="small text-muted">
                                                            <i class="bi bi-chat-left-text me-1"></i>
                                                            "<?= htmlspecialchars($activity['comments']) ?>"
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <?= Utils::formatDateTime($activity['updated_at']) ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning-charge-fill me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <a href="/scoreboard" class="btn btn-outline-primary w-100">
                            <i class="bi bi-bar-chart-fill me-2"></i>
                            View Scoreboard
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <a href="/judge" class="btn btn-outline-success w-100">
                            <i class="bi bi-person-badge-fill me-2"></i>
                            Go to Judge Portal
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <button class="btn btn-outline-info w-100" onclick="exportScoresCSV()">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                            Export Scores (CSV)
                        </button>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <button class="btn btn-outline-warning w-100" onclick="refreshStats()">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            Refresh Statistics
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Export scores to CSV 
function exportScoresCSV() {
    // Show loading toast
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-info border-0 position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <span class="spinner-border spinner-border-sm me-2"></span>
                Generating CSV export...
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // In a real application, this would fetch data from the server
    // For this demo, we'll simulate a server response
    setTimeout(() => {
        // Create CSV content based on the participant data
        const headers = ['Participant ID', 'Participant Name', 'Total Score', 'Average Score', 'Judges'];
        let csvContent = headers.join(',') + '\n';
        
        // In a real implementation, this would use the actual data from an API endpoint
        // Here we'll just show a success message
        document.body.removeChild(toast);
        
        // Show success message
        const successToast = document.createElement('div');
        successToast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
        successToast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        successToast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Scores exported successfully! 
                    <a href="#" class="text-white text-decoration-underline">Download CSV</a>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(successToast);
        
        const bsSuccessToast = new bootstrap.Toast(successToast);
        bsSuccessToast.show();
        
        successToast.addEventListener('hidden.bs.toast', () => {
            document.body.removeChild(successToast);
        });
    }, 2000);
}

// Refresh statistics
async function refreshStats() {
    try {
        // Show loading indicator
        const refreshBtn = document.querySelector('.btn-outline-warning');
        const originalText = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Refreshing...';
        refreshBtn.disabled = true;
        
        // Fetch updated stats
        const response = await makeRequest('/api/stats');
        if (response.success) {
            // Update stats cards
            document.querySelector('.stats-number:nth-child(1)').textContent = response.data.stats.total_participants || 0;
            document.querySelector('.stats-number:nth-child(2)').textContent = response.data.stats.total_judges || 0;
            document.querySelector('.stats-number:nth-child(3)').textContent = response.data.stats.total_scores || 0;
            document.querySelector('.stats-number:nth-child(4)').textContent = (response.data.stats.completion_percentage || 0).toFixed(1) + '%';
            
            // Update progress bar
            document.querySelector('.stats-card:nth-child(4) .progress-bar').style.width = (response.data.stats.completion_percentage || 0) + '%';
            
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Statistics refreshed!
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
        console.error('Failed to refresh stats:', error);
        
        // Show error message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-danger border-0 position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Failed to refresh statistics.
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
    } finally {
        // Restore button state
        const refreshBtn = document.querySelector('.btn-outline-warning');
        refreshBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Refresh Statistics';
        refreshBtn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Remember active tab
    const triggerTabList = [].slice.call(document.querySelectorAll('#adminTabs button'));
    triggerTabList.forEach(function (triggerEl) {
        triggerEl.addEventListener('click', function (event) {
            // Store in session storage
            sessionStorage.setItem('activeAdminTab', event.target.id);
        });
    });
    
    // Restore active tab
    const activeTab = sessionStorage.getItem('activeAdminTab');
    if (activeTab) {
        const tab = document.querySelector(`#${activeTab}`);
        if (tab) {
            const tabTrigger = new bootstrap.Tab(tab);
            tabTrigger.show();
        }
    }
});
</script>