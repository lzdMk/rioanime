<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit Profile - <?= esc($username) ?></title>
	<?= $this->include('partials/custom_link') ?>
	<?php helper(['url','anime']); ?>
</head>
<body>
	<?= $this->include('partials/header') ?>
	<div class="top-nav">
		<div class="nav-container">
			<a href="<?= base_url('account/profile') ?>" class="nav-tab active">
				<i class="fas fa-user icon-profile"></i>
				Profile
			</a>
			<a href="<?= base_url('account/continue-watching') ?>" class="nav-tab">
				<i class="fas fa-play icon-play"></i>
				Continue Watching
			</a>
			<a href="#" class="nav-tab">
				<i class="fas fa-heart icon-heart"></i>
				Watch List
			</a>
			<a href="<?= base_url('account/notifications') ?>" class="nav-tab">
				<i class="fas fa-bell icon-bell"></i>
				Notification
			</a>
			<a href="#" class="nav-tab">
				<i class="fas fa-gear icon-gear"></i>
				Settings
			</a>
			<a href="#" class="nav-tab">
				<i class="fas fa-paper-plane icon-mail"></i>
				MAL
			</a>
		</div>
	</div>

	<div class="main-content">
		<div class="profile-container">
		<div class="profile-header">
			<i class="fas fa-user icon icon-profile"></i>
			<h1>Edit Profile</h1>
		</div>

		<form action="<?= base_url('account/updateProfile') ?>" method="POST" class="profile-form">
			<?= csrf_field() ?>
            
			<div class="form-fields">
				<div class="form-group">
					<label class="form-label">Email Address</label>
					<input type="email" name="email" class="form-input" value="<?= esc($email ?? 'xyrusx@proton.me') ?>" required>
					<div class="verified-badge">
						<i class="fas fa-check-circle icon-verified"></i>
						Verified
					</div>
				</div>

				<div class="form-group">
					<label class="form-label">Your Name</label>
					<input type="text" name="username" class="form-input" value="<?= esc($username) ?>" required>
				</div>

				<div class="form-group">
					<label class="form-label">Joined</label>
					<input type="text" class="form-input" value="<?= date('Y-m-d', strtotime(session('created_at') ?? '2024-10-26')) ?>" disabled>
				</div>

				<div class="form-group">
					<a href="<?= base_url('account/changePassword') ?>" class="change-password-btn">
						<i class="fas fa-lock icon-lock"></i>
						Change password
					</a>
				</div>

				<button type="submit" class="save-btn">
					Save
				</button>
			</div>

			<div class="avatar-section">
				<div class="avatar-container">
					<div class="avatar">
						<?php if (!empty($user_profile)): ?>
							<img src="<?= esc($user_profile) ?>" alt="Profile Picture" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;" />
						<?php elseif (!empty($username)): ?>
							<?= strtoupper(substr(trim($username), 0, 1)) ?>
						<?php endif; ?>
					</div>
					<button type="button" class="edit-avatar-btn" aria-label="Edit avatar">
						<i class="fas fa-pen"></i>
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?= $this->include('partials/footer') ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const editAvatarBtn = document.querySelector('.edit-avatar-btn');
			const form = document.querySelector('form');
            
			editAvatarBtn.addEventListener('click', function() {
				alert('Avatar upload functionality would go here');
			});
            
			form.addEventListener('submit', function(e) {
				const username = document.querySelector('input[name="username"]').value;
				const email = document.querySelector('input[name="email"]').value;
                
				if (!username.trim() || !email.trim()) {
					e.preventDefault();
					alert('Please fill in all required fields');
				}
			});
		});
	</script>
</body>
</html>
