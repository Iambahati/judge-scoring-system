<!-- Simple Scoreboard Header -->
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-trophy text-primary me-2"></i>Scoreboard</h2>
        <p class="text-muted">Competition rankings and statistics</p>
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
                <h3 class="text-primary mb-1"><?= number_format($stats['average_score'] ?? 0, 1) ?></h3>
                <p class="text-muted mb-0">Average Score</p>
            </div>
        </div>
    </div>
</div>

<!-- Rankings Table Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-list-ol me-2"></i>Rankings</h5>
    </div>
    
    <div class="card-body p-0">
        <?php if (empty($scoreboard)): ?>
            <div class="text-center py-5">
                <i class="bi bi-trophy text-muted" style="font-size: 2.5rem;"></i>
                <h5 class="mt-3">No scores available</h5>
                <p class="text-muted">Scores will appear once judges submit evaluations</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Participant</th>
                            <th class="text-end">Score</th>
                            <th class="text-end">Average</th>
                            <th class="text-center">Judges</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scoreboard as $participant): ?>
                            <tr>
                                <td width="60">
                                    <?php if ($participant['ranking'] <= 3): ?>
                                        <span class="badge rounded-pill 
                                            <?= ($participant['ranking'] == 1) ? 'bg-warning text-dark' : 
                                               (($participant['ranking'] == 2) ? 'bg-secondary' : 'bg-danger') ?>">
                                            <?= $participant['ranking'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">
                                            <?= $participant['ranking'] ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
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
                                <td class="text-end fw-bold">
                                    <?= number_format($participant['total_score'] ?? 0, 1) ?>
                                </td>
                                <td class="text-end">
                                    <?= number_format($participant['average_score'] ?? 0, 2) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">
                                        <?= $participant['total_judges'] ?? 0 ?>/<?= $stats['total_judges'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($recentActivity)): ?>
<!-- Recent Activity Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Activity</h5>
    </div>
    <div class="card-body p-0">
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
    </div>
</div>
<?php endif; ?>
