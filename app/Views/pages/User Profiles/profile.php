<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit Profile - <?= esc($username) ?></title>
	<?= $this->include('partials/custom_link') ?>
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
			<a href="<?= base_url('account/watch-list') ?>" class="nav-tab">
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

		<form action="<?= base_url('account/updateProfile') ?>" method="POST" class="profile-form" id="profileForm">
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
					<button type="button" class="change-password-btn" id="openChangePassword">
						<i class="fas fa-lock icon-lock"></i>
						Change password
					</button>
				</div>

				<button type="submit" class="save-btn">
					Save
				</button>
			</div>

			<div class="avatar-section">
				<div class="avatar-container">
					<div class="avatar">
						<?php if (!empty($user_profile)): ?>
							<img src="<?= esc($user_profile) ?>" alt="Profile Picture" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" />
						<?php elseif (!empty($username)): ?>
							<?= strtoupper(substr(trim($username), 0, 1)) ?>
						<?php endif; ?>
					</div>
					<button type="button" class="edit-avatar-btn" aria-label="Edit avatar" id="editAvatarBtn" data-bs-toggle="modal" data-bs-target="#avatarModal">
						<i class="fas fa-pen"></i>
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?= $this->include('partials/footer') ?>

<!-- Password Change Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content" style="background:#1b1b35;color:#fff;border:1px solid rgba(255,255,255,0.08)">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fas fa-lock me-2"></i>Change Password</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="passwordForm">
					<?= csrf_field() ?>
					<div class="mb-3">
						<label class="form-label">Current Password</label>
						<input type="password" class="form-control" name="current_password" required>
					</div>
					<div class="mb-3">
						<label class="form-label">New Password</label>
						<input type="password" class="form-control" name="new_password" minlength="8" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Confirm New Password</label>
						<input type="password" class="form-control" name="confirm_password" minlength="8" required>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="savePasswordBtn">Save</button>
			</div>
		</div>
	</div>
	</div>

<!-- Avatar Crop/Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered avatar-modal">
		<div class="modal-content" style="background:#1b1b35;color:#fff;border:1px solid rgba(255,255,255,0.08)">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fas fa-image me-2"></i>Update Avatar</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
					<div class="modal-body">
						<div class="avatar-editor">
							<div class="mb-3 d-flex align-items-center gap-2 w-100">
								<input class="form-control" type="file" id="avatarFile" accept="image/png,image/jpeg,image/webp">
								<small class="text-muted">Max 3MB</small>
							</div>
													<div class="circle-stage">
														<div class="circle-clip">
															<img id="avatarPreview" alt="Preview">
														</div>
														<div class="circle-ring" aria-hidden="true"></div>
													</div>
							<div class="d-flex align-items-center justify-content-center gap-3 mt-2 w-100">
								<i class="fas fa-image"></i>
								<input type="range" id="zoomRange" min="1" max="2.5" value="1" step="0.01" class="form-range flex-fill" style="max-width:280px;">
								<i class="fas fa-search-plus"></i>
							</div>
						</div>
					</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-success" id="uploadAvatarBtn"><i class="fas fa-cloud-upload-alt me-2"></i>Upload</button>
			</div>
		</div>
	</div>
</div>

<!-- Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
	<div id="liveToast" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite" aria-atomic="true">
		<div class="d-flex">
			<div class="toast-body" id="toastBody">Action completed.</div>
			<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
		</div>
	</div>
</div>

