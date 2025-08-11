<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Continue Watching - <?= esc($username) ?></title>
	<?= $this->include('partials/custom_link') ?>
	<?php helper(['url','anime']); ?>
</head>
<body>
<?= $this->include('partials/header') ?>
<div class="top-nav">
	<div class="nav-container">
		<a href="<?= base_url('account/profile') ?>" class="nav-tab"><span>👤</span> Profile</a>
		<a href="<?= base_url('account/continue-watching') ?>" class="nav-tab active"><span>▶️</span> Continue Watching</a>
		<a href="#" class="nav-tab"><span>❤️</span> Watch List</a>
		<a href="#" class="nav-tab"><span>🔔</span> Notification</a>
		<a href="#" class="nav-tab"><span>⚙️</span> Settings</a>
		<a href="#" class="nav-tab"><span>📧</span> MAL</a>
	</div>
</div>
<div class="main-content">
	<div class="continue-container">
		<div class="profile-header continue-heading" style="margin-bottom:1.5rem;">
			<span class="icon">▶️</span>
			<h1>Continue Watching</h1>
		</div>
		<?php if (!empty($watchedAnime)): ?>
			<div class="anime-grid">
				<?php foreach ($watchedAnime as $anime): ?>
					<a href="<?= base_url('watch/' . createSlug($anime['title'])) ?>" class="anime-card-link">
						<div class="anime-card" data-type="<?= strtolower(esc($anime['type'])) ?>">
							<div class="anime-poster">
								<img src="<?= !empty($anime['backgroundImage']) ? esc($anime['backgroundImage']) : 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=400&fit=crop' ?>" alt="<?= esc($anime['title']) ?>" loading="lazy">
								<div class="anime-overlay"><div class="play-button"><i class="fas fa-play"></i></div></div>
								<div class="anime-badge <?= esc(!empty($anime['type']) ? strtolower(preg_replace('/[^a-z0-9]+/i','-', $anime['type'])) : 'unknown') ?>"><?= esc($anime['type']) ?></div>
								<div class="episode-count"><?= !empty($anime['total_ep']) ? 'Ep ' . esc($anime['total_ep']) : 'New' ?></div>
							</div>
							<div class="anime-info">
								<h3 class="anime-title" title="<?= esc($anime['title']) ?>"><?= esc(truncateTitle($anime['title'])) ?></h3>
							</div>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<p style="color: var(--text-secondary); font-size:0.9rem;">No watched anime yet.</p>
		<?php endif; ?>
</div>
</div>
<?= $this->include('partials/footer') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
