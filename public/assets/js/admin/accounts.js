/**
 * Admin Accounts Management JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = window.location.origin + '/rioanime/';
    
    // Toast helper
    function showToast(message, type = 'success') {
        const toastBody = document.getElementById('toastBody');
        const toast = document.getElementById('liveToast');
        
        toastBody.textContent = message;
        toast.className = `toast align-items-center text-bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }

    // Add Account Form
    const addAccountForm = document.getElementById('addAccountForm');
    if (addAccountForm) {
        addAccountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addAccountForm);
            
            fetch(baseUrl + 'admin/api/account/create', {
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
                    location.reload(); // Refresh the page to show new account
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

    // View Account Details
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-account')) {
            const accountId = e.target.closest('.view-account').dataset.id;
            viewAccountDetails(accountId);
        }
    });

    function viewAccountDetails(accountId) {
        fetch(baseUrl + `admin/api/account/${accountId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateViewModal(data.account, data.watched_anime, data.followed_anime);
                new bootstrap.Modal(document.getElementById('viewAccountModal')).show();
            } else {
                showToast(data.message || 'Failed to load account details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function populateViewModal(account, watchedAnime, followedAnime) {
        // Basic info
        document.getElementById('viewUsername').textContent = account.username;
        document.getElementById('viewEmail').textContent = account.email;
        document.getElementById('viewDisplayName').textContent = account.display_name || account.username;
        document.getElementById('viewCreated').textContent = new Date(account.created_at).toLocaleDateString();
        document.getElementById('viewWatchedCount').textContent = watchedAnime.length;
        document.getElementById('viewFollowedCount').textContent = followedAnime.length;

        // Avatar
        const avatarContainer = document.getElementById('viewAvatar');
        if (account.user_profile) {
            avatarContainer.innerHTML = `<img src="${account.user_profile}" alt="Avatar" class="admin-avatar" style="width: 80px; height: 80px;">`;
        } else {
            avatarContainer.innerHTML = `<div class="admin-avatar-letter" style="width: 80px; height: 80px; font-size: 2rem;">${account.username.charAt(0).toUpperCase()}</div>`;
        }

        // Account type badge
        const typeBadge = document.getElementById('viewType');
        typeBadge.textContent = account.type.charAt(0).toUpperCase() + account.type.slice(1);
        typeBadge.className = `badge admin-badge-${account.type === 'admin' ? 'danger' : 'primary'}`;

        // Watched anime list
        const watchedList = document.getElementById('watchedAnimeList');
        populateAnimeList(watchedList, watchedAnime, 'watched');

        // Followed anime list
        const followedList = document.getElementById('followedAnimeList');
        populateAnimeList(followedList, followedAnime, 'followed');

        // Reset expand buttons
        resetExpandButtons();
    }

    function populateAnimeList(container, animeList, type) {
        if (animeList.length > 0) {
            const maxItems = 5; // Show only first 5 items initially
            const itemsToShow = animeList.slice(0, maxItems);
            const hasMore = animeList.length > maxItems;

            let html = itemsToShow.map(anime => createAnimeItem(anime)).join('');
            
            if (hasMore) {
                html += `<div class="anime-item text-center show-more-item" style="background: rgba(120, 120, 255, 0.1); border: 1px dashed var(--primary-color);">
                    <div class="w-100 text-center">
                        <i class="fas fa-plus-circle admin-text-primary me-2"></i>
                        <span class="admin-text-primary">+${animeList.length - maxItems} more items</span>
                    </div>
                </div>`;
            }

            container.innerHTML = html;
            container.dataset.fullList = JSON.stringify(animeList);
            container.dataset.type = type;
        } else {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-film"></i>
                    <p class="mb-0">No ${type} anime yet.</p>
                </div>
            `;
        }
    }

    function createAnimeItem(anime) {
        return `
            <div class="anime-item">
                <img src="${anime.backgroundImage || 'https://via.placeholder.com/50x70'}" alt="${anime.title}">
                <div class="anime-info">
                    <h6 class="mb-1">${anime.title}</h6>
                    <small class="admin-text-muted">${anime.type} • ${anime.total_ep ? 'Ep ' + anime.total_ep : 'Ongoing'}</small>
                </div>
            </div>
        `;
    }

    function resetExpandButtons() {
        const expandWatched = document.getElementById('expandWatched');
        const expandFollowed = document.getElementById('expandFollowed');
        
        expandWatched.innerHTML = '<i class="fas fa-expand-alt"></i> Show All';
        expandFollowed.innerHTML = '<i class="fas fa-expand-alt"></i> Show All';
        
        expandWatched.classList.remove('expanded');
        expandFollowed.classList.remove('expanded');
    }

    // Expand/Collapse functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('#expandWatched')) {
            toggleAnimeList('watchedAnimeList', 'expandWatched');
        }
        
        if (e.target.closest('#expandFollowed')) {
            toggleAnimeList('followedAnimeList', 'expandFollowed');
        }
    });

    function toggleAnimeList(containerId, buttonId) {
        const container = document.getElementById(containerId);
        const button = document.getElementById(buttonId);
        const fullList = JSON.parse(container.dataset.fullList || '[]');
        const type = container.dataset.type;

        if (button.classList.contains('expanded')) {
            // Collapse - show only first 5 items
            populateAnimeList(container, fullList, type);
            button.innerHTML = '<i class="fas fa-expand-alt"></i> Show All';
            button.classList.remove('expanded');
            container.classList.remove('expanded');
        } else {
            // Expand - show all items
            const html = fullList.map(anime => createAnimeItem(anime)).join('');
            container.innerHTML = html;
            button.innerHTML = '<i class="fas fa-compress-alt"></i> Show Less';
            button.classList.add('expanded');
            container.classList.add('expanded');
        }
    }

    // Edit Account
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-account')) {
            const accountId = e.target.closest('.edit-account').dataset.id;
            loadAccountForEdit(accountId);
        }
    });

    function loadAccountForEdit(accountId) {
        fetch(baseUrl + `admin/api/account/${accountId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditModal(data.account);
                new bootstrap.Modal(document.getElementById('editAccountModal')).show();
            } else {
                showToast(data.message || 'Failed to load account details', 'error');
            }
        });
    }

    function populateEditModal(account) {
        document.getElementById('editAccountId').value = account.id;
        document.getElementById('editUsername').value = account.username;
        document.getElementById('editDisplayName').value = account.display_name || '';
        document.getElementById('editEmail').value = account.email;
        document.getElementById('editType').value = account.type;
        document.getElementById('editPassword').value = '';
    }

    // Edit Account Form
    const editAccountForm = document.getElementById('editAccountForm');
    if (editAccountForm) {
        editAccountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(editAccountForm);
            const accountId = document.getElementById('editAccountId').value;
            
            fetch(baseUrl + `admin/api/account/update/${accountId}`, {
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
                    location.reload();
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

    // Delete Account
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-account')) {
            const accountId = e.target.closest('.delete-account').dataset.id;
            const row = e.target.closest('tr');
            const username = row.querySelector('td:nth-child(2)').textContent;
            
            if (confirm(`Are you sure you want to delete the account "${username}"? This action cannot be undone.`)) {
                deleteAccount(accountId);
            }
        }
    });

    function deleteAccount(accountId) {
        fetch(baseUrl + `admin/api/account/delete/${accountId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                location.reload();
            } else {
                showToast(data.message || 'Failed to delete account', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
        const rows = document.querySelectorAll('#accountsTable tbody tr');
        
        rows.forEach(row => {
            const username = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const type = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            
            const matchesSearch = username.includes(searchTerm) || email.includes(searchTerm);
            const matchesType = !typeFilter || type.includes(typeFilter);
            
            row.style.display = (matchesSearch && matchesType) ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    
    if (typeFilter) {
        typeFilter.addEventListener('change', filterTable);
    }

    // Refresh button
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            location.reload();
        });
    }
});