<!-- Cropper.js (for client-side crop) -->
<link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const toastEl = document.getElementById('liveToast');
	const toastBody = document.getElementById('toastBody');
	const showToast = (msg) => { toastBody.textContent = msg; new bootstrap.Toast(toastEl, { delay: 2500 }).show(); };

	// Profile form AJAX submit
	const profileForm = document.getElementById('profileForm');
	profileForm.addEventListener('submit', async function(e){
		e.preventDefault();
		const formData = new FormData(profileForm);
		const res = await fetch(profileForm.action, { method: 'POST', body: formData });
		const json = await res.json();
		if (json.success) {
			showToast('Profile updated');
		} else {
			const msg = json.errors ? Object.values(json.errors).join('\n') : (json.message || 'Update failed');
			showToast(msg);
		}
	});

	// Change password
	document.getElementById('openChangePassword').addEventListener('click', () => {
		new bootstrap.Modal(document.getElementById('passwordModal')).show();
	});
	document.getElementById('savePasswordBtn').addEventListener('click', async () => {
		const form = document.getElementById('passwordForm');
		const data = new FormData(form);
		const res = await fetch('<?= base_url('account/changePassword') ?>', { method:'POST', body:data });
		const json = await res.json();
		if (json.success) {
			showToast('Password changed');
			bootstrap.Modal.getInstance(document.getElementById('passwordModal')).hide();
			form.reset();
		} else {
			const msg = json.errors ? Object.values(json.errors).join('\n') : (json.message || 'Failed to change password');
			showToast(msg);
		}
	});

	// Avatar cropper
		let cropper = null;
		const avatarFile = document.getElementById('avatarFile');
		const avatarPreview = document.getElementById('avatarPreview');
		const zoomRange = document.getElementById('zoomRange');
			avatarFile.addEventListener('change', () => {
			const file = avatarFile.files[0];
			if (!file) return;
			if (file.size > 3 * 1024 * 1024) { showToast('File too large. Max 3MB'); avatarFile.value=''; return; }
			const url = URL.createObjectURL(file);
			avatarPreview.src = url;
			avatarPreview.onload = () => {
				if (cropper) cropper.destroy();
						cropper = new Cropper(avatarPreview, {
							aspectRatio: 1,
							viewMode: 2,
							background: false,
							guides: false,
							autoCropArea: 1,
							movable: true,
							dragMode: 'move',
							cropBoxMovable: false,
							cropBoxResizable: false,
							toggleDragModeOnDblclick: false,
							zoomOnWheel: true,
							responsive: true,
						ready() {
							const ring = document.querySelector('.circle-ring');
							const container = cropper.getContainerData();
							const size = Math.min(ring ? ring.offsetWidth : 280, container.width, container.height);
							const left = (container.width - size) / 2;
							const top = (container.height - size) / 2;
							cropper.setCropBoxData({ width: size, height: size, left, top });
							// Ensure image covers the circular area
							const imageData = cropper.getImageData();
							const minZoom = Math.max(size / imageData.naturalWidth, size / imageData.naturalHeight);
							cropper.zoomTo(minZoom);
							zoomRange.min = minZoom;
							zoomRange.value = minZoom;
						}
						});
			};
		});

			zoomRange.addEventListener('input', () => {
				if (!cropper) return;
				const value = parseFloat(zoomRange.value);
				// Apply zoom smoothly relative to current state
				cropper.zoomTo(value);
				
				// Auto-center the image after zoom changes
				setTimeout(() => {
					const canvasData = cropper.getCanvasData();
					const cropBoxData = cropper.getCropBoxData();
					const centerX = cropBoxData.left + (cropBoxData.width / 2);
					const centerY = cropBoxData.top + (cropBoxData.height / 2);
					const newLeft = centerX - (canvasData.width / 2);
					const newTop = centerY - (canvasData.height / 2);
					cropper.setCanvasData({ 
						left: newLeft, 
						top: newTop 
					});
				}, 10);
			});

	document.getElementById('uploadAvatarBtn').addEventListener('click', async () => {
		if (!cropper) { showToast('Please choose an image first'); return; }
		const size = 512;
		const canvas = cropper.getCroppedCanvas({ width: size, height: size, imageSmoothingQuality: 'high' });
		// Create circular mask
		const masked = document.createElement('canvas');
		masked.width = size; masked.height = size;
		const ctx = masked.getContext('2d');
		ctx.save();
		ctx.beginPath();
		ctx.arc(size/2, size/2, size/2, 0, Math.PI*2);
		ctx.closePath();
		ctx.clip();
		ctx.drawImage(canvas, 0, 0, size, size);
		ctx.restore();
		const blob = await new Promise(r => masked.toBlob(r, 'image/png', 0.95));
		const fd = new FormData();
		fd.append('avatar', blob, 'avatar.png');
		fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
		const res = await fetch('<?= base_url('account/uploadAvatar') ?>', { method:'POST', body: fd });
		const json = await res.json();
			if (json.success) {
				showToast('Avatar updated');
				const bustUrl = json.url + (json.url.includes('?') ? '&' : '?') + 't=' + Date.now();
				// Update avatar preview in panel with correct styling
				const avatarNode = document.querySelector('.avatar');
				if (avatarNode) {
					avatarNode.innerHTML = '<img src="'+ bustUrl +'" alt="Profile Picture" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" />';
				}
				// Update navbar avatar
				const navbarAvatar = document.getElementById('navbarAvatar');
				if (navbarAvatar) {
					if (navbarAvatar.tagName === 'IMG') {
						navbarAvatar.src = bustUrl;
					} else {
						// Replace letter avatar with image
						navbarAvatar.outerHTML = '<img id="navbarAvatar" src="' + bustUrl + '" alt="User" class="user-avatar rounded-circle border">';
					}
				}
				bootstrap.Modal.getInstance(document.getElementById('avatarModal')).hide();
				avatarFile.value = '';
				if (cropper) { cropper.destroy(); cropper = null; }
			} else {
			const msg = json.errors ? Object.values(json.errors).join('\n') : (json.message || 'Upload failed');
			showToast(msg);
		}
	});
});
</script>
</body>
</html>
