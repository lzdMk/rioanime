/**
 * Admin Anime Management JavaScript with Custom Pagination
 */
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = window.location.origin + '/rioanime/';
    let animePagination = null;
    let currentPage = 1;
    let currentSearch = '';
    let currentTypeFilter = '';
    let currentStatusFilter = '';
    let currentPerPage = 8;
    const minPerPage = 5;

    // DOM elements
    const searchInput = document.getElementById('animeSearch');
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const customPerPageInput = document.getElementById('customPerPageInput');
    const refreshBtn = document.getElementById('refreshBtn');
    const tableContainer = document.getElementById('animeTableContainer');
    const paginationContainer = document.getElementById('animePagination');
    
    // Initialize
    loadAnime();
    
    // Search functionality
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = this.value;
                currentPage = 1;
                loadAnime();
            }, 500);
        });
    }
    
    // Type filter functionality
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            currentTypeFilter = this.value;
            currentPage = 1;
            loadAnime();
        });
    }
    
    // Status filter functionality
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            currentStatusFilter = this.value;
            currentPage = 1;
            loadAnime();
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
                loadAnime();
            }
        });
    }
    
    if (customPerPageInput) {
        customPerPageInput.addEventListener('blur', function() {
            const value = parseInt(this.value);
            if (value && value >= minPerPage) {
                currentPerPage = value;
                currentPage = 1;
                loadAnime();
            } else {
                this.value = Math.max(minPerPage, currentPerPage);
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
            loadAnime();
        });
    }
    
    // Load anime function
    function loadAnime() {
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
        
        if (currentStatusFilter) {
            params.append('status', currentStatusFilter);
        }
        
        fetch(`${baseUrl}admin/getAnimeList?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderAnimeTable(data.anime_list);
                setupPagination(data.pagination);
            } else {
                showToast('Failed to load anime', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error loading anime', 'error');
        });
    }
    
    // Render anime table
    function renderAnimeTable(animeList) {
        if (animeList.length === 0) {
            tableContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-film fa-4x admin-text-muted mb-3"></i>
                    <h5 class="admin-text-muted">No anime found</h5>
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
                            <th>Image</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Episodes</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Language</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="admin-table-body">
                        ${animeList.map(anime => `
                            <tr data-anime-id="${anime.anime_id}">
                                <td class="text-center">
                                    <img src="${escapeHtml(anime.backgroundImage)}" alt="${escapeHtml(anime.title)}" class="admin-anime-thumb">
                                </td>
                                <td class="admin-text-primary fw-bold">${escapeHtml(anime.title)}</td>
                                <td>
                                    <span class="badge admin-badge-${anime.type.toLowerCase() === 'movie' ? 'warning' : 'info'}">
                                        ${escapeHtml(anime.type)}
                                    </span>
                                </td>
                                <td class="admin-text-muted">${anime.total_ep || 'N/A'}</td>
                                <td>
                                    <span class="badge admin-badge-${getStatusBadgeClass(anime.status)}">
                                        ${escapeHtml(anime.status)}
                                    </span>
                                </td>
                                <td class="admin-text-muted">${anime.ratings || 'N/A'}</td>
                                <td class="admin-text-muted">${anime.language || 'N/A'}</td>
                                <td>
                                    <div class="btn-group admin-action-buttons" role="group">
                                        <button class="btn admin-btn-info btn-sm view-anime" data-id="${anime.anime_id}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn admin-btn-warning btn-sm edit-anime" data-id="${anime.anime_id}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn admin-btn-danger btn-sm delete-anime" data-id="${anime.anime_id}" title="Delete">
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
        if (animePagination) {
            animePagination.destroy();
        }
        
        animePagination = new AdminPagination({
            container: paginationContainer,
            currentPage: paginationData.current_page,
            totalPages: paginationData.total_pages,
            totalItems: paginationData.total_items,
            perPage: paginationData.per_page,
            onPageChange: (page) => {
                currentPage = page;
                loadAnime();
            }
        });
    }
    
    // Attach event listeners to table buttons
    function attachTableEventListeners() {
        // View anime buttons
        document.querySelectorAll('.view-anime').forEach(btn => {
            btn.addEventListener('click', function() {
                const animeId = this.getAttribute('data-id');
                viewAnime(animeId);
            });
        });
        
        // Edit anime buttons
        document.querySelectorAll('.edit-anime').forEach(btn => {
            btn.addEventListener('click', function() {
                const animeId = this.getAttribute('data-id');
                editAnime(animeId);
            });
        });
        
        // Delete anime buttons
        document.querySelectorAll('.delete-anime').forEach(btn => {
            btn.addEventListener('click', function() {
                const animeId = this.getAttribute('data-id');
                deleteAnime(animeId);
            });
        });
    }
    
    // Helper functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function getStatusBadgeClass(status) {
        const statusLower = status.toLowerCase();
        if (statusLower === 'finished airing') return 'success';
        if (statusLower === 'airing') return 'primary';
        if (statusLower === 'incomplete') return 'warning';
        return 'secondary';
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
    function viewAnime(animeId) {
        fetch(`${baseUrl}admin/getAnime/${animeId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateViewModal(data.anime);
                const modal = new bootstrap.Modal(document.getElementById('viewAnimeModal'));
                modal.show();
            } else {
                showToast(data.message || 'Failed to load anime details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error loading anime details', 'error');
        });
    }
    
    function editAnime(animeId) {
        fetch(`${baseUrl}admin/getAnime/${animeId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditModal(data.anime);
                const modal = new bootstrap.Modal(document.getElementById('editAnimeModal'));
                modal.show();
            } else {
                showToast(data.message || 'Failed to load anime details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error loading anime details', 'error');
        });
    }
    
    function deleteAnime(animeId) {
        // Get anime title for confirmation
        const animeRow = document.querySelector(`tr[data-anime-id="${animeId}"]`);
        const animeTitle = animeRow ? animeRow.querySelector('.admin-text-primary').textContent : 'this anime';
        
        // Set the title in the modal
        const titleElement = document.getElementById('deleteAnimeTitle');
        if (titleElement) titleElement.textContent = animeTitle;
        
        // Show the modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteAnimeModal'));
        deleteModal.show();
        
        // Set up the confirm button
        const confirmBtn = document.getElementById('confirmDeleteAnime');
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        newConfirmBtn.addEventListener('click', function() {
            fetch(`${baseUrl}admin/deleteAnime/${animeId}`, {
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
                    loadAnime();
                } else {
                    showToast(data.message || 'Failed to delete anime', 'error');
                }
            })
            .catch(error => {
                deleteModal.hide();
                console.error('Error:', error);
                showToast('Error deleting anime', 'error');
            });
        });
    }
    
    function populateViewModal(anime) {
        const modal = document.getElementById('viewAnimeModal');
        if (modal) {
            const title = modal.querySelector('#viewAnimeTitle');
            const image = modal.querySelector('#viewAnimeImage');
            const rating = modal.querySelector('#viewAnimeRating');
            const synopsis = modal.querySelector('#viewAnimeSynopsis');
            const type = modal.querySelector('#viewAnimeType');
            const status = modal.querySelector('#viewAnimeStatus');
            const language = modal.querySelector('#viewAnimeLanguage');
            const episodes = modal.querySelector('#viewAnimeEpisodes');
            const genres = modal.querySelector('#viewAnimeGenres');
            const studios = modal.querySelector('#viewAnimeStudios');
            const urls = modal.querySelector('#viewAnimeUrls');
            
            if (title) title.textContent = anime.title;
            if (image) image.innerHTML = `<img src="${anime.backgroundImage}" alt="${anime.title}" class="img-fluid rounded">`;
            if (rating) rating.textContent = anime.ratings ? `${anime.ratings}/10` : 'N/A';
            if (synopsis) synopsis.textContent = anime.synopsis || 'No synopsis available';

            if (type) {
                type.textContent = anime.type || 'N/A';
                type.className = `badge admin-badge-${(anime.type || '').toLowerCase() === 'movie' ? 'warning' : 'info'}`;
            }
            if (status) {
                const statusLower = (anime.status || '').toLowerCase();
                status.textContent = anime.status || 'Unknown';
                status.className = `badge admin-badge-${statusLower === 'finished airing' ? 'success' : statusLower === 'airing' ? 'primary' : statusLower === 'incomplete' ? 'warning' : 'secondary'}`;
            }
            if (language) language.textContent = anime.language || 'N/A';
            if (episodes) episodes.textContent = anime.total_ep != null ? anime.total_ep : 'N/A';
            if (genres) genres.textContent = anime.genres || '—';
            if (studios) studios.textContent = anime.studios || '—';
            if (urls) {
                if (anime.urls) {
                    const list = anime.urls
                        .split(/\r?\n/)
                        .map(u => u.trim())
                        .filter(u => u.length > 0)
                        .map(u => `<div><a href="${u}" target="_blank" rel="noopener" class="admin-link">${u}</a></div>`) 
                        .join('');
                    urls.innerHTML = list || '<span class="admin-text-muted">No URLs provided</span>';
                } else {
                    urls.innerHTML = '<span class="admin-text-muted">No URLs provided</span>';
                }
            }
        }
    }
    
    function populateEditModal(anime) {
        const modal = document.getElementById('editAnimeModal');
        if (modal) {
            const idField = modal.querySelector('#editAnimeId');
            const titleField = modal.querySelector('#editTitle');
            const typeField = modal.querySelector('#editType');
            const languageField = modal.querySelector('#editLanguage');
            const totalEpField = modal.querySelector('#editTotalEp');
            const ratingsField = modal.querySelector('#editRatings');
            const statusField = modal.querySelector('#editStatus');
            const genresField = modal.querySelector('#editGenres');
            const studiosField = modal.querySelector('#editStudios');
            const backgroundImageField = modal.querySelector('#editBackgroundImage');
            const urlsField = modal.querySelector('#editUrls');
            const synopsisField = modal.querySelector('#editSynopsis');
            
            if (idField) idField.value = anime.anime_id || '';
            if (titleField) titleField.value = anime.title || '';
            if (typeField) typeField.value = anime.type || '';
            if (languageField) languageField.value = anime.language || '';
            if (totalEpField) totalEpField.value = anime.total_ep || '';
            if (ratingsField) ratingsField.value = anime.ratings || '';
            if (statusField) statusField.value = anime.status || '';
            if (genresField) genresField.value = anime.genres || '';
            if (studiosField) studiosField.value = anime.studios || '';
            if (backgroundImageField) backgroundImageField.value = anime.backgroundImage || '';
            if (urlsField) urlsField.value = anime.urls || '';
            if (synopsisField) synopsisField.value = anime.synopsis || '';
        }
    }

    // Form handlers
    const addAnimeForm = document.getElementById('addAnimeForm');
    if (addAnimeForm) {
        addAnimeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addAnimeForm);
            
            fetch(`${baseUrl}admin/createAnime`, {
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
                    bootstrap.Modal.getInstance(document.getElementById('addAnimeModal')).hide();
                    addAnimeForm.reset();
                    loadAnime();
                } else {
                    showToast(data.message || 'Failed to create anime', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred', 'error');
            });
        });
    }

    const editAnimeForm = document.getElementById('editAnimeForm');
    if (editAnimeForm) {
        editAnimeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(editAnimeForm);
            const animeId = formData.get('anime_id');
            
            fetch(`${baseUrl}admin/updateAnime/${animeId}`, {
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
                    bootstrap.Modal.getInstance(document.getElementById('editAnimeModal')).hide();
                    loadAnime();
                } else {
                    showToast(data.message || 'Failed to update anime', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred', 'error');
            });
        });
    }
});
