/**
 * Admin Accounts Management JavaScript with Custom Pagination
 */
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = window.location.origin + '/rioanime/';
    let accountsPagination = null;
    let currentPage = 1;
    let currentSearch = '';
    let currentTypeFilter = '';
    let currentPerPage = 8;
    
    // DOM elements
    const searchInput = document.getElementById('accountSearch');
    const typeFilter = document.getElementById('typeFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const customPerPageInput = document.getElementById('customPerPageInput');
    const refreshBtn = document.getElementById('refreshBtn');
    const tableContainer = document.getElementById('accountsTableContainer');
    const paginationContainer = document.getElementById('accountsPagination');
    
    // Initialize
    loadAccounts();
    
    // Search functionality
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = this.value;
                currentPage = 1;
                loadAccounts();
            }, 500);
        });
    }
    
    // Filter functionality
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            currentTypeFilter = this.value;
            currentPage = 1;
            loadAccounts();
        });
    }
    
    // Per-page functionality
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const value = this.value;
            if (value === 'custom') {
                customPerPageInput.style.display = 'block';
                customPerPageInput.focus();
            } else {
                customPerPageInput.style.display = 'none';
                currentPerPage = parseInt(value);
                currentPage = 1;
                loadAccounts();
            }
        });
    }
    
    if (customPerPageInput) {
        customPerPageInput.addEventListener('blur', function() {
            const value = parseInt(this.value);
            if (value && value >= 8) {
                currentPerPage = value;
                currentPage = 1;
                loadAccounts();
            } else {
                this.value = Math.max(8, currentPerPage);
            }
        });
        
        customPerPageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.blur();
            }
        });
    }
    
    // Refresh button
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            loadAccounts();
        });
    }
    
    // Load accounts function
    function loadAccounts() {
        const params = new URLSearchParams({
            page: currentPage,
            per_page: currentPerPage
        });
        
        if (currentSearch) {
            params.append('search', currentSearch);
        }
        
        if (currentTypeFilter) {
            params.append('type', currentTypeFilter);
        }
        
        fetch(`${baseUrl}admin/getAccounts?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderAccountsTable(data.accounts);
                setupPagination(data.pagination);
            } else {
                showToast('Failed to load accounts', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error loading accounts', 'error');
        });
    }
    
    // Render accounts table
    function renderAccountsTable(accounts) {
        if (accounts.length === 0) {
            tableContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x admin-text-muted mb-3"></i>
                    <h5 class="admin-text-muted">No accounts found</h5>
                    <p class="admin-text-muted">Try adjusting your search criteria</p>
                </div>
            `;
            return;
        }
        
        const tableHtml = `
            <div class="table-responsive">
                <table class="table admin-table table-hover">
                    <thead class="admin-table-header">
                        <tr>
                            <th>Avatar</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="admin-table-body">
                        ${accounts.map(account => `
                            <tr data-account-id="${account.id}">
                                <td class="text-center">
                                    ${account.user_profile 
                                        ? `<img src="${escapeHtml(account.user_profile)}" alt="Avatar" class="admin-avatar">`
                                        : `<div class="admin-avatar-letter">${escapeHtml(account.username.charAt(0).toUpperCase())}</div>`
                                    }
                                </td>
                                <td class="admin-text-primary fw-bold">${escapeHtml(account.username)}</td>
                                <td class="admin-text-muted">${escapeHtml(account.email)}</td>
                                <td>
                                    <span class="badge admin-badge-${
                                        account.type === 'admin' ? 'danger' : 
                                        account.type === 'moderator' ? 'warning' : 'primary'
                                    }">
                                        ${escapeHtml(account.type.charAt(0).toUpperCase() + account.type.slice(1))}
                                    </span>
                                </td>
                                <td class="admin-text-muted">${formatDate(account.created_at)}</td>
                                <td>
                                    <div class="btn-group admin-action-buttons" role="group">
                                        <button class="btn admin-btn-info btn-sm view-account" data-id="${account.id}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn admin-btn-warning btn-sm edit-account" data-id="${account.id}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn admin-btn-danger btn-sm delete-account" data-id="${account.id}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
        
        tableContainer.innerHTML = tableHtml;
        
        // Attach event listeners to new buttons
        attachTableEventListeners();
    }
    
    // Setup pagination
    function setupPagination(paginationData) {
        if (accountsPagination) {
            accountsPagination.destroy();
        }
        
        accountsPagination = new AdminPagination({
            container: paginationContainer,
            currentPage: paginationData.current_page,
            totalPages: paginationData.total_pages,
            totalItems: paginationData.total_items,
            perPage: paginationData.per_page,
            onPageChange: (page) => {
                currentPage = page;
                loadAccounts();
            }
        });
    }
    
    // Attach event listeners to table buttons
    function attachTableEventListeners() {
        // View account buttons
        document.querySelectorAll('.view-account').forEach(btn => {
            btn.addEventListener('click', function() {
                const accountId = this.getAttribute('data-id');
                viewAccount(accountId);
            });
        });
        
        // Edit account buttons
        document.querySelectorAll('.edit-account').forEach(btn => {
            btn.addEventListener('click', function() {
                const accountId = this.getAttribute('data-id');
                editAccount(accountId);
            });
        });
        
        // Delete account buttons
        document.querySelectorAll('.delete-account').forEach(btn => {
            btn.addEventListener('click', function() {
                const accountId = this.getAttribute('data-id');
                deleteAccount(accountId);
            });
        });
    }
    
    // Helper functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }
    
    function showToast(message, type = 'success') {
        const toastBody = document.getElementById('toastBody');
        const toast = document.getElementById('liveToast');
        
        if (toastBody && toast) {
            toastBody.textContent = message;
            toast.className = `toast align-items-center text-bg-${type === 'success' ? 'success' : 'danger'} border-0`;
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }
    }

    // CRUD Operations
    function viewAccount(accountId) {
        fetch(`${baseUrl}admin/getAccount/${accountId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateViewModal(data.account, data.watched_anime, data.followed_anime);
                const modal = new bootstrap.Modal(document.getElementById('viewAccountModal'));
                modal.show();
            } else {
                showToast(data.message || 'Failed to load account details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error loading account details', 'error');
        });
    }
    
    function editAccount(accountId) {
        fetch(`${baseUrl}admin/getAccount/${accountId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditModal(data.account);
                const modal = new bootstrap.Modal(document.getElementById('editAccountModal'));
                modal.show();
            } else {
                showToast(data.message || 'Failed to load account details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error loading account details', 'error');
        });
    }
    
    function deleteAccount(accountId) {
        // Get account username for confirmation
        const accountRow = document.querySelector(`tr[data-account-id="${accountId}"]`);
        const username = accountRow ? accountRow.querySelector('.admin-text-primary').textContent : 'this account';
        
        // Set the username in the modal
        const usernameElement = document.getElementById('deleteAccountUsername');
        if (usernameElement) usernameElement.textContent = username;
        
        // Show the modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
        deleteModal.show();
        
        // Set up the confirm button
        const confirmBtn = document.getElementById('confirmDeleteAccount');
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        newConfirmBtn.addEventListener('click', function() {
            fetch(`${baseUrl}admin/deleteAccount/${accountId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                deleteModal.hide();
                if (data.success) {
                    showToast(data.message);
                    loadAccounts();
                } else {
                    showToast(data.message || 'Failed to delete account', 'error');
                }
            })
            .catch(error => {
                deleteModal.hide();
                console.error('Error:', error);
                showToast('Error deleting account', 'error');
            });
        });
    }
    
    function populateViewModal(account, watchedAnime, followedAnime) {
        const modal = document.getElementById('viewAccountModal');
        if (modal) {
            // Basic account info
            const username = modal.querySelector('#viewUsername');
            const type = modal.querySelector('#viewType');
            const email = modal.querySelector('#viewEmail');
            const displayName = modal.querySelector('#viewDisplayName');
            const created = modal.querySelector('#viewCreated');
            const avatar = modal.querySelector('#viewAvatar');
            
            // Anime counts
            const watchedCount = modal.querySelector('#viewWatchedCount');
            const followedCount = modal.querySelector('#viewFollowedCount');
            
            // Anime lists
            const watchedAnimeList = modal.querySelector('#watchedAnimeList');
            const followedAnimeList = modal.querySelector('#followedAnimeList');
            
            // Populate basic info
            if (username) username.textContent = account.username;
            if (email) email.textContent = account.email;
            if (displayName) displayName.textContent = account.display_name || 'N/A';
            if (created) created.textContent = formatDate(account.created_at);
            
            // Populate type badge
            if (type) {
                type.textContent = account.type.charAt(0).toUpperCase() + account.type.slice(1);
                type.className = `badge admin-badge-${
                    account.type === 'admin' ? 'danger' : 
                    account.type === 'moderator' ? 'warning' : 'primary'
                }`;
            }
            
            // Populate avatar
            if (avatar) {
                if (account.user_profile && account.user_profile.trim() !== '') {
                    avatar.innerHTML = `<img src="${escapeHtml(account.user_profile)}" alt="Avatar" class="admin-avatar-large">`;
                } else {
                    avatar.innerHTML = `<div class="admin-avatar-letter-large">${escapeHtml(account.username.charAt(0).toUpperCase())}</div>`;
                }
            }
            
            // Populate counts
            if (watchedCount) watchedCount.textContent = watchedAnime ? watchedAnime.length : 0;
            if (followedCount) followedCount.textContent = followedAnime ? followedAnime.length : 0;
            
            // Populate anime lists
            if (watchedAnimeList) {
                if (watchedAnime && watchedAnime.length > 0) {
                    const limitedWatched = watchedAnime.slice(0, 5); // Show first 5
                    watchedAnimeList.innerHTML = limitedWatched.map(anime => `
                        <div class="admin-anime-item">
                            <small class="admin-text-primary">${escapeHtml(anime.title)}</small>
                        </div>
                    `).join('');
                    
                    if (watchedAnime.length > 5) {
                        watchedAnimeList.innerHTML += `<div class="admin-text-muted small">... and ${watchedAnime.length - 5} more</div>`;
                    }
                } else {
                    watchedAnimeList.innerHTML = '<div class="admin-text-muted small">No watched anime</div>';
                }
            }
            
            if (followedAnimeList) {
                if (followedAnime && followedAnime.length > 0) {
                    const limitedFollowed = followedAnime.slice(0, 5); // Show first 5
                    followedAnimeList.innerHTML = limitedFollowed.map(anime => `
                        <div class="admin-anime-item">
                            <small class="admin-text-primary">${escapeHtml(anime.title)}</small>
                        </div>
                    `).join('');
                    
                    if (followedAnime.length > 5) {
                        followedAnimeList.innerHTML += `<div class="admin-text-muted small">... and ${followedAnime.length - 5} more</div>`;
                    }
                } else {
                    followedAnimeList.innerHTML = '<div class="admin-text-muted small">No followed anime</div>';
                }
            }
        }
    }
    
    function populateEditModal(account) {
        const modal = document.getElementById('editAccountModal');
        if (modal) {
            const idField = modal.querySelector('#editAccountId');
            const usernameField = modal.querySelector('#editUsername');
            const displayNameField = modal.querySelector('#editDisplayName');
            const emailField = modal.querySelector('#editEmail');
            const typeField = modal.querySelector('#editType');
            
            if (idField) idField.value = account.id;
            if (usernameField) usernameField.value = account.username;
            if (displayNameField) displayNameField.value = account.display_name || '';
            if (emailField) emailField.value = account.email;
            if (typeField) typeField.value = account.type;
        }
    }

    // Form handlers
    const addAccountForm = document.getElementById('addAccountForm');
    if (addAccountForm) {
        addAccountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addAccountForm);
            
            fetch(`${baseUrl}admin/createAccount`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    bootstrap.Modal.getInstance(document.getElementById('addAccountModal')).hide();
                    addAccountForm.reset();
                    loadAccounts();
                } else {
                    showToast(data.message || 'Failed to create account', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred', 'error');
            });
        });
    }

    const editAccountForm = document.getElementById('editAccountForm');
    if (editAccountForm) {
        editAccountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(editAccountForm);
            const accountId = formData.get('account_id');
            
            fetch(`${baseUrl}admin/updateAccount/${accountId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    bootstrap.Modal.getInstance(document.getElementById('editAccountModal')).hide();
                    loadAccounts();
                } else {
                    showToast(data.message || 'Failed to update account', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred', 'error');
            });
        });
    }
});
