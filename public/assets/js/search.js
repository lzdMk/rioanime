/**
 * Search functionality for RioAnime
 * Handles real-time search with AJAX requests
 */

class SearchManager {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.searchResults = document.getElementById('searchResults');
        this.searchUrl = (window.baseUrl || '') + 'search/ajax';
        this.debounceDelay = 300;
        this.debounceTimer = null;
        this.isSearching = false;
        
        this.init();
    }

    init() {
        if (!this.searchInput || !this.searchResults) {
            console.warn('Search elements not found');
            return;
        }

        this.bindEvents();
        this.hideResults(); // Initially hide results
    }

    bindEvents() {
        // Input event for real-time search
        this.searchInput.addEventListener('input', (e) => {
            this.handleSearchInput(e.target.value);
        });

        // Focus event to show results if there's content
        this.searchInput.addEventListener('focus', () => {
            if (this.searchInput.value.trim() && this.searchResults.children.length > 0) {
                this.showResults();
            }
        });

        // Click outside to hide results
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                this.hideResults();
            }
        });

        // Handle keyboard navigation
        this.searchInput.addEventListener('keydown', (e) => {
            this.handleKeyboardNavigation(e);
        });
    }

    handleSearchInput(query) {
        // Clear existing debounce timer
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }

        // Trim query
        query = query.trim();

        // If query is empty, hide results
        if (!query) {
            this.hideResults();
            return;
        }

        // Debounce the search request
        this.debounceTimer = setTimeout(() => {
            this.performSearch(query);
        }, this.debounceDelay);
    }

    async performSearch(query) {
        if (this.isSearching) {
            return;
        }

        this.isSearching = true;
        this.showLoadingState();

        try {
            const formData = new FormData();
            formData.append('query', query);

            const response = await fetch(this.searchUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.status === 'success') {
                this.displayResults(data.results);
            } else {
                this.displayError(data.message || 'Search failed');
            }

        } catch (error) {
            console.error('Search error:', error);
            this.displayError('Failed to search. Please try again.');
        } finally {
            this.isSearching = false;
        }
    }

    showLoadingState() {
        this.searchResults.innerHTML = `
            <div class="search-loading">
                <div style="display: flex; align-items: center; justify-content: center; padding: 20px; color: #bdb7d6;">
                    <i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>
                    Searching...
                </div>
            </div>
        `;
        this.showResults();
    }

    displayResults(results) {
        if (!results || results.length === 0) {
            this.displayNoResults();
            return;
        }

        let html = '';
        results.forEach(anime => {
            html += this.createResultItem(anime);
        });

        this.searchResults.innerHTML = html;
        this.showResults();
    }

    createResultItem(anime) {
        // Format the meta information
        const metaInfo = [];
        if (anime.type && anime.type !== 'Unknown') {
            metaInfo.push(anime.type);
        }
        if (anime.status && anime.status !== 'Unknown') {
            metaInfo.push(anime.status);
        }
        if (anime.rating && anime.rating !== 'N/A') {
            metaInfo.push(`★ ${anime.rating}`);
        }

        const metaText = metaInfo.join(' • ');

        return `
            <div class="search-result-item" onclick="window.location.href='${anime.url}'">
                <img src="${anime.image}" alt="${anime.title}" class="search-result-image" onerror="this.src='https://via.placeholder.com/56x80/8B5CF6/ffffff?text=No+Image'">
                <div class="search-result-content">
                    <div class="search-result-title">${this.escapeHtml(anime.title)}</div>
                    <div class="search-result-meta">${metaText}</div>
                </div>
            </div>
        `;
    }

    displayNoResults() {
        this.searchResults.innerHTML = `
            <div class="search-no-results">
                <div style="display: flex; align-items: center; justify-content: center; padding: 20px; color: #bdb7d6;">
                    <i class="fas fa-search" style="margin-right: 8px; opacity: 0.5;"></i>
                    No anime found
                </div>
            </div>
        `;
        this.showResults();
    }

    displayError(message) {
        this.searchResults.innerHTML = `
            <div class="search-error">
                <div style="display: flex; align-items: center; justify-content: center; padding: 20px; color: #ff6b6b;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                    ${this.escapeHtml(message)}
                </div>
            </div>
        `;
        this.showResults();
    }

    showResults() {
        this.searchResults.style.display = 'block';
    }

    hideResults() {
        this.searchResults.style.display = 'none';
    }

    handleKeyboardNavigation(e) {
        const items = this.searchResults.querySelectorAll('.search-result-item');
        if (items.length === 0) return;

        let activeIndex = -1;
        items.forEach((item, index) => {
            if (item.classList.contains('active')) {
                activeIndex = index;
                item.classList.remove('active');
            }
        });

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
                items[activeIndex].classList.add('active');
                break;
            
            case 'ArrowUp':
                e.preventDefault();
                activeIndex = activeIndex <= 0 ? items.length - 1 : activeIndex - 1;
                items[activeIndex].classList.add('active');
                break;
            
            case 'Enter':
                e.preventDefault();
                if (activeIndex >= 0) {
                    items[activeIndex].click();
                }
                break;
            
            case 'Escape':
                this.hideResults();
                this.searchInput.blur();
                break;
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize search when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new SearchManager();
});

// Export for external use if needed
window.SearchManager = SearchManager;
