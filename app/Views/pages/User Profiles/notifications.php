<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notifications - <?= esc($username) ?></title>
    <?= $this->include('partials/custom_link') ?>
    <?php helper(['url']); ?>
</head>
<body>
<?= $this->include('partials/header') ?>
<div class="top-nav">
    <div class="nav-container">
    <a href="<?= base_url('account/profile') ?>" class="nav-tab"><i class="fas fa-user icon-profile"></i> Profile</a>
    <a href="<?= base_url('account/continue-watching') ?>" class="nav-tab"><i class="fas fa-play icon-play"></i> Continue Watching</a>
    <a href="#" class="nav-tab"><i class="fas fa-heart icon-heart"></i> Watch List</a>
    <a href="<?= base_url('account/notifications') ?>" class="nav-tab active"><i class="fas fa-bell icon-bell"></i> Notification</a>
    <a href="#" class="nav-tab"><i class="fas fa-gear icon-gear"></i> Settings</a>
    <a href="#" class="nav-tab"><i class="fas fa-paper-plane icon-mail"></i> MAL</a>
    </div>
</div>
<div class="main-content">
  <div class="notifications-wrapper">
    <div class="notifications-header">
        <div class="title-group">
            <i class="fas fa-bell notif-icon"></i>
            <h1>Notification</h1>
        </div>
        <div class="actions-group">
            <button class="btn btn-mark-read" disabled>
                <i class="fas fa-check"></i> Mark all as read
            </button>
            <button class="btn btn-clear-all" disabled>
                <i class="fas fa-trash"></i> Clear All
            </button>
        </div>
    </div>

    <div class="notif-tabs">
        <button class="notif-tab active" data-tab="anime">Anime</button>
        <button class="notif-tab" data-tab="community">Community</button>
    </div>

    <div class="notif-content">
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-box-open"></i></div>
            <p>No Notifications</p>
        </div>

        <!-- Example notification item (hidden by default until functionality added) -->
        <div class="notification-item sample d-none" data-category="anime">
            <div class="notification-left">
                <div class="badge-type">NEW</div>
                <div class="notification-info">
                    <div class="notification-title">Episode 5 is out!</div>
                    <div class="notification-meta">Just now · Series Name</div>
                </div>
            </div>
            <div class="notification-right">
                <span class="dot unread"></span>
            </div>
        </div>
    </div>
  </div>
</div>
<?= $this->include('partials/footer') ?>
</body>
</html>
