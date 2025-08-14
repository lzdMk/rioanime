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
    <div class="modal-dialog modal-lg" style="margin-top: 5vh;">
        <div class="modal-content" style="background:#1b1b35;color:#fff;border:1px solid rgba(255,255,255,0.08)">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalTitle">
                    <i class="fas fa-bell me-2"></i>Notification Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="notification-detail">
                    <div class="notification-header-detail mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="notification-icon-detail me-3" id="notificationIconDetail">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <h6 class="mb-1" id="notificationTitleDetail">Title</h6>
                                <small class="text-muted" id="notificationTimeDetail">Time</small>
                            </div>
                        </div>
                    </div>
                    <div class="notification-message-detail">
                        <p id="notificationMessageDetail" style="line-height: 1.6; margin-bottom: 0;">Message content</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="markAsReadFromModal" style="display: none;">
                    <i class="fas fa-check me-1"></i>Mark as Read
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationContent = document.getElementById('notificationContent');
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    const notificationsList = document.getElementById('notificationsList');
    const notificationCount = document.getElementById('notificationCount');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const markReadBtn = document.getElementById('markReadBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const toastEl = document.getElementById('liveToast');
    const toastBody = document.getElementById('toastBody');

    let notifications = [];
    let selectedNotifications = new Set();

    const showToast = (message) => {
        toastBody.textContent = message;
        new bootstrap.Toast(toastEl, { delay: 3000 }).show();
    };

    // Modern confirm dialog using Bootstrap Modal
    function confirmDialog(message) {
        return new Promise((resolve) => {
            const modalEl = document.getElementById('confirmModal');
            const msgEl = document.getElementById('confirmMessage');
            const yesBtn = document.getElementById('confirmYesBtn');
            msgEl.textContent = message;
            const modal = new bootstrap.Modal(modalEl);

            const onYes = () => { cleanup(); modal.hide(); resolve(true); };
            const onHide = () => { cleanup(); resolve(false); };
            const cleanup = () => {
                yesBtn.removeEventListener('click', onYes);
                modalEl.removeEventListener('hidden.bs.modal', onHide);
            };

            yesBtn.addEventListener('click', onYes);
            modalEl.addEventListener('hidden.bs.modal', onHide, { once: true });
            modal.show();
        });
    }

    // Load notifications
    async function loadNotifications() {
        try {
            const response = await fetch('<?= base_url('api/notifications') ?>');
            const data = await response.json();
            
            if (data.success) {
                notifications = data.notifications;
                renderNotifications();
                updateCount();
            } else {
                showToast('Failed to load notifications');
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
            showToast('Error loading notifications');
        } finally {
            loadingState.style.display = 'none';
        }
    }

    // Render notifications
    function renderNotifications() {
        if (notifications.length === 0) {
            emptyState.style.display = 'block';
            notificationsList.style.display = 'none';
            return;
        }

        emptyState.style.display = 'none';
        notificationsList.style.display = 'block';
        
        notificationsList.innerHTML = notifications.map(notification => {
            const timeAgo = getTimeAgo(notification.created_at);
            const typeIcon = getTypeIcon(notification.type);
            const isUnread = notification.is_read == 0;
            
            return `
                <div class="notification-item ${isUnread ? 'unread' : ''}" data-id="${notification.id}">
                    <div class="notification-checkbox">
                        <input type="checkbox" class="form-check-input notification-select" value="${notification.id}">
                    </div>
                    <div class="notification-icon ${notification.type}">
                        <i class="${typeIcon}"></i>
                    </div>
                    <div class="notification-content notification-clickable" data-notification-id="${notification.id}">
                        <div class="notification-title">${escapeHtml(notification.title)}</div>
                        <div class="notification-message">${escapeHtml(truncateMessage(notification.message, 80))}</div>
                        <div class="notification-time">${timeAgo}</div>
                    </div>
                    ${isUnread ? '<div class="unread-indicator"></div>' : ''}
                </div>
            `;
        }).join('');

        // Add event listeners for checkboxes
        document.querySelectorAll('.notification-select').forEach(checkbox => {
            checkbox.addEventListener('change', handleCheckboxChange);
        });

        // Add click-to-show-detail functionality
        document.querySelectorAll('.notification-clickable').forEach(content => {
            content.addEventListener('click', function() {
                const notificationId = parseInt(this.dataset.notificationId);
                const notification = notifications.find(n => Number(n.id) === notificationId);
                
                if (notification) {
                    showNotificationDetail(notification);
                }
            });
        });
    }

    // Handle checkbox changes
    function handleCheckboxChange() {
        selectedNotifications.clear();
        document.querySelectorAll('.notification-select:checked').forEach(checkbox => {
            selectedNotifications.add(parseInt(checkbox.value));
        });
        updateActionButtons();
    }

    // Update action buttons state
    function updateActionButtons() {
        const hasSelection = selectedNotifications.size > 0;
        markReadBtn.disabled = !hasSelection;
        deleteBtn.disabled = !hasSelection;
        
        // Update select all button text
        const totalCheckboxes = document.querySelectorAll('.notification-select').length;
        const checkedCheckboxes = document.querySelectorAll('.notification-select:checked').length;
        
        if (checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0) {
            selectAllBtn.innerHTML = '<i class="fas fa-minus"></i> Deselect All';
        } else {
            selectAllBtn.innerHTML = '<i class="fas fa-check-double"></i> Select All';
        }
    }

    // Show notification detail modal
    function showNotificationDetail(notification) {
        const modal = document.getElementById('notificationModal');
        const titleEl = document.getElementById('notificationTitleDetail');
        const messageEl = document.getElementById('notificationMessageDetail');
        const timeEl = document.getElementById('notificationTimeDetail');
        const iconEl = document.getElementById('notificationIconDetail');
        const markReadBtn = document.getElementById('markAsReadFromModal');
        
        // Set content
        titleEl.textContent = notification.title || 'Notification';
        messageEl.textContent = notification.message || '';
        timeEl.textContent = getTimeAgo(notification.created_at);
        
        // Set icon
        const typeIcon = getTypeIcon(notification.type);
        iconEl.innerHTML = `<i class="${typeIcon}"></i>`;
        iconEl.className = `notification-icon-detail me-3 ${notification.type}`;
        
        // Show/hide mark as read button
        const isUnread = notification.is_read == 0;
        if (isUnread) {
            markReadBtn.style.display = 'inline-block';
            markReadBtn.onclick = async () => {
                await markSingleAsRead(notification.id);
                bootstrap.Modal.getInstance(modal).hide();
            };
        } else {
            markReadBtn.style.display = 'none';
        }
        
        // Show modal
        new bootstrap.Modal(modal).show();
    }

    // Update notification count
    function updateCount() {
        const unreadCount = notifications.filter(n => n.is_read == 0).length;
        notificationCount.textContent = unreadCount;
        // Hide the bubble when 0
        if (unreadCount > 0) {
            notificationCount.style.display = 'inline-flex';
        } else {
            notificationCount.style.display = 'none';
        }
        
        // Update navbar badge if exists
        const navbarBadge = document.querySelector('.notification-badge');
        if (navbarBadge) {
            navbarBadge.textContent = unreadCount;
            navbarBadge.style.display = unreadCount > 0 ? 'inline' : 'none';
        }
    }

    // Mark single notification as read (click functionality)
    async function markSingleAsRead(notificationId) {
        try {
            const formData = new FormData();
            formData.append('notification_ids', JSON.stringify([notificationId]));
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            const response = await fetch('<?= base_url('api/notifications/mark-read') ?>', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            if (data.success) {
                // Update local data
                const notification = notifications.find(n => Number(n.id) === notificationId);
                if (notification) {
                    notification.is_read = 1;
                }
                renderNotifications();
                updateCount();
                showToast('Notification marked as read');
            } else {
                showToast(data.message || 'Failed to mark as read');
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
            showToast('Error marking notification as read');
        }
    }

    // Select/Deselect all
    selectAllBtn.addEventListener('click', () => {
        const checkboxes = document.querySelectorAll('.notification-select');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        
        handleCheckboxChange();
    });

    // Mark as read
    markReadBtn.addEventListener('click', async () => {
        if (selectedNotifications.size === 0) return;
        
        try {
            const formData = new FormData();
            formData.append('notification_ids', JSON.stringify(Array.from(selectedNotifications)));
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            const response = await fetch('<?= base_url('api/notifications/mark-read') ?>', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            if (data.success) {
                showToast(data.message);
                // Update local data
                notifications.forEach(n => {
                    if (selectedNotifications.has(Number(n.id))) {
                        n.is_read = 1;
                    }
                });
                selectedNotifications.clear();
                renderNotifications();
                updateCount();
            } else {
                showToast(data.message || 'Failed to mark as read');
            }
        } catch (error) {
            console.error('Error marking as read:', error);
            showToast('Error marking notifications as read');
        }
    });

    // Delete notifications
    deleteBtn.addEventListener('click', async () => {
        if (selectedNotifications.size === 0) return;
        
    const ok = await confirmDialog(`Delete ${selectedNotifications.size} notification(s)? This action cannot be undone.`);
    if (!ok) return;
        
        try {
            const formData = new FormData();
            formData.append('notification_ids', JSON.stringify(Array.from(selectedNotifications)));
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            const response = await fetch('<?= base_url('api/notifications/delete') ?>', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            if (data.success) {
                showToast(data.message);
                // Remove from local data
                notifications = notifications.filter(n => 
                    !selectedNotifications.has(Number(n.id))
                );
                selectedNotifications.clear();
                renderNotifications();
                updateCount();
            } else {
                showToast(data.message || 'Failed to delete notifications');
            }
        } catch (error) {
            console.error('Error deleting notifications:', error);
            showToast('Error deleting notifications');
        }
    });

    // Utility functions
    function getTimeAgo(dateString) {
        // Parse the date string properly for local timezone
        const date = new Date(dateString.replace(/-/g, '/'));
        const now = new Date();
        
        // Calculate difference in seconds
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
        
        return date.toLocaleDateString();
    }

    function getTypeIcon(type) {
        const icons = {
            'profile_update': 'fas fa-user-edit',
            'security': 'fas fa-shield-alt',
            'warning': 'fas fa-exclamation-triangle',
            'error': 'fas fa-times-circle',
            'info': 'fas fa-info-circle',
            'success': 'fas fa-check-circle',
            'welcome': 'fas fa-heart'
        };
        return icons[type] || 'fas fa-bell';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function truncateMessage(message, maxLength = 80) {
        if (!message) return '';
        if (message.length <= maxLength) return message;
        return message.substring(0, maxLength).trim() + '...';
    }

    // Initial load
    loadNotifications();
});
</script>
</body>
</html>
