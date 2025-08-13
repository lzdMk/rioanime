/**
 * Admin Anime Management JavaScript
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

    // Add Anime Form
    const addAnimeForm = document.getElementById('addAnimeForm');
    if (addAnimeForm) {
        addAnimeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addAnimeForm);
            
            fetch(baseUrl + 'admin/api/anime/create', {
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
                    location.reload();
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

    // View Anime Details
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-anime')) {
            const animeId = e.target.closest('.view-anime').dataset.id;
            viewAnimeDetails(animeId);
        }
    });

    function viewAnimeDetails(animeId) {
        fetch(baseUrl + `admin/api/anime/${animeId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateViewModal(data.anime);
                new bootstrap.Modal(document.getElementById('viewAnimeModal')).show();
            } else {
                showToast(data.message || 'Failed to load anime details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function populateViewModal(anime) {
        // Basic info
        document.getElementById('viewAnimeTitle').textContent = anime.title;
        document.getElementById('viewAnimeLanguage').textContent = anime.language || 'N/A';
        document.getElementById('viewAnimeEpisodes').textContent = anime.total_ep || 'N/A';
        document.getElementById('viewAnimeGenres').textContent = anime.genres || 'N/A';
        document.getElementById('viewAnimeStudios').textContent = anime.studios || 'N/A';
        document.getElementById('viewAnimeSynopsis').textContent = anime.synopsis || 'No synopsis available.';

        // Image
        const imageContainer = document.getElementById('viewAnimeImage');
        if (anime.backgroundImage) {
            imageContainer.innerHTML = `<img src="${anime.backgroundImage}" alt="${anime.title}" class="admin-anime-image">`;
        } else {
            imageContainer.innerHTML = `<div class="admin-anime-image d-flex align-items-center justify-content-center bg-secondary text-white"><i class="fas fa-film fa-3x"></i></div>`;
        }

        // Type badge
        const typeBadge = document.getElementById('viewAnimeType');
        typeBadge.textContent = anime.type;
        typeBadge.className = `badge admin-badge-${anime.type.toLowerCase() === 'movie' ? 'warning' : 'info'}`;

        // Status badge
        const statusBadge = document.getElementById('viewAnimeStatus');
        statusBadge.textContent = anime.status;
        const statusLower = anime.status.toLowerCase();
        const statusClass = statusLower === 'finish airing' ? 'success' : 
                           (statusLower === 'airing' ? 'primary' : 
                           (statusLower === 'incomplete' ? 'warning' : 'secondary'));
        statusBadge.className = `badge admin-badge-${statusClass}`;

        // Rating
        const ratingContainer = document.getElementById('viewAnimeRating');
        if (anime.ratings) {
            ratingContainer.innerHTML = `<i class="fas fa-star"></i> ${anime.ratings}`;
        } else {
            ratingContainer.innerHTML = '<span class="admin-text-muted">No rating</span>';
        }

        // URLs
        const urlsContainer = document.getElementById('viewAnimeUrls');
        if (anime.urls) {
            const urls = anime.urls.split('\n').filter(url => url.trim());
            if (urls.length > 0) {
                urlsContainer.innerHTML = urls.map((url, index) => 
                    `<div class="mb-2">
                        <span class="badge admin-badge-primary me-2">URL ${index + 1}</span>
                        <a href="${url.trim()}" target="_blank" class="admin-text-primary">${url.trim()}</a>
                    </div>`
                ).join('');
            } else {
                urlsContainer.innerHTML = '<span class="admin-text-muted">No streaming URLs available</span>';
            }
        } else {
            urlsContainer.innerHTML = '<span class="admin-text-muted">No streaming URLs available</span>';
        }
    }

    // Edit Anime
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-anime')) {
            const animeId = e.target.closest('.edit-anime').dataset.id;
            loadAnimeForEdit(animeId);
        }
    });

    function loadAnimeForEdit(animeId) {
        fetch(baseUrl + `admin/api/anime/${animeId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditModal(data.anime);
                new bootstrap.Modal(document.getElementById('editAnimeModal')).show();
            } else {
                showToast(data.message || 'Failed to load anime details', 'error');
            }
        });
    }

    function populateEditModal(anime) {
        document.getElementById('editAnimeId').value = anime.id;
        document.getElementById('editTitle').value = anime.title || '';
        document.getElementById('editType').value = anime.type || '';
        document.getElementById('editLanguage').value = anime.language || '';
        document.getElementById('editTotalEp').value = anime.total_ep || '';
        document.getElementById('editStatus').value = anime.status || '';
        document.getElementById('editRatings').value = anime.ratings || '';
        document.getElementById('editGenres').value = anime.genres || '';
        document.getElementById('editStudios').value = anime.studios || '';
        document.getElementById('editBackgroundImage').value = anime.backgroundImage || '';
        document.getElementById('editUrls').value = anime.urls || '';
        document.getElementById('editSynopsis').value = anime.synopsis || '';
    }

    // Edit Anime Form
    const editAnimeForm = document.getElementById('editAnimeForm');
    if (editAnimeForm) {
        editAnimeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(editAnimeForm);
            const animeId = document.getElementById('editAnimeId').value;
            
            fetch(baseUrl + `admin/api/anime/update/${animeId}`, {
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
                    location.reload();
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

    // Delete Anime
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-anime')) {
            const animeId = e.target.closest('.delete-anime').dataset.id;
            const row = e.target.closest('tr');
            const title = row.querySelector('td:nth-child(2)').textContent;
            
            if (confirm(`Are you sure you want to delete "${title}"? This action cannot be undone.`)) {
                deleteAnime(animeId);
            }
        }
    });

    function deleteAnime(animeId) {
        fetch(baseUrl + `admin/api/anime/delete/${animeId}`, {
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
                showToast(data.message || 'Failed to delete anime', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    // Search and Filter functionality
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const languageFilter = document.getElementById('languageFilter');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeValue = typeFilter.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const languageValue = languageFilter.value.toLowerCase();
        const rows = document.querySelectorAll('#animeTable tbody tr');
        
        rows.forEach(row => {
            const title = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const type = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
            const language = row.querySelector('td:nth-child(7)').textContent.toLowerCase();
            
            const matchesSearch = title.includes(searchTerm);
            const matchesType = !typeValue || type.includes(typeValue);
            const matchesStatus = !statusValue || status.includes(statusValue);
            const matchesLanguage = !languageValue || language.includes(languageValue);
            
            row.style.display = (matchesSearch && matchesType && matchesStatus && matchesLanguage) ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    
    if (typeFilter) {
        typeFilter.addEventListener('change', filterTable);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
    
    if (languageFilter) {
        languageFilter.addEventListener('change', filterTable);
    }

    // Refresh button
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            location.reload();
        });
    }
});
