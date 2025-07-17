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
            <a href="#" class="nav-tab active">
                <span>👤</span>
                Profile
            </a>
            <a href="#" class="nav-tab">
                <span>▶️</span>
                Continue Watching
            </a>
            <a href="#" class="nav-tab">
                <span>❤️</span>
                Watch List
            </a>
            <a href="#" class="nav-tab">
                <span>🔔</span>
                Notification
            </a>
            <a href="#" class="nav-tab">
                <span>⚙️</span>
                Settings
            </a>
            <a href="#" class="nav-tab">
                <span>📧</span>
                MAL
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="profile-container">
        <div class="profile-header">
            <span class="icon">👤</span>
            <h1>Edit Profile</h1>
        </div>

        <form action="<?= base_url('account/updateProfile') ?>" method="POST" class="profile-form">
            <?= csrf_field() ?>
            
            <div class="form-fields">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" value="<?= esc($email ?? 'xyrusx@proton.me') ?>" required>
                    <div class="verified-badge">
                        <span>✓</span>
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
                        <span>🔒</span>
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
                    <button type="button" class="edit-avatar-btn">
                        ✏️
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->include('partials/footer') ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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