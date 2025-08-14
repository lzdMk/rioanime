// Dashboard Notification Management JavaScript
// Centralized user management with multiple selection capability

document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = window.location.origin + '/rioanime/';
    let allUsers = [];
    let filteredUsers = [];
    let selectedUsers = [];

    initializeDashboard();

    function initializeDashboard() {
        // Initialize character counters
        initializeCharacterCounters();
        
        // Initialize target type selection handler
        initializeTargetTypeHandler();
        
        // Initialize user search functionality
        initializeUserSearch();
        
        // Initialize schedule functionality
        initializeScheduleHandler();
        
        // Initialize form submission handler
        initializeFormHandler();
        
        // Load users when page loads
        loadUsers();
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
        
        if (targetType) {
            targetType.addEventListener('change', function() {
                // Hide all sections first
                if (specificUserSection) specificUserSection.style.display = 'none';
                if (groupSection) groupSection.style.display = 'none';
                
                if (this.value === 'specific') {
                    if (specificUserSection) {
                        specificUserSection.style.display = 'block';
                        // Show users if already loaded
                        if (allUsers.length > 0) {
                            displayUsers(allUsers);
                        }
                    }
                } else if (this.value === 'group') {
                    if (groupSection) groupSection.style.display = 'block';
                }
                
                // Clear user selection when changing target type
                clearUserSelection();
            });
        }
    }

    function initializeUserSearch() {
        const userSearchInput = document.getElementById('user_search_input');
        
        if (userSearchInput) {
            let searchTimeout;
            userSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchTerm = this.value.toLowerCase().trim();
                    filterUsers(searchTerm);
                }, 300);
            });
        }
    }

    function initializeScheduleHandler() {
        const sendImmediately = document.getElementById('send_immediately');
        const scheduleSend = document.getElementById('schedule_send');
        const scheduleDetails = document.getElementById('schedule_details');
        const scheduleDate = document.getElementById('schedule_date');
        const scheduleTime = document.getElementById('schedule_time');
        
        if (sendImmediately && scheduleSend && scheduleDetails) {
            // Handle immediate send checkbox
            sendImmediately.addEventListener('change', function() {
                if (this.checked) {
                    scheduleSend.checked = false;
                    scheduleDetails.style.display = 'none';
                    clearScheduleValidation();
                }
            });
            
            // Handle schedule send checkbox
            scheduleSend.addEventListener('change', function() {
                if (this.checked) {
                    sendImmediately.checked = false;
                    scheduleDetails.style.display = 'block';
                    setDefaultScheduleDateTime();
                } else {
                    scheduleDetails.style.display = 'none';
                    clearScheduleValidation();
                }
            });
            
            // Validate schedule date/time
            if (scheduleDate) {
                scheduleDate.addEventListener('change', validateScheduleDateTime);
            }
            if (scheduleTime) {
                scheduleTime.addEventListener('change', validateScheduleDateTime);
            }
        }
    }

    function setDefaultScheduleDateTime() {
        const scheduleDate = document.getElementById('schedule_date');
        const scheduleTime = document.getElementById('schedule_time');
        
        if (scheduleDate && !scheduleDate.value) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            scheduleDate.value = tomorrow.toISOString().split('T')[0];
        }
        
        if (scheduleTime && !scheduleTime.value) {
            const now = new Date();
            now.setHours(now.getHours() + 1);
            scheduleTime.value = now.toTimeString().slice(0, 5);
        }
    }

    function validateScheduleDateTime() {
        const scheduleDate = document.getElementById('schedule_date');
        const scheduleTime = document.getElementById('schedule_time');
        const scheduleSend = document.getElementById('schedule_send');
        
        if (!scheduleSend || !scheduleSend.checked) return true;
        
        if (!scheduleDate?.value || !scheduleTime?.value) {
            showScheduleError('Please select both date and time for scheduled delivery.');
            return false;
        }
        
        const scheduledDateTime = new Date(`${scheduleDate.value}T${scheduleTime.value}`);
        const now = new Date();
        
        if (scheduledDateTime <= now) {
            showScheduleError('Scheduled time must be in the future.');
            return false;
        }
        
        clearScheduleValidation();
        return true;
    }

    function showScheduleError(message) {
        clearScheduleValidation();
        const scheduleDetails = document.getElementById('schedule_details');
        if (scheduleDetails) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-custom alert-custom-error mt-2';
            errorDiv.id = 'schedule_error';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${message}`;
            scheduleDetails.appendChild(errorDiv);
        }
    }

    function clearScheduleValidation() {
        const existingError = document.getElementById('schedule_error');
        if (existingError) {
            existingError.remove();
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

    // Load users using the centralized getAccounts endpoint
    function loadUsers() {
        console.log('Loading users...');
        
        const usersContainer = document.getElementById('users_list_container');
        if (usersContainer) {
            usersContainer.innerHTML = `
                <div class="text-center py-4 admin-text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <div>Loading users...</div>
                </div>
            `;
        }
        
        // Use the same endpoint as accounts page but with larger per_page to get all users
        fetch(`${baseUrl}admin/getAccounts?per_page=1000`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Users loaded:', data);
            if (data.success && data.accounts) {
                allUsers = data.accounts;
                filteredUsers = [...allUsers];
                
                // Display users if specific user section is visible
                const specificUserSection = document.getElementById('specific_user_section');
                if (specificUserSection && specificUserSection.style.display !== 'none') {
                    displayUsers(allUsers);
                } else {
                    // Show ready state
                    if (usersContainer) {
                        usersContainer.innerHTML = `
                            <div class="text-center py-4 admin-text-muted">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <div>Ready to select users</div>
                                <small>Select "Specific Users" to choose recipients</small>
                            </div>
                        `;
                    }
                }
                
                console.log(`Loaded ${allUsers.length} users`);
            } else {
                console.error('Failed to load users:', data.message || 'Unknown error');
                showAlert('error', data.message || 'Failed to load users');
                if (usersContainer) {
                    usersContainer.innerHTML = `
                        <div class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <div>Failed to load users</div>
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            showAlert('error', 'Network error while loading users');
            if (usersContainer) {
                usersContainer.innerHTML = `
                    <div class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <div>Network error</div>
                    </div>
                `;
            }
        });
    }

    // Filter users based on search term
    function filterUsers(searchTerm) {
        if (!searchTerm) {
            filteredUsers = [...allUsers];
        } else {
            filteredUsers = allUsers.filter(user => 
                user.username.toLowerCase().includes(searchTerm) ||
                (user.display_name && user.display_name.toLowerCase().includes(searchTerm))
            );
        }
        
        displayUsers(filteredUsers);
    }

    // Display users in the list
    function displayUsers(users) {
        const usersContainer = document.getElementById('users_list_container');
        
        if (!usersContainer) return;
        
        if (users.length === 0) {
            usersContainer.innerHTML = `
                <div class="no-users-message">
                    <i class="fas fa-user-slash"></i>
                    <div>No users found</div>
                    <small>Try adjusting your search</small>
                </div>
            `;
            return;
        }
        
        let html = '';
        users.forEach(user => {
            const isSelected = selectedUsers.some(selected => selected.id === user.id);
            const userType = user.type || 'viewer';
            const typeClass = userType === 'admin' ? 'user-type-admin' : 'user-type-viewer';
            
            html += `
                <div class="user-card ${isSelected ? 'selected' : ''}" data-user-id="${user.id}">
                    <div class="d-flex align-items-center">
                        <input class="form-check-input me-3" type="checkbox" 
                               id="user_${user.id}" value="${user.id}" 
                               ${isSelected ? 'checked' : ''}
                               onchange="toggleUserSelection(${user.id}, '${user.username}', '${user.display_name || user.username}')">
                        
                        <div class="user-avatar">
                            ${user.username.charAt(0).toUpperCase()}
                        </div>
                        
                        <div class="flex-grow-1">
                            <div class="fw-bold text-white mb-1">${user.username}</div>
                            <div class="small text-gray-300">${user.display_name || user.username}</div>
                        </div>
                        
                        <div class="d-flex flex-column align-items-end">
                            <span class="user-type-badge ${typeClass} mb-2">${userType}</span>
                            ${isSelected ? '<i class="fas fa-check-circle text-success"></i>' : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        usersContainer.innerHTML = html;
        
        // Add click handlers for user cards
        const userCards = usersContainer.querySelectorAll('.user-card');
        userCards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.type !== 'checkbox') {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.click();
                    }
                }
            });
        });
        
        updateSelectedUsersSummary();
    }

    // Toggle user selection
    window.toggleUserSelection = function(userId, username, displayName) {
        const userIndex = selectedUsers.findIndex(user => user.id === userId);
        
        if (userIndex > -1) {
            // Remove user from selection
            selectedUsers.splice(userIndex, 1);
        } else {
            // Add user to selection
            selectedUsers.push({
                id: userId,
                username: username,
                display_name: displayName
            });
        }
        
        updateSelectedUsersSummary();
        updateHiddenInput();
        
        // Update the visual state
        const targetType = document.getElementById('target_type');
        if (targetType && targetType.value === 'specific') {
            displayUsers(filteredUsers);
        }
    };

    // Update selected users summary
    function updateSelectedUsersSummary() {
        const countSpan = document.getElementById('selected_count');
        const clearBtn = document.getElementById('clear_selection_btn');
        
        if (countSpan) {
            countSpan.textContent = selectedUsers.length;
        }
        
        if (clearBtn) {
            clearBtn.style.display = selectedUsers.length > 0 ? 'inline-block' : 'none';
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

    // Clear user selection
    window.clearUserSelection = function() {
        selectedUsers = [];
        updateSelectedUsersSummary();
        updateHiddenInput();
        
        // Uncheck all checkboxes
        const checkboxes = document.querySelectorAll('#users_list_container input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Refresh display
        const targetType = document.getElementById('target_type');
        if (targetType && targetType.value === 'specific') {
            displayUsers(filteredUsers);
        }
        
        // Clear search
        const userSearchInput = document.getElementById('user_search_input');
        if (userSearchInput) {
            userSearchInput.value = '';
            filterUsers(''); // Reset filter
        }
    };

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
        
        // Make AJAX call to send notification
        fetch(`${baseUrl}admin/send-notification`, {
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
            console.error('Form submission error:', error);
            showAlert('error', 'Network error occurred while sending notification');
        })
        .finally(() => {
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    // Validate form function
    function validateForm() {
        const targetType = document.getElementById('target_type')?.value;
        const title = document.getElementById('notification_title')?.value.trim();
        const message = document.getElementById('notification_message')?.value.trim();
        const notificationType = document.getElementById('notification_type')?.value;
        const priority = document.getElementById('notification_priority')?.value;
        const userGroup = document.getElementById('user_group')?.value;
        const sendImmediately = document.getElementById('send_immediately')?.checked;
        const scheduleSend = document.getElementById('schedule_send')?.checked;
        
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
        
        // Validate send options
        if (!sendImmediately && !scheduleSend) {
            showAlert('error', 'Please select when to send the notification.');
            return false;
        }
        
        // Validate schedule if selected
        if (scheduleSend && !validateScheduleDateTime()) {
            return false;
        }
        
        return true;
    }

    // Reset form function
    function resetForm(form) {
        const titleCount = document.getElementById('title_count');
        const messageCount = document.getElementById('message_count');
        const specificUserSection = document.getElementById('specific_user_section');
        const groupSection = document.getElementById('group_section');
        const userSearchInput = document.getElementById('user_search_input');
        const scheduleDetails = document.getElementById('schedule_details');
        const sendImmediately = document.getElementById('send_immediately');
        const scheduleSend = document.getElementById('schedule_send');
        
        form.reset();
        
        if (titleCount) titleCount.textContent = '0';
        if (messageCount) messageCount.textContent = '0';
        if (specificUserSection) specificUserSection.style.display = 'none';
        if (groupSection) groupSection.style.display = 'none';
        if (scheduleDetails) scheduleDetails.style.display = 'none';
        
        // Reset schedule options
        if (sendImmediately) sendImmediately.checked = true;
        if (scheduleSend) scheduleSend.checked = false;
        
        // Clear user selection
        clearUserSelection();
        
        // Clear search
        if (userSearchInput) {
            userSearchInput.value = '';
        }
        
        // Clear schedule validation
        clearScheduleValidation();
    }

    // Clear form function (global)
    window.clearForm = function() {
        if (confirm('Are you sure you want to clear the form?')) {
            const form = document.getElementById('notificationForm');
            if (form) {
                resetForm(form);
            }
        }
    };

    // Preview notification function (global)
    window.previewNotification = function() {
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
                    <div class="modal-content admin-modal">
                        <div class="modal-header admin-modal-header">
                            <h5 class="modal-title admin-text-primary">Notification Preview</h5>
                            <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body admin-modal-body">
                            <div class="alert alert-${type === 'error' ? 'danger' : type} alert-dismissible">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-${getTypeIcon(type)} me-2"></i>
                                    <div>
                                        <strong>${title}</strong>
                                        <div class="mt-1">${message}</div>
                                        <small class="admin-text-muted">Priority: ${priority.charAt(0).toUpperCase() + priority.slice(1)}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer admin-modal-footer">
                            <button type="button" class="btn admin-btn-secondary" data-bs-dismiss="modal">Close</button>
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
    };

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
});

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
