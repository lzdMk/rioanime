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
    <a href="<?= base_url('account/watch-list') ?>" class="nav-tab"><i class="fas fa-heart icon-heart"></i> Watch List</a>
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
            <h1>Notifications</h1>
            <span class="notification-count" id="notificationCount">0</span>
        </div>
        <div class="actions-group">
            <button class="btn btn-select-all" id="selectAllBtn">
                <i class="fas fa-check-double"></i> Select All
            </button>
            <button class="btn btn-mark-read" id="markReadBtn" disabled>
                <i class="fas fa-check"></i> Mark as Read
            </button>
            <button class="btn btn-delete" id="deleteBtn" disabled>
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>

    <div class="notif-content" id="notificationContent">
        <div class="loading-state" id="loadingState">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <p>Loading notifications...</p>
        </div>

        <div class="empty-state" id="emptyState" style="display: none;">
            <div class="empty-icon"><i class="fas fa-bell-slash"></i></div>
            <h3>No Notifications</h3>
            <p>You're all caught up! Check back later for updates.</p>
        </div>

        <div class="notifications-list" id="notificationsList" style="display: none;">
            <!-- Notifications will be loaded here via JavaScript -->
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

<?= $this->include('partials/footer') ?>

<!-- Confirm Modal (modern, Bootstrap) -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="margin-top: 8vh;">
        <div class="modal-content" style="background:#1b1b35;color:#fff;border:1px solid rgba(255,255,255,0.08)">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">Are you sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmYesBtn"><i class="fas fa-trash me-1"></i>Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Detail Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="margin-top: 5vh; max-height: 90vh;">
        <div class="modal-content" style="background:#1b1b35;color:#fff;border:1px solid rgba(255,255,255,0.08); max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title" id="notificationModalTitle">
                    <i class="fas fa-bell me-2"></i>Notification Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="flex: 1; overflow-y: auto;">
                <div class="notification-detail">
                    <div class="notification-header-detail mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="notification-icon-detail me-3" id="notificationIconDetail">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <h6 class="mb-1" id="notificationTitleDetail">Title</h6>
                                <small id="notificationTimeDetail">Time</small>
                            </div>
                        </div>
                    </div>
                    <div class="notification-message-detail">
                        <p id="notificationMessageDetail" style="line-height: 1.6; margin-bottom: 0;">Message content</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="markAsReadFromModal" style="display: none;">
                    <i class="fas fa-check me-1"></i>Mark as Read
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Configuration for notifications JS (endpoints and CSRF token)
window.rioNotificationsConfig = {
    endpoints: {
        list: '<?= base_url('api/notifications') ?>',
        markRead: '<?= base_url('api/notifications/mark-read') ?>',
        delete: '<?= base_url('api/notifications/delete') ?>'
    },
    csrf: {
        tokenName: '<?= csrf_token() ?>',
        hash: '<?= csrf_hash() ?>'
    }
};
</script>
<script src="<?= base_url('assets/js/notifications.js') ?>"></script>
</body>
</html>
