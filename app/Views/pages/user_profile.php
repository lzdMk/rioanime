<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?= esc($username) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

     <!-- Custom CSS and Fonts -->
     <?= $this->include('partials/custom_link') ?>
</head>
<body>
    <?= $this->include('partials/header') ?>
    <div class="user-profile">
        <div class="container">
            <div class="user-profile-header">
                <h1>Hi, <?= esc($username) ?></h1>
                
                <div class="user-profile-nav">
                    <a href="#" class="user-profile-nav-tab active">
                        <span><i class="fas fa-user user-profile-icon user-profile-icon-user"></i></span>
                        Profile
                    </a>
                    <a href="#" class="user-profile-nav-tab">
                        <span><i class="fas fa-play user-profile-icon user-profile-icon-play"></i></span>
                        Continue Watching
                    </a>
                    <a href="#" class="user-profile-nav-tab">
                        <span><i class="fas fa-heart user-profile-icon user-profile-icon-heart"></i></span>
                        Watch List
                    </a>
                    <a href="#" class="user-profile-nav-tab">
                        <span><i class="fas fa-bell user-profile-icon user-profile-icon-bell"></i></span>
                        Notification
                    </a>
                    <a href="#" class="user-profile-nav-tab">
                        <span><i class="fas fa-cog user-profile-icon user-profile-icon-cog"></i></span>
                        Settings
                    </a>
                    <a href="#" class="user-profile-nav-tab">
                        <span><i class="fas fa-envelope user-profile-icon user-profile-icon-envelope"></i></span>
                        MAL
                    </a>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="user-profile-form-card">
                                <h2 class="user-profile-section-title">
                                    <span><i class="fas fa-user user-profile-icon user-profile-icon-user"></i></span>
                                    Edit Profile
                                </h2>

                                <form action="<?= base_url('account/updateProfile') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    
                                    <div class="mb-4">
                                        <label class="user-profile-form-label">Email Address</label>
                                        <input type="email" name="email" class="user-profile-form-input" value="<?= esc($email) ?>" required>
                                        <div class="user-profile-verified-badge">
                                            <span>✓</span>
                                            Verified
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="user-profile-form-label">Your Name</label>
                                        <input type="text" name="username" class="user-profile-form-input" value="<?= esc($username) ?>" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="user-profile-form-label">Account Type</label>
                                        <input type="text" class="user-profile-form-input" value="<?= esc($type) ?>" disabled>
                                    </div>

                                    <div class="mb-4">
                                        <label class="user-profile-form-label">Member Since</label>
                                        <input type="text" class="user-profile-form-input" value="<?= date('Y-m-d', strtotime(session('created_at') ?? 'now')) ?>" disabled>
                                    </div>

                                    <div class="mb-4">
                                        <a href="<?= base_url('account/changePassword') ?>" class="user-profile-change-password-btn">
                                            <span>🔒</span>
                                            Change password
                                        </a>
                                    </div>

                                    <button type="submit" class="user-profile-save-btn">
                                        Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="user-profile-avatar-container">
                                    <div class="user-profile-avatar">
                                        <?= strtoupper(substr($username, 0, 1)) ?>
                                    </div>
                                    <button class="user-profile-edit-avatar">
                                        ✏️
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->include('partials/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            const editAvatar = document.querySelector('.user-profile-edit-avatar');
            const form = document.querySelector('form');
            
            editAvatar.addEventListener('click', function() {
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