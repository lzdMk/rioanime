// Global variables
let allUsers = [];
let selectedUsers = [];

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    // Add CSS for user selection
    addUserSelectionStyles();
    
    // Initialize character counters
    initializeCharacterCounters();
    
    // Initialize target type selection handler
    initializeTargetTypeHandler();
    
    // Initialize user search functionality
    initializeUserSearch();
    
    // Initialize form submission handler
    initializeFormHandler();
}

function addUserSelectionStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .user-item {
            transition: background-color 0.2s ease;
        }
        .user-item:hover {
            background-color: #f8f9fa;
        }
        .user-item:last-child {
            border-bottom: none !important;
        }
        .user-checkbox:checked + .form-check-label {
            background-color: #e3f2fd;
            border-radius: 6px;
            padding: 8px;
        }
        .user-info {
            flex-grow: 1;
        }
        #users_container {
            border: 1px solid #dee2e6;
        }
        .user-avatar {
            min-width: 40px;
        }
    `;
    document.head.appendChild(style);
}

function initializeCharacterCounters() {
    const titleInput = document.getElementById('notification_title');
    const messageInput = document.getElementById('notification_message');
    const titleCount = document.getElementById('title_count');
    const messageCount = document.getElementById('message_count');
    
    if (titleInput && titleCount) {
        titleInput.addEventListener('input', function() {
            titleCount.textContent = this.value.length;
        });
    }
    
    if (messageInput && messageCount) {
        messageInput.addEventListener('input', function() {
            messageCount.textContent = this.value.length;
        });
    }
}

function initializeTargetTypeHandler() {
    const targetType = document.getElementById('target_type');
    const specificUserSection = document.getElementById('specific_user_section');
    const groupSection = document.getElementById('group_section');
    const usersContainer = document.getElementById('users_container');
    
    if (targetType) {
        targetType.addEventListener('change', function() {
            // Hide all sections first
            if (specificUserSection) specificUserSection.style.display = 'none';
            if (groupSection) groupSection.style.display = 'none';
            
            if (this.value === 'specific') {
                if (specificUserSection) specificUserSection.style.display = 'block';
                // Reset users container
                if (usersContainer) {
                    usersContainer.innerHTML = `
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <div>Click "Load" to fetch users</div>
                        </div>
                    `;
                }
            } else if (this.value === 'group') {
                if (groupSection) groupSection.style.display = 'block';
            }
        });
    }
}

function initializeUserSearch() {
    const userSearch = document.getElementById('user_search');
    
    if (userSearch) {
        userSearch.addEventListener('input', function() {
            filterUsers(this.value);
        });
    }
}

function initializeFormHandler() {
    const form = document.getElementById('notificationForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmission(this);
        });
    }
}

// Load users function
function loadUsers() {
    const loadBtn = document.getElementById('load_users_btn');
    const usersContainer = document.getElementById('users_container');
    
    if (!loadBtn || !usersContainer) return;
    
    const originalText = loadBtn.innerHTML;
    loadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
    loadBtn.disabled = true;
    
    usersContainer.innerHTML = `
        <div class="text-center text-muted py-3">
            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
            <div>Loading users...</div>
        </div>
    `;
    
    // Get the base URL for the AJAX call
    const baseUrl = document.querySelector('meta[name="base-url"]')?.content || 
                   window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/');
    
    // Make AJAX call to load users
    fetch(`${baseUrl}/admin/getUsers`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            allUsers = data.users;
            displayUsers(allUsers);
        } else {
            usersContainer.innerHTML = `
                <div class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <div>Error: ${data.message || 'Failed to load users'}</div>
                </div>
            `;
            showAlert('error', data.message || 'Failed to load users');
        }
    })
    .catch(error => {
        console.error('Error loading users:', error);
        usersContainer.innerHTML = `
            <div class="text-center text-danger py-3">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <div>Network error while loading users</div>
            </div>
        `;
        showAlert('error', 'Network error while loading users');
    })
    .finally(() => {
        loadBtn.innerHTML = originalText;
        loadBtn.disabled = false;
    });
}

// Display users function
function displayUsers(users) {
    const usersContainer = document.getElementById('users_container');
    
    if (!usersContainer) return;
    
    if (users.length === 0) {
        usersContainer.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-user-slash fa-2x mb-2"></i>
                <div>No users found</div>
            </div>
        `;
        return;
    }
    
    let html = '';
    users.forEach(user => {
        const isSelected = selectedUsers.some(selected => selected.id === user.id);
        html += `
            <div class="user-item border-bottom py-2" data-user-id="${user.id}">
                <div class="form-check">
                    <input class="form-check-input user-checkbox" type="checkbox" 
                           id="user_${user.id}" value="${user.id}" 
                           ${isSelected ? 'checked' : ''}
                           onchange="toggleUserSelection(${user.id}, '${user.username}', '${user.email}')">
                    <label class="form-check-label w-100" for="user_${user.id}">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-3">
                                <i class="fas fa-user-circle fa-2x text-secondary"></i>
                            </div>
                            <div class="user-info">
                                <div class="fw-bold">${user.username}</div>
                                <small class="text-muted">${user.email}</small>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        `;
    });
    
    usersContainer.innerHTML = html;
    updateSelectedUsersSummary();
}

// Filter users function
function filterUsers(searchTerm) {
    if (!searchTerm.trim()) {
        displayUsers(allUsers);
        return;
    }
    
    const filtered = allUsers.filter(user => 
        user.username.toLowerCase().includes(searchTerm.toLowerCase()) ||
        user.email.toLowerCase().includes(searchTerm.toLowerCase())
    );
    
    displayUsers(filtered);
}

