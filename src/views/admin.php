<!-- Admin Panel Header -->
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-gear text-primary me-2"></i>Admin Panel</h2>
        <p class="text-muted">Manage judges, participants and system settings</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <h3 class="text-primary mb-1"><?= $stats['total_participants'] ?? 0 ?></h3>
                <p class="text-muted mb-0">Participants</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <h3 class="text-primary mb-1"><?= $stats['total_judges'] ?? 0 ?></h3>
                <p class="text-muted mb-0">Judges</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <h3 class="text-primary mb-1"><?= $stats['total_scores'] ?? 0 ?></h3>
                <p class="text-muted mb-0">Total Scores</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <h3 class="text-primary mb-1"><?= number_format($stats['completion_percentage'] ?? 0, 1) ?>%</h3>
                <p class="text-muted mb-0">Completion</p>
            </div>
        </div>
    </div>
</div>

<!-- Management Tabs -->
<ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="judges-tab" data-bs-toggle="tab" data-bs-target="#judges" type="button" role="tab">
            <i class="bi bi-person-badge me-2"></i>Judges
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="participants-tab" data-bs-toggle="tab" data-bs-target="#participants" type="button" role="tab">
            <i class="bi bi-people me-2"></i>Participants
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">
            <i class="bi bi-activity me-2"></i>Activity
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="adminTabsContent">
    
    <!-- Judges Tab -->
    <div class="tab-pane fade show active" id="judges" role="tabpanel">
        <div class="row mb-4">
            <div class="col-md-5 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Add New Judge</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/admin/judge">
                            <input type="hidden" name="csrf_token" value="<?= Utils::generateCSRFToken() ?>">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="display_name" class="form-label">Display Name</label>
                                <input type="text" class="form-control" id="display_name" name="display_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                Add Judge
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Judges List</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($judges)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-person-badge text-muted" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3">No judges added</h5>
                                <p class="text-muted">Add judges using the form</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Judge</th>
                                            <th>Email</th>
                                            <th class="text-center">Scores</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($judges as $judge): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-light rounded-circle text-center d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                            <?= strtoupper(substr($judge['display_name'], 0, 1)) ?>
                                                        </div>
                                                        <div>
                                                            <?= htmlspecialchars($judge['display_name']) ?>
                                                            <div class="small text-muted">@<?= htmlspecialchars($judge['username']) ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($judge['email']) ?></td>
                                                <td class="text-center"><span class="badge bg-primary">0</span></td>
                                                <td class="text-center">
                                                    <?php if ($judge['is_active']): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
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
            </div>
        </div>
    </div>
    
    <!-- Participants Tab -->
    <div class="tab-pane fade" id="participants" role="tabpanel">
        <div class="row mb-4">
            <div class="col-md-5 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Add New Participant</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/admin/user">
                            <input type="hidden" name="csrf_token" value="<?= Utils::generateCSRFToken() ?>">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="display_name" class="form-label">Display Name</label>
                                <input type="text" class="form-control" id="display_name" name="display_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                Add Participant
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Participants List</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($participants)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-people text-muted" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3">No participants added</h5>
                                <p class="text-muted">Add participants using the form</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Participant</th>
                                            <th>Email</th>
                                            <th>Bio</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($participants as $participant): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-light rounded-circle text-center d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                            <?= strtoupper(substr($participant['display_name'], 0, 1)) ?>
                                                        </div>
                                                        <div>
                                                            <?= htmlspecialchars($participant['display_name']) ?>
                                                            <div class="small text-muted">@<?= htmlspecialchars($participant['username']) ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($participant['email']) ?></td>
                                                <td>
                                                    <small>
                                                        <?= empty($participant['bio']) ? '—' : htmlspecialchars(substr($participant['bio'], 0, 50)) . (strlen($participant['bio']) > 50 ? '...' : '') ?>
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($participant['is_active']): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
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
            </div>
        </div>
    </div>
    
    <!-- Activity Tab -->
    <div class="tab-pane fade" id="activity" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Recent Activity</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentActivity)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-clock-history text-muted" style="font-size: 2.5rem;"></i>
                        <h5 class="mt-3">No recent activity</h5>
                        <p class="text-muted">Activity will appear here once judges start scoring</p>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recentActivity as $activity): ?>
                            <li class="list-group-item py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <strong><?= htmlspecialchars($activity['judge_name']) ?></strong>
                                        scored 
                                        <strong><?= htmlspecialchars($activity['participant_name']) ?></strong>
                                        with 
                                        <span class="badge bg-primary"><?= $activity['score_value'] ?></span>
                                        
                                        <?php if (!empty($activity['comments'])): ?>
                                            <p class="text-muted small mt-1 ms-4 fst-italic">
                                                "<?= htmlspecialchars(substr($activity['comments'], 0, 100)) ?>
                                                <?= strlen($activity['comments']) > 100 ? '...' : '' ?>"
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        <?= date('M j, g:i A', strtotime($activity['created_at'])) ?>
                                    </small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
