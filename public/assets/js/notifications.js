(function (window, document) {
  'use strict';

  const config = window.rioNotificationsConfig || {};
  const endpoints = (config.endpoints || {});
  const csrf = (config.csrf || {});

  document.addEventListener('DOMContentLoaded', function () {
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
      if (toastBody) toastBody.textContent = message;
      if (toastEl) new bootstrap.Toast(toastEl, { delay: 3000 }).show();
    };

    // Modern confirm dialog using Bootstrap Modal
    function confirmDialog(message) {
      return new Promise((resolve) => {
        const modalEl = document.getElementById('confirmModal');
        const msgEl = document.getElementById('confirmMessage');
        const yesBtn = document.getElementById('confirmYesBtn');
        if (msgEl) msgEl.textContent = message;
        const modal = new bootstrap.Modal(modalEl);

        const onYes = () => { cleanup(); modal.hide(); resolve(true); };
        const onHide = () => { cleanup(); resolve(false); };
        const cleanup = () => {
          if (yesBtn) yesBtn.removeEventListener('click', onYes);
          if (modalEl) modalEl.removeEventListener('hidden.bs.modal', onHide);
        };

        if (yesBtn) yesBtn.addEventListener('click', onYes);
        if (modalEl) modalEl.addEventListener('hidden.bs.modal', onHide, { once: true });
        modal.show();
      });
    }

    // Load notifications
    async function loadNotifications() {
      try {
        const response = await fetch(endpoints.list);
        const data = await response.json();

        if (data.success) {
          notifications = data.notifications;
          renderNotifications();
          updateCount();
        } else {
          showToast('Failed to load notifications');
        }
      } catch (error) {
        // eslint-disable-next-line no-console
        console.error('Error loading notifications:', error);
        showToast('Error loading notifications');
      } finally {
        if (loadingState) loadingState.style.display = 'none';
      }
    }

    // Render notifications
    function renderNotifications() {
      if (!notificationsList) return;

      if (!notifications || notifications.length === 0) {
        if (emptyState) emptyState.style.display = 'block';
        notificationsList.style.display = 'none';
        return;
      }

      if (emptyState) emptyState.style.display = 'none';
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
                        <div class="notification-message">${formatMessageWithLineBreaks(truncateMessage(notification.message, 120))}</div>
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
        content.addEventListener('click', function () {
          const notificationId = parseInt(this.dataset.notificationId, 10);
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
        selectedNotifications.add(parseInt(checkbox.value, 10));
      });
      updateActionButtons();
    }

    // Update action buttons state
    function updateActionButtons() {
      const hasSelection = selectedNotifications.size > 0;
      if (markReadBtn) markReadBtn.disabled = !hasSelection;
      if (deleteBtn) deleteBtn.disabled = !hasSelection;

      // Update select all button text
      const totalCheckboxes = document.querySelectorAll('.notification-select').length;
      const checkedCheckboxes = document.querySelectorAll('.notification-select:checked').length;

      if (checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0) {
        if (selectAllBtn) selectAllBtn.innerHTML = '<i class="fas fa-minus"></i> Deselect All';
      } else {
        if (selectAllBtn) selectAllBtn.innerHTML = '<i class="fas fa-check-double"></i> Select All';
      }
    }

    // Show notification detail modal
    function showNotificationDetail(notification) {
      const modal = document.getElementById('notificationModal');
      const titleEl = document.getElementById('notificationTitleDetail');
      const messageEl = document.getElementById('notificationMessageDetail');
      const timeEl = document.getElementById('notificationTimeDetail');
      const iconEl = document.getElementById('notificationIconDetail');
      const markReadBtnFromModal = document.getElementById('markAsReadFromModal');

      // Set content
      if (titleEl) titleEl.textContent = notification.title || 'Notification';
      if (messageEl) messageEl.innerHTML = formatMessageWithLineBreaks(notification.message || '');
      if (timeEl) timeEl.textContent = getTimeAgo(notification.created_at);

      // Set icon
      const typeIcon = getTypeIcon(notification.type);
      if (iconEl) iconEl.innerHTML = `<i class="${typeIcon}"></i>`;
      if (iconEl) iconEl.className = `notification-icon-detail me-3 ${notification.type}`;

      // Show/hide mark as read button
      const isUnread = notification.is_read == 0;
      if (isUnread) {
        if (markReadBtnFromModal) markReadBtnFromModal.style.display = 'inline-block';
        // ensure we pass a numeric id to avoid string/number mismatches
        if (markReadBtnFromModal) markReadBtnFromModal.onclick = async () => {
          await markSingleAsRead(Number(notification.id));
          bootstrap.Modal.getInstance(modal).hide();
        };
      } else {
        if (markReadBtnFromModal) markReadBtnFromModal.style.display = 'none';
      }

      // Show modal
      new bootstrap.Modal(modal).show();
    }

    // Update notification count
    function updateCount() {
      const unreadCount = notifications.filter(n => n.is_read == 0).length;
      if (notificationCount) notificationCount.textContent = unreadCount;
      // Hide the bubble when 0
      if (notificationCount) {
        if (unreadCount > 0) {
          notificationCount.style.display = 'inline-flex';
        } else {
          notificationCount.style.display = 'none';
        }
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
      // normalize id to number to avoid strict equality mismatches
      const id = Number(notificationId);
      try {
        const formData = new FormData();
        formData.append('notification_ids', JSON.stringify([id]));
        if (csrf.tokenName) formData.append(csrf.tokenName, csrf.hash);

        const response = await fetch(endpoints.markRead, {
          method: 'POST',
          body: formData
        });

        const data = await response.json();
        if (data.success) {
          // Update local data
          const notification = notifications.find(n => Number(n.id) === id);
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
        // eslint-disable-next-line no-console
        console.error('Error marking notification as read:', error);
        showToast('Error marking notification as read');
      }
    }

    // Select/Deselect all
    if (selectAllBtn) selectAllBtn.addEventListener('click', () => {
      const checkboxes = document.querySelectorAll('.notification-select');
      const allChecked = Array.from(checkboxes).every(cb => cb.checked);

      checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
      });

      handleCheckboxChange();
    });

    // Mark as read
    if (markReadBtn) markReadBtn.addEventListener('click', async () => {
      if (selectedNotifications.size === 0) return;

      try {
        const formData = new FormData();
        formData.append('notification_ids', JSON.stringify(Array.from(selectedNotifications)));
        if (csrf.tokenName) formData.append(csrf.tokenName, csrf.hash);

        const response = await fetch(endpoints.markRead, {
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
        // eslint-disable-next-line no-console
        console.error('Error marking as read:', error);
        showToast('Error marking notifications as read');
      }
    });

    // Delete notifications
    if (deleteBtn) deleteBtn.addEventListener('click', async () => {
      if (selectedNotifications.size === 0) return;

      const ok = await confirmDialog(`Delete ${selectedNotifications.size} notification(s)? This action cannot be undone.`);
      if (!ok) return;

      try {
        const formData = new FormData();
        formData.append('notification_ids', JSON.stringify(Array.from(selectedNotifications)));
        if (csrf.tokenName) formData.append(csrf.tokenName, csrf.hash);

        const response = await fetch(endpoints.delete, {
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
        // eslint-disable-next-line no-console
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

    function formatMessageWithLineBreaks(message) {
      if (!message) return '';
      // First escape HTML to prevent XSS, then convert newlines to <br>
      return escapeHtml(message).replace(/\n/g, '<br>');
    }

    function truncateMessage(message, maxLength = 80) {
      if (!message) return '';
      if (message.length <= maxLength) return message;
      return message.substring(0, maxLength).trim() + '...';
    }

    // Initial load
    loadNotifications();
  });
}(window, document));
