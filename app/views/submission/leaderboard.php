<div class="page-header">
  <div class="page-header__inner">
    <h1>🏆 Classement des participations</h1>
    <p>Les créations les plus votées par la communauté ChallengeHub</p>
  </div>
</div>

<div class="container section">

  <?php if (empty($topSubmissions)): ?>
    <div class="empty-state">
      <div class="empty-state__icon">🏆</div>
      <h3>Aucune participation votée pour l'instant</h3>
      <p>Participez aux défis et votez pour vos créations préférées !</p>
      <a href="<?= BASE_URL ?>/index.php?page=challenges" class="btn btn-primary" style="margin-top:1.5rem;">Voir les défis →</a>
    </div>
  <?php else: ?>

    <!-- Top 3 Podium -->
    <?php $top3 = array_slice($topSubmissions, 0, 3); ?>
    <div style="display:flex; gap:1.5rem; justify-content:center; margin-bottom:3rem; flex-wrap:wrap; align-items:flex-end;">
      <?php
      $podiumOrder = [1, 0, 2]; // Silver, Gold, Bronze display order
      $podiumLabels = ['🥇', '🥈', '🥉'];
      $podiumHeights = ['180px', '150px', '130px'];
      ?>
      <?php foreach ($podiumOrder as $pos): ?>
        <?php if (!isset($top3[$pos])) continue; $sub = $top3[$pos]; ?>
        <div style="text-align:center; width:200px;">
          <div class="rank-badge rank-<?= $pos + 1 ?>" style="width:48px; height:48px; font-size:1.3rem; margin:0 auto .75rem;">
            <?= $podiumLabels[$pos] ?>
          </div>
          <div style="background:var(--clr-surface); border:1px solid var(--clr-border); border-radius:var(--radius-lg); padding:1.25rem; height:<?= $podiumHeights[$pos] ?>; display:flex; flex-direction:column; justify-content:space-between;">
            <div>
              <p style="font-weight:700; font-size:.9rem; color:var(--clr-text);"><?= htmlspecialchars(mb_substr($sub['username'], 0, 20)) ?></p>
              <p class="text-dim" style="font-size:.75rem;"><?= htmlspecialchars(mb_substr($sub['challenge_title'], 0, 40)) ?></p>
            </div>
            <div>
              <span class="badge badge-primary" style="font-size:.85rem;">⭐ <?= (int)$sub['vote_count'] ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Full Leaderboard Table -->
    <div class="glass-panel" style="overflow-x:auto;">
      <table class="leaderboard-table" id="leaderboard-table">
        <thead>
          <tr>
            <th style="width:60px">#</th>
            <th>Participant</th>
            <th>Description</th>
            <th>Défi</th>
            <th style="text-align:center">Votes</th>
            <th style="text-align:right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($topSubmissions as $i => $sub): ?>
          <tr id="leaderboard-row-<?= $sub['id'] ?>">
            <td>
              <div class="rank-badge rank-<?= $i < 3 ? $i + 1 : 'n' ?>"><?= $i + 1 ?></div>
            </td>
            <td>
              <div class="flex items-center gap-1">
                <?php if (!empty($sub['avatar'])): ?>
                  <img src="<?= UPLOAD_URL . htmlspecialchars($sub['avatar']) ?>" class="avatar-sm" alt="">
                <?php else: ?>
                  <div class="avatar-placeholder sm"><?= strtoupper(substr($sub['username'], 0, 1)) ?></div>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/index.php?page=profile&id=<?= $sub['user_id'] ?>" style="color:var(--clr-primary-l); font-weight:600; font-size:.875rem;">
                  <?= htmlspecialchars($sub['username']) ?>
                </a>
              </div>
            </td>
            <td style="max-width:250px;">
              <span style="font-size:.85rem; color:var(--clr-text-muted);">
                <?= htmlspecialchars(mb_substr($sub['description'], 0, 80)) ?><?= strlen($sub['description']) > 80 ? '...' : '' ?>
              </span>
            </td>
            <td>
              <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $sub['challenge_id'] ?>" style="font-size:.85rem; color:var(--clr-accent-2);">
                <?= htmlspecialchars(mb_substr($sub['challenge_title'], 0, 40)) ?>
              </a>
            </td>
            <td style="text-align:center;">
              <span class="badge badge-primary">⭐ <?= (int)$sub['vote_count'] ?></span>
            </td>
            <td style="text-align:right;">
              <a href="<?= BASE_URL ?>/index.php?page=submission-show&id=<?= $sub['id'] ?>" class="btn btn-ghost btn-sm">Voir →</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  <?php endif; ?>
</div>
