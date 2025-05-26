<!-- Judge Portal Header -->
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-person-badge text-primary me-2"></i>Judge Portal</h2>
        <p class="text-muted">Evaluate participants and track your progress</p>
    </div>
</div>

<!-- Judge Selection Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Select Judge</h5>
    </div>
    <div class="card-body">
        <?php if (empty($judges)): ?>
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>
                No judges available. Please contact the administrator.
            </div>
        <?php else: ?>
            <form method="GET" action="/judge" class="row g-3">
                <div class="col-md-8">
                    <select name="judge_id" class="form-select" required onchange="this.form.submit()">
                        <option value="">Select a judge...</option>
                        <?php foreach ($judges as $judge): ?>
                            <option value="<?= $judge['judge_id'] ?>" <?= $selectedJudgeId == $judge['judge_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($judge['display_name']) ?> 
                                (<?= htmlspecialchars($judge['username']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Continue</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if ($selectedJudgeId > 0 && !empty($progress)): ?>
    <!-- Scoring Status Row -->
    <div class="row mb-4">
        <!-- Progress Card -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Scoring Progress</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Participants scored:</span>
                        <span class="fw-bold"><?= $progress['scores_given'] ?> / <?= $progress['total_participants'] ?></span>
                    </div>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" 
                             style="width: <?= $progress['completion_percentage'] ?>%">
                            <?= number_format($progress['completion_percentage'], 1) ?>%
                        </div>
                    </div>
                    <?php if ($progress['completion_percentage'] >= 100): ?>
                        <div class="alert alert-success mb-0 py-2">
                            <i class="bi bi-check-circle me-2"></i>
                            All participants scored!
                        </div>
                    <?php else: ?>
                        <div class="text-muted small">
                            <?= $progress['total_participants'] - $progress['scores_given'] ?> participants remaining
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Guidelines Card -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Scoring Guidelines</h5>
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
                        <li>
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Changes are automatically saved
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Participants Scoring Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Score Participants</h5>
            <div>
                <span class="badge bg-success me-1"><?= count(array_filter($participants, fn($p) => $p['is_scored'])) ?> Scored</span>
                <span class="badge bg-light text-dark"><?= count(array_filter($participants, fn($p) => !$p['is_scored'])) ?> Pending</span>
            </div>
        </div>
        
        <div class="card-body p-0">
            <?php if (empty($participants)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-3">No participants available</h5>
                    <p class="text-muted">Please contact the administrator to add participants</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Participant</th>
                                <th style="width: 120px;">Score</th>
                                <th>Comments</th>
                                <th style="width: 120px;" class="text-center">Action</th>
                                <th style="width: 90px;" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participants as $participant): ?>
                                <tr class="<?= $participant['is_scored'] ? 'bg-light' : '' ?>">
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
                                    
                                    <td>
                                        <form id="score-form-<?= $participant['user_id'] ?>" method="POST" action="/judge/score" class="score-form">
                                            <input type="hidden" name="csrf_token" value="<?= Utils::generateCSRFToken() ?>">
                                            <input type="hidden" name="judge_id" value="<?= $selectedJudgeId ?>">
                                            <input type="hidden" name="user_id" value="<?= $participant['user_id'] ?>">
                                            
                                            <input type="number" 
                                                   name="score_value" 
                                                   class="form-control" 
                                                   min="<?= Config::getInstance()->scoreMin ?>" 
                                                   max="<?= Config::getInstance()->scoreMax ?>" 
                                                   value="<?= $participant['score_value'] ?? '' ?>"
                                                   placeholder="Score" required>
                                        </form>
                                    </td>
                                    
                                    <td>
                                        <textarea name="comments" 
                                                  class="form-control" 
                                                  rows="1" 
                                                  placeholder="Optional comments"
                                                  form="score-form-<?= $participant['user_id'] ?>"><?= htmlspecialchars($participant['comments'] ?? '') ?></textarea>
                                    </td>
                                    
                                    <td class="text-center">
                                        <button type="submit" 
                                                form="score-form-<?= $participant['user_id'] ?>"
                                                class="btn <?= $participant['is_scored'] ? 'btn-outline-primary' : 'btn-primary' ?>">
                                            <?= $participant['is_scored'] ? 'Update' : 'Save' ?>
                                        </button>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php if ($participant['is_scored']): ?>
                                            <span class="badge bg-success">Scored</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark">Pending</span>
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
<?php endif; ?>