// Toggle user selection
function toggleUserSelection(userId, username, email) {
    const userIndex = selectedUsers.findIndex(user => user.id === userId);
    
    if (userIndex > -1) {
        // Remove user from selection
        selectedUsers.splice(userIndex, 1);
    } else {
        // Add user to selection
        selectedUsers.push({
            id: userId,
            username: username,
            email: email
        });
    }
    
    updateSelectedUsersSummary();
    updateHiddenInput();
}

// Update selected users summary
function updateSelectedUsersSummary() {
    const summaryDiv = document.getElementById('selected_users_summary');
    const countSpan = document.getElementById('selected_count');
    
    if (summaryDiv && countSpan) {
        if (selectedUsers.length > 0) {
            countSpan.textContent = selectedUsers.length;
            summaryDiv.style.display = 'block';
        } else {
            summaryDiv.style.display = 'none';
        }
    }
}

// Update hidden input with selected user IDs
function updateHiddenInput() {
    const hiddenInput = document.getElementById('selected_user_ids');
    
    if (hiddenInput) {
        const userIds = selectedUsers.map(user => user.id);
        hiddenInput.value = userIds.join(',');
    }
}

// Form submission handler
function handleFormSubmission(form) {
    // Validate form
    if (!validateForm()) {
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    submitBtn.disabled = true;
    
    // Prepare form data
    const formData = new FormData(form);
    
    // Get the base URL for the AJAX call
    const baseUrl = document.querySelector('meta[name="base-url"]')?.content || 
                   window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/');
    
    // Make AJAX call to send notification
    fetch(`${baseUrl}/admin/send-notification`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', data.message);
            
            // Reset form
            resetForm(form);
        } else {
            // Show error message
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'Network error occurred while sending notification');
    })
    .finally(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Reset form function
function resetForm(form) {
    const titleCount = document.getElementById('title_count');
    const messageCount = document.getElementById('message_count');
    const specificUserSection = document.getElementById('specific_user_section');
    const groupSection = document.getElementById('group_section');
    const usersContainer = document.getElementById('users_container');
    const userSearch = document.getElementById('user_search');
    
    form.reset();
    
    if (titleCount) titleCount.textContent = '0';
    if (messageCount) messageCount.textContent = '0';
    if (specificUserSection) specificUserSection.style.display = 'none';
    if (groupSection) groupSection.style.display = 'none';
    
    // Reset user selection
    selectedUsers = [];
    updateSelectedUsersSummary();
    updateHiddenInput();
    
    // Reset users container
    if (usersContainer) {
        usersContainer.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-users fa-2x mb-2"></i>
                <div>Click "Load" to fetch users</div>
            </div>
        `;
    }
    
    // Clear search
    if (userSearch) {
        userSearch.value = '';
    }
}

// Validate form function
function validateForm() {
    const targetType = document.getElementById('target_type')?.value;
    const title = document.getElementById('notification_title')?.value.trim();
    const message = document.getElementById('notification_message')?.value.trim();
    const notificationType = document.getElementById('notification_type')?.value;
    const priority = document.getElementById('notification_priority')?.value;
    const userGroup = document.getElementById('user_group')?.value;
    
    if (!targetType) {
        showAlert('error', 'Please select a target for the notification.');
        return false;
    }
    
    if (targetType === 'specific' && selectedUsers.length === 0) {
        showAlert('error', 'Please select at least one user.');
        return false;
    }
    
    if (targetType === 'group' && !userGroup) {
        showAlert('error', 'Please select a user group.');
        return false;
    }
    
    if (!title) {
        showAlert('error', 'Please enter a notification title.');
        return false;
    }
    
    if (!message) {
        showAlert('error', 'Please enter a notification message.');
        return false;
    }
    
    if (!notificationType) {
        showAlert('error', 'Please select a notification type.');
        return false;
    }
    
    if (!priority) {
        showAlert('error', 'Please select a notification priority.');
        return false;
    }
    
    return true;
}

// Clear form function
function clearForm() {
    if (confirm('Are you sure you want to clear the form?')) {
        const form = document.getElementById('notificationForm');
        const usersContainer = document.getElementById('users_container');
        const userSearch = document.getElementById('user_search');
        
        if (form) {
            resetForm(form);
        }
    }
}

// Preview notification function
function previewNotification() {
    const title = document.getElementById('notification_title')?.value.trim();
    const message = document.getElementById('notification_message')?.value.trim();
    const type = document.getElementById('notification_type')?.value;
    const priority = document.getElementById('notification_priority')?.value;
    
    if (!title || !message) {
        showAlert('warning', 'Please enter both title and message to preview.');
        return;
    }
    
    // Create preview modal
    const previewHtml = `
        <div class="modal fade" id="previewModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Notification Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-${type === 'error' ? 'danger' : type} alert-dismissible">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-${getTypeIcon(type)} me-2"></i>
                                <div>
                                    <strong>${title}</strong>
                                    <div class="mt-1">${message}</div>
                                    <small class="text-muted">Priority: ${priority.charAt(0).toUpperCase() + priority.slice(1)}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing preview modal
    const existingModal = document.getElementById('previewModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add new preview modal
    document.body.insertAdjacentHTML('beforeend', previewHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

// Get type icon function
function getTypeIcon(type) {
    const icons = {
        'info': 'info-circle',
        'success': 'check-circle',
        'warning': 'exclamation-triangle',
        'error': 'exclamation-circle',
        'announcement': 'bullhorn'
    };
    return icons[type] || 'bell';
}

// Show alert function
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert-temp');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertHtml = `
        <div class="alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show alert-temp" role="alert">
            <i class="fas fa-${getTypeIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert alert at the top of the form
    const form = document.getElementById('notificationForm');
    if (form) {
        form.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            const tempAlert = document.querySelector('.alert-temp');
            if (tempAlert) {
                tempAlert.remove();
            }
        }, 5000);
    }
}
