<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Watch List - <?= esc($username) ?></title>
	<?= $this->include('partials/custom_link') ?>
	<?php helper(['url','anime']); ?>
</head>
<body>
<?= $this->include('partials/header') ?>
<div class="top-nav">
	<div class="nav-container">
		<a href="<?= base_url('account/profile') ?>" class="nav-tab"><i class="fas fa-user icon-profile"></i> Profile</a>
		<a href="<?= base_url('account/continue-watching') ?>" class="nav-tab"><i class="fas fa-play icon-play"></i> Continue Watching</a>
		<a href="<?= base_url('account/watch-list') ?>" class="nav-tab active"><i class="fas fa-heart icon-heart"></i> Watch List</a>
		<a href="<?= base_url('account/notifications') ?>" class="nav-tab"><i class="fas fa-bell icon-bell"></i> Notification</a>
		<a href="#" class="nav-tab"><i class="fas fa-gear icon-gear"></i> Settings</a>
		<a href="#" class="nav-tab"><i class="fas fa-paper-plane icon-mail"></i> MAL</a>
	</div>
</div>
<div class="main-content">
	<div class="notifications-wrapper">
		<div class="notifications-header">
			<div class="title-group">
				<i class="fas fa-heart notif-icon" style="color: #ec4899;"></i>
				<h1>Watch List</h1>
			</div>
		</div>

		<div class="notif-content">
			<?php if (!empty($followedAnime)): ?>
				<div class="anime-grid">
					<?php foreach ($followedAnime as $anime): ?>
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
				<div class="empty-state">
					<div class="empty-icon"><i class="fas fa-heart-broken"></i></div>
					<h3>No Anime in Watch List</h3>
					<p>Follow anime you want to watch later by clicking the "Follow" button on anime pages. Your followed anime will appear here.</p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?= $this->include('partials/footer') ?>
</body>
</html>
